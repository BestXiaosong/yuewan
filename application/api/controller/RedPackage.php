<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/2 0002
 * Time: 10:27
 */

namespace app\api\controller;


use app\common\model\RedHistory;
use app\common\model\Role;
use app\common\model\Users;
use CreateRed\Client;
use think\Controller;
use think\Db;
use think\Request;

class RedPackage extends User
{
    //发送红包
    public function sendRed2()
    {
        $cache = cache('sendRed_'.$this->user_id);
        if ($cache){
            api_return(0,'请求过于频繁,请稍后重试');
        }else{
            cache('sendRed_'.$this->user_id,1,1);
        }
        $role_id = $this->role_id;
        $user_id = $this->user_id;
        $param = Request::instance()->post();
        if (!is_int((int)$param['num'])) api_return(0,'请输入正整数');
        $param['role_id'] = $role_id;
        $param['user_id'] = $user_id;
        $to_id_hash = Request::instance()->post('to_id');
        $param['to_id'] = dehashid($to_id_hash);
        //type  1 个人 2 聊天室 3 直播间
        $type_arr = array(1, 2, 3);
        $type = request()->post('type');
        $param['type'] = $type;
        if (!in_array($type, $type_arr)) {
            api_return(0, '非法参数值');
        }
        $minArr = Db::table('cl_extend')->field('inte_min_unit,btc_min_unit,eth_min_unit,BCDN_min_unit')->where('id=1')->find();
        $equal = round($param['money'] / $param['num'],2);
        if($param['num']<3&&$param['red_type']==2){
            $equal = round($param['money']/$param['num'],2);
//            $param['red_type'] = 1;
        }

//        coin表中的coin_id
//        $param['money_type']

        $model = new Users();
        $money = $model->getMoneyByUserId($user_id);
        switch ($param['money_type']) {
            case 1:
                //积分
                if ($minArr['inte_min_unit'] > $equal) {
                    api_return(0, '发送红包低于最低限制');
                }
                //查询用户积分余额
                if ($money['money'] < $param['money']) {
                    api_return(0, '余额不足');
                }

                if ($param['red_type'] == 1) {
                    //普通红包
                    $red = Client::equal($param['money'], $param['num'], 0.01);
                } else {
                    //拼手气红包
                    $max = 2 * $equal;

                    $red = Client::rand($param['money'], $param['num'], $minArr['inte_min_unit'], $max, 2);
                }
                break;
            case 2:
                //比特币
                if ($minArr['btc_min_unit'] > $equal) {
                    api_return(0, '发送红包低于最低限制');
                }
                //查询用户比特币余额
                if ($money['btc'] < $param['money']) {
                    api_return(0, '余额不足');
                }

                if ($param['red_type'] == 1) {
                    //普通红包
                    $red = Client::equal($param['money'], $param['num'], $minArr['btc_min_unit']);
                } else {
                    //拼手气红包
                    $max = 2 * $equal;
                    $red = Client::rand($param['money'], $param['num'], $minArr['btc_min_unit'], $max, 3);
                }
                break;
            case 3:
                if ($minArr['eth_min_unit'] > $equal) {
                    api_return(0, '发送红包低于最低限制');
                }
                //查询用户以太币余额
                if ($money['eth'] < $param['money']) {
                    api_return(0, '余额不足');
                }

                if ($param['red_type'] == 1) {
                    //普通红包
                    $red = Client::equal($param['money'], $param['num'], $minArr['eth_min_unit']);
                } else {
                    //拼手气红包
                    $max = 2 * $equal;
                    $red = Client::rand($param['money'], $param['num'], $minArr['eth_min_unit'], $max, 2);
                }
                break;
            case 4:
//                api_return(0,'min:'.$minArr['BCDN_min_unit'].'euq:'.$equal);
                if ($minArr['BCDN_min_unit'] > $equal) {
                    api_return(0, '发送红包低于最低限制');
                }
                //查询用户以太币余额
                if ($money['BCDN'] < $param['money']) {
                    api_return(0, '余额不足');
                }

                if ($param['red_type'] == 1) {
                    //普通红包
                    $red = Client::equal($param['money'], $param['num'], $minArr['BCDN_min_unit']);
                } else {
                    //拼手气红包
                    $max = 2 * $equal;
                    $red = Client::rand($param['money'], $param['num'], $minArr['BCDN_min_unit'], $max, 2);
                }
                break;
            default:
                api_return(0,'金币类型错误');
                break;

        }

        $redis = redisConnect();

        $maxval = 0;
        foreach ($red as $k => $v) {
            if ($maxval < $v) {
                $maxval = $v;
            }
        }


        //开启事务
        Db::startTrans();

        try {

            //生成记录
            $param['luck_king_money'] = $maxval;

            $param['create_time'] = time();
            $param['expire_time'] = $param['create_time'] + 24 * 3600;

            $model = new \app\common\logic\RedPackage();
            $res = $model->addRedPackage($param);
            //减小用户金额
            $userLogic = new \app\common\logic\Users();
            switch ($param['money_type']) {
                case 1:
                    //扣除积分
                    $userLogic->where('user_id', $user_id)->setDec('money', $param['money']);
                    //生成记录
                    money($user_id, 1, -$param['money']);
                    break;

                case 2:
                    //扣除比特币
                    $userLogic->where('user_id', $user_id)->setDec('btc', $param['money']);
                    //生成记录
                    money($user_id, 1, -$param['money'], 2);

                    break;
                case 3:
                    //扣除以太币
                    $userLogic->where('user_id', $user_id)->setDec('eth', $param['money']);
                    //生成记录
                    money($user_id, 1, -$param['money'], 3);
                    break;
                case 4:
                    //扣除BCDN
                    $userLogic->where('user_id', $user_id)->setDec('BCDN', $param['money']);
                    //生成记录
                    money($user_id, 1, -$param['money'], 4);
                    break;
            }

            foreach ($red as $v) {
                $redis->rpush("red_package_" . $res, $v);
            }
            //加密红包id
            $res = hashid($res);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            api_return(0, '发送失败');
        }
        $role = Db::name('role')->where('role_id',$role_id)->field('header_img,role_name')->cache(60)->find();
        if ($type == 3) { //直播间红包
            $this->sendMsg('play_' . $to_id_hash, 2, array('red_id' => $res, 'red_type' => $param['red_type'], 'role_id' => hashid($role_id), 'role_name' => $role['role_name'], 'header_img' => $role['header_img'], 'msg' => $param['msg']));
        } elseif ($type == 2) { //聊天室红包
            $this->sendMsg('room_' . $to_id_hash, 2, array('red_id' => $res, 'red_type' => $param['red_type'], 'role_id' => hashid($role_id), 'role_name' => $role['role_name'], 'header_img' => $role['header_img'], 'msg' => $param['msg']));
        }
        api_return(1, '发送成功', ['red_id' => $res, 'red_type' => $param['red_type'], 'msg' => $param['msg']]);

    }


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 发送红包  小松修改
     */
    public function sendRed()
    {
//        $this->ApiLimit(1,$this->user_id);

        $role_id = $this->role_id;
        $user_id = $this->user_id;
        $param = Request::instance()->post();
        if (!is_int((int)$param['num'])) api_return(0,'请输入正整数');
        $param['role_id'] = $role_id;
        $param['user_id'] = $user_id;
        $to_id_hash = Request::instance()->post('to_id');
        $param['to_id'] = dehashid($to_id_hash);
        //type  1 个人 2 聊天室 3 直播间
        $type_arr = array(1, 2, 3);
        $type = request()->post('type');
        $param['type'] = $type;
        if (!in_array($type, $type_arr)) {
            api_return(0, '非法参数值');
        }

        $coin = Db::name('coin')->where('coin_id',$param['money_type'])->cache(60)->find();

        $equal = number_format($param['money'] / $param['num'],$coin['length'],'.','');

//        coin表中的coin_id
//        $param['money_type']
        $money = Db::name($coin['table'])->where('user_id',$user_id)->value($coin['field'])??0;
        if ($coin['red_mini'] > $equal) {
            api_return(0, '发送红包低于最低限制');
        }

        if ($money < $param['money']) {
            api_return(0, '余额不足');
        }

        if ($param['red_type'] == 1) {
            //普通红包
            $red = Client::equal($param['money'], $param['num'], $coin['red_mini']);
        } else {
            //拼手气红包
//            $max = 2 * $equal;
            if ($param['num'] <= 2){
                $max = 2 * $equal;
            }else{
                $max =  rand(1,$param['num']-1) * $equal;
            }
            $red = Client::rand($param['money'], $param['num'], $coin['red_mini'], $max, $coin['length']);
            //获取将所有红包转为整数应乘以的倍数
            $num = pow(10,$coin['length'])??1;

            if ($param['num'] > 1){
                foreach ($red as $k => $v){ //循环处理返回的红包数组

                    $int = $v * $num; //将红包转为整数
                    if ($int > 5){ //红包大于5的时候进行处理
                        (int)$rand  = rand(1,$int-3); // 从1和当前红包金额之前随机取出一个值
                        $money = ($int - $rand) / $num; //计算当前红包应减掉金额后的值
                        $red[$k] = number_format($money,$coin['length'],'.',''); //为当前红包重新赋值
                        $rand2 = get_rand_num(0,$param['num']-1,$k);
                        $money2 = $red[$rand2] + ($rand /$num);
                        $red[$rand2] =number_format($money2,$coin['length'],'.','');
                    }

                }
            }
        }



        $maxval = max($red); //获取运气王代表的值
        $redis = redisConnect();

        //开启事务
        Db::startTrans();
        try {

            //生成记录
            $param['luck_king_money'] = $maxval;
            $param['create_time'] = time();
//            $param['expire_time'] = $param['create_time'] + 24 * 3600;
            $param['expire_time'] = $param['create_time'] + 60;
            $model = new \app\common\logic\RedPackage();
            $res = $model->addRedPackage($param);

            //扣除金币
            Db::name($coin['table'])->where('user_id', $user_id)->setDec($coin['field'],$param['money']);
            //生成记录
            money($user_id, $coin['coin_id'], -$param['money']);
            //将红包数组放入队列
            foreach ($red as $v) {
                $redis->rpush("red_package_".$res, $v);
            }
            //加密红包id
            $res = hashid($res);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            api_return(0, '发送失败');
        }
        $role = Db::name('role')->where('role_id',$role_id)->field('header_img,role_name')->cache(60)->find();
        if ($type == 3) { //直播间红包
            $this->sendMsg('play_'.$to_id_hash, 2, array('red_id' => $res, 'red_type' => $param['red_type'], 'role_id' => hashid($role_id), 'role_name' => $role['role_name'], 'header_img' => $role['header_img'], 'msg' => $param['msg']));
        } elseif ($type == 2) { //聊天室红包
            $this->sendMsg('room_'.$to_id_hash, 2, array('red_id' => $res, 'red_type' => $param['red_type'], 'role_id' => hashid($role_id), 'role_name' => $role['role_name'], 'header_img' => $role['header_img'], 'msg' => $param['msg']));
        }
        api_return(1, '发送成功', ['red_id' => $res, 'red_type' => $param['red_type'], 'msg' => $param['msg']]);

    }






    /**
     * 接收红包
     */

    public function receiveRed()
    {
        $user_id = $this->user_id;
        $role_id = $this->role_id;
        $red_id_hash = Request::instance()->post('red_id');
        $red_id = dehashid($red_id_hash);
        $to_id_hash = Request::instance()->post('to_id');
        $to_id = dehashid($to_id_hash);
        $red_type = Request::instance()->post('red_type');
        $model = new \app\common\model\RedPackage();
        $redinfo = $model->getRedPackageOne($red_id);
        if (empty($redinfo)) {
            api_return(0, '红包不存在');
        }
        //todo 只判断单一状态
        if ($redinfo->status == 0) {
            api_return(0, '红包已领完');
        }
        if ($redinfo->red_type != $red_type) {
            api_return(0, '红包不存在');
        }
        if ($redinfo->status == 2 || $to_id != $redinfo->to_id) {
            api_return(0, '红包不可领取');
        }
        $now_time = time();
        if ($redinfo->expire_time < $now_time) {
            api_return(0, '红包已过期');
        }

        $redis = redisConnect();
        $is_set = $redis->hGet('user_' . $red_id, $user_id);
        if ($is_set) {
            api_return(200, '不能重复领取');
        }
        //队列长度
        $len = $redis->lLen('red_package_' . $red_id);
        // 过期时间
        $exp = $redinfo->expire_time - $now_time;
        //取出队列的值
        if ($len > 0) {
            $value = $redis->lpop('red_package_' . $red_id);
            //生成领取记录
            $receive['user_id'] = $user_id;
            $receive['role_id'] = $role_id;
            $receive['red_id'] = $red_id;
            $receive['money'] = $value;
            if ($value == $redinfo->luck_king_money && $red_type == 2 && !$redis->get('luck_king_flag_' . $red_id)) {
                $receive['luck_king'] = 1;
                //设置产生手气王标识
                $redis->set('luck_king_flag_' . $red_id, $exp);
            } else {
                $receive['luck_king'] = 0;
            }
            $receive['create_time'] = time();

            if ($len == 1) {
                //最后一个红包记录结束时间和更新状态
                $logic = new \app\common\logic\RedPackage();
                $logic->updateEndTime($red_id);
            }
            //将记录写入数据库
            $historymodel = new \app\common\logic\RedHistory();
            $res = $historymodel->saveRedHistory($receive);
            if ($res) {
                //增加用户金币
                $userLogic = new \app\common\logic\Users();

                switch ($redinfo->money_type) {
                    case 1:
                        //加积分
                        $userLogic->where('user_id', $user_id)->setInc('money', $value);
                        //生成记录
                        money($user_id, 1, $value);
                        break;

                    case 2:
                        //加比特币
                        $userLogic->where('user_id', $user_id)->setInc('btc', $value);
                        //生成记录
                        money($user_id, 1, $value, 2);

                        break;
                    case 3:
                        //加以太币
                        $userLogic->where('user_id', $user_id)->setInc('eth', $value);
                        //生成记录
                        money($user_id, 1, $value, 3);
                        break;
                    case 4:
                        //加BCDN
                        $userLogic->where('user_id', $user_id)->setInc('BCDN', $value);
                        //生成记录
                        money($user_id, 1, $value, 4);
                        break;
                }

                //钱包明细
//                money($user_id,1,$value,$redinfo->money_type);
                //领取成功,将用户存入redis，去重不能重复领取
                $ret = $redis->hSet('user_' . $red_id, $user_id, $user_id);
                //去重设置过期时间
                $redis->expire('user_' . $red_id, $exp);
                api_return(1, '领取成功', ['money' => $value, 'red_id' => $red_id_hash, 'luck_king' => $receive['luck_king']]);
            }
        } else {
            api_return(0, '红包已领完');
        }


    }

    //我的红包发送记录

    public function mySendRed()
    {
        $user_id = $this->user_id;
        $role_id = $this->role_id;

        $model = new \app\common\model\RedPackage();
        $list = $model->sendRecord($user_id, $role_id);
        if ($list) {
            $count = $model->redAcount($user_id, $role_id);
            $list['count'] = $count;
            api_return(1, '获取成功', $list);
        }
        api_return(0, '获取失败');

    }

    //红包详情
    public function redDetails()
    {
        $role_id = $this->role_id;
        $red_id = Request::instance()->post('red_id');
        $red_id = dehashid($red_id);
        $model = new RedHistory();

        $res = $model->getHistoryByRedId($red_id);
        $redpackage = new \app\common\model\RedPackage();
        $role = $redpackage->getRoleByRedId($red_id,$role_id,$res['count']);
        $res['top'] = $role;
        if ($res) {
            api_return(1, '获取成功', $res);
        }
        api_return(0, '获取失败');

    }

    //我接收的红包
    public function myReceive()
    {
        $user_id = $this->user_id;
        $role_id = $this->role_id;

        $model = new RedHistory();
        $rows = $model->getReceiveByRoleId($role_id);

        if ($rows) {
            $count = $model->receiveAcount($user_id, $role_id);
            $rows['acount'] = $count;
            api_return(1, '获取成功', $rows);
        }
        api_return(0, '没有数据');
    }

    /**
     * 红包领取状态
     */

    public function redStatus()
    {
        $user_id = $this->user_id;
        $role_id = $this->role_id;
        $red_id_hash = Request::instance()->post('red_id');
        $red_id = dehashid($red_id_hash);
        $to_id_hash = Request::instance()->post('to_id');
        $to_id = dehashid($to_id_hash);
        $red_type = Request::instance()->post('red_type');
        $model = new \app\common\model\RedPackage();
        $redinfo = $model->getRedPackageOne($red_id);
        if (empty($redinfo)) {
            api_return(10, '红包不可领取');
        }

        $redis = redisConnect();
        $is_set = $redis->hGet('user_' . $red_id, $user_id);
        if ($is_set) {
            api_return(20, '红包已领取');
        }
        //todo 只判断单一状态
        if ($redinfo->status == 0) {
            api_return(30, '红包已领完');
        }
        if ($redinfo->red_type != $red_type) {
            api_return(10, '红包不可领取');
        }
        if ($redinfo->status == 2 || $to_id != $redinfo->to_id) {
            api_return(10, '红包不可领取');
        }
        $now_time = time();
        if ($redinfo->expire_time < $now_time) {
            api_return(10, '红包不可领取');
        }
        if ($redinfo->status == 1) {
            api_return(1, '可领取');
        }

    }


}