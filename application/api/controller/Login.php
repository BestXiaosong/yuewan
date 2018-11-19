<?php
namespace app\api\controller;

use app\common\logic\Users;
use think\Db;
use think\Request;

class Login extends Base
{

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * @param Request $request
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * 登录接口
     */
    public function login(Request $request)
    {

        if ($request->isPost()){
            $data = $request->only(['phone','j_push_id','code','log','lat','place'],'post');
            if (!empty($data['phone'])){
                $this->checkCode();
                $row = Db::name('users')->where('phone',$data['phone'])->field('user_id,status')->find();
                if (!empty($row)){
                    if($row['status'] == 1){
                        //登陆日志
                        $this->log($row['user_id']);
                        $token = $this->makeToken($row['user_id']);
                        $r_token = $this->R_token($row['user_id']);
                        api_return(1,'登陆成功',['token'=>$token,'r_token'=>$r_token,'is_register'=>0]);
                    }
                    api_return(0,'账号被禁用');
                }else{
                    //快捷注册
                    $model  = new Users();
                    $row = $model->change($data);
                    if ($row){
                        $this->log($model->user_id);
                        $token = $this->makeToken($model->user_id);
                        $r_token = $this->R_token($model->user_id);
                        api_return(1,'登陆成功',['token'=>$token,'r_token'=>$r_token,'is_register'=>1]);
                    }else{
                        api_return(0,'登录失败,请稍后重试');
                    }
                }
            }
            api_return(0,'手机号不能为空');
        }else{
            api_return(0,'访问类型错误');
        }
        api_return(0,'系统错误');
    }


    /**
     * app微信QQ第三方登录
     */
    public function third()
    {
        if (request()->isPost()){
            $data = \request()->only(['type','open_id','header_img','sex','nick_name','log','lat','place','j_push_id'],'post');

            $array = ['wx','qq','wb'];//允许的第三方登录方式

            if (!in_array($data['type'],$array)){
                api_return(0,'无此登录方式');
            }

            if (empty($data['open_id'])){
                api_return(0,'请输入正确的open_id');
            }

            $row = Db::name('users')->where($data['type'],$data['open_id'])->field('phone,user_id,status')->find();
            if ($row){
                //正常登陆流程
                if($row['status'] == 1){
                    //登陆日志
                    $this->log($row['user_id']);
                    $token = $this->makeToken($row['user_id']);
                    $r_token = $this->R_token($row['user_id']);
                    api_return(1,'登陆成功',['token'=>$token,'r_token'=>$r_token,'is_register'=>0]);
                }else{
                    api_return(0,'账号被禁用');
                }
            }else{
                //注册流程
                $model  = new Users();
                $data[$data['type']] = $data['open_id'];
                $result = $model->userAdd($data);
                if ($result !== false){
                    //登陆日志
                    $this->log($model->user_id);
                    $token = $this->makeToken($model->user_id);
                    $r_token = $this->R_token($model->user_id);
                    api_return(1,'登陆成功',['token'=>$token,'r_token'=>$r_token,'is_register'=>1]);
                }
            }
        }
    }



    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * @param $user_id
     * 登录日志
     */
    public function log($user_id)
    {
        //登陆日志
        $log['ip'] = request()->ip();
        if (empty(input('post.model'))){
            if (request()->isMobile()){
                $log['model'] = 'wap';
            }else{
                $log['model'] = 'PC';
            }
        }else{
            $log['model'] = input('post.model');
        }
        $log['user_id'] = $user_id;
        $log['create_time'] = time();
        Db::name('login_log')->insert($log);
    }

    /**
     * 用户注册接口
     */
    public function register()
    {
        if(request()->isPost()){
            $data['phone']     = input('post.phone');
            if (!empty(input('id'))) $data['proxy_id']  = dehashid(input('id'));
            $data['password']  = input('post.password');
            $open_id  = input('post.open_id');
            $type  = input('post.type');
            $this->checkCode();
            if($this->is_register($data['phone'])){
                $model  = new Users();
                $result = $model->change($data);
                //公钥
                $row['public'] = hashToken($model->user_id);
                //私钥
                $row['private_key'] = $model->where('user_id',$model->user_id)->value('private_key');
                if ($result){
                    if(!empty($open_id)&&!empty($type)){
                        $models = new \app\common\model\Users();
                        $result = $models->binding($type,$open_id,array('phone'=>$data['phone']));
                        if($result == 1){
                            api_return(1,'注册成功',$row);
                        }else{
                            api_return(0,$result);
                        }
                    }else{
                        api_return(1,'注册成功',$row);

                    }
                }else{
                    api_return(0,$model->getError());
                }
            }
            api_return(0,'该手机号码已注册');
        }
    }







    /**
     * app微信QQ第三方登录绑定手机号
     */
    public function bind()
    {
        if (request()->isPost()){
            $data['phone']     = input('post.phone');
            $data['code']     = input('post.code');
//            if (!empty(input('id'))) $data['proxy_id']  = dehashid(input('id'));
//            $data['password']  = input('post.password');
//            $data['trade_password']  = input('post.trade_password');
            $data['type'] = input('post.type');
            $data['open_id'] = input('post.open_id');
            $code = cache('code'.$data['phone']);
            if (empty($code) || $data['code'] != $code){
                api_return(0,'验证码错误');
            }
            if ($data['type'] == 'wx'){
                $data['wx'] = $data['open_id'];
            }else if($data['type'] == 'qq'){
                $data['qq'] = $data['open_id'];
            }else{
                api_return(0,'参数错误');
            }
            $has = Db::name('users')->where('phone',$data['phone'])->find();
            if (empty($has)){//手机和第三方账号都未登录过
                api_return(4005,'手机号码未注册');
                //注册流程
                $model  = new Users();
                if (!empty($data['id'])){
                    unset($data['id']);
                }
                $result = $model->saveChange($data);
                if ($result){
                    //登陆日志
                    $this->log($model->user_id);
                    $token = $this->makeToken($model->user_id);
                    api_return(1,'登陆成功',['token'=>$token,'phone'=>$data['phone']]);
                }
                api_return(0,$model->getError());
            }else{
                //手机号码存在判断是否已经绑定了
                if (!empty($has[$data['type']])){
                    api_return(0,'该手机号码已绑定其它账号');
                }else{
                    $result = Db::name('users')->where('user_id',$has['user_id'])->update([$data['type'] => $data['open_id']]);
                    if ($result){
                        //登陆日志
                        $this->log($has['user_id']);
                        $token = $this->makeToken($has['user_id']);
                        api_return(1,'绑定成功',['token'=>$token,'phone'=>$has['phone']]);
                    }
                    api_return(0,Db::name('users')->getLastSql());
                }
            }
        }
    }


}
