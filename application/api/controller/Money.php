<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/2
 * Time: 10:44
 */

namespace app\api\controller;

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
        $where['user_id'] = $this->user_id;
        $where['status'] = 1;
        $type = $this->request->post('type');
        if($type != 0){
            $where['money_type'] = $type;
        }
        $model = new MoneyDetail();
        $result = $model->getList($where);
        if($result){
            api_return(1,'成功',$result);
        }else{
            api_return(0,'暂无数据');
        }
    }

}