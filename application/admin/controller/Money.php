<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/28
 * Time: 11:35
 */

namespace app\admin\controller;


use app\common\model\Bankroll;
use app\common\model\CapitalFlow;
use app\common\model\UserAccount;
use think\Db;
use think\Exception;

class Money extends Base
{

    protected $status = [
        2 => ['status' => '2' ,'msg' => '待审核'],
//        3 => ['status' => '3' ,'msg' => '提现处理中'],
        4 => ['status' => '4' ,'msg' => '驳回'],
        5 => ['status' => '5' ,'msg' => '提现成功'],
    ];


    /**
     * 流水类型
     */
    protected $stream_status = [
        1 => ['key' => '1' ,'val' => '平台流水'],
        2 => ['key' => '2' ,'val' => '用户充值'],
        3 => ['key' => '3' ,'val' => '用户提现'],
    ];




    /**
     * 充值记录列表
     */
    public function index()
    {
        $where = [];
        if (!empty($_GET['status'])) $where['a.status']  = input('get.status');
        if (!empty($_GET['nick_name'])) $where['u.nick_name']  = ['like','%'.trim(input('get.nick_name')).'%'];
        $where['a.type'] = 1;
        $model = new Bankroll();
        $rows  = $model->cashList($where);
        $this->assign([
            'title' => '充值记录',
            'rows' => $rows,
            'pageHTML' => $rows->render(),
        ]);
        return view();
    }

    /**
     * 充值详情
     */
    public function detail2()
    {
        $model = new Bankroll();
        $where['a.b_id'] = input('id');
        $data = $model->getDetail($where);
        $this->assign([
            'data' => $data,
            'title' => '充值',
            'status' => $this->status,
        ]);
        return view();
    }


    /**
     * 提现申请列表
     */
    public function cash()
    {
        $where = [];
        if (!empty($_GET['status'])) $where['a.status']  = input('get.status');
        if (!empty($_GET['nick_name'])) $where['u.nick_name']  = ['like','%'.trim(input('get.nick_name')).'%'];
        $where['a.type'] = 2;
        $model = new Bankroll();
        $rows  = $model->cashList($where);
        $this->assign([
            'title' => '提现申请列表',
            'rows' => $rows,
            'pageHTML' => $rows->render(),
            'status' => $this->status
        ]);
        return view();
    }

    /**
     * 提现审核
     */
    public function edit()
    {
        $model = new Bankroll();
        if (request()->isPost()){
            $data = input('post.');
            $repeat = cache('cash_edit_repeat_'.$data['id']);
            if ($repeat){ //防止重复操作
                $this->error('请求过于频繁,请稍后重试');
            }else{
                cache('cash_edit_repeat_'.$data['id'],1,3);
            }
            $cash = $model->getDetail(['b_id'=>$data['id']]);
            if ($cash['status'] !== 2) $this->error('该申请已处理,请勿重复操作');
            if ($data['status'] == 1){//审核通过

                switch ($cash['acType']){
                    case '1':

                        $update['status'] = 5;
                        Db::name('bankroll')->where('b_id',$data['id'])->update($update);

                        break;
                    case '2'://支付宝
                        //TODO 支付宝申请下来后开放自动打款
//                        break;
                    default:
                        api_return(0,'未处理提现方式');
                }




            }else{//驳回
                Db::startTrans();
                try{
                    $update['status'] = 4;
                    Db::name('bankroll')->where('b_id',$data['id'])->update($update);
                    Db::name('users')->where('user_id',$cash['user_id'])->setInc('cash',$cash['money']);
                    Db::commit();
                }catch (Exception $e){
                    Db::rollback();
                    $this->error('系统错误');
                }
            }
            $this->success('审核成功',url('cash'));
        }
        $where['a.b_id'] = input('id');
        $data = $model->getDetail($where);
        $this->assign([
            'title' => '提现审核',
            'data' => $data,
            'status' => $this->status,
        ]);
        return view();
    }



        /*
         * 统计图
         */
    public function map(){
        $map=[];
        if(!empty($_GET['status'])){
            $map['status']=intval(trim(input('get.status')));
        }else{
            $map['status'] = ['in','1,5'];
        }


        if(!empty($_GET['startDate'])){
            $startDate=strtotime(trim(input('get.startDate')));
        }
        if(!empty($_GET['endDate'])){
            $endDate=strtotime(trim(input('get.endDate')));
        }
        if($startDate&&$endDate){
            $map['create_time']=['between time',[$startDate,$endDate]];
        }else if($startDate&&empty($endDate)){
            $map['create_time']=['> time',$startDate];
        }else if($endDate&&empty($startDate)){
            $map['create_time']=['< time',$endDate];
        }
        $res  = Db::name('bankroll')->where($map)->field('create_time,money,status,type')->order('create_time')->select();
        $rows = [];
        foreach ($res as $k => $v){
            $date = date('Y-m-d',$v['create_time']);
            if ($v['status'] == 1){ //充值
                if(array_key_exists($date,$rows)){
                    $rows[$date]['recharge'] += $v['money'];
                }else{
                    $rows[$date]['recharge']  = $v['money'];
                    $rows[$date]['cash'] = 0;
                }
            }else{ //提现
                if(array_key_exists($date,$rows)){
                    $rows[$date]['cash'] += $v['money'];
                }else{
                    $rows[$date]['cash']  = $v['money'];
                    $rows[$date]['recharge'] = 0;
                }
            }
        }
        $recharge = json_encode(array_column($rows,'recharge'));
        $cash = json_encode(array_column($rows,'cash'));
        $days  = json_encode(array_keys($rows));
        $this->assign([
            'days'=>$days,
            'recharge'=>$recharge,
            'cash'=>$cash,
            'status'=>input('get.status')
        ]);
        return view();
    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 平台资金流水
     */
    public function stream()
    {
        $where = [];

        if (!isEmpty(input('status'))){
            $where['a.status'] = input('status');
        }

        if (!isEmpty(input('money_type'))){
            $where['a.money_type'] = input('money_type');
        }


        if (!empty(input('startDate'))){
            if (empty(input('endDate'))){
                $where['a.create_time'] = ['> time',input('startDate')];
            }else{
                $where['a.create_time'] = ['between time',[input('startDate'),input('endDate')]];
            }
        }elseif (!empty(input('endDate'))){
            $where['a.create_time'] = ['<= time',input('endDate')];
        }
//        dump($where);exit;


        $model = new CapitalFlow();
        $rows  = $model->getList($where);

        $where['a.status'] = 1;
        //平台流水统计
        $stream = $model->alias('a')->where($where)->sum('money');

        //充值统计
        $where['a.status'] = 2;
        $recharge = $model->alias('a')->where($where)->sum('money');

        //提现统计
        $where['a.status'] = 3;
        $cash = $model->alias('a')->where($where)->sum('money');


        $coin = Db::name('coin')->select();
        $money_type = array_key($coin,'coin_id');
        $this->assign([
            'title' => '平台流水',
            'rows' => $rows,
            'pageHTML' => $rows->render(),
            'money_type' => $money_type,
            'status' => $this->stream_status,
            'stream' => $stream,
            'recharge' => $recharge,
            'cash' => $cash,
        ]);
        return view();
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 提现账号
     */
    public function account()
    {

        $where = [];
        if (!empty($_GET['status'])) $where['a.status']  = input('get.status');
        if (!empty($_GET['nick_name'])) $where['u.nick_name']  = ['like','%'.trim(input('get.nick_name')).'%'];

        $model = new UserAccount();
        $rows  = $model->getList($where);
        $this->assign([
            'title' => '充值记录',
            'rows' => $rows,
            'pageHTML' => $rows->render(),
        ]);


        return view();
    }

    public function change()
    {
        $this->_change('user_account');
    }


    
}