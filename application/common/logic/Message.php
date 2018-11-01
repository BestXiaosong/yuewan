<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6 0006
 * Time: 10:58
 */

namespace app\common\logic;


use think\Model;

class Message extends Model
{
    public function saveChange($data){
        $data['update_time'] = time();
        if(is_numeric($data['id'])){
            return $this->validate(true)->allowField(true)->save($data,['mid'=>$data['id']]);
        }else{
            $data['create_time'] = time();
            return $this->validate(true)->allowField(true)->save($data);
        }
    }


}