<?php

namespace app\Controllers;

use app\Models\AppModel;
use Server\CoreBase\Controller;

/**
 * Created by PhpStorm.
 * User: zhangjincheng
 * Date: 16-7-15
 * Time: 下午3:51
 */
class RoomController extends Controller
{
    /**
     * @var AppModel
     */
    public $CommModel;
    public $data; // 传过来数据
    public $mid; //传过来的id
    public $room_id;//传过来的房间号
    public $roomInfo;//房间信息 
    public $userInfo;//用户信息
    public $gameInfo;//游戏信息
    public $uids;//所有玩家mid
    public $user; //个人信息
   /**
     * 一共6个队列
     * 庄  z.'_'.$this->room_id.
     * 闲  x.'_'.$this->room_id.
     * 和  h.'_'.$this->room_id.
     * 庄天王  ztw.'_'.$this->room_id.
     * 闲天王  xtw.'_'.$this->room_id.
     * 所有总和  zonghe.'_'.$this->room_id.
     *
     * 两个哈希
     * 1. keys=> $this->room_id
     *     key=>roomInfo[‘numb’=》1W
    ‘zj’=》【头像，金币，姓名】
    'z'=>0 //总数
    'x=>0,/总数
    'h'=>0,/总数
    'ztw'=>0,/总数
    'xtw'=>0,/总数
    'status'=> 1  //状态
    'lun'=>1  // 第几轮
    'ju'=>2   第几局
    luzi=》【  路单
    0=》z
    1=》x
    2=》h]

   userInfo = 【mid=》【'z'=>0,'x'=>0,'h'=>0,.....】】

   2. 表名 uids_roomid
        字段mid【 用户信息】

     * User: shijunyi
     * Date: 3/22
     *管道里面存的消息[mid-金额]
     */
    protected function initialization($controller_name,$method_name)
    {
        parent::initialization($controller_name, $method_name);
        $this->CommModel = $this->loader->model('CommModel', $this);

        $this->data = $this->client_data->data;
        $this->mid = $this->data->mid;
        $this->room_id = $this->data->room_id;

       $room  =  yield $this->redis_pool->getCoroutine()->hgetall($this->room_id);
         //获取个人信息
   
        $user =  yield $this->redis_pool->hget('uids_' . $this->room_id,$this->mid);
        $this->user = unserialize($user);
       
        if (isset($room['roomInfo']) && $room['roomInfo']){
          $this->roomInfo = unserialize($room['roomInfo']);
        }
        if(isset($room['userInfo']) && $room['userInfo']){
              $this->userInfo = unserialize($room['userInfo']);
        }

        $res =  yield $this->CommModel->exiit($this->data);//判断传过来的类型;

        if($res){
            $this->send(reData('out', '数据有误'),false);
            $this->close();
            return;
        }
        //获取房间所有人
        $this->uids = yield $this->redis_pool->getCoroutine()->hkeys('uids_' . $this->room_id);
        if(!$this->uid){

            $this->bindUid($this->mid);
        }
      
    }
    /**
     * 进入房间流程.
     * User: shijunyi
     * Date: 3/22
     *
     */
    public function jinru()
    {
        D('jinru',$this->data);

        if ($this->is_destroy) {

            return;
        }
        $roomid = $this->room_id;
        $re = yield $this->CommModel->jinru($this->mid, $this->roomInfo,$roomid);//判断传过来的类型;
        if(!$re){
            $this->send(reData('out', '数据有误'),false);
            $this->close();
            return;
        }
         // $this->user = $re;
        //存玩家信息
        if($re['user']){//如果是重连
          $userInfo = $this->userInfo;

        }else{
        $userInfo = [];
        $userInfo[$this->mid]['z'] = 0;//庄
        $userInfo[$this->mid]['x'] = 0; //闲
        $userInfo[$this->mid]['h'] = 0;//和
        $userInfo[$this->mid]['ztw'] = 0;//庄天王
        $userInfo[$this->mid]['xtw'] = 0;//闲天王
        $userInfo[$this->mid]['status'] = 0;
        yield $this->redis_pool->getCoroutine()->hset('uids_' . $this->room_id,$this->mid,serialize($re['member']));
        yield $this->redis_pool->getCoroutine()->hset($this->room_id,'userInfo',serialize($userInfo));
        }
     
       $roomz = yield $this->redis_pool->getCoroutine()->lrange('zonghe_'.$this->room_id,0 ,-1); //取出房间中所有的庄码数
       // $time = time();
       $roomInfo = $this->roomInfo;
       $ti = $roomInfo['time']+26 - time();
       D('房间倒计时 时间为:',$ti);
       $data = [
        'time'=>$ti,
        'roomInfo'=>$this->roomInfo,//庄家信息，房间信息
        'user'=>$re['member'], //个人信息
        'userInfo'=>$userInfo,
        'pop'=>$roomz
       ];
       $this->sendToUid($this->mid,reData('jinru',$data),false);
        $this->destroy();

    }


    /**
     * 押注.
     * User: shijunyi
     * Date: 3/22
     *传 [mid-z-金额]
     *存[mid-金额]
     */
    public function yazhu()
    {
        $z = $this->data->yazhu;
         $men  = explode('-',$z);
         $zhu = $men[0].'-'.$men[2];
         $k = $men[1];
         $money = $men[2];
         $roomInfo = $this->roomInfo;
    
         // 1为押注时间 2为等待时间 3为开牌结算时间
         if($roomInfo['status'] != 1 ){
             $this->sendToUid($this->mid,reData('error',['msg'=>'未到押注时间']));
             return ;
             $this->destroy();
         }
         $re = yield $this->CommModel->money($this->room_id,$this->mid,$money);
         if(!$re){
            $this->sendToUid($this->mid,reData('error',['msg'=>'金币不足']));
            return;
             $this->destroy();
         }
           //存玩家信息
        $userInfo = [];
        $userInfo[$this->mid]['z'] = 0;//庄
        $userInfo[$this->mid]['x'] = 0; //闲
        $userInfo[$this->mid]['h'] = 0;//和
        $userInfo[$this->mid]['ztw'] = 0;//庄天王
        $userInfo[$this->mid]['xtw'] = 0;//闲天王
         yield $this->redis_pool->getCoroutine()->lpush($k.'_'.$this->room_id,$zhu);
         yield $this->redis_pool->getCoroutine()->lpush('zonghe_'.$this->room_id,$z);
         yield $this->fanhui($k);
          $this->destroy();
    }

    /**
     * 获取所有玩家.
     * User: shijunyi
     * Date: 3/22
     */
    public function getusers()
    {
          $us  =  yield $this->redis_pool->getCoroutine()->hgetall('uids_' . $this->room_id);
          $users = [];
          foreach ($us as $key => $value) {
              $users[$key] = unserialize($value);
          }

          $this->sendToUid($this->mid,reData('getusers',$users));
           $this->destroy();

    }
      /**
     * 上庄.mid room_id
     * User: shijunyi
     * Date: 3/22
     */
    public function zhuang()
    {
        $money = 20000000;
        $roomInfo = $this->roomInfo;
        $roomInfo['zj']['ju'] = 0;
        // $roomInfo['zj'] = $this->user;
        $re = yield $this->CommModel->money($this->room_id,$this->mid,$money);//// 判断玩家金币是否可以下注
        $user =  $re;
          if(!$re){
            $this->sendToUid($this->mid,reData('error',['msg'=>'金币不足']));
            return;
         }
        if(!empty($roomInfo['zj'])){

           if($roomInfo['zj']['numb'] > $user['numb']){
             $this->sendToUid($this->mid,reData('error',['msg'=>'金币没有庄家多']));
            return;
           } 
        }
         $re = yield $this->CommModel->money($this->room_id,$this->mid,$money);
        
         $data = [
            'status'=>1,
            'user'=>$this->user
         ];
           $this->sendToUids($this->uids,reData('zhuang',$data),false);
        $roomInfo['zj']['ju'] += 1;
        $roomInfo['zj'][$this->mid] = $this->user;
         yield $this->redis_pool->getCoroutine()->hset($this->room_id,'roomInfo',serialize($roomInfo));
           $this->destroy();
    }
    //循环取list 表数据，发送给前端
    private function fanhui($k)
    {
        $roomInfo = $this->roomInfo;
        $userInfo = $this->userInfo;
        // while(1){    //并发的时候用 取出所有
        $pop =  yield $this->redis_pool->getCoroutine()->rpop($k.'_'.$this->room_id);
        D($k.'里面的值',$pop);
        // if(!$pop){
        //     break;
        // }
        $pop = explode('-',$pop);
        $mid = $pop[0];
        $money = $pop[1];
        if(!isset($userInfo[$mid][$k])){
            $userInfo[$mid][$k] = 0;
        }
        $userInfo[$mid][$k] += $money; //用户压得注 和金额
        $userInfo[$mid]['status'] = 1;
        $roomInfo[$k] += $money; //房间总注，金额
        $pop = $mid.'-'.$k.'-'.$money ;
        $user =  $this->user;
        $user['num'] -= $money;
        D('用户'.$mid.'押注-'.$pop,$user);
        $data = [
            'mid'=>$mid,
            'roomInfo'=>$roomInfo,
            'userInfo'=>$userInfo[$mid],//用户压注情况
            'user'=>$user, //用户个人信息
            'zhu'=>$pop //返回压得注
        ];

             //存用户
       yield $this->redis_pool->getCoroutine()->hset('uids_' . $this->room_id,$mid,serialize($user));
        $this->sendToUids($this->uids,reData('yazhu',$data),false);
        yield $this->redis_pool->getCoroutine()->hset($this->room_id,'userInfo',serialize($userInfo),'roomInfo',serialize($roomInfo));
   
        // }
    }
    /**
     * 离开房间.
     * User: shijunyi
     * Date: 3/22
     *
     */
    public function game_exit()
    {
        if ($this->is_destroy) {
            return;
        }
        //模型处理数据$mid, $room_id, $roomInfo,$gameInfo,$userInfo
        $re = yield $this->CommModel->likai($this->mid, $this->room_id, $this->roomInfo);
        if($re){


            $this->sendToUids($this->uids, reData('game_exit',$re), false);

        }
        $this->destroy();
    }


    public function heartbeat()
    {
        if ($this->is_destroy) {
            return;
        }
//        if(yield $this->redis_pool->getCoroutine()->get('del_'.$this->room_id)){
//            $this->send(reData('out', '房主解散房间'), false);
//        }
        if($this->room_id){
            $redis_key = 'heartbeat_' . $this->room_id;
            if (yield $this->redis_pool->getCoroutine()->setnx($redis_key, time())) {
                //expireAt 设置时间周期
                yield $this->redis_pool->expireAt($redis_key, time() + 10);
                if($this->uids){
                    $this->sendToUids($this->uids,reData('heartbeat',$this->room_id));
                }

            } else {
                $this->destroy();
            }
        }
        $this->destroy();
    }

  public function kaipai()
    {
        //检查房间输赢总池如否存在
       $zong = yield $this->redis_pool->getCoroutine()->EXISTS('bjl_zong');//取出房间信息
       if($zong){
           $zwin =  yield $this->redis_pool->getCoroutine()->Hget('bjl_zong','ying');
           $zwin = intval($zwin);
           $zshu =   yield $this->redis_pool->getCoroutine()->Hget('bjl_zong','shu');
           $zshu = intval($zshu);
       }else{
            yield $this->redis_pool->getCoroutine()->Hset('bjl_zong','shu',1,'ying',1);
       }
        D('kaipai',['status'=>1]);
        $roomid = 1;
        $time = time();
      
         $roomInfo  =  yield $this->redis_pool->getCoroutine()->hget($roomid,'roomInfo');//取出房间信息
      
         $uids = yield $this->redis_pool->getCoroutine()->hkeys('uids_' . $roomid);//取出所有人的id
         $roomInfo =  unserialize($roomInfo);

        $roomInfo['time'] = time();  //存开始投注时间
        $roomInfo['status'] = 1;  // 1为押注时间20秒 2为等待时间 3为开牌结算时间
        // yield $this->redis_pool->getCoroutine()->hset($roomid,'roomInfo',serialize($user));
        $time = $roomInfo['time']+20 - time();
        $luzi = $roomInfo['luzi'];
        $this->sendToUids($uids,reData('bjl_time',['time'=>$time,'status'=>1,'luzi'=>$luzi]),false); //time = 120
        // 机器人下注
        yield $this->redis_pool->getCoroutine()->hset($roomid,'roomInfo',serialize($roomInfo));
         yield sleepCoroutine(1000); 
        D('机器人下注',['status'=>1]);
        $i = 0;
        while($i<20){
                //没秒下注次数注
             $rand = rand(1,50);
             
        for($j=0;$j<$rand;$j++){
                 $rooms  =  yield $this->redis_pool->getCoroutine()->hget($roomid,'roomInfo');//取出房间信息
                   $rooms =  unserialize($rooms);
                 // var_dump($rooms['status']);
                $proarr = bet_rand();
                $zxh = robot_rand($proarr); //庄闲和ztw xtw
                $money = robot_rand(money());
    
                $rooms[$zxh] += $money;
                //[mid-z-金额]
                $zhu = '0-'.$zxh.'-'.$money;
                 yield $this->redis_pool->getCoroutine()->lpush('zonghe_'.$roomid,$zhu);//压入总注池子
                 yield $this->redis_pool->getCoroutine()->hset($roomid,'roomInfo',serialize($rooms));
            $data = [
            'status'=>2,
            'mid'=>0,
            'roomInfo'=>$roomInfo,
            'userInfo'=>[],//用户压注情况
            'user'=>[], //用户个人信息
            'zhu'=>$zhu //返回压得注
        ];

        $this->sendToUids($this->uids,reData('yazhu',$data),false);
        }
           yield sleepCoroutine(1000);
           $i++;
        }
        $roomInfo['status'] = 2;  // 1为押注时间20秒 2为等待时间 3为开牌结算时间
            D('等待3秒',['status'=>2]);
        yield $this->redis_pool->getCoroutine()->hset($roomid,'roomInfo',serialize($roomInfo));

        $this->sendToUids($uids,reData('bjl_time',['time'=>3,'status'=>2]),false); 
    
         $pai = $roomInfo['pai'];//房间剩余牌 //////////分控操作pais
            if(count($pai) <= 6){
                  $pai = washCards();
                 $roomInfo['pai'] = $pai;
                 $roomInfo['lun'] +=1;
            }
         //WinTw($pais,$win)  天王控 ztw xtw
         //control($pais,$win) 庄闲控 z x
         $bili = $zshu/$zwin;
         D('庄家输赢比例',$bili);
         $kongbi = 1/5;  //风控比
         $gailv =  robot_rand(fenkong()) ;
         //取出所有用户押注
                $userInfo  =  yield $this->redis_pool->getCoroutine()->hget($roomid,'userInfo');
              $userInfo = unserialize($userInfo);
         if($bili > $kongbi && $gailv == 1){ //如果输多了 开启分控
            $zx = 0;
            $zz = 0;
            //所有用户下得注
            foreach($userInfo as $u =>$tz){
                $zz += $tz['z'];
                $zx += $tz['x'];
            }
              if($zx > $zz){
                    $win = 'z';
              }else{
                $win = 'x';
              }
              E("分控开启 赢{$win}");
            $jieguo = controlzong($pai,$win);
         }else{
            E('分控未开启');
               $jieguo =  getPai($pai);
         }

        $roomInfo['pai'] = $jieguo['pais'];
        $roomInfo['ju'] += 1;
        yield sleepCoroutine(3000);
        $roomInfo['status'] = 3;  // 1为押注时间20秒 2为等待时间 3为开牌结算时间
        D('开牌结算',['status'=>3]);
        yield $this->redis_pool->getCoroutine()->hset($roomid,'roomInfo',serialize($roomInfo));
        $this->sendToUids($uids,reData('kaipai',$jieguo),false); //time = 120
        //第一位数为z x h 谁赢 1为闲大 2为庄大 3为和
        //第二位 为天王 0没有天王 1为闲天王 2为庄天王 3为庄闲都是天王
        $win = $jieguo['win']; 
        $one = substr($win, 0, 1); // 截取第一位
        $tow = substr($win, 1, 1); 
        $zhuanhuan = zhuanhuan($one,$tow);
        $one = $zhuanhuan['zxh'];
        $tow = $zhuanhuan['tw'];
        $tree = $zhuanhuan['twh'];
        $shuz = $zhuanhuan['shuz'];//输庄
        $shux = $zhuanhuan['shux'];//输闲
        $shuh = $zhuanhuan['shuh'];//输和
        $shuztw = $zhuanhuan['shuztw'];//输庄天王
        $shuxtw = $zhuanhuan['shuxtw'];//输闲天王
        $peilu = Site();//['x'=>2,'z'=>1.95,'h'=>9,'ztw'=>3,'xtw'=>3];
            // sleep(1);
          $user  =  yield $this->redis_pool->getCoroutine()->hgetall('uids_' . $roomid);//取出所有用户信息
        
            yield sleepCoroutine(1000);
            $zhuangy = 0;//所有赢的
            $zhuangs = 0;//所有输的
            //如果有用户下注则返回结果
            if(!empty($userInfo)){
                 foreach ($userInfo as $k => $v) {  //！！！！！！！！！！！！
                 if($v['status'] == 1){ //如果没有参与游戏 跳出此次循环
                          $zongwin = 0;//总赢
                         $zongshu = 0;//总输
            if($one && $tree){//如果开奖结果庄闲都是天王

                $winzxh = $v[$one] * $peilu[$one]; //庄闲和
                $wintwh = $v['ztw'] * $peilu['ztw']; // 庄闲天王都中
                $wintwh += $v['xtw'] * $peilu['xtw'];
                $zongwin = $winzxh+$wintwh; 
                // $zhuangs += $zongwin;
            }elseif($one && $tow){
                $winzxh = $v[$one] * $peilu[$one]; 
                $wintwh = $v[$tow] * $peilu[$tow];
                $zongwin = $winzxh+$wintwh; 
                // $zhuangs +=$zongwin;
            }else{
               $zongwin = $v[$one] * $peilu[$one]; 
               // $zhuangs = $zongwin;
            }
            if($shuz){
                $zongshu += $v[$shuz];
         
            }
            if($shux){
                $zongshu += $v[$shux];
         
            }
            if($shuh){
                $zongshu += $v[$shuh];
         
            }
            if($shuztw){
               $zongshu += $v[$shuztw];
              
            }
            if($shuxtw){
                $zongshu += $v[$shuxtw];
               
            }
              $zhuangy += $zongshu;
              $zhuangs += $zongwin;
            $money = $zongshu-$zongwin;
                 D('money',$money);
                yield $this->mysql_pool->dbQueryBuilder
                    ->update('gs_member')
                    ->set('num',"num-{$money}",false)
                    ->where('id',$k)
                    ->coroutineSend();
            $user = unserialize($user[$k]);
             $member = yield $this->mysql_pool->dbQueryBuilder
                    ->select('headimgurl')
                    ->select('nickname')
                    ->select('num')
                    ->select('ip')
                    ->select('sex')
                    ->where('id', $k)
                    ->from('gs_member')
                    ->coroutineSend();
                 $member = $member['result'][0];
                 $user = $member;
                 D('用户信息',$user);
             D('用户'.$k.'结算信息',['shu'=>$zongshu,'ying'=>$zongwin]);
             yield $this->redis_pool->getCoroutine()->hset('uids_' . $roomid,$k,serialize($user));//存每个玩家输赢
            $data = [
                'status'=>3,
                'win'=>$zongwin,
                'shu'=>$zongshu,
                'user'=>$user
            ];
            $this->sendToUid($k,reData('jieguo',$data),false);
                    }   
        $userInfo[$k]['z']=0;
         $userInfo[$k]['x']=0;
          $userInfo[$k]['h']=0;
           $userInfo[$k]['ztw']=0;
            $userInfo[$k]['xtw']=0;
            $userInfo[$k]['status'] = 0;
        }

            }
              //   $zhuangy += $zongshu;
              // $zhuangs += $zongwin;
        $ying = $zhuangy-$zhuangs;
        if($ying < 0){
            $zshu = abs($ying);
            yield $this->redis_pool->getCoroutine()->Hincrbyfloat('bjl_zong','shu',$zshu);
        }else{
            $zying = $ying;
             yield $this->redis_pool->getCoroutine()->Hincrbyfloat('bjl_zong','ying',$zying);
        }

        //如果庄家存在
        if(!empty($roomInfo['zj'])){
            $roomInfo['zj']['numb'] -= $ying;
        }
        // $roomInfo['zj']['numb'] += $ying; //庄家输赢
        $roomInfo['luzi'][] = $one;
        $roomInfo['z'] = 0;
        $roomInfo['x'] = 0;
        $roomInfo['h'] = 0;
        $roomInfo['ztw'] = 0;
        $roomInfo['xtw'] = 0;
        
        //清空本局押注情况
        yield $this->redis_pool->getCoroutine()->del('z_'.$roomid);
        yield $this->redis_pool->getCoroutine()->del('x_'.$roomid);
        yield $this->redis_pool->getCoroutine()->del('h_'.$roomid);
        yield $this->redis_pool->getCoroutine()->del('ztw_'.$roomid);
        yield $this->redis_pool->getCoroutine()->del('xtw_'.$roomid);
        yield $this->redis_pool->getCoroutine()->del('zonghe_'.$roomid);
        yield $this->redis_pool->getCoroutine()->hset($roomid,'roomInfo',serialize($roomInfo),'userInfo',serialize($userInfo));
          $this->destroy();
    }

}