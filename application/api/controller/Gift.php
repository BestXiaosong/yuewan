<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 20$this->user_id8/7/3$this->user_id 003$this->user_id
 * Time: $this->user_id6:43
 */

namespace app\api\controller;
use app\common\model\GiftRecord;
use app\common\model\Gift as model;

class Gift extends  User
{
    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 礼物列表
     */
    public function  giftList()
    {
        $model=new model();

        $map['status'] = 1;

        $rows = $model->giftList($map);

        api_return(1,'获取成功',$rows);

    }


    /**
     * 我的资产 -> 礼物 统计列表
     */
    public function myList()
    {
        $where['a.to_user'] = $this->user_id;
        $map['a.user_id']   = $this->user_id;
        $model = new GiftRecord();
        $rows['get']  = $model->giftCount($where);
        $rows['send'] = $model->giftCount($map);
        if (empty($rows['get']) && empty($rows['send'])) api_return(0,'暂无数据');
        api_return(1,'获取成功',$rows);
    }


    /**
     * 我收到|发出的礼物详情
     */
    public function giftDetail()
    {
        $where['a.gift_id'] = input('post.id');
        if (!empty(input('post.time'))){
            $time = input('post.time');
            $info = date_parse_from_format('Y-m',$time);
            if ( 0 != $info['warning_count'] || 0 != $info['error_count']) api_return(0,'时间错误');
            $start = strtotime($time);
            $end   = strtotime($time.'-'.date('t', strtotime($start)).' 23:59:59');
            $where['a.create_time'] = ['between time',[$start,$end]];
        }
        $type = input('post.type');
        if (!is_numeric($where['a.gift_id'])) api_return(0,'参数错误');
        if ($type == 1){
            $where['a.to_user'] = $this->user_id;
        }elseif ($type == 2){
            $where['a.user_id'] = $this->user_id;
        }else{
            api_return(0,'参数错误');
        }
        $model = new GiftRecord();
        $rows  = $model->myDetail($where,$type);
        if ($rows !== false) api_return(1,'获取成功',$rows);
        api_return(0,'暂无数据');

    }




}