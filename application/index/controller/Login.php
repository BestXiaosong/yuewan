<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/5
 * Time: 16:20
 */

namespace app\index\controller;


use qq\Qc;
use think\Config;
use think\Request;
use wxlogin\WxOauth;

class Login extends Base
{
    public function login()
    {
        return view();
    }

    /**
     * 手机登录
     */
    public function phone()
    {
        return view();
    }

    /**
     * 莓果登录
     */
    public function mg()
    {
        return view();
    }

    /**
     * 微信登陆
     */

    public function wxLogin(){
        $we_oauth = new WxOauth();
        $callback = Config::get('wxCallback');
        $url = $we_oauth->getOauthUrl($callback);
        header('Location:'.$url);
    }

    /**
     * 微信登陆回调
     */

    //微信第三方登陆回调

    public function wxCallback(){
        $code = Request::instance()->get('code');
        $state = Request::instance()->get('state');
        if (empty($code)){
            return $this->error('已取消授权','/index/index');
        }
        $qc = new WxOauth();
        $res = $qc->getAccessToken($code,$state);
        if (!$res){
            return $this->error('请求出错');
        }
        $openid = $res['openid'];
        $param = array();
        $param['openid'] = $openid;
        $param['type'] = 1;
        $method = '/login/thirdlogincallback';
        $url = config('api_domain').$method;
        $json = curl_post($url,$param);
        $data = json_decode($json,true);
        if($data['code']==1){
            session('userInfo',$data['data']);
            $this->redirect('/web/index');
        }else{
            return $this->error($data['msg'],'/login/bind/type/web_wx/openid/'.$openid);
        }
    }

    /**
     * qq登录
     */
    public function qqLogin(){
        $qc = new Qc();
        $qc->qqLogin();
    }

    /**
     * 登录成功后的回调，获取token和openid
     *
     */

    public function qqCallback(){
        $code = Request::instance()->get('code');
        if (empty($code)){
            return $this->error('已取消授权','/index/index');
        }
        $state = Request::instance()->get('state');
        $qc = new Qc();
        $res = $qc->qqCallback($code,$state);
        if (!$res){
            return $this->error('请求出错','/index/index');
        }
        $openid = $qc->getOpenid();

        $param = array();
        $param['openid'] = $openid;
        $param['type'] = 2;
        $method = '/login/thirdlogincallback';
        $url = config('api_domain').$method;
        $json = curl_post($url,$param);
        $data = json_decode($json,true);
        if($data['code']==1){
            session('userInfo',$data['data']);
            return $this->success('登陆成功','/web/index');
        }else{
            $this->open_id = $openid;
            $this->type = $param['type'];
            return $this->error($data['msg'],'/login/bind/type/web_qq/openid/'.$openid);
        }

    }



        //第三方帐号绑定用户帐号页面
    public function bind(){
            $type = $this->request->param('type');
            $openid = $this->request->param('openid');
            return view('',[
                'type'=>$type,
                'openid'=>$openid
            ]);
    }

        //注册
    public function register(){

            $type = $this->request->param('type');
            $openid = $this->request->param('openid');
            $id = $this->request->param('id');
            $download = db('explain')->where(['id'=>9])->value('content');
            if(!empty($type)){
                $this->assign('type',$type);
                $this->assign('openid',$openid);
            }
            if(!empty($id)){
                $this->assign('id',$id);
            }
            $this->assign('download',$download);
            return view();
    }
        //第三方绑定帐号
    public function binding(){
        $url = '/login/binding';
        $param['type'] = $this->request->post('type');
        $param['open_id'] =  $this->request->post('openid');

        $param['phone'] = addslashes($this->request->post('phone'));
        $param['code']  = input('post.code');
        $url = config('api_domain').$url;
        $json = curl_post($url,$param);
        $data = json_decode($json,true);
//        dump($param);exit;
        if ($data['code'] == -1){
            if (request()->isMobile()){ //wap未登录
                $this->redirect('wap/login');
            }else{//pc未登录
                $this->redirect('login/login');
            }
        }elseif($data['code'] == 1){
            api_return(1,'绑定成功',$data['data']);
        }else{
            $this->code = $data['code'];
            $this->errorMsg = $data['msg'];
            api_return(0,  $this->errorMsg);
        }

    }


    public function share(){
        $id = $this->request->param('user_id');
        if(\request()->isMobile()){
            $this->redirect('/wap/register',array('id'=>$id));
        }else{
            $this->redirect('register',array('id'=>$id));
        }
    }

    public  function forget(){
        return view();
    }
}