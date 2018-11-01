<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/31
 * Time: 15:11
 */

namespace app\index\controller;


use app\common\model\Room;

class Index extends Base
{
    public function ws()
    {
        echo 'xxxx';
    }

    /**
     * 网页设置用户信息存入session
     */
    public function setUserInfo()
    {
        $userInfo['phone'] = input('phone');
        $userInfo['token'] = input('token');
        if (empty($userInfo['phone'])) api_return(0,'手机号码不能为空');
        if (empty($userInfo['token'])) api_return(0,'token不能为空');
        $role_id=db('users')->where(['phone'=>$userInfo['phone'],'token'=>$userInfo['token']])->value('role_id');
        $role_name=db('role')->where(['role_id'=>$role_id])->value('role_name');
        $userInfo['role_name']=sub_str($role_name,6);
        $userInfo['photo']=db('role')->where(['role_id'=>$role_id])->value('header_img');
        session('userInfo',$userInfo);
        api_return(1,'成功');
    }

    public function loginOut()
    {
        session('userInfo',null);
        api_return(1,'退出成功');
    }


    public function index()
    {
        echo 'pc';
    }

    public function test()
    {
        cache('verify',input('post.'));
    }

    public function sharetest(){

        return view();

    }
    protected function httpPost($data = [],$method = ''){
        $url = config('api_domain').$method;
        $json = curl_post($url,$data);
        $data = json_decode($json,true);
        if ($data['code'] == -1){
            $this->TimeOut();
        }elseif($data['code'] == 1){
            return $data['data'];
        }else{
            $this->code = $data['code'];
            $this->json = $json;
            $this->errorMsg = $data['msg'];
            return false;
        }
    }

    public function details(){
        $id = request()->param('new_id');
        $glroom = $this->httpPost(array('news_id'=>$id), '/index/newsdetails');
        $this->assign('data', $glroom['detail']);
        return view();
    }





    public function share_detail(){
      $token = $this->R_token();
//      dump($token);exit;
      $room_id = dehashid($this->request->param('room_id'));
      $user_id = $this->request->param('user_id');
      $model = new Room();
      $notice_model = new \app\common\logic\Room();
      $notice = $notice_model->new_notice($room_id);
      $result = $model->share_room_info($room_id);
      $download_url = db('explain')->where(['id'=>9])->value('content');
      $play_info = $this->getPlayInfo($room_id);
//      dump($result);exit;
      if($this->request->isMobile()){
          if($result){
            return view('share_wap',[
                'room_info'=>$result,
                'token'=>$token,
                'notice'=>$notice,
                'id'=>$user_id,
                'download_url'=>$download_url,
                'play_info'=>$play_info,
            ]);
          }else{
            $this->redirect('wap/register',array('id'=>$user_id));
          }
      }else{
          if($result){
              return view('share_pc',[
                  'room_info'=>$result,
                  'notice'=>$notice,
                  'token'=>$token,
                  'id'=>$user_id,
                  'download_url'=>$download_url,
                  'play_info'=>$play_info,
              ]);
          }else{
              $this->redirect('login/register',array('id'=>$user_id));
          }
        }
    }


    public function share_vod_detail(){
        $token = $this->R_token();
//      dump($token);exit;
        $pid = dehashid($this->request->param('pid'));
        $user_id = $this->request->param('user_id');
        $model = new \app\common\logic\Vod();
        $result = $model->share_click($pid);
        $download_url = db('explain')->where(['id'=>9])->value('content');

//      dump($result);exit;
        if($this->request->isMobile()){
            if($result){
                return view('share_vod_wap',[
                    'result'=>$result,
                    'token'=>$token,
                    'id'=>$user_id,
                    'download_url'=>$download_url,
                ]);
            }else{
                $this->redirect('wap/register',array('id'=>$user_id));
            }
        }else{
            if($result){
                return view('share_vod_pc',[
                    'result'=>$result,
                    'token'=>$token,
                    'id'=>$user_id,
                    'download_url'=>$download_url,
                ]);
            }else{
                $this->redirect('login/register',array('id'=>$user_id));
            }
        }
    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * web唤起app测试
     */
    public function scheme()
    {
        $param = request()->param();
        $string = http_build_query($param);

        $this->assign([
            'user_id' => $param['user_id']??0,
            'string' => $string,
        ]);

        return view();
    }




}