<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/14 0014
 * Time: 16:34
 */

namespace app\api\controller;

use app\common\model\Banner;
use app\common\model\Coin;
use app\common\model\PlayCategory;
use rongyun\api\RongCloud;
use think\Db;

class Api extends User
{


    /**
     * 获取banner及广告图
     */
    public function getBanner()
    {
        $model = new Banner();
        $where['status'] = 1;
        $where['cid'] = input('post.cid');
        $limit = is_numeric(input('post.num')) ? input('post.num') : 6;
        $rows = $model->getBanner($where, $limit);
        if (!empty($rows)) api_return(1, '获取成功', $rows);
        api_return(0, '暂无数据');
    }



    /**
     * 获取融云token
     */
    public function token()
    {
        $token = $this->R_token($this->role_id);
        if ($token !== false) api_return(1, '获取成功', $token);
        api_return(0, '服务器繁忙,请稍后重试');
    }


    /**
     * 获取说明文档
     */
    public function explain()
    {
        $id = input('post.id');
        if (!is_numeric($id)) api_return(0, '参数错误');
        $data = Db::name('explain')->where('id', $id)->value('content');
        if (!empty($data)) api_return(1, '获取成功', $data);
        api_return(0, '暂无数据');
    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 客服列表
     */
    public function service()
    {
        $map['status']     = 1;
        $map['is_service'] = 1;
        $rows = Db::name('admins')->where($map)->field('user_id,nick_name,header_img')->select();

        foreach ($rows as $k => $v){
            $rows[$k]['user_id'] = 'admin'.hashid($v['user_id']);
        }

        api_return(1,'获取成功',$rows);

    }















}