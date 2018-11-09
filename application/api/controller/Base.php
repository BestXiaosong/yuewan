<?php
namespace app\api\controller;

use rongyun\api\RongCloud;
use think\Cache;
use think\Controller;
use think\Db;
use Qiniu\Auth;
use Qiniu\Config;
use Qiniu\Storage\BucketManager;
use Qiniu\Pili\Client;
use function Qiniu\Pili\HDLPlayURL;
use function Qiniu\Pili\HLSPlayURL;
use Qiniu\Pili\Hub;
use Qiniu\Pili\Mac;
use function Qiniu\Pili\RTMPPlayURL;
use function Qiniu\Pili\RTMPPublishURL;
use function Qiniu\Pili\SnapshotPlayURL;
use think\Exception;
use think\Request;

class Base extends Controller
{




    public function _initialize()
    {
//        $ip = request()->ip();
//        $num = Cache::get($ip);
//        if (!empty($num)){
//            if ($num > 60) api_return(0,'访问过于频繁,请稍后重试');
//            Cache::inc($ip);
//        }else{
//            Cache::set($ip,1,60);
//        }
    }

    /**
     * @param string $url 文件第三方完整地址
     * @return array
     * $array[code] 查询状态 1=>查询成功  0查询失败
     * $array[msg] 提示信息
     * $array[size] 查询出的文件大小 单位MB
     * $array[data] 第三方返回的信息  用于调试
     */
    protected function fileInfo($url = '')
    {
        $array['code'] = 0;
        if (empty($url)){
            $array['msg'] = '文件不存在';
        } else{
            $key = substr($url,(strripos($url,'/')+1));
            $accessKey = config('qiniu.ACCESSKEY');
            $secretKey = config('qiniu.SECRETKEY');
            $bucket = config('qiniu.bucket');
            $auth = new Auth($accessKey, $secretKey);
            $config = new Config();
            $bucketManager = new BucketManager($auth, $config);
            list($fileInfo, $err) = $bucketManager->stat($bucket,$key);
            if ($err) {
                $array['msg'] = '文件信息查询失败';
                $array['data'] = $err;
            } else {
                $array['code'] = 1;
                $array['msg'] = '文件信息查询成功';
                $array['size'] = round($fileInfo['fsize']/1024/1024,2);
                $array['data'] = $fileInfo;
            }
        }
        return $array;
    }



    public function _empty()
    {
        api_return(0,'路由错误');
    }

    protected function phone_to_id(){
        $phone = input('post.phone');
        $usr = Db::name('users')->where(['phone'=>$phone,'status'=>1])->value('user_id');
        if(!$usr||is_null($usr)){
            api_return(0,'用户信息不存在或被禁用');
        }
        return $usr;
    }

    /**
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * 创建token 并存储在数据库中
     */
//    protected function makeToken($user_id){
//        $token   = md5($user_id.time());
//        if (input('j_push_id')) $data['j_push_id'] = input('j_push_id');
//        $data['token']  = $token;
//        $data['token_expire'] = time()+config('token_expire');
//        $result  = Db::name('users')->where('user_id',$user_id)->update($data);
//        if (!$result){
//            //如果绑定token失败就重复绑定一次
//            Db::name('users')->where('user_id',$user_id)->update($data);
//        }
//        return $token;
//    }


    /**
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * 创建token 并存储在缓存中
     */
    protected function makeToken($user_id){
        $hash  = hashid($user_id);
        $key   = md5($user_id.time());
        $token = $hash.'+'.$key;

        if (input('j_push_id')){
            $data['j_push_id'] = input('j_push_id');
            $result  = Db::name('users')->where('user_id',$user_id)->update($data);
            if (!$result){
                //如果更新失败就重复更新一次
                Db::name('users')->where('user_id',$user_id)->update($data);
            }
        }
        $cache['key']     = $key;
        $cache['user_id'] = $user_id;

        cache('token_'.$hash,$cache,time()+config('token_expire'));

        return $token;
    }

    /**
     * token对比
     * 用于验证是否登陆
     */
    protected function checkToken(){
        $request = Request::instance();
        if ($request->isPost()){
            $token = $request->post('token')??$request->header()['token'];
            if (empty($token)){
                api_return(-1,'未登录');
            }
            $data  = explode('+',$token);
            $cache = cache('token_'.$data[0]);
            if ($cache['key'] == $data[1]){
                return $cache['user_id'];
            }else{
                api_return(-1,'登录过期');
            }
        }
        api_return(0,'访问错误');
    }


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 检查验证码是否正确
     */
    protected function checkCode($field = 'phone',$msg = '验证码错误',$code = 'code')
    {
        $phone = is_numeric($field)??input("post.$field");
        $code  = input("post.$code");
        $cache = cache('code'.$phone);
        if (!$cache || $code != $cache){
            api_return(0,$msg);
        }else{
            cache('code'.$phone,null);
        }
    }


//    /**
//     * token对比
//     * 用于验证是否登陆
//     */
//    protected function checkToken(){
//        if (request()->isPost()){
//            $token = input('post.token')??request()->header()['token'];
//            if (empty($token)){
//                api_return(-1,'登录过期');
//            }
//            $data  = Db::name('users')->where('token',$token)->field('user_id,token,token_expire,status')->find();
//            if ($data['status'] == 1){
//                if ($token == $data['token']){
//                    if(time() < $data['token_expire']){
//                        return $data['user_id'];
//                    }
//                }
//                api_return(-1,'登录过期');
//            }
//            api_return(0,'账号不存在或被禁用');
//        }
//        api_return(0,'访问错误');
//    }





    /**
     * @param int $phone
     * @return bool
     * 检查手机号是否可以注册
     */
    protected function is_register($phone = 0)
    {
        $data   = Db::name('users')->where('phone',$phone)->value('user_id');
        if ($data) return false;
        return true;
    }

    /**
     * 验证昵称是否可用
     */
    protected function exist($nick_name){
        $data  = Db::name('users')->where('nick_name',$nick_name)->value('user_id');
        if ($data){
            return false;
        }else{
            $arr = Db::name('extend')->where('id',1)->value('nick_name');
            $array = explode(',',$arr);
            if (in_array($nick_name,$array)){
                return false;
            }
        }
        return true;
    }


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 查询单独一条数据
     * @param string $db 要操作的数据库
     * @param string|array $map 传入string为单字段筛选根据post获取到的id进行查找  传入array即代表调用方法前已处理数据 直接筛选
     * @param string $field 要查找的数据字段
     * @param bool $type 为false表示查找$field字段 为true表示过滤$field字段
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function _find($db = '',$map = '',$field = '', $type = false){
        if (is_array($map)){
            $where = $map;
        }else{
            $id = input('post.id');
            if(is_numeric($id)){
                $where[$map] = $id;
            }else{
                api_return(0,'参数错误');
            }
        }
        return Db::name($db)->field($field,$type)->where($where)->find();
    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 获取单独字段数据
     * @param string $db 要操作的表
     * @param string $field 要获取的字段
     * @param string|array $map 传入string为单字段筛选根据post获取到的id进行查找  传入array即代表调用方法前已处理数据直接查找
     * @return mixed
     */
    protected function _value($db = '',$field = '',$map = ''){
        if (is_array($map)){
            $where = $map;
        }else{
            $id = input('post.id');
            if(is_numeric($id)){
                $where[$map] = $id;
            }else{
                api_return(0,'参数错误');
            }
        }
        return Db::name($db)->where($where)->value($field);

    }


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 数据删除
     * @param string $db 要进行操作的数据库
     * @param string $field 要操作的字段名
     * @param bool $type false 假删 true 真删
     * @return int|string
     * @throws Exception
     * @throws \think\exception\PDOException
     */
    protected function _pass($db = '',$field = '',$type = false){
        $id = input('post.id');
        if(is_numeric($id)){
            if ($type){
                return Db::name($db)->where($field,$id)->delete();
            }else{
                return Db::name($db)->where($field,$id)->update(['status' => 0]);
            }
        }
        api_return(0,'参数错误');
    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * @param string $user_id
     * @return mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 获取融云token
     */
    protected function R_token($user_id = '')
    {

        $token  = cache('r_token_'.$user_id);
        if ($token) return $token;
        $userId     = hashid($user_id);
        $userInfo   = Db::name('users')->where('user_id',$user_id)->field('nick_name,header_img')->find();
        $nick_name  = $userInfo['nick_name']??'游客'.rand(111111,99999);
        $header_img = $userInfo['header_img']??config('default_img');

        $model  = new RongCloud(config('rongyun')['appKey'],config('rongyun')['appSecret']);
        $result = $model->user()->getToken($userId,$nick_name,$header_img);
        $res    = json_decode($result,true);
        if ($res['code'] == 200){
            if (is_numeric($user_id)) cache('r_token_'.$user_id,$res['token'],86400*7);
            return $res['token'];
        }
        return "";
    }


    /**
     * @param $room_id string  要发送到的聊天室id
     * @param $type   int  消息类型 详见switch注释
     * @param $rows  array 消息主体内容
     *
     *  $rows 解释
     *  gift  礼物消息
     *  $rows[gift_name]  礼物名称
     *  $rows[img]  礼物图片地址
     *  $rows[num]  送礼数量
     *  $rows[role_name]  送礼者角色名
     *  $rows[role_id]  送礼者加密后角色id
     *
     *
     *  red_package 红包消息
     *  $rows[msg]  红包信息
     *  $rows[red_id]  红包id
     *  $rows[role_name]  发送者角色名
     *  $rows[header_img]  发送者头像
     *  $rows[role_id]  发送者加密后角色id
     *
     *
     * guess 竞猜消息
     * $rows['guess_id'] 竞猜id
     * $rows['title']  竞猜标题
     * $rows['answer_A']  选项A
     * $rows['answer_B']  选项B
     *
     *融云自定义消息发送
     */
    protected function sendMsg($room_id,$type,$rows = []){
        $model = new RongCloud(config('rongyun')['appKey'],config('rongyun')['appSecret']);
        switch ($type){
            case 1:
                $data['type'] = 'gift';//礼物消息
                $data['data'] = $rows;
                break;
            case 2:
                $data['type'] = 'red_package';//红包消息
                $data['data'] = $rows;
                break;
            case 3:
                $data['type'] = 'guess';//竞猜消息
                $data['data'] = $rows;
                break;
            case 4:
                $data['type'] = 'system';//系统通知消息
                $data['data'] = $rows;
                break;
            case 5:
                $data['type'] = 'ban';//开启全员禁言
                break;
            case 6:
                $data['type'] = 'play';//开启直播
                break;
            case 7:
                $data['type'] = 'close';//关闭直播
                break;
            case 8:
                $data['type'] = 'remove';//解除禁言
                break;
            default:
                break;
        }
        $content = json_encode(['content'=>$data,'extra'=>'']);
//        echo $content;
//        dump($content);exit;
        return $model->message()->publishChatroom('system', [$room_id], 'RC:custom',$content);
    }

    /**
     * 房间禁言名单获取
     */
    protected function banList($room_id)
    {
        $RongCloud = new RongCloud(config('rongyun')['appKey'],config('rongyun')['appSecret']);
        $result    =  $RongCloud->group()->lisGagUser($room_id);
        $json = json_decode($result,true);
        if ($json['code'] == 200)

            return $json['users'];
        return false;
    }

    /**
     * 禁言
     */
    protected function ban($room_id,$role_id,$time = 10)
    {
        $RongCloud = new RongCloud(config('rongyun')['appKey'],config('rongyun')['appSecret']);
        $result = $RongCloud->group()->addGagUser($role_id, $room_id, $time);
        $json = json_decode($result,true);
        if ($json['code'] == 200)
            return true;
        return false;
    }
    
    
    /**
     * 解除禁言
     */
    public function pick($role_id,$room_id)
    {
        $RongCloud = new RongCloud(config('rongyun')['appKey'],config('rongyun')['appSecret']);
        $result = $RongCloud->group()->rollBackGagUser([$role_id], $room_id);
        $json = json_decode($result,true);
        if ($json['code'] == 200)
            return true;
        return false;
    }

    /**
     * 聊天室禁言
     */
    protected function chatban($room_id,$role_id,$time = 10)
    {
        $RongCloud = new RongCloud(config('rongyun')['appKey'],config('rongyun')['appSecret']);
        $result = $RongCloud->chatRoom()->addGagUser($role_id, $room_id, $time);
        $json = json_decode($result,true);
        if ($json['code'] == 200)
            return true;
        return false;
    }

    /**
     * 聊天室禁言名单获取|检查某个用户在当前房间是否被禁言
     */
    protected function chatbanList($room_id,$role_id = 0)
    {
        if ($role_id){
            $cache = cache('chat_'.$room_id.'_'.$role_id);
            if ($cache) return $cache;
        }else{
            $cache = cache('banlist_'.$room_id);
            if ($cache) return $cache;
        }
        $RongCloud = new RongCloud(config('rongyun')['appKey'],config('rongyun')['appSecret']);
        $result    =  $RongCloud->chatRoom()->ListGagUser($room_id);
        $json = json_decode($result,true);
        if ($json['code'] == 200){
            if ($role_id){
                if (is_numeric($role_id)) $role_id = hashid($role_id);
                $rows = array_key($json['users'],'userId');
                $data = isset($rows[$role_id]);
                cache('chat_'.$room_id.'_'.$role_id,$data,3);
                return $data;
            }
            cache('banlist_'.$room_id,$json['users'],3);
            return $json['users'];
        }
        return false;
    }


    /**
     * 聊天室解除禁言
     */
    public function chatpick($role_id,$room_id)
    {
        $RongCloud = new RongCloud(config('rongyun')['appKey'],config('rongyun')['appSecret']);
        $result = $RongCloud->chatRoom()->rollBackGagUser([$role_id], $room_id);
        $json = json_decode($result,true);
        if ($json['code'] == 200)
            return true;
        return false;
    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 接口单独限流
     */
    protected function ApiLimit($time = 1,$user = 0)
    {
        $key   = request()->path().$user;
        $cache = cache($key);
        if ($cache){
            api_return(0,'请求过于频繁,请稍后重试');
        }else{
            cache($key,1,$time);
        }
    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 推送
     * @param $type 1 房间 2 资产 3钱包
     * @param string $j_push_id
     * @param string $title
     * @param $room_id
     * @return bool
     *
     */
    protected function Push($type,$j_push_id = '',$title = '来自聊天约玩直播的推送消息',$room_id)
    {
        if (!$j_push_id) return false;

        $extend = [];
        if ($type == 1){
            $extend['extras'] = [
                'type' => 'room',//跳转至房间
                'room_id' => $room_id,
            ] ;
        }elseif ($type == 2){
            $extend['extras'] = [
                'type' => 'assets',//跳转至资产
            ] ;
        }elseif ($type == 3){
            $extend['extras'] = [
                'type' => 'wallet',//跳转至钱包
            ] ;
        }

         j_push($title,$j_push_id,$extend);
        dump($extend);exit;
    }



}
