<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/2 0002
 * Time: 11:10
 */

namespace app\common\logic;


use think\Model;

class RedHistory extends Model
{
    public function saveRedHistory($data)
    {
        return $this->allowField(true)->save($data);
    }

}