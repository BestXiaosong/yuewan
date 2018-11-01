<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/27
 * Time: 14:10
 */

namespace app\index\controller;


use app\common\logic\Vod;
use think\Controller;
use think\Request;

class User extends  Base
{


    //接口phone
    protected $phone = '';
    // 返回码
    protected  $code = 0;
    //接口token
    protected $token = '';

    //接口调用错误信息获取
    protected $errorMsg = '';

    //接口原始参数
    protected $json = [];

    protected function _initialize(Request $request = null)
    {
        parent::_initialize();
        $this->phone =  session('userInfo')['phone'];
        $this->token =  session('userInfo')['token'];
//        if (empty($this->phone) || empty($this->token)) {
//            $this->TimeOut();
//        }
        $this->assign([
            'phone' => $this->phone,
            'token' => $this->token,
        ]);

    }


    /**
     * 登录过期跳转方法
     */
    protected function TimeOut(){
        if (request()->isMobile()){ //wap未登录
            $this->redirect('wap/login');
        }else{//pc未登录
            $this->redirect('login/login');
        }
    }

    /**
     * 接口公用方法
     * @param array $data 除token phone之外的其它请求参数
     * @param string $method 除域名外请求url
     * @return bool | array
     */
    protected function httpPost($data = [],$method = ''){
        $data['phone'] = $this->phone;
        $data['token'] = $this->token;
        $url = config('api_domain').$method;
        $json = curl_post($url,$data);
        $data = json_decode($json,true);

        if ($data['code'] == -1){
            if(empty($data['phone'])&&empty($data['token'])){

            }else{
                $this->TimeOut();
            }
        }elseif($data['code'] == 1){
           return $data['data'];
        }else{
            $this->code = $data['code'];
            $this->json = $json;
            $this->errorMsg = $data['msg'];
            return false;
        }
    }


    /**
     * 房间直播页面
     */
    public function play()
    {
        $row['id'] = input('id')??hashid(13);
        $data = $this->httpPost($row,'/play/joinRoom');
//              var_dump($data);exit;
        $url = db('explain')->where(['id'=>9])->value('content');
        if ($data === false){dump($this->errorMsg);dump($this->json);exit();}
        $this->assign([
            'data' => $data,
            'url'=>$url
        ]);
        return view();
    }

    /**
     * 房间视频回放页面
     */
    public function vod_detail(){
        $datas['pid'] = $this->request->param('vod_id');
        $datas['token'] = $this->token;
        $datas['phone'] = $this->phone;
        $curlPost = $datas;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,config('api_domain').'/Sale/click');//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);

        $data = json_decode($data,true);
        $detail = $data['data'];
//        print_r($detail);exit;
        $this->assign([
            'detail'=>$detail
        ]);
        return view();
    }





}