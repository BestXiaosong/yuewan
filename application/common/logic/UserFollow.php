<?php
namespace app\common\logic;

use think\Model;


class UserFollow extends Model
{
    public function saveChange($data){
        if(is_numeric($data['id'])){
            return $this->allowField(true)->save($data,[$this->getPk()=>$data['id']]);
        }else{
            return $this->allowField(true)->save($data);
        }
    }




}