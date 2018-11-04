<?php
namespace app\admin\controller;

use think\Db;



class Index extends Base
{

    function _initialize()
    {
        parent::_initialize();
        $this->assign('stamp',123);
    }
    public function index2()
    {
//        $menuList = session('menuList');
//        if(empty($menuList)){
//            $menuList = $this->getMenuList();
//            session('menuList',$menuList);
//        }
        $menuList = $this->getMenuList();

        $model = new RongCloud(config('rongyun')['appKey'],config('rongyun')['appSecret']);
        $header_img = str_replace('\\','/','test');
        $result = $model->user()->getToken('xiaosong','xiaosong','1.png');
        $res = json_decode($result,true);
        $r_token = '';
        if ($res['code'] == 200){
            $r_token = $res['token'];
        }

        $this->assign('r_token',$r_token);
        $this->assign('menuList',$menuList);
        return view();
    }

    public function index1()
    {
        $model = new RongCloud(config('rongyun')['appKey'],config('rongyun')['appSecret']);
        $header_img = str_replace('\\','/','test');
        $result = $model->user()->getToken('xiaoli','小莉','1.png');
        $res = json_decode($result,true);
        $r_token = '';
        if ($res['code'] == 200){
            $r_token = $res['token'];
        }
        $this->assign('r_token',$r_token);
        return view();
    }


    /**
     *修改密码
     */
    public function editPassword()
    {
        if (request()->isPost()){
            $data = input('post.');
            if (empty($data['old']) || empty($data['password']) || empty($data['check']))$this->error('请输入完整的数据');
            $has = Db::name('admins')->where('user_id',session('user_id'))->find();
            if ($has['password'] != md5($data['old'].'tz'))$this->error('原密码错误');
            if ($data['password'] != $data['check'])$this->error('两次密码输入不一致');
            $password = md5($data['password'].'tz');
            $result = Db::name('admins')->where('user_id',session('user_id'))->update(['password'=>$password]);
            if ($result !== false)api_return(3,'修改成功',url('login/login'));
            $this->error('修改失败');
        }
    }

    public function index()
    {
        $menuList = session('menuList');
        if(empty($menuList)){
            $menuList = $this->getMenuList();
            session('menuList',$menuList);
        }
//        $menuList = $this->getMenuList();
        $this->assign('menuList',$menuList);
        return view();
    }

    public function aaa(){
        echo substr(microtime(),2,8);
    }

    public function login(){
        return view();
    }

    public function logout(){
        session('user_id',null);
        session('menuList',null);
        $this->redirect('/login/login');
    }





}
