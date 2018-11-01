<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/30 0030
 * Time: 17:29
 */
namespace app\common\logic;

use think\Model;


class RoleCheck extends Base
{
    public function saveChange($data){
        if(is_numeric($data['id'])){

            $data['start_time'] = strtotime($data['start_time']);
            $data['end_time'] = strtotime($data['end_time']);
            return $this->validate(true)->allowField(true)->save($data,['sale_id'=>$data['id']]);
        }else{
            $data['create_time'] = time();
            $data['start_time'] = strtotime($data['start_time']);
            $data['end_time'] = strtotime($data['end_time']);
            return $this->validate(true)->allowField(true)->save($data);
        }
    }



}