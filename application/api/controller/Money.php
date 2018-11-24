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
    
    
    
    

}