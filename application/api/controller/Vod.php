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
        $map['status']  = ['between','1,2'];
        $vodNum = Db::name('vod')->where($map)->count('pid');

        $vodMax = Db::name('extend')->where('id',1)->cache(60)->value('vod_max');

        if ($vodMax <= $vodNum) api_return(0,'每个人只能发布'.$vodMax.'条视频动态(含审核中的动态)');

        $model = new model();

        $result = $model->saveChange($data);

        if ($result){
            api_return(1,'添加成功');
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

        $vod = Db::name('vod')->where($map)->field('pid,play_url')->find();

        if (!$vod) api_return(0,'参数错误');

        $result = Db::name('vod')->delete($vod['pid']);

        if ($result){
            delVod($vod['play_url']);
            api_return(1,'删除成功');
        }

        api_return(0,'服务器繁忙,请稍后重试');
    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 回放视频播放
     */
    public function play()
    {
        $id = input('post.id');
        Db::name('vod')->where('pid',$id)->setInc('play_num');
        api_return(1,'操作成功');
    }





}