<?php
namespace app\Tasks;

use Server\CoreBase\Task;

/**
 * Created by PhpStorm.
 * User: zhangjincheng
 * Date: 16-7-15
 * Time: 下午1:06
 */
class GameTask extends Task
{
    public function testTask()
    {
        return "test task\n";
    }

    public function kaipai()
    {
    	D('kaipai',['status'=>1]);
        $roomid = 1;
    	$time = time();
    	//房间号有问题
    	 $room  =  yield $this->redis_pool->getCoroutine()->hgetall($roomid);//取出房间信息及押注信息
    	 $user  =  yield $this->redis_pool->getCoroutine()->hgetall('uids_' . $roomid);//取出所有用户信息
    	 $uids = yield $this->redis_pool->getCoroutine()->hkeys('uids_' . $roomid);//取出所有人的id
    	 $roomInfo =  unserialize($room['roomInfo']);
    	 $userInfo = 	unserialize($room['userInfo']);
    	 $pai = $roomInfo['pai'];//房间剩余牌 //////////分控操作pais
    	$jieguo =  getPai($pais);
    	if(!$jieguo){
    		$pai = washCards();
    		$roomInfo['pai'] = $pai;
    		$roomInfo['lun'] +=1;
    		$jieguo =  getPai($pais);
    	}
    	$roomInfo['pai'] = $jieguo['pais'];
    	$roomInfo['ju'] += 1;
    	$roomInfo['time'] = time();  //如100
    	$roomInfo['status'] = 1;  // 1为押注时间20秒 2为等待时间 3为开牌结算时间
    	// yield $this->redis_pool->getCoroutine()->hset($roomid,'roomInfo',serialize($user));
    	$time = $roomInfo['time']+20 - time();
    	$luzi = $roomInfo['luzi'];
    	$this->sendToUids($uids,reData('bjl_time',['time'=>$time,'status'=>1,'luzi'=>$luzi]),false); //time = 120
    	// 机器人下注
    	D('机器人下注',['status'=>1]);
    	for($i=0;$i<100;$i++){
    			$proarr = bet_rand();
				$zxh = robot_rand($proarr); //庄闲和ztw xtw
				$money = robot_rand(money());
				$roomInfo[$zxh] += $moeny;
				//[mid-z-金额]
				$zhu = '0-'.$zxh.'-'.$money;
				 yield $this->redis_pool->getCoroutine()->lpush('zonghe_'.$roomid,$z);//压入总注池子
				 yield $this->redis_pool->getCoroutine()->hset($roomid,'roomInfo',serialize($user));
			$data = [
            'mid'=>0,
            'roomInfo'=>$roomInfo,
            'userInfo'=>[],//用户压注情况
            'user'=>[], //用户个人信息
            'zhu'=>$pop //返回压得注
        ];
        $this->sendToUids($this->uids,reData('yazhu',$data),false);
    	}
    
    	
    	sleep(20);
    	// $roomInfo['time'] = time();  //120
    	$roomInfo['status'] = 2;  // 1为押注时间20秒 2为等待时间 3为开牌结算时间
    	 	D('等待3秒',['status'=>2]);
    	yield $this->redis_pool->getCoroutine()->hset($roomid,'roomInfo',serialize($user));
    	// $time = $roomInfo['time']+3 -time();
    	$this->sendToUids($uids,reData('bjl_time',['time'=>3,'status'=>2]),false); 
    	sleep(3);
    	$roomInfo['status'] = 3;  // 1为押注时间20秒 2为等待时间 3为开牌结算时间
    	D('开牌结算',['status'=>3]);
    	yield $this->redis_pool->getCoroutine()->hset($roomid,'roomInfo',serialize($user));
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
    	    sleep(1);
    	    $zhuangy = 0;//所有赢的
    	    $zhuangs = 0;//所有输的
    	foreach ($userInfo as $k => $v) { //！！！！！！！！！！！！
    		$zongwin = 0;//总赢
    		$zongshu = 0;//总输
    		if($one && $tree){//如果开奖结果庄闲都是天王
    			$winzxh = $v[$one] * $peilu[$one]; //庄闲和
    			$wintwh = $v['ztw'] * $peilu['ztw']; // 庄闲天王都中
    			$wintwh += $v['xtw'] * $peilu['xtw'];
    			$zongwin = $winzxh+$wintwh; 
    			$zhuangy += $zongwin;
    		}elseif($one && $tow){
    			$winzxh = $v[$one] * $peilu[$one]; 
    			$wintwh = $v[$tow] * $peilu[$tow];
    			$zongwin = $winzxh+$wintwh; 
    			$zhuangy +=$zongwin;
    		}
    		if($shuz){
    			$zongshu += $v[$shuz];
    			$zhuangs += $zongshu;
    		}
    		if($shux){
    			$zongshu += $v[$shux];
    			$zhuangs += $zongshu;
    		}
    		if($shuh){
    			$zongshu += $v[$shuh];
    			$zhuangs += $zongshu;
    		}
    		if($shuztw){
    			$zongshu += $shuztw;
    			$zhuangs += $zongshu;
    		}
    		if($shuxtw){
    			$zongshu += $shuxtw;
    			$zhuangs += $zongshu;
    		}
    		$user = unserialize($user[$k]);
    		$user['numb'] += $zongwin;
    		$user['numb'] -= $zongshu;
    		yield $this->redis_pool->getCoroutine()->hset('uids_' . $roomid,$k,serialize($user));
    		$data = [
    			'win'=>$zongwin,
    			'shu'=>$zongshu,
    			'user'=>$user
    		];
    		$this->sendToUid($k,reData('kaipai',$data),false);
    	}
    	$ying = $zhuangs-$zhuangy;
    	$roomInfo['zj']['numb'] += $ying;
    	$roomInfo['luzi'][] = $one;
    	yield $this->redis_pool->getCoroutine()->hset($roomid,'roomInfo',serialize($user));
    	  $this->destroy();
    }
	

}