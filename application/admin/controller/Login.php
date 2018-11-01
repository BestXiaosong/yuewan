<?php
/**
 * Created by PhpStorm.
 * Users: Administrator
 * Date: 2018/3/13
 * Time: 9:52
 */
namespace  app\admin\controller;
use think\Controller;
use think\Db;

class Login extends Controller
{

    public function login()
    {
        if (request()->isPost()){
            $data = input('post.');
            if (!empty($data['user_name']) and !empty($data['password'])){
                $row = Db::name('admins')->where('user_name',$data['user_name'])->field('user_id,user_name,password,status')->find();
                if (!empty($data)){
                    if ($row['status'] == 1){
                        $password = md5($data['password'].'tz');
                        if ($password == $row['password']){
                            session('user_id',$row['user_id']);
                            $this->success('登陆成功',"/index/index");
                        }else{
                            $this->error('账号或密码错误');
                        }
                    }
                }
                $this->error('用户不存在或被禁用');
            }
            $this->error('账号或密码不能为空');
        }
        return view();
    }


    /**
     * 代理登录
     */
    public function agent()
    {
        if (request()->isPost()){
            $data = input('post.');
            if (!empty($data['user_name']) and !empty($data['password'])){
                $row = Db::table('users')->where('user_name',$data['user_name'])->field('user_id,user_name,password,status')->find();
                if (!empty($data)){
                    if ($row['status'] == 1){
                        $password = md5($data['password'].'tz');
                        if ($password == $row['password']){
                            session('user_id',$row['user_id']);
                            $this->success('登陆成功',"/index/index");
                        }   else{
                            $this->error('账号或密码错误');
                        }
                    }
                }
                $this->error('用户不存在或被禁用');
            }
            $this->error('账号或密码不能为空');
        }
        return view();
    }



}