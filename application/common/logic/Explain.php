<?php
namespace app\common\logic;

use think\Model;


class Explain extends Model
{

    public function change($data = [])
    {
        return $this->saveAll($data);
    }




}