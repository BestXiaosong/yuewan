<?php
/**
 * Created by xiaosong
 * E-mail:306027376@qq.com
 * Date: 2018/11/14
 * Time: 16:14
 */

namespace app\api\controller;
use app\common\model\Skill as model;

class Skill extends User
{


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 添加资质
     */
    public function skill()
    {

        $model = new model();

        //TODO 资质申请完成后填充数据
        $data['my']   = $model->getRows(['type'=>3]);
        $data['game'] = $model->getRows(['type'=>2]);
        $data['fun']  = $model->getRows(['type'=>1]);

        api_return(1,'获取成功',$data);

    }



}