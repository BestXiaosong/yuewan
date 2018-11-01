<?php
namespace app\common\logic;

use think\Model;


class Explain extends Base
{

    public function change($data = [])
    {
        return $this->saveAll($data);
    }




}