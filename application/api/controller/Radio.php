<?php
/**
 * Created by xiaosong
 * E-mail:4155433@gmail.com
 * Date: 2018/12/4
 * Time: 16:33
 * 电台管理模块
 */

namespace app\api\controller;


use app\common\logic\Logic;
use app\common\model\GiftRecord;
use app\common\model\RoomFollow;
use app\common\model\RoomLog;
use think\Db;
use wheat\Wheat;

class Radio extends User
{
    protected static $roomInfo = null;

    protected static $room_id  = null;

    protected static $roomType = 1;

    protected static $generalNotUp = [1];

    public function _initialize()
    {
        parent::_initialize();
        self::$room_id = input('post.room_id');
        self::$roomInfo = $this->roomInfo(self::$room_id);
        $this->roomType();
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 房间类型拦截
     */
    protected function roomType(){

        if (self::$roomInfo['type'] != self::$roomType){
            api_return(0,'房间类型访问错误');
        }
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 封麦|开麦
     */
    public function wheatChange()
    {
        $this->checkOwner();

        $data = request()->only(['wheat_id','type'],'post');

        $wheat = new Wheat();

        if ($data['type'] == 1){ //封麦
            $ret = $wheat->lock(self::$room_id,$data['wheat_id']);
        }else{ //开麦
            $ret = $wheat->open(self::$room_id,$data['wheat_id']);
        }

        if($ret['code']){
            api_return(1,$ret['msg'],$ret['data']['wheat']);
        }else{
            api_return(0,$ret['msg']);
        }

    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 检查用户是否为房主
     */
    protected function owner($user_id = null){

        if (!$user_id){
            $user_id = $this->user_id;
        }
        if ($user_id != self::$roomInfo['user_id']){
            return false;
        }
        return true;
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 非房主权限拦截
     */
    protected function checkOwner($user_id = null){
        if (!$user_id){
            $user_id = $this->user_id;
        }

        if (!$this->owner($user_id)){
            api_return(0,'您不是房主,不能进行操作');
        };
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 获取管理权限代码
     */
    protected function power($user_id = null)
    {
        if (!$user_id){
            $user_id = $this->user_id;
        }
        $power = self::roomPower(self::$room_id,$user_id);
        if ($power == 0 || $power == 1){
            return false;
        }
        return $power;
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     *  非管理操作拦截 为管理返回权限代码
     */
    protected function checkPower($user_id = null,$msg = '权限不足,不能操作')
    {
        if (!$user_id){
            $user_id = $this->user_id;
        }
        $power = $this->getPowerCode($user_id);

        if (!$power){
            api_return(0,$msg);
        }
        return $power;

    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * @param null $user_id
     * @return bool|int|mixed
     * 获取权限操作码
     */
    protected function getPowerCode($user_id = null){
        if (!$user_id){
            $user_id = $this->user_id;
        }

        $owner = $this->owner($user_id);
        if ($owner){
            $power = 1;
        }else{
            $power = $this->power($user_id);
        }
        if (!$power){
            $power = 0;
        }
        return $power;
    }




    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 下麦|踢下麦
     */
    public function downWheat()
    {

        $data = request()->only(['wheat_id','type'],'post');

        $wheat = new Wheat();

        if ($data['type'] == 1){ //踢下麦

            $this->checkPower();

        }else{ //自己下麦

            $info = $wheat->getWheat(self::$room_id)[$data['wheat_id']-1];

            if ($info['user_id'] !== hashid($this->user_id)){
                api_return(0,'您不在麦位上,不能下麦');
            }

        }

        $ret = $wheat->down(self::$room_id,$data['wheat_id']);

        if($ret['code']){
            api_return(1,$ret['msg'],$ret['data']['wheat']);
        }else{
            api_return(0,$ret['msg']);
        }
    }

    /**
     * 上麦、换麦、抱麦
     * */
    public function upWheat(){

        $post = request()->only(['wheat_id','type','user_id'],'post');
        //验证数据
        $result = $this->validate($post,'Wheat.up');
        if(true !== $result){
            api_return(0,$result);
        }
        //不传默认登录用户ID
        if(!empty($post['user_id'])){
            $post['user_id'] = dehashid($post['user_id']);
            if (!is_numeric($post['user_id'])) api_return(0,'参数错误');
        }

        $wheat = new Wheat();
        if(isset($post['type']) && $post['type']  == 1){//抱麦

            $this->checkPower($this->user_id);

            if ($post['wheat_id'] == 1){
                api_return(0,'主播位不能抱麦');
            }

            $ret = $wheat->embrace($post['user_id'],self::$room_id,$post['wheat_id']);
        }else{//上麦

            $powerCode = $this->getPowerCode();

            if (in_array($post['wheat_id'],self::$generalNotUp)){
                if (!$powerCode){
                    api_return(0,'非管理员不能上麦');
                }
            }

            if ($post['wheat_id'] == 1){
                $this->checkPower($this->user_id,'权限不足,不能上主播位');
            }
            $ret = $wheat->on($this->user_id,self::$room_id,$post['wheat_id'],$powerCode);
        }
        if($ret['code']){
            api_return(1,$ret['msg'],$ret['data']['wheat']);
        }else{
            api_return(0,$ret['msg']);
        }
    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 拉人入黑名单
     */
    protected function black($id,$room_id,$time = 600){
        $map['user_id']  = $id;
        $map['room_id']  = $room_id;

        $model = new Logic();
        $model->changeTable('room_blacklist');

        $data = $model->where($map)->find();

        if ($data['status'] == 1 && $data['end_time'] > time()){
            api_return(0,'该用户已在黑名单中');
        }

        $minute = $time/60;
        if ($data){
            $save['status']   = 1;
            $save['end_time'] = time() + $time;
            $save['minute']   = $minute;
            $result = $data->save($save);
        }else{
            $map['end_time'] = time() + $time;
            $map['minute']   = $minute;
            $result = $model->save($map);
        }
        if ($result){

            $content = '<tag>'.$this->userInfo('nick_name').
                '</tag> 把  <tag>'.$this->userInfo('nick_name',$id).
                '</tag> 踢出房间'.$minute.'分钟';

            $this->writeLog($content,$room_id);
            api_return(1,'操作成功');
        }
        api_return(0,'操作失败');
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 写入房间操作日志
     */
    protected function writeLog($content,$room_id)
    {
        //写入操作日志
        $log['create_time'] = time();
        $log['update_time'] = time();
        $log['room_id']     = $room_id;
        $log['content']     = $content;
        Db::name('room_log')->insert($log);

    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 将用户拉入黑名单
     */
    public function addBlack()
    {

        $data = request()->only(['id','time']);

        $id = dehashid($data['id']);

        if (!is_numeric($id)){
            api_return(0,'用户id错误');
        }

        if (!isInt($data['time'])){
            api_return(0,'封禁时间错误');
        }

        $this->checkPower();

        $userPowerCode = $this->getPowerCode($id);

        if ($userPowerCode != 0){
            api_return(0,'您不能将房主或房间管理踢出房间');
        }

        $noble_id = self::checkNoble($this->userExtra('noble_id,noble_time,user_id',$id));

        $array = [3,4,5];

        if (in_array($noble_id,$array)){
            api_return(0,'贵族不能被踢出房间');
        }

        $this->black($id,self::$room_id,$data['time']);
    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 设为房间管理
     */
    public function roomManage()
    {
        $id     = dehashid(input('post.id'));
        $status = input('post.status')??2;

        if ($status != 2 && $status != 3){
            api_return(0,'参数错误');
        }

        if (!is_numeric($id)){
            api_return(0,'用户id错误');
        }

        $this->checkPower();

        $powerCode = $this->getPowerCode($id);

        if ($powerCode){
            api_return(0,'该用户已是房间管理,不能重复操作');
        }

        $map['room_id'] = self::$room_id;
        $map['user_id'] = $id;

        $model = new Logic();
        $model->changeTable('room_follow');

        $data = $model->where($map)->find();

        if ($data){
            $result = $data->save(['status'=>$status]);
        }else{
            $map['status'] = $status;
            $result = $model->save($map);
        }

        if ($result){

            //写入操作日志
            $content = '<tag>'.$this->userInfo('nick_name').
                '</tag> 把  <tag>'.$this->userInfo('nick_name',$id).
                '</tag> 设为管理';

            $this->writeLog($content,self::$room_id);

            api_return(1,'操作成功');
        }
        api_return(0,$data->getError().$model->getError());
    }
    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 取消房间管理
     */
    public function cancelManage()
    {
        $id     = dehashid(input('post.id'));

        if (!is_numeric($id)){
            api_return(0,'用户id错误');
        }

        $this->checkPower();

        $powerCode = $this->getPowerCode($id);

        if (!$powerCode){
            api_return(0,'该用户不是房间管理,不能操作');
        }

        $map['room_id'] = self::$room_id;
        $map['user_id'] = $id;

        $model = new Logic();
        $model->changeTable('room_follow');

        $data = $model->where($map)->find();

        if ($data){
           $data->save(['status'=>1]);
        }else{
            api_return(0,$data->getError());
        }

        //写入操作日志
        $content = '<tag>'.$this->userInfo('nick_name').
            '</tag> 取消  <tag>'.$this->userInfo('nick_name',$id).
            '</tag> 的管理权限';

        $this->writeLog($content,self::$room_id);
        api_return(1,'操作成功');

    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 将用户移出黑名单
     */
    public function removeBlack()
    {

        $id = dehashid(input('post.id'));

        if (!is_numeric($id)){
            api_return(0,'用户id错误');
        }

        $this->checkPower();

        $map['user_id']  = $id;
        $map['room_id']  = self::$room_id;

        $model = new Logic();
        $model->changeTable('room_blacklist');

        $data = $model->where($map)->find();

        if ($data['status'] != 1 || $data['end_time'] < time()){
            api_return(0,'该用户不在黑名单中');
        }

        $save['status']   = 0;
        $save['end_time'] = 0;
        $result = $data->save($save);

        if ($result){

            //写入操作日志
            $content = '<tag>'.$this->userInfo('nick_name').
                '</tag> 把  <tag>'.$this->userInfo('nick_name',$id).
                '</tag> 移出封禁列表';

            $this->writeLog($content,self::$room_id);

            api_return(1,'操作成功');
        }
        api_return(0,'操作失败');

    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 房间管理列表
     */
    public function manageList()
    {
        $map['a.status']  = 2;
        $map['a.room_id'] = self::$room_id;
        $model = new RoomFollow();
        $rows = $model->getRows($map);
        api_return(1,'获取成功',$rows);
    }


    


}