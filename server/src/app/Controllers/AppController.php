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
class AppController extends Controller
{
    /**
     * @var AppModel
     */
    public $AppModel;

    protected function initialization($controller_name, $method_name)
    {
        parent::initialization($controller_name, $method_name);
        $this->AppModel = $this->loader->model('AppModel', $this);
    }

    public function bind($uid)
    {
        $this->bindUid($uid);
        $this->send("ok.$uid");
    }
    public function onClose()
    {
        E($this->uid.'下线');
        //获取玩家基本信息
        $member = yield $this->mysql_pool->dbQueryBuilder
            ->select('room_id')
            ->where('id', $this->uid)
            ->from('gs_member')
            ->coroutineSend();
        if (empty($member['result'])) {
            E('错误：查询不到用户信息');
            return false;
        }
        //获取房间号
        $room_id = $member['result'][0]['room_id'];
        if($room_id){
            $uids = yield $this->redis_pool->getCoroutine()->HKEYS('uids_' . $room_id);
            $this->sendToUids($uids, reData('duanxian', $this->uid), false);
        }
        $this->destroy();
    }


    public function onConnect()
    {
        $this->destroy();
    }


}