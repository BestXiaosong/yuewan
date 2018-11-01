<?php
namespace app\api\controller;
use app\common\logic\Bankroll;
use app\common\logic\Role;
use app\common\logic\Room;
use app\common\model\Exchange;
use app\common\model\RoleName;
use app\common\model\RoomName;
use think\Db;
use app\common\model\Users;
use think\Exception;
use app\common\model\RoomFollow;


class User extends Base
{



    public function _initialize()
    {
        parent::_initialize();
        $user = $this->checkToken();
        $this->user_id = $user['user_id'];
        $this->role_id = $user['role_id'];
        $this->room_id = $user['room_id'];
        $this->phone   = $user['phone'];
    }

    /**
     * 退出登陆
     */
    public function loginOut()
    {
        $user_id = $this->user_id;
        $result  = Db::name('users')->where('user_id',$user_id)->update(['token_expire'=>0]);
        if ($result !== false){
            session('userInfo',null);
            api_return(1,'操作成功');
        }
        api_return(0,'操作失败');
    }

    /**
     * 获取用户信息
     */
    public function getUser()
    {
        if (request()->isPost()){
            $where['a.user_id'] = $this->user_id;
            $model  = new Users();
            $result = $model->getDetail($where);
            if ($result !== false) {
                api_return(1, '获取成功', $result);
            } else {
                api_return(0, '获取失败');
            }
        }
    }






    /**
     * 修改手机号码
     */
    public function phone()
    {
        $data = input('post.');
        $userPhone = Db::name('users')->where('user_id',$this->user_id)->value('phone');
        $userCode  = cache('code'.$userPhone);
        if (empty($userCode) || $userCode != $data['userCode']) api_return(0,'原手机号码验证码错误');
        $newCode = cache('code'.$data['newPhone']);
        if (empty($data['newCode']) || $data['newCode'] != $newCode) api_return(0,'新手机号码验证码错误');
        $has = Db::name('users')->where('phone',$data['newPhone'])->value('user_id');
        if ($has) api_return(0,'新手机号码已注册');
        $result = Db::name('users')->where('user_id',$this->user_id)->update(['phone'=>$data['newPhone']]);
        if ($result !== false) {
            Db::name('users')->where('user_id',$this->user_id)->update(['token_expire'=>0]);
            api_return(1,'更换手机成功');
        }
        api_return(0,'更换手机失败');
    }



    /**
     * 根据role_id获取角色信息及是否关注
     */
    public function roleInfo()
    {
        $role_id = input('post.id');

        $where['a.role_id'] = dehashid($role_id);
//        $where['a.role_id'] = input('post.id');
        if (!is_numeric($where['a.role_id'])) api_return(0,'参数错误');
        $model = new \app\common\model\Role();
        $data  = $model->getOne($where,$this->role_id);
        if ($data !== false) {
            if (!empty(input('post.chat'))){
                $chat = input('post.chat');
                $data['baned'] = $this->chatbanList($chat,$role_id);
                $info = explode('_',$chat);
                $room_id = $info[1];
                $roomInfo = Db::name('room')->where('room_id',$room_id)->field('user_id')->cache(60)->find();
                if ($roomInfo['user_id'] == $this->user_id){
                    $data['is_admin'] = 1;
                }else{
                    $roomfollow = new RoomFollow();
                    $res = $roomfollow->getStatus(dehashid($room_id), $where['a.role_id']);
                    if ($res['status'] == 2) {
                        $data['is_admin'] = 1;
                    }else{
                        $data['is_admin'] = 0;
                    }
                }
            }
            api_return(1,'获取成功',$data);
        }
        api_return(0,'服务器繁忙,请稍后重试');
    }




    /**
     * 获取用户资产明细
     */
    public function getMoney()
    {
        $data = Db::name('users')->where('user_id',$this->user_id)->field('eth,btc,money,BCDN')->find();
        api_return(1,'获取成功',$data);
    }

    /**
     * 获取当前角色粉丝列表
     */
    public function my_fans(){
        $id = input('post.id');
        if (!empty($id)){
            $role_id = dehashid($id);
            if (!is_numeric($role_id)) api_return(0,'参数错误');
        }else{
            $role_id = $this->role_id;
        }
        $model = new Users();
        $list_num = $this->request->post('list_num')?$this->request->post('list_num'):'';
        $result = $model->fans($role_id,$list_num);
        if($result){
            api_return(1,'获取成功',$result);
        }else{
            api_return(0,'暂无数据');
        }
    }


    /**
     * 充值提现记录表
     */
    public function bankroll()
    {

        $model = new \app\common\model\Bankroll();
        $where['user_id'] = $this->user_id;
        $rows = $model->getRows($where);
        if ($rows !== false)  api_return(1,'获取成功',$rows);
        api_return(0,'暂无数据');
    }

    /**
     * 查看下级
     */
    public function son(int $type)
    {
        $user_id = $this->user_id;
        $model = new Users();
        switch ($type){
            case 1:
                $where['proxy_id'] = $user_id;
                $rows = $model->getList($where);
                break;
            case 2:
                $two = $model->where('proxy_id',$user_id)->column('user_id');
                if (count($two) == 0) api_return(0,'暂无数据');
                $where['proxy_id'] = ['in',$two];
                $rows = $model->getList($where);
                break;
            case 3:
                $two = $model->where('proxy_id',$user_id)->column('user_id');
                if (count($two) == 0) api_return(0,'暂无数据');
                $in['proxy_id'] = ['in',$two];
                $three = $model->where($in)->column('user_id');
                if (count($three) == 0) api_return(0,'暂无数据');
                $where['proxy_id'] = ['in',$three];
                $rows = $model->getList($where);
                break;
            default:
                api_return(0,'参数错误');
        }
        if (empty($rows['data'])) api_return(0,'暂无数据');
        api_return(1,'获取成功',$rows);
    }



    /**
     *   分享二维码、链接获取
     */
    public function qrcode(){
        $where = db('explain')->where(['id'=>12])->value('content');
        $rule = db('explain')->where(['id'=>13])->value('content');
        $url = $where.'login/share?user_id='.hashid($this->user_id);
        if(!empty(cache('code'.$this->user_id))){
            $pic = cache('code'.$this->user_id);
        }else{
            $pic = code($url);
            cache('code'.$this->user_id,$pic,24*60*60);
        }
        $data['qrcode'] = $pic;
        $data['url'] = $url;
        $data['rule'] = $rule;
        $data['backGround'] = Db::name('extend')->where('id',1)->value('backGround');
        api_return(1,'',$data);
    }
}
