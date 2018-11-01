<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/9
 * Time: 10:34
 */

namespace app\common\logic;


use think\Model;

class RoomActivity extends Model
{
    public function saveChange($data = [])
    {
        if(is_numeric($data['id'])){
            return $this->validate(true)->allowField(true)->save($data,['activity_id'=>$data['id']]);
        }else{
            return $this->validate(true)->allowField(true)->save($data);
        }
    }
}