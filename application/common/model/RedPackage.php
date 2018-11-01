<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/27 0027
 * Time: 15:56
 */

namespace app\common\model;

use think\Db;
use think\Model;

class RedPackage extends Model
{
    public function getRedPackageList($where)
    {
        return $this->alias('r')->field('r.*,t.money_type_name')
            ->where($where)->join(['cl_red_money_type' => 't'], 'r.money_type=t.type_id', 'LEFT')
            ->order('r.red_id desc')
            ->paginate('', false, ['query' => request()->param()]);
    }

    public function getRedPackageOne($id)
    {
        return $this->where('red_id', $id)->cache(true, 5)->find();

    }

    //查询发送的红包记录
    public function sendRecord($user_id, $role_id)
    {
        $rows = $this->field('red_type,red_id,money,create_time,status,money_type')->where('user_id', $user_id)->where('role_id', $role_id)
            ->order('red_id desc')
            ->paginate();
        $items = $rows->items();
        if (empty($items)) return false;
        $redis = redisConnect();
        foreach ($items as $k => $va) {
            if ($va['status'] == 0) {
                $items[$k]['status'] = '已领完';
            } elseif ($va['status'] == 1) {
                $len = $redis->lLen('red_package_' . $va['red_id']);
                $items[$k]['status'] = '还剩' . $len . '个';
            } else {
                //=2 未领玩，已退回。
                $items[$k]['status'] = '已领完';
            }
            $items[$k]['red_id'] = hashid($va['red_id']);

        }
        return ['thisPage' => $rows->currentPage(), 'hasNext' => $rows->hasMore(), 'data' => $items];
    }

    //红包统计
    public function redAcount($user_id, $role_id)
    {
        return $this->field('count(*) as num,sum(money) as sum_money')->where('user_id', $user_id)->where('role_id', $role_id)->cache(true, 60)->group('role_id')->find();
    }

    //红包详情也查询发送红包的角色昵称和头像

    public function getRoleByRedId($red_id,$role_id=0,$s = 0)
    {

        $map['role_id'] = $role_id;
        $map['red_id']  = $red_id;
        $top = Db::name('red_history')->where($map)->find('h_id,money,luck_king,role_id');
        $red = $this->field('msg,num,status,money_type,create_time,end_time')
            ->where('red_id', $red_id)
            ->find();
        if (!$top){
            $top = Db::name('red_history')->where('red_id',$red_id)->order('money desc')->field('h_id,money,luck_king,role_id')->find();
        }
        $roleinfo = db('role')->field('header_img,role_name')->where('role_id',$top['role_id'])->cache(6)->find();

        $red['money'] = $top['money']??0;
        $red['header_img'] = $roleinfo['header_img']??config('default_img');
        $red['role_name'] = $roleinfo['role_name']??'暂无人领取';

        //获取货币名
        $red['coin_name'] = Db::name('coin')->where('coin_id',$red['money_type'])->value('coin_name');

        if ($red['status'] == 1) {
            //未领完
            $red['status'] = '已领取' . $s . '/' . $red['num'];
        } elseif ($red['status'] == 0) {
            $time = 0;
            $t = $red['end_time'] - strtotime($red['create_time']);
            if($t<60){
                $time = $t.'秒领完';
            }elseif($t<3600){
                $time = floor($t/60).'分钟领完';
            }else{
                $time = floor($t/3600).'小时领完';
            }
            $red['status'] = '已领完('.$red['num'].'个红包,'.$time.')';
        } else {
            //=2 未领完 已退回
            $red['status'] = '已领取' . $s . '/' . $red['num'].'(红包已过期)';
        }

        unset($red['create_time']);
        unset($red['end_time']);
        unset($red['num']);
        return $red;

    }

    //查询未领完并且过期的红包退还用户
    public function getExpireRed()
    {
        return $this->field('red_id,user_id,money_type,money')
            ->where('expire_time', '<', time())->where('status',1)->select();
    }

    //更改红包状态值
    public function updateStatus($red_id, $status)
    {
        return $this->where('red_id',$red_id)->save(['status'=>$status]);
    }


}