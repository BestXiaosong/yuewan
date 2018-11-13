<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/30 0030
 * Time: 17:29
 */
namespace app\common\logic;

use think\Model;


class UserId extends Model
{
    public function saveChange($data){
        if(is_numeric($data['ID'])){
            if($data['status'] == 1){
                db('users')->where(['user_id'=>$data['user_id']])->update([$this->getPk()=>$data['ID'],'check'=>1]);
            }
            return $this->validate(true)->allowField(true)->save($data,[$this->getPk()=>$data['ID']]);
        }
    }


    public function saves($data){
        $data['create_time'] = time();
        $data['update_time'] = time();
        $id =  $this->validate(true)->insertGetId($data);
        $result = db('users')->where(['user_id'=>$data['user_id']])->update([$this->getPk()=>$id,'check'=>1]);
        if($result){
            return true;
        }else{
            return false;
        }
    }
    public function getCan($user_id){
        $result = $this->where(['user_id'=>$user_id])->find();
        return $result;
    }
    public function changStatus($user_id){
        $result = $this->where(['user_id'=>$user_id,'status'=>2])->update(['status'=>3]);
        return $result;
    }
}