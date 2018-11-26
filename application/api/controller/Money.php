<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/2
 * Time: 10:44
 */

namespace app\api\controller;

use app\common\model\Bankroll;
use app\common\model\MoneyDetail;
use think\Db;
use think\Exception;

/**
 * 资金管理
 */
class Money extends  User
{
    /**
     * 资金明细接口
     */
    public function detail()
    {
        $data = input('post.');

        $where['user_id'] = $this->user_id;
        $where['status'] = 1;

        if ($data['month']){

            $info = date_parse_from_format('Y-m', $data['month']);

            $check =  0 == $info['warning_count'] && 0 == $info['error_count'];

            if ($check){

                $where['create_time'] = ['between time',getMonthRange($data['month'])];

            }else{

                api_return(0,'时间格式错误');

            }

        }

        if(is_numeric($data['type'])){
            $where['type'] = $data['type'];
        }

        $model = new MoneyDetail();

        $rows  = $model->getList($where);

        api_return(1,'获取成功',$rows);
    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 用户充值提现明细
     */
    public function roll()
    {
        $data = input('post.');

        $where['user_id'] = $this->user_id;

        if ($data['month']){

            $info = date_parse_from_format('Y-m', $data['month']);

            $check =  0 == $info['warning_count'] && 0 == $info['error_count'];

            if ($check){

                $where['create_time'] = ['between time',getMonthRange($data['month'])];

            }else{

                api_return(0,'时间格式错误');

            }

        }

        if(is_numeric($data['type'])){

            $where['type'] = $data['type'];

        }

        $model = new Bankroll();

        $rows = $model->getRows($where);

        api_return(1,'获取成功',$rows);

    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 提现界面
     */
    public function cashView()
    {
        $data['balance'] = $this->userBalance();

        $map['user_id'] = $this->user_id;
        $map['type'] = 1;
        $data['card'] = Db::name('user_account')->field('account_id,type,status')->where($map)->find();
        $map['type'] = 2;
        $data['alipay'] = Db::name('user_account')->field('account_id,type,status')->where($map)->find();

        api_return(1,'获取成功',$data);
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 提现申请
     */
    public function cash()
    {
        $this->ApiLimit(2,$this->user_id);

        $data = request()->only(['money','account_id']);

        $cash['user_id'] = $this->user_id;
        $cash['status']  = 2;
        $cash['type']    = 2;

        $b_id = Db::name('bankroll')->where($cash)->value('b_id');
        if ($b_id) api_return(0,'您有提现申请正在审核中,请等待审核成功后重试');

        $money = Db::name('extend')->where('id',1)->value('money');
        if (!is_numeric($data['money']) || $data['money'] < $money ) api_return(0,'提现金额为'.$money.'钻起');

        $map['user_id']    = $this->user_id;
        $map['account_id'] = $data['account_id'];
        $map['status']     = 1;

        $account = Db::name('user_account')->where($map)->find();

        if (!$account) api_return(0,'账号不存在或审核未通过');

        if ($data['money'] > $this->userBalance()['cash']) api_return(0,'账户可提现余额不足');

        $data['user_id'] = $this->user_id;
        $data['status']  = 2;
        $data['type']    = 2;
        $data['create_time'] = time();
        switch ($account['type']){
            case 1:
                $data['trade_type'] = 4;
                break;
            case 2:
                $data['trade_type'] = 5;
                break;
            default:
                api_return(0,'提现账户信息错误');
        }

        Db::startTrans();
        try{
            Db::name('users')->where('user_id',$this->user_id)->setDec('cash',$data['money']);
            Db::name('bankroll')->insert($data);
            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            api_return(0,'系统错误',$e->getMessage());
        }

        api_return(1,'提交成功');

    }

}