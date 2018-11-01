<?php
namespace app\api\controller;

use app\common\logic\Users;
use think\Db;
class Login extends Base
{


    public function index()
    {
        return view();
    }

    /**
     * 密码登陆
     * url = index/login
     * phone => 用户手机号
     * password => 密码
     * type = post
     */
    public function login()
    {
        if (request()->isPost()){
            $data = input('post.');
            if (!empty($data['phone'])){
                if (!empty($data['password'])){
                    $row = Db::name('users')->where('phone',$data['phone'])->field('user_id,salt,password,status')->find();
                    if (!empty($row)){
                        if($row['status'] == 1){
                            $password = md5(md5($data['password']).$row['salt']);
                            if ($password == $row['password']){
                                $token = $this->makeToken($row['user_id']);
                                $this->log($row['user_id']);
                                api_return(1,'登录成功',['phone'=>$data['phone'],'token'=>$token]);
                            }
                            api_return(0,'账号或密码错误');
                        }
                    }
                    api_return(0,'账户不存在或被禁用');
                }else if(!empty($data['token'])){
                    $row = Db::name('users')->where('phone',$data['phone'])->field('user_id,token,token_expire,status')->find();
                    if (!empty($row)){
                        if($row['status'] == 1){
                            if ($data['token'] == $row['token'] && time() < $row['token_expire']){
                                $data = Db::name('users')->where('user_id',$row['user_id'])->update(['token_expire'=>(time()+config('token_expire'))]);
                                if (!$data){
                                    Db::name('users')->where('user_id',$row['user_id'])->update(['token_expire'=>(time()+config('token_expire'))]);
                                }
                                $this->log($row['user_id']);
                                api_return(1,'登录成功');
                            }
                            api_return(-1,'登录过期');
                        }
                    }
                    api_return(0,'账户不存在或被禁用');
                }else if (!empty($data['code'])){
                    $code = cache('code'.$data['phone']);
                    if (!empty($code) && $code == $data['code']){
                        $row = Db::name('users')->where('phone',$data['phone'])->field('user_id,status')->find();
                        if (!empty($row)){
                            if($row['status'] == 1){
                                //登陆日志
                                $this->log($row['user_id']);
                                $token = $this->makeToken($row['user_id']);
                                api_return(1,'登陆成功',['phone'=>$data['phone'],'token'=>$token]);
                            }
                            api_return(0,'账号被禁用');
                        }else{
                            api_return(0,'您的手机号码尚未注册,请前往注册页面进行注册');
                        }
                    }
                    api_return(0,'验证码错误');
                }
                api_return(0,'请输入完整的数据');
            }
            api_return(0,'手机号不能为空');
        }else{
            api_return(0,'访问类型错误');
        }
        api_return(0,'系统错误');
    }
    /**
     * 修改密码
     */
    public function editPassWord()
    {
        if (request()->isPost()){
            $data = input('post.');
            if (!empty($data['password'])){
                if (empty($data['newPassword'])) api_return(0,'请输入密码');
                //根据原密码修改密码
                $row = Db::name('users')->where('phone',$data['phone'])->field('salt,user_id,password,status')->find();
                if (!empty($row)){
                    if($row['status'] == 1){
                        $password = md5(md5($data['password']).$row['salt']);
                        if ($password == $row['password']){
                            $newPassword = md5(md5($data['newPassword']).$row['salt']);
                            $result = Db::name('users')->where('user_id',$row['user_id'])->update(['password'=>$newPassword]);
                            if ($result !== false){
                                api_return(1,'修改成功');
                            }
                            api_return(0,'修改失败');
                        }
                        api_return(0,'密码错误');
                    }
                }
                api_return(0,'非法操作');
            }else{
                //根据手机短信验证修改密码
                $code = cache('code'.$data['phone']);
                if ($code != null && $code = $data['code']){
                    cache('code'.$data['phone'],null);
                    $row = Db::name('users')->where('phone',$data['phone'])->field('salt,user_id,password,status')->find();
                    if (!empty($row)){
                        if($row['status'] == 1){
                            $password = md5(md5($data['newPassword']).$row['salt']);
                            $result = Db::name('users')->where('user_id',$row['user_id'])->update(['password'=>$password]);
                            if ($result !== false){
                                api_return(1,'修改成功');
                            }
                            api_return(0,'修改失败');
                        }
                    }
                    api_return(0,'非法操作');
                }
                api_return(0,'验证码错误');
            }
        }
    }

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
            $data['trade_password']  = input('post.trade_password');
            $data['money'] = 3000000;
            $data['BCDN']  = 3000000;
            $code  = input('post.code');
            $open_id  = input('post.open_id');
            $type  = input('post.type');
            $cache = cache('code'.$data['phone']);
//            if ($code != $cache) api_return(0,'验证码错误');
            if($this->is_register($data['phone'])){
                cache('code'.$data['phone'],null);
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
     * 霉果app登录
     */
    public function MBerry()
    {
        $data['Pwd'] = input('post.password');
        $data['Account'] = input('post.phone');
        $result = MBerryApi($data,'api.Login');
        if ($result['Msg'] == 'success'){
//            dump($result);exit;
            $user = Db::name('user_extend')->where('UID',$result['Data']['UID'])->value('user_id');
            if ($user){
                $token = $this->makeToken($user);
                $phone = Db::name('users')->where('user_id',$user)->value('phone');
                $this->log($user);
                api_return(1,'登录成功',['phone'=>$phone,'token'=>$token]);
            }else{
                api_return(101,'首次登录请先绑定已有账号或新注册账号',$result['Data']['UID']);
            }
        }else{
            $error = explode(',',$result['Msg']);
            if ($error[0] == '-102'){
                api_return(0,'账号或密码错误');
            } else{
                api_return(0,'服务器繁忙,请稍后重试');
            }
        }
    }

    /**
     * 根据用户密钥修改登录密码
     */
    public function key1()
    {

        if (request()->isPost()){
            $data = request()->only(['phone','private_key','password','check'],'post');
            $check = cache('private_key'.$data['private_key']);
            if ($check){
                api_return(0,'服务器繁忙,请稍后重试');
            }else{
                cache('key_phone_'.$data['phone'],1,3);
            }
            if ($data['password'] != $data['check']) api_return(0,'两次密码输入不一致');
            $validate = validate('base');
            if (!$validate->scene('key')->check($data)) api_return(0,$validate->getError());
            $row = Db::name('users')->where('phone',$data['phone'])->field('user_id,salt,private_key')->find();
            if ($row['private_key'] != $data['private_key']) api_return(0,'私钥错误!');
            $model = new Users();
            $map['id'] = $row['user_id'];
            $map['password'] = $data['password'];
            $result = $model->change($map);
            if ($result !== false) api_return(1,'修改成功');
            api_return(0,$model->getError());
        }
    }

    public function key()
    {
        if (request()->isPost()){
            $data = request()->only(['phone','private_key','password','check'],'post');
            $check = cache('private_key'.$data['private_key']);
            if ($check){
                api_return(0,'服务器繁忙,请稍后重试');
            }else{
                cache('key_phone_'.$data['phone'],1,3);
            }
            if ($data['password'] != $data['check']) api_return(0,'两次密码输入不一致');
            $validate = validate('base');
            if (!$validate->scene('key')->check($data)) api_return(0,$validate->getError());
            $row = Db::name('users')->where('private_key',$data['private_key'])->field('user_id,salt,private_key')->find();
            if ($row){
                $model = new Users();
                $map['id'] = $row['user_id'];
                $map['password'] = $data['password'];
                $map['token_expire'] = 0;
                $result = $model->change($map);
                if ($result !== false) api_return(1,'修改成功');
                api_return(0,$model->getError());
            }else{
                api_return(0,'密钥错误');
            }

        }
        api_return(0,'服务器繁忙,请稍后重试');
    }




    /**
     * app根据用户密钥修改登录密码及交易密码
     */
    public function edit()
    {

        if (request()->isPost()){
            $data = request()->only(['phone','private_key','password','trade_password'],'post');
            $check = cache('edit_phone_'.$data['phone']);
            if ($check){
                api_return(0,'服务器繁忙,请稍后重试');
            }else{
                cache('edit_phone_'.$data['phone'],1,3);
            }
            $validate = validate('base');
            if (!$validate->scene('setKey')->check($data)) api_return(0,$validate->getError());
            $row = Db::name('users')->where('phone',$data['phone'])->field('user_id,salt,private_key')->find();
            if ($row['private_key'] != $data['private_key']) api_return(0,'私钥错误!');
            $model = new Users();
            $map['id'] = $row['user_id'];
            $map['password'] = $data['password'];
            $map['trade_password'] = $data['trade_password'];
            $result = $model->change($map);
            if ($result !== false) api_return(1,'修改成功');
            api_return(0,$model->getError());
        }
    }


    /**
     * app微信QQ第三方登录
     */
    public function third()
    {
        if (request()->isPost()){
            $data = input('post.');
            $row = Db::name('users')->where($data['type'],$data['open_id'])->field('phone,user_id,status')->find();
            if (!empty($row)){
                if (empty($row['phone']))api_return(4005,'请先绑定手机号');
                //正常登陆流程
                if($row['status'] == 1){
                    //登陆日志
                    $this->log($row['user_id']);
                    $token = $this->makeToken($row['user_id']);
                    api_return(1,'登陆成功',['token'=>$token,'phone'=>$row['phone']]);
                }
                api_return(0,'账号被禁用');
            }else{
                api_return(4005,'请先绑定手机号');
                //注册流程
                $model  = new Users();
                if (!empty($data['id'])){
                    unset($data['id']);
                }
                $result = $model->saveChange($data);
                if ($result){
                    api_return(4005,'请先绑定手机号');
                    //登陆日志
//                    $this->log($model->user_id);
//                    $token = $this->makeToken($model->user_id);
//                    api_return(1,'登陆成功',['token'=>$token,'phone'=>$model->phone]);
                }
            }
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


    /**
     * @param $openid //地三方登陆的openid
     * @param int $type type=>1 微信回调  type=》2 QQ
     * 第三方的登陆回调
     */
    public function thirdLoginCallBack()
    {
        $openid = request()->post('openid');
        $type = request()->post('type',1);
        if($type==1){
            $res = Db::name('user_extend')->where('web_wx',$openid)->find();
        }else{
            $res = Db::name('user_extend')->where('web_qq',$openid)->find();
        }
        if(empty($res)){
            api_return('0','请先绑定手机号');
        }else{
            $user_id = $res['user_id'];
            $row = Db::name('users')->field('user_id,phone,status')->where('user_id',$user_id)->where('status',1)->find();
            if(!empty($row)){
                $token = $this->makeToken($row['user_id']);
                $this->log($row['user_id']);
                api_return(1,'登录成功',['phone'=>$row['phone'],'token'=>$token]);
            }

            api_return(0,'账户不存在或被禁用');
        }

    }
    
    /**
     * 第三方信息绑定
     */
    public function binding(){
        $type = addslashes($this->request->post('type'));
        $open_id = addslashes($this->request->post('open_id'));
        $data['phone'] = addslashes($this->request->post('phone'));
        $code  = input('post.code');
        $cache = cache('code'.$data['phone']);
        if (empty($cache) || $code != $cache ) api_return(0,'验证码错误');
        if(empty($type)) api_return(0,'参数错误');
        if(empty( $data['phone'])) api_return(0,'参数错误');
        $model = new \app\common\model\Users();
        $datas = $model->binding($type,$open_id,$data);
        if($datas == 1){
            $user_id = db('users')->where(['phone'=>$data['phone']])->value('user_id');
            $data['token'] = $this->makeToken($user_id);
            api_return(1,'绑定成功',$data);
        }else{
            api_return(0,$datas);
        }
    }
}
