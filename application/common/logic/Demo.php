<?php
namespace app\common\logic;

use think\Model;


class Demo extends Model
{
    public function saveChange($data){
        if(is_numeric($data['id'])){
            return $this->validate(true)->allowField(true)->isUpdate(true)->save($data,[$this->getPk()=>$data['id']]);
        }else{
            $data['create_time'] = time();
            return $this->validate(true)->allowField(true)->save($data);
        }
    }

//    public function saveChange($data){
//        $num = $this->where(['phone'=>$data['phone']])->value('eid');
//        if(is_numeric($data['id'])){
//            if ($num != $data['id']){
//                $this->error = '该手机号码已存在';
//                return false;
//            }
//            return $this->validate(true)->allowField(true)->isUpdate(true)->save($data,['eid'=>$data['id']]);
//        }else{
//            if (is_numeric($num)){
//                $this->error = '该手机号码已存在';
//                return false;
//            }
//            $data['create_time'] = time();
//            return $this->validate(true)->allowField(true)->save($data);
//        }
//    }

}