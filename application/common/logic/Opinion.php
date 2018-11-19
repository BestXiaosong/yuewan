<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6 0006
 * Time: 10:37
 */

namespace app\common\logic;


use think\Model;

class Opinion extends Model
{

    public function saveOpinion($data)
    {

        return $this->allowField(true)->validate(true)->save($data);

    }

}