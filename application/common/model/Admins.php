<?php

namespace app\common\model;

use think\Model;

class Admins extends Base
{
    protected $auto = [];
    protected $insert = [];
    protected $update = [];


    protected function setStimeAttr()
    {
        return time();
    }

    //检验登入
    public function checkLogin($username,$password){
        $rs = $this->get(['use_rname' => $username]);
        session('info',$rs);
        if(!$rs){
            $this->error='用户不存在或已被禁用！';
            return false;
        }else{
            if($rs['password']!== md5($password)){
                $this->error='密码错误！';
                return false;
            }
        }
        //更行数据
        $data =array(
            'id'=>$rs['id'],
            'lg_ip'=>request()->ip(),
            'lg_time'=>time(),
        );

      $this->update($data);


        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'uid'             => $rs['uid'],
            'user_name'        => $rs['user_name'],
            'lg_time'         => $rs['lg_time'],
        );
        session('admin_login', $auth);
        session('admin_login_sign',data_auth_sign($auth));
        return true;
    }
}