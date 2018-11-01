<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/28
 * Time: 11:35
 */

namespace app\common\model;


use think\Model;

class Bankroll extends Model
{
    public function cashList($where = []){
        return $this->alias('a')
            ->join('users u','u.user_id = a.user_id','LEFT')
            ->where($where)
            ->order('a.create_time desc')
            ->field('a.*,u.phone')
            ->paginate('',false,['query'=>request()->param()]);
    }

    /**
     * 提现已审核未成交数据
     */
    public function getList($page = 1,$num = 300)
    {
        $where['type'] = 2;
        $where['status'] = 3;
        return $this->where($where)->order('update_time desc')->field('TxHash,b_id,user_id,money')->page($page,$num)->select();
    }

    /**
     * 用户个人充值记录
     */
    public function getRows($where = [])
    {
        $rows = $this->where($where)->field('order_num,money,money_type,status,type,create_time')->paginate();
        $item = $rows->items();
        if (empty($item)) return false;
        foreach ($item as $k => $v){
            if ($v['type'] == 1){
                $item[$k]['money'] = '+'.$v['money'];
            }else{
                $item[$k]['money'] = '-'.$v['money'];
            }
            switch ($v['status']){
                case 1:
                    $item[$k]['status'] = '充值成功';
                    break;
                case 2:
                    $item[$k]['status'] = '待审核';
                    break;
                case 3:
                    $item[$k]['status'] = '处理中';
                    break;
                case 4:
                    $item[$k]['status'] = '审核失败';
                    break;
                case 5:
                    $item[$k]['status'] = '提现成功';
                    break;
            }
        }
        return ['thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$item];

    }


    public function getDetail($where = [])
    {
        return $this->alias('a')
            ->join('users u','u.user_id = a.user_id','LEFT')
            ->where($where)
            ->field('a.*,u.phone')
            ->find();
    }


}