<?php
namespace app\api\controller;
use app\common\logic\UserAccount;
use app\common\model\Bankroll;
use app\common\model\Job;
use app\common\model\RechargeConfig;
use think\Db;
use app\common\model\Users;


class User extends Base
{
    //用户id
    protected $user_id = 0;

    // 用户等级
    protected $level = null;

    //用户经验值
    protected $experience = null;


    public function _initialize()
    {
        parent::_initialize();
        $this->user_id = $this->checkToken();
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
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 获取自己的配置信息
     */
    protected function selfExtra($str = '')
    {

        if (!$this->$str){

            $data = Db::name('user_extend')->where('user_id',$this->user_id)->cache(3)->find();

            $this->level = $data['level'];
            $this->experience = $data['experience'];

            if ($str){
                return $data[$str];
            }

        }else{
            return $this->$str;
        }

    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * @param string $str
     * @param int $user_id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 获取其它用户的配置信息
     */
    protected function otherExtra($str = '',$user_id = 0){
        $data = Db::name('user_extend')->where('user_id',$user_id)->cache(1)->find();
        return $data[$str];
    }


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 获取用户信息
     */
    protected function userInfo($field = '',$user_id = null,$cache = 3){

        if ($user_id){

            $map['user_id'] = $user_id;

        }else{

            $map['user_id'] = $this->user_id;

        }

        if (strstr($field,',') || empty($field)){

            return Db::name('users')->field($field)->where($map)->cache($cache)->find();

        }else{

            return Db::name('users')->where($map)->cache($cache)->value($field);

        }

    }


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 获取用户扩展信息
     */
    protected function userExtra($field = '',$user_id = null,$cache = 3){

        if ($user_id){

            $map['user_id'] = $user_id;

        }else{

            $map['user_id'] = $this->user_id;

        }

        if (strstr($field,',') || empty($field)){

            return Db::name('user_extend')->field($field)->where($map)->cache($cache)->find();

        }else{

            return Db::name('user_extend')->where($map)->cache($cache)->value($field);

        }

    }


    /**
     * 获取用户信息
     */
    public function getUser()
    {
        $where['a.user_id'] = $this->user_id;
        $model  = new Users();
        $result = $model->getDetail($where);
        api_return(1, '获取成功', $result);
    }

    /**
     * 获取用户信息及修改
     */
    public function info()
    {

        $model = new Users();
        $data  = $model->getInfo($this->user_id);
        api_return(1,'获取成功',$data);

    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 形象照修改||添加
     */
    public function editImg()
    {
        $data = request()->only(['img_id','img'],'post');

        if (!$data['img']) api_return(0,'请上传图片');

        $data['update_time'] = time();

        $map['user_id'] = $this->user_id;
        $map['status']  = 1;

        if (is_numeric($data['img_id'])){  //形象照修改

            $map['img_id']  = $data['img_id'];
            $img_id = Db::name('user_img')->where($map)->value('img_id');
            if ($img_id){

                $result = Db::name('user_img')->update($data);
                if ($result){
                    api_return(1,'修改成功');
                }else{
                    api_return(0,'修改失败');
                }

            }

            api_return(0,'非法参数');

        }

        //形象照添加

        $num = Db::name('user_img')->where($map)->count();
        $max = Db::name('extend')->where('id',1)->cache(60)->value('img_max');

        if ($num >= $max){
            api_return(0,'形象照最多只允许上传'.$max.'张');
        }

        $data['create_time'] = time();
        $data['user_id']     = $this->user_id;
        $result = Db::name('user_img')->strict(false)->insert($data);
        if ($result){
            api_return(1,'上传成功');
        }else{
            api_return(0,'上传失败');
        }

    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 个人资料编辑
     */
    public function editInfo()
    {
        $data = request()->only(['header_img','nick_name','sex','job','sign','tag','birthday']);

        if ($data['header_img']){
            $cache = cache('header_img'.$this->user_id);
            if ($cache) api_return(0,'头像每天只能修改一次!');
            cache('header_img'.$this->user_id,1,todayEndTime());
        }
        if ($data['nick_name']){
            $cache = cache('nick_name'.$this->user_id);
            if ($cache) api_return(0,'昵称每天只能修改一次!');
            cache('nick_name'.$this->user_id,1,todayEndTime());
        }

        $model  = new \app\common\logic\Users();

        $data['id'] = $this->user_id;

        $result = $model->change($data);

        if ($result){
            api_return(1,'修改成功');
        }
        api_return(0,$model->getError());
    }


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 用户个人详情
     */


    public function userDetail()
    {

        $model = new Users();
        $data  = $model->getInfo($this->user_id);
        $data['constellation'] = get_constellation($data['birthday']);
        $data['place'] = Db::name('user_extend')->where('user_id',$this->user_id)->value('place');

        $data['onLine'] = checkOnline(hashid($this->user_id));

        $map['status']  = 1;
        $map['user_id'] = $this->user_id;

        $data['vod']   = Db::name('vod')->where($map)->field('pid,play_url,play_num')->select();

        $data['vod_max'] = Db::name('extend')->where('id',1)->value('vod_max');

        $gift['a.status']  = 1;
        $gift['a.user_id'] = $this->user_id;
        $data['gift_count'] = Db::name('gift_record')->alias('a')->where($gift)->sum('a.num');

        $data['gift_list']  = Db::name('gift_record')
            ->alias('a')
            ->join([
                ['gift g','g.gift_id = a.gift_id','left']
            ])
            ->where($gift)
            ->field("sum(a.num)as num,g.img,g.gift_name")
            ->group('a.gift_id')
            ->order('g.price')
            ->select();

//TODO 所在圈子  房间 家族  及自身通过的技能列表待处理
        api_return(1,'获取成功',$data);

    }



    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 获取职业列表
     */
    public function job()
    {
        $model = new Job();
        $rows  = $model->getList();
        api_return(1,'获取成功',$rows);

    }



    /**
     * 修改手机号码
     */
    public function changePhone()
    {
        $data = input('post.');
        $userPhone = Db::name('users')->where('user_id',$this->user_id)->value('phone');
        $this->checkCode($userPhone,'原手机号码验证码错误');
        $this->checkCode('newPhone','新手机号码验证码错误','newCode');
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
     * 用户钱包余额
     */
    public function wallet()
    {
        $data = $this->userBalance();

        $map['user_id'] = $this->user_id;
        $map['status']  = 1;

        $data['ID'] = Db::name('user_id')->where($map)->value('ID');

        $data['account_id'] = Db::name('user_account')->where($map)->value('account_id');

        api_return(1,'获取成功',$data);
    }


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 用户账户余额
     */
    protected function userBalance($user_id = null)
    {

        if (!$user_id){

            $user_id = $this->user_id;

        }

        $data = Db::name('users')->where('user_id',$user_id)->field('money,cash')->find();
        $data['total'] = bcadd($data['money'],$data['cash'],2);
        return $data;

    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 扣除用户账户余额
     */
    protected function moneyDec($money = 0,$user_id = null){

        if (!$user_id){

            $user_id = $this->user_id;

        }

        $balance = $this->userBalance($user_id);

        if ($money > $balance['total']) api_return(0,'余额不足');

        if ($balance['money'] >= $money){

            $result = Db::name('users')->where('user_id',$user_id)->setDec('money',$money);
            if (!$result) api_return(0,'扣款失败');

        }else{


            if ($balance['money'] > 0){

                $next = bcsub($money,$balance['money'],2);
                $result = Db::name('users')->where('user_id',$user_id)->setDec('money',$balance['money']);
                if (!$result) api_return(0,'扣款失败');

            }else{

                $next = $money;

            }

            $result = Db::name('users')->where('user_id',$user_id)->setDec('cash',$next);

            if (!$result){
                Db::name('users')->where('user_id',$user_id)->setInc('money',$balance['money']);
                api_return(0,'扣款失败');
            }

        }

    }



    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 获取用户账户余额
     */
    public function balance()
    {

        api_return(1,'获取成功',$this->userBalance());

    }


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 充值参数获取
     */
    public function rechargeConfig()
    {
        $data['balance'] = $this->userBalance();

        $map['status'] = 1;

        $model = new RechargeConfig();

        $data['rows']  = $model->getRows($map);

        api_return(1,'获取成功',$data);

    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 获取我的提现账户
     */
    public function account()
    {

        $type = input('post.type');

        if ($type != 1 && $type != 2) api_return(0,'查询类型错误');

        $map['user_id'] = $this->user_id;
        $map['type']    = $type;

        $data = Db::name('user_account')->field('account_id,real_name,account,remark,status')->where($map)->find();

        api_return(1,'获取成功',$data);

    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 提现账号编辑
     */
    public function editAccount()
    {

        $data = request()->only(['remark','id','account','real_name','type'],'POST');

        if ($data['type'] != 1 && $data['type'] != 2) api_return(0,'账号类型错误');

        $map['user_id'] = $this->user_id;
        $map['type']    = $data['type'];

        $account = Db::name('user_account')->where($map)->find();

        if (is_numeric($data['id'])){

            if ($data['id'] != $account['account_id']) api_return(0,'参数错误');

            if ($account['status'] == 2) api_return(0,'账号正在审核中,请勿重复操作');

        }else{

            //添加时验证
            if ($account) api_return(0,'您已添加有提现账号,不能继续添加新账号');

        }

        $data['user_id'] = $this->user_id;
        $data['status']  = 2;

        $model = new UserAccount();

        $result = $model->saveChange($data);

        if ($result) api_return(1,'提交成功');
        api_return(0,$model->getError());

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
     *   分享二维码、链接获取
     */
    public function qrcode(){
        $where = db('explain')->where(['id'=>12])->value('content');
        $rule  = db('explain')->where(['id'=>13])->value('content');
        $url   = $where.'login/share?user_id='.hashid($this->user_id);
        if(!empty(cache('code'.$this->user_id))){
            $pic = cache('code'.$this->user_id);
        }else{
            $pic = code($url);
            cache('code'.$this->user_id,$pic,24*60*60);
        }
        $data['qrcode'] = $pic;
        $data['url']    = $url;
        $data['rule']   = $rule;
        $data['backGround'] = Db::name('extend')->where('id',1)->value('backGround');
        api_return(1,'',$data);
    }


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 根据用户当前等级和当前经验获取下一等级信息
     */
    protected function nextLevel($level,$experience)
    {

        $rows = Db::name('user_level')->cache(300)->select();

        $array = array_key($rows,'level');

        $key = $level+1;
        $data['level']          = $level;
        $data['color']          = $array[$level]['color'];
        $data['nexLevel']       = $array[$key]['level'];
        $data['nexColor']       = $array[$key]['color'];
        $data['experience']     = $array[$key]['experience'];
        $data['nextExperience'] = $array[$key]['experience'] - $experience;

        return $data;
    }


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 获取用户会员信息
     */
    public function level()
    {

        $data = $this->nextLevel($this->selfExtra('level'),$this->selfExtra('experience'));

        api_return(1,'获取成功',$data);

    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 修改用户配置信息
     */
    public function editExtra()
    {

        $data = request()->only(['invite','dispatch','filter','j_push_id','log','lat','place'],'post');

        if (!$data) api_return(0,'请输入要修改的数据');

        $data['id'] = $this->user_id;

        $result = $this->_edit('user_extend','base.edit_extend',$data);

        if ($result){

            api_return(1,'修改成功');

        }

        api_return(0,$this->editError);

    }









}
