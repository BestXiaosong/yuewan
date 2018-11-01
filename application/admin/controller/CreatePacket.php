<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/27 0027
 * Time: 11:28
 */

namespace app\admin\controller;

use CreateRed\Client;
use think\Controller;
use think\Exception;
use think\Loader;
use think\Request;


class CreatePacket extends Controller
{

    public function rand()
    {
//        Loader::import('CreateRed.Client',EXTEND_PATH);

        $param = Request::instance()->post();


        //随机红包[修数据]
        $data = Client::rand(100,10,1,15);

        dump($data);
    }


    public function equal()
    {
        //固定红包

        $data = Client::equal(100,10);
        print_r($data);

    }

}
