<?php
namespace app\common\logic;

use think\Model;


class PlayCategory extends Model
{
    protected $autoWriteTimestamp = 'datetime';

    public function saveChange($data){
        if(is_numeric($data['id'])){
            return $this->validate(true)->allowField(true)->save($data,[$this->getPk()=>$data['id']]);
        }else{
            return $this->validate(true)->allowField(true)->save($data);
        }
    }
}