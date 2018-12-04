<?php
/**
 * Created by xiaosong
 * E-mail:4155433@gmail.com
 * Date: 2018/12/4
 * Time: 15:14
 */

namespace app\api\controller;


use app\common\logic\Logic;
use think\Db;
use think\Exception;
use think\helper\Time;

class Guard extends Api
{

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 获取守护价格
     */
    public function guardPrice()
    {
        $rows = $this->extend('guard_1,guard_2,guard_3');
        api_return(1,'获取成功',$rows);
    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 积分支付守护用户
     */
    public function guardUser()
    {
        $data = request()->only(['guard_user','type']);

        $price = $this->extend('guard_'.$data['type']);

        if (!is_numeric($price) || $price < 1){
            api_return(0,'参数错误');
        }

        $data['guard_user'] = dehashid($data['guard_user']);

        if (!is_numeric($data['guard_user'])){
            api_return(0,'用户id错误');
        }

        if ($data['guard_user'] == $this->user_id){
            api_return(0,'您不能守护您自己');
        }
        $logic  = new Logic();
        $logic->changeTable('user_guard');
        $map['user_id'] = $this->user_id;
        $map['guard_user'] = $data['guard_user'];

        $item = $logic->where($map)->find();

        $data['end_time'] = time() + Time::weekToSecond();
        if ($item){
            $data['id'] = $item[$logic->getPk()];
            if ($item['type'] == $data['type'] && $item['end_time'] > time()){
                $data['end_time'] = $item['end_time'] + Time::weekToSecond();
            }
        }

        $data['user_id'] = $this->user_id;

        Db::startTrans();
        try{

            $this->moneyDec($price);

            $logic->saveChange('user_guard',$data,false);

            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            api_return(0,'系统错误',$e->getMessage());
        }
        api_return(1,'开通成功');
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 查询与某一用户的守护关系
     */
    public function guardQuery()
    {
        $guard_user = dehashid(input('post.id'));

        if (!is_numeric($guard_user)){
            api_return(0,'用户id错误');
        }

        $map['guard_user'] = $guard_user;
        $map['user_id']    = $this->user_id;
        $map['status']     = 1;
        $map['end_time']   = ['>',time()];

        $type = Db::name('user_guard')->where($map)->value('type')??0;
        api_return(1,'获取成功',$type);
    }




    
}