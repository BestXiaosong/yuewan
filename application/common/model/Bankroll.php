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
            ->join('user_account ac','ac.account_id = a.account_id','LEFT')
            ->where($where)
            ->order('a.b_id desc')
            ->field('a.*,u.nick_name,ac.type as acType')
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
        $rows = $this->where($where)->field('money,status,type,create_time,trade_type')->paginate();
        $item = $rows->items();
        if (empty($item)) return false;
        foreach ($item as $k => $v){

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
                default:
                    $item[$k]['trade_type'] = '未知状态';
                    break;
            }

            switch ($v['trade_type']){
                case 1:
                    $item[$k]['trade_type'] = '微信';
                    break;
                case 2:
                    $item[$k]['trade_type'] = '支付宝';
                    break;
                case 3:
                    $item[$k]['trade_type'] = '苹果内购';
                    break;
                case 4:
                    $item[$k]['trade_type'] = '银行卡提现';
                    break;
                case 5:
                    $item[$k]['trade_type'] = '支付宝提现';
                    break;
                default:
                    $item[$k]['trade_type'] = '未知类型';
                    break;
            }

        }
        return ['thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$item];

    }


    public function getDetail($where = [])
    {
        return $this->alias('a')
            ->join('users u','u.user_id = a.user_id','LEFT')
            ->join('user_account ac','ac.account_id = a.account_id','LEFT')
            ->where($where)
            ->field('a.*,u.nick_name,ac.type as acType,ac.account,ac.remark,ac.real_name')
            ->find();
    }


}