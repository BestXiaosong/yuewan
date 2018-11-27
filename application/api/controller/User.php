<?php
namespace app\api\controller;
use app\common\logic\UserAccount;
use app\common\model\Bankroll;
use app\common\model\Job;
use app\common\model\RechargeConfig;
use think\Db;
use app\common\model\Users;
use think\helper\Time;


class User extends Base
{
    //用户id
    protected $user_id = 0;


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
        cache('token_'.hashid($this->user_id),null);
        api_return(1,'退出成功');
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

        $data =  Db::name('users')->where($map)->cache($cache)->find();
        if (strstr($field,',') || empty($field)){
            if (!$field){ return $data; }
            $arr = explode(',',$field);
            $array = [];
            foreach ($arr as $v){
                $array[$v] = $data[$v];
            }
            return $array;
        }else{
            return $data[$field];
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
        $data =  Db::name('user_extend')->where($map)->cache($cache)->find();
        if (strstr($field,',') || empty($field)){
            if (!$field){ return $data; }
            $arr = explode(',',$field);
            $array = [];
            foreach ($arr as $v){
                $array[$v] = $data[$v];
            }
            return $array;
        }else{
           return $data[$field];
        }
    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 获取用户会员信息
     */
    protected function userNoble($field = '',$user_id = null,$cache = 15){
        if ($user_id){
            $map['user_id'] = $user_id;
        }else{
            $map['user_id'] = $this->user_id;
        }
        $noble_id =  Db::name('user_extend')->where($map)->value('noble_id');
        $data = Db::name('noble')->where('noble_id',$noble_id)->cache($cache)->find();
        if (strstr($field,',') || empty($field)){
            if (!$field){ return $data; }
            $arr = explode(',',$field);
            foreach ($arr as $v){
                $array[$v] = $data[$v];
            }
        }else{
            return $data[$field];
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
     * E-mail:4155433@gmail.com
     * 根据经验值及当前等级获取应该修改的等级  用于升级时判断
     */
    protected function levelNum($experience = 0,$level = 0)
    {
        $rows = Db::name('user_level')->cache(300)->select();

        $array = array_key($rows,'level');


        $max = count($array)-$level;

        for ($i = $level;$i <= $max;$i++){
            $options = ['options'=>['min_range'=>$array[$i-1]['experience']+1,'max_range'=>$array[$i]['experience']]];
            if(
                filter_var($experience, FILTER_VALIDATE_INT, $options) !== false
            ){
                return $array[$i]['level'];
            }
        }

    }



    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 增加用户经验
     */
    protected function addLevel($experience = 0,$user_id = null)
    {
        if (!$experience) return;
        if (!$user_id){
            $user_id = $this->user_id;
        }
        $nowLevel = $this->userExtra('level',$user_id);
        $nowExperience = $this->userExtra('experience',$user_id);
        $data = $this->nextLevel($nowLevel,$nowExperience);

        $item['experience'] = bcadd($experience,$nowExperience,0);

        if ($experience >= $data['nextExperience']){
           $maxLevel = Db::name('user_level')->order('level desc')->cache(30)->value('level');
           if ($nowLevel < $maxLevel){
               //达到等级升级经验且当前等级小于最大等级更新大厅VIP等级
               $item['level'] = $this->levelNum($item['experience'],$nowLevel);
           }
        }

        $result = Db::name('user_extend')->where('user_id',$user_id)->update($item);

        if (!$result){
            api_return(0,'增加经验失败!');
        }
        return true;
    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 获取用户会员信息
     */
    public function level()
    {

        $data = $this->nextLevel($this->userExtra('level'),$this->userExtra('experience'));

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


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 根据用户id和要购买的贵族id获取用户购买贵族所需的价格、赠送金额和购买后过期时间
     */
    protected function noblePrice(int $user_id)
    {
        $data = request()->only(['noble_id'],'post');
        $noble = Db::name('noble')->where('noble_id',$data['noble_id'])->cache(60)->find();
        if (!$noble) api_return(0,'参数错误');
        //获取用户当前贵族信息
        $userExtra = $this->userExtra('noble_id,noble_time',$user_id);
        $data['noble_time'] = time() + Time::daysToSecond(31);
        if (
            $data['noble_id'] == $userExtra['noble_id'] &&
            $userExtra['noble_time'] + Time::daysToSecond(31) > time()
        ){
            //处于续费保护期内
            $renew_rebate = $this->extend('renew_rebate');
            $rebate = bcdiv($renew_rebate,100,2);
            if ($userExtra['noble_time'] > time()){
                $data['noble_time'] = $userExtra['noble_time'] + Time::daysToSecond(31);
            }
        }else{
            $rebate = 1;
        }
        $data['price'] = bcmul($noble['price'],$rebate,2);
        $data['give']  = bcmul($noble['give'],$rebate,2);
        return $data;
    }





}
