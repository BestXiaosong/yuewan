<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/30
 * Time: 11:53
 */

namespace app\index\controller;


use think\Controller;

class Wap extends Base
{


    public function test()
    {
        dump(session('userInfo'));
    }

    /**
     * 登录页面
     */
    public function login()
    {
        if (request()->isPost()){
            $this->error('参数错误');
        }


        return view();
    }

    /**
     * 手机验证码登录页面
     */
    public function phone()
    {
        return view();
    }

    /**
     * 莓果账号登录页面
     */
    public function mg()
    {
        return view();
    }

    /**
     * 私钥修改密码
     */
    public function edit()
    {
        return view();

    }

    public function register(){

        $id = $this->request->param('id');
        if(!empty($id)){
            $this->assign('id',$id);
        }
        return view();
    }
    public function download(){
        $android = db('explain')->where(['id'=>14])->value('content');
        $ios = db('explain')->where(['id'=>15])->value('content');
        return view('',[
            'android'=>$android,
            'ios'=>$ios,
        ]);
    }


}