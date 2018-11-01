<?php
namespace app\common\logic;

use think\Model;


class Bankroll extends Model
{
    public function saveChange($data){
        if(is_numeric($data['id'])){
            return $this->allowField(true)->save($data,['b_id'=>$data['id']]);
        }else{
            $this->allowField(true)->save($data);
        }
    }




}