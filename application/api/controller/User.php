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
     * 查看用户是否还能添加角色
     */
    public function checkRole()
    {
        if ($this->Role()) api_return(1,'查询成功',true);
        api_return(1,'查询成功',false);
    }

    /**
     * 检查用户能否添加角色
     */
    protected function Role(){
        $user = Db::name('users')->where('user_id',$this->user_id)->field('role_num,role_max')->find();
        if ($user['role_num'] < $user['role_max']) return true;
        return false;
    }

    /**
     * 添加角色
     */
    public function addRole()
    {
        if (!$this->Role()) api_return(0,'您的角色创建次数已用完');
        $data = request()->only(['role_name','sex','birthday','header_img','first_time','sign','place'],'post');
        if (empty($data['header_img'])) $data['header_img'] = config('default_img');
        $data['user_id'] = $this->user_id;
        $model  = new Role();
        $result = $model->saveChange($data);
        if ($result !== false){
            Db::name('role')->where('role_id',$model->role_id)->update(['role_name'=>$model->role_name.substr(hashToken($this->user_id),-6)]);
            Db::name('users')->where('user_id',$this->user_id)->setInc('role_num');
            api_return(1,'角色创建成功',hashid($model->role_id));
        }
        api_return(0,$model->getError());
    }

    /**
     * 当前用户角色列表
     */
    public function roleList()
    {
        $model = new \app\common\model\Role();
        $where['user_id'] = $this->user_id;
        $rows = $model->userRoles($where,$this->role_id);
        if ($rows !== false) api_return(1,'获取成功',$rows);
        api_return(0,'您还没有拥有角色');
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
     * 切换角色
     */
    public function changeRole()
    {
        $where['role_id'] = dehashid(input('post.id'));
        $where['user_id'] = $this->user_id;
        $where['status']  = 1;
        $model = new Users();
        $on = Db::name('role')->where($where)->value('user_id');
        if ($on){
            $result = $model->where('user_id',$this->user_id)->update(['role_id'=>$where['role_id']]);
            if ($result !== false) api_return(1,'切换成功');
        }
        api_return(0,'服务器繁忙,请稍后重试');
    }



    /**
     * 获取角色下的房间列表
     */
    public function roomList()
    {
        $where['user_id'] = $this->user_id;
        $model = new \app\common\model\Room();
        $rows  = $model->getRoom($where);
        if ($rows !== false) api_return(1,'获取成功',$rows);
        api_return(0,'暂无数据');
    }

    /**
     * 查看角色是否还可以新建房间
     */
    public function checkRoom()
    {
        $id = $this->role_id;
        if ($this->Room($id)) api_return(1,'查询成功',true);
        api_return(1,'查询成功',false);
    }

    /**
     * 检查当前用户是否还能新建房间
     */
    protected function Room(){
        $where['user_id'] = $this->user_id;
        $role = Db::name('users')->where($where)->field('room_num,room_max')->find();
        if ($role['room_num'] < $role['room_max']) return true;
        return false;
    }

    /**
     * 新建房间
     */
    public function addRoom()
    {
        $data = request()->only(['room_name','detail','img','cid'],'post');
        $id   = $this->user_id;
        if (!$this->Room()) api_return(0,'您的房间创建次数已用完');
        $data['role_id'] = $this->role_id;
        $data['user_id'] = $id;
        $data['start_time'] = time();
        $model = new Room();
        $result = $model->saveChange($data);
        if ($result !== false){
            Db::name('users')->where('user_id',$this->user_id)->setInc('room_num');
            api_return(1,'房间创建成功');
        }
        api_return(0,$model->getError());
    }

    /**
     * 获取用户可用角色昵称列表
     */
    public function roleName()
    {
        $model = new RoleName();
        $where['user_id'] = $this->user_id;
        $where['status']  = 1;
        $rows  = $model->getName($where);
        if ($rows !== false) api_return(1,'获取成功',$rows);
        api_return(0,'暂无数据');
    }

    /**
     * 修改用户角色名
     */
    public function editName()
    {
        $map['role_id'] = $this->role_id;
        $map['user_id'] = $this->user_id;
        $map['status']  = 1;
        $role = new Role();
        $has  = $role->where($map)->field('role_id,role_name')->find();
        if (empty($has)) api_return(0,'角色id错误');
        $where['name_id'] = dehashid(input('post.id'));
        $where['user_id'] = $this->user_id;
        $where['status']  = 1;
        $model = new RoleName();
        $on = $model->where($where)->field('role_name')->find();
        if (empty($on)) api_return(0,'参数错误');
        try{
            $model->where('name_id',$where['name_id'])->update(['status'=>0]);
            $model->where('role_name',$has['role_name'])->update(['status'=>1]);
            $role->where('role_id',$map['role_id'])->update(['role_name'=>$on['role_name']]);
            Db::commit();
        }catch (Exception $e) {
            Db::rollback();
            api_return(0,'系统错误');
        }
        api_return(1,'修改成功');
    }

    /**
     * 角色信息修改
     */
    public function editRole()
    {
        $model = new Role();
//        $where['role_id'] = $this->role_id;
//        $where['user_id'] = $this->user_id;
//        $on = $model->where($where)->value('role_id');
//        if (empty($on)) api_return(0,'参数错误');
        $data = request()->only(['header_img','sex','place','first_time','birthday','sign'],'post');
        if (empty($data)) api_return(0,'请输入参数');
        $data['id'] = $this->role_id;
        $result = $model->saveChange($data);
        if ($result !== false) api_return(1,'修改成功');
        api_return(0,$model->getError());
    }



    /**
     * 获取用户可用房间名列表
     */
    public function roomName()
    {
        $model = new RoomName();
        $where['user_id'] = $this->user_id;
        $where['status']  = 1;
        $rows  = $model->getName($where);
        if ($rows !== false) api_return(1,'获取成功',$rows);
        api_return(0,'暂无数据');
    }

    /**
     * 修改房间信息
     */
    public function editRoom()
    {
        $map['room_id'] = dehashid(input('post.id'))??21;
        if (!is_numeric($map['room_id'])) api_return(0,'非法参数');
        $map['status']  = 1;
        $model = new Room();
        $room  = $model->where($map)->field('room_id,user_id')->find();
        if (empty($room)) api_return(0,'非法参数');
        if ($room['user_id'] != $this->user_id){
            $where['role_id'] = $this->role_id;
            $where['status']  = 2;
            $power  = Db::name('room_follow')->where($where)->value('follow_id');
            if (!$power) api_return(0,'您没有权限修改房间信息');
        }
        $data = request()->only('img,detail,is_close,cid','post');
        if (empty($data)) api_return(0,'请输入要修改的内容');
        $data['id'] = $map['room_id'];
        $result = $model->saveChange($data);
        if ($result !== false){
            if ( isset($data['is_close']) && $data['is_close'] == 1){
                $this->sendMsg('room_'.hashid($map['room_id']),5);
                $this->sendMsg('play_'.hashid($map['room_id']),5);
            }elseif (isset($data['is_close']) && $data['is_close'] == 0){
                $this->sendMsg('room_'.hashid($map['room_id']),8);
                $this->sendMsg('play_'.hashid($map['room_id']),8);
            }
            api_return(1,'修改成功');
        }
        api_return(0,$model->getError());
    }


    /**
     * eth充值
     */
    public function recharge()
    {
        $ETHAddr = Db::name('user_extend')->where('user_id',$this->user_id)->value('ETHAddr');
        if ($ETHAddr){
            api_return(1,'获取成功',$ETHAddr);
        }else{
            $arr['Pwd'] = md5($this->user_id.'soha');
            $data = MBerryApi($arr,'api.Charge',$this->user_id);
            if ($data['Msg'] == 'success'){
                $item['Pwd'] = $data['Data']['Pwd'];
                $item['ETHAddr'] = $data['Data']['ETHAddr'];
                $item['Keystore'] = $data['Data']['Keystore'];
                $result = Db::name('user_extend')->where('user_id',$this->user_id)->update($item);
                if ($result){
                    api_return(1,'获取成功',$item['ETHAddr']);
                }else{
                    api_return(0,'服务器繁忙,请稍后重试');
                }
            }else{
                api_return(0,'系统繁忙,请稍后重试');
            }
        }
    }

    /**
     * eth提现地址绑定
     */
    public function eth()
    {
        $ETHurl = input('post.ETHurl');
        $trade_password = input('post.trade_password');
        $user = Db::name('users')->where('user_id',$this->user_id)->field('salt,trade_password')->find();
        if ($user['trade_password'] !=  md5(md5($trade_password).$user['salt'])) api_return(0,'交易密码错误');
        if (empty($ETHurl)) api_return(0,'参数错误');
        $where['user_id'] = $this->user_id;
        $where['status']  = ['between','2,3'];
        $b_id = Db::name('bankroll')->where($where)->count('b_id');
        if ($b_id) api_return(0,'您有提现正在处理中,请等待提现完成后更改提现地址');
        $result = db('user_extend')->where('user_id',$this->user_id)->update(['ETHurl'=>$ETHurl]);
        if ($result) api_return(1,'操作成功');
        api_return(0,'服务器繁忙,请稍后重试');
    }

    /**
     * 获取eth地址
     */
    public function getEth()
    {
        $data = db('user_extend')->where('user_id',$this->user_id)->value('ETHurl');
        if (!empty($data)) api_return(1,'获取成功',$data);
        api_return(0,'您还未绑定提现地址,请先绑定');
    }

    /**
     * 提现申请
     */
    public function cash()
    {
        //TODO 其它类型货币提现待确认
        $time = cache('cash_user_time_'.$this->user_id);
        if ($time){
            api_return(0,'服务器繁忙,请稍后重试');
        } else{
            cache('cash_user_time_'.$this->user_id,1,5);
        }
        $data = request()->only(['money','money_type'],'post');
        if (!is_numeric($data['money'])) api_return(0,'提现金额必须是整数');
        $data['ETHAddr'] = db('user_extend')->where('user_id',$this->user_id)->value('ETHurl');
        if (empty($data['ETHAddr'])) api_return(0,'请先绑定提现地址');
        $data['user_id'] = $this->user_id;
        switch ($data['money_type']){
            case 'eth'://以特币
                $mini = Db::name('extend')->where('id',1)->cache(60)->value('eth');
                break;
            case 'BCDN'://bcdn
                $mini = Db::name('extend')->where('id',1)->cache(60)->value('BCDN');
                break;
            default:
                api_return(0,'货币类型错误');
                break;
        }
        if ($data['money'] < $mini) api_return(0,$data['money_type'].'最低提现额为:'.$mini);
        $money = Db::name('users')->where('user_id',$this->user_id)->value($data['money_type']);
        if ($money < $data['money']) api_return(0,'账户余额不足');
        Db::startTrans();
        try{
            $data['status']  = 2;
            $data['type']    = 2;
            $data['order_num'] = 'RE'.hashid($this->user_id).date("Ymd").rand(1000,9999);
            $model = new Bankroll();
            $model->saveChange($data);
            Db::name('users')->where('user_id',$this->user_id)->setDec($data['money_type'],$data['money']);
            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            api_return(0,'系统错误');
        }
        api_return(1,'提现发起成功,请等待管理员审核',['time'=>date('Y-m-d H:i'),'order_num'=>$data['order_num']]);
    }

    /**
     * 积分BCDN互换
     */
    public function exchange()
    {
        $cache = cache('exchange_cache_'.$this->user_id);
        if ($cache){
            api_return(0,'访问过于频繁,请稍后重试');
        }else{
            cache('exchange_cache_'.$this->user_id,1,5);
        }
        $type  = input('post.type');
        $data = Db::name('extend')->where('id',1)->cache(5)->value('BCDN_to_money');
        $rate = $data != 0?$data:1;
        //手续费比例%
        $charge = Db::name('extend')->where('id',1)->cache(5)->value('charge');
        $money = input('post.money');
        //应扣手续费
        $change_money = bcmul($money,($charge /100),2);
        $item['user_id'] = $this->user_id;
        $item['num'] = $money;
        $item['create_time'] = time();
        if ($type == 'BCDN'){ //BCDN兑积分
            $item['type'] = 1;
            $money_type   = 4;
            $remark = '用户'."($this->phone)"."BCDN兑换积分扣除手续费$charge%(".$change_money.'BCDN)';
            $inc    = 'money';
            $dec    = 'BCDN';
            if ($money < 1 ) api_return(0,'1BCDN起换');
            $change = bcmul(bcsub($money,$change_money,2),$rate,2);
        }elseif($type == 'money'){ //积分兑BCDN
            $item['type'] = 2;
            $inc    = 'BCDN';
            $dec    = 'money';
            $money_type   = 1;
            $remark = '用户'."($this->phone)"."积分兑换BCDN扣除手续费$charge%(".$change_money.'积分)';
            if ($money < $data ) api_return(0,$data.'积分起换');
            $change = bcdiv(bcsub($money,$change_money,2),$rate,2);
        }else{
            api_return(0,'参数错误');
        }
        $balance = Db::name('users')->where('user_id',$this->user_id)->value($type);
        if ($balance < $money) api_return(0,'余额不足');
        Db::startTrans();
        try{
            Db::name('users')->where('user_id',$this->user_id)->setInc($inc,$change);
            Db::name('users')->where('user_id',$this->user_id)->setDec($dec,$money);
            Db::name('exchange')->insert($item);
            stream($change_money,$money_type,$remark,1);
            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            api_return(0,'系统错误');
        }
        api_return(1,'兑换成功');

    }

    /**
     * 货币兑换记录
     */
    public function record()
    {
        $where['user_id'] = $this->user_id;
        $where['type']    = input('post.type');
        $model = new Exchange();
        $rows  = $model->getList($where);
        if ($rows !== false) api_return(1,'获取成功',$rows);
        api_return(0,'暂无数据');
    }
    /**
     * 根据私钥修改手机号码
     */
    public function changePhone()
    {
        if (request()->isPost()){
            $data = request()->only(['phone','newPhone','private_key','code'],'post');
            $check = cache('newPhone_phone_'.$data['phone']);
            if ($check){
                api_return(0,'服务器繁忙,请稍后重试');
            }else{
                cache('newPhone_phone_'.$data['phone'],1,3);
            }
            if ($data['phone'] == $data['newPhone']) api_return(0,'新手机号不能和旧手机号重复');
            $cache = cache('code'.$data['newPhone']);
            if (empty($cache) || $cache != $data['code']) api_return(0,'验证码错误');
            $row = Db::name('users')->where('phone',$data['phone'])->field('user_id,private_key')->find();
            if ($row['private_key'] != $data['private_key']) api_return(0,'私钥错误!');
            $model = new \app\common\logic\Users();
            $map['id'] = $row['user_id'];
            $map['phone'] = $data['newPhone'];
            $result = $model->change($map);
            if ($result !== false) api_return(1,'修改成功');
            api_return(0,$model->getError());
        }
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
     * 下级人数以及总收益获取
    */
    public  function  all_detail(){
        $user_id = $this->user_id;
        $result['money'] = abs(all_money($user_id));
        $result['total_people'] = junior($user_id);
        api_return(1,'获取完成',$result);
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
