<?php
/**
 * Created by PhpStorm.
 * User: zhangjincheng
 * Date: 16-7-15
 * Time: 下午1:44
 */

namespace app\Models;


use Server\CoreBase\Model;

class CommModel extends Model
{
    //判断进来的玩家数据是否存在切携带的金币和房间规定金币 roominfo['numb']
    public function exiit($data)
    {
       if(empty($data->mid) || empty($data->room_id)){
        return true;
       }
       if (!yield $this->redis_pool->EXISTS($data->room_id)) { 
            return true;
        }
    
                    return false;
    }
    //判断房间信息是否存在
    public function jinru($mid,$room,$roomid)
    {
           //获取玩家信息
                $member = yield $this->mysql_pool->dbQueryBuilder
                    ->select('headimgurl')
                    ->select('nickname')
                    ->select('num')
                    ->select('ip')
                    ->select('sex')
                    ->where('id', $mid)
                    ->from('gs_member')
                    ->coroutineSend();
                    if(empty($member)){
        
                        return false;
                    }
                     $member = $member['result'][0];
                     D('玩家信息：',$member);
                     if($member['num'] < $room['numb']){
                              return false;
                     }
                      $nub =  yield $this->redis_pool->getCoroutine()->hexists('uids_' . $roomid,$mid);
                   if($nub){ //如果有信息 则重连
                    $user = 1;
                   }else{
                    $user = 0;
                   }
                  //获取玩家信息
            return ['user'=>$user,'member'=>$member];
    }
    // 判断玩家金币是否可以下注
    public function money($roomid,$mid,$money)
    {
        $user =  yield $this->redis_pool->hget('uids_' . $roomid,$mid);
        $user = unserialize($user);
        if($user['num'] < $money){
            return false;
        }
        return $user;
    }
    public function likai($mid, $room_id,$roomInfo)
    {
        $us  =  yield $this->redis_pool->getCoroutine()->Hdel('uids_' . $room_id,$mid);
        unset($roomInfo['zj'][$mid]);
        yield $this->mysql_pool->dbQueryBuilder
            ->update('gs_member')
            ->set('room_id', 0)
            ->where('id', $mid)
            ->coroutineSend();
        yield $this->redis_pool->hset($room_id, 'roomInfo', serialize($roomInfo));
        return ['mid'=>$mid,'status'=>1];
    }
}