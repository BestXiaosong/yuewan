<?php
/**
 * Created by xiaosong
 * E-mail:4155433@gmail.com
 * Date: 2018/12/6
 * Time: 9:51
 * 娱乐房间模块
 */

namespace app\api\controller;


class Fun extends Radio
{

    public function _initialize()
    {
        self::$roomType = 2;
        self::$generalNotUp = [1,2];
        parent::_initialize();
    }












}