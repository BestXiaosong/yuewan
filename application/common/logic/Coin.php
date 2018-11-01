<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/28
 * Time: 11:35
 */

namespace app\common\logic;


use think\Model;

class Coin extends Model
{

    public function saveChange($data){
        if(is_numeric($data['id'])){
            return $this->validate(true)->allowField(true)->save($data,['coin_id'=>$data['id']]);
        }else{
            return $this->validate(true)->allowField(true)->save($data);
        }
    }


}