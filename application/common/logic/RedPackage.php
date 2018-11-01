<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/27 0027
 * Time: 15:56
 */

namespace app\common\logic;


use think\Model;

class RedPackage extends Base
{
    public function saveChange($data)
    {
        if (is_numeric($data['id'])) {
            return $this->validate(true)->allowField(true)->isUpdate(true)->save($data, ['red_id' => $data['id']]);
        } else {
            return $this->validate('red_package.add')->allowField(true)->save($data);
        }

    }

    public function addRedPackage($data)
    {

        $this->role_id = $data['role_id'];
        $this->user_id = $data['user_id'];
        $this->money_type = $data['money_type'];
        $this->msg = $data['msg'];
        $this->money = $data['money'];
        $this->red_type = $data['red_type'];
        $this->num = $data['num'];
        $this->luck_king_money = $data['luck_king_money'];
        $this->create_time = $data['create_time'];
        $this->expire_time = $data['expire_time'];
        $this->to_id = $data['to_id'];
        $this->type = $data['type'];
        $this->save();
        return $this->red_id;
    }

    public function updateEndTime($red_id)
    {
        return $this->isUpdate(true)->save(['end_time'=>time(),'status'=>0], ['red_id' => $red_id]);
    }

    //更改红包状态值
    public function updateStatus($red_id, $status)
    {
        return $this->where('red_id',$red_id)->update(['status'=>$status]);
    }


}