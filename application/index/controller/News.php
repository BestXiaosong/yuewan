<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/31
 * Time: 15:11
 */

namespace app\index\controller;


class News extends User
{

    public function index()
    {
        return view();
    }

    public function details(){
        $id = request()->param('new_id');

        $glroom = $this->httpPost(array('news_id'=>$id), '/news/newsdetails');
        $this->assign('data', $glroom['detail']);
        return view();
    }


}