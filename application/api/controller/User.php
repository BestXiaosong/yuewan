<?php
namespace app\api\controller;
use app\common\model\Bankroll;
use think\Db;
use app\common\model\Users;


class User extends Base
{
    //用户id
    protected $user_id = 0;


    public function _initialize()
    {
        parent::_initialize();
        $this->user_id = $this->checkToken();
    }

    public function test()
    {
        return $this->R_token($this->user_id);
    }

    /**
     * 退出登陆
     */
    public function loginOut()
    {
        $user_id = $this->user_id;
        $result  = Db::name('users')->where('user_id',$user_id)->update(['token_expire'=>0]);
        if ($result !== false){
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
            api_return(1, '获取成功', $result);
        }
    }


    /**
     * 修改手机号码
     */
    public function changePhone()
    {
        $data = input('post.');
        $userPhone = Db::name('users')->where('user_id',$this->user_id)->value('phone');
        $this->checkCode($userPhone,'原手机号码验证码错误');
        $this->checkCode('newPhone','新手机号码验证码错误');
        $has = Db::name('users')->where('phone',$data['newPhone'])->value('user_id');
        if ($has) api_return(0,'新手机号码已注册');
        $result = Db::name('users')->where('user_id',$this->user_id)->update(['phone'=>$data['newPhone']]);
        if ($result !== false) {
            api_return(1,'更换手机成功');
        }
        api_return(0,'更换手机失败');
    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 绑定手机号码
     */
    public function bindPhone()
    {
        $user = Db::name('users')->where('user_id',$this->user_id)->field('phone')->find();
        if ($user['phone']) api_return(0,'您已绑定手机,请进行修改');
        $this->checkCode();
        $phone = input('post.phone');
        $info  = Db::name('users')->where('phone',$phone)->value('user_id');
        if ($info) api_return(0,'该手机号码已绑定其它账号!');
        $result = Db::name('users')->where('user_id',$this->user_id)->update(['phone'=>$phone]);
        if ($result)api_return(1,'绑定成功');
        api_return(0,'绑定失败');
    }




    /**
     * 充值提现记录表
     */
    public function bankroll()
    {

        $model = new Bankroll();
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
