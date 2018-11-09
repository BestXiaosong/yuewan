<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/2
 * Time: 10:36
 */

namespace app\api\controller;

use \app\common\logic\Vod as model;
use think\Db;

class Vod extends User
{

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 添加视频动态
     */
    public function addVod()
    {

        $data = request()->only(['play_url'],'post');


        $map['user_id'] = $this->user_id;
        $map['status']  = 1;
        $vodNum = Db::name('vod')->where($map)->count('pid');

        $vodMax = Db::name('extend')->where('id',1)->cache(60)->value('vod_max');

        if ($vodMax <= $vodNum) api_return(0,'每个人只能发布'.$vodMax.'条视频动态');

        $model = new model();

        $result = $model->saveChange($data);

        if ($result){
            api_return(1,'操作成功');
        }
        api_return($model->getError());

    }


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 删除视频动态
     */
    public function delVod()
    {
        $data = request()->only(['id'],'post');

        $map['pid']     = $data['id'];
        $map['user_id'] = $this->user_id;
        $map['status']  = 1;

        $pid = Db::name('vod')->where($map)->value('pid');

        if (!$pid) api_return(0,'参数错误');


        $result = Db::name('vod')->delete($pid);

        if ($result){
            //TODO 删除七牛云第三方上的视频


            api_return(1,'删除成功');
        }


        api_return(0,'服务器繁忙,请稍后重试');

    }









    /**
     * 获取回放推荐列表
     */
    public function lists()
    {
        $where['v.top'] = 1;
        $where['v.status'] = 1;
//        $id = 1;
        $id = $this->user_id;
        $model = new model();
        $result = $model->getLists($where,$id);
//        echo $result;exit;
        if($result){
            api_return(1,'成功',$result);
        }else{
            api_return(0,'暂无数据');
        }
    }






}