<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/14 0014
 * Time: 16:34
 */

namespace app\api\controller;

use app\common\model\Banner;
use app\common\model\SkillApply;
use function Composer\Autoload\includeFile;
use think\Db;

class Api extends User
{





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


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 筛选分类下用户
     */
    public function screen()
    {

        $data  = input('post.');
        $map   = [];
        $order = '';

        //是否使用距离查询 默认false
        $distance = false;

        $map['a.status']  = 1;
        $map['a.is_use']  = 1;

        $map['a.skill_id'] = $data['skill_id'];

        if ($data['grade']){

            $map['a.my_grade'] = ['in',$data['grade']];

        }

        if ($data['form_id']){

            $map['a.my_form'] = ['like','%'.$data['form_id'].'%'];

        }

        if ($data['sex']){

            $map['u.sex'] = $data['sex'];

        }


        switch ($data['order']){ //筛选条件

            case 'new': //新人

                $map['a.num'] = ['<=',3];

                break;

            case 'discount': //特惠

                $order = ['a.mini_price'];

                break;
            case 'score': //根据好评排序 评分

                $order = 'a.score desc';

                break;


            case 'online':

                $map['e.online_status'] = 1;

                break;


            case 'city': //同城查询 根据距离查询

                $distance = true;

                break;

            default://热门

                $order = ['a.num'=>'desc','a.score'=>'desc'];

                break;

        }

        $model = new SkillApply();

        if ($distance){

            //根据距离查询
            $page = input('page')??5;

            $cache = cache('city_'.$this->user_id.'_'.$page);

            //如果当前用户筛选距离有缓存  返回缓存  没有缓存 就查询数据后返回数据并缓存下来  保留30s

            if ($cache){

                $rows = $cache;

            }else{
                //TODO 人多了之后把排除自己加上
//                $map['a.user_id'] = ['neq',$this->user_id];
                $max  = Db::name('extend')->where('id',1)->cache(60)->value('distance');

                $rows = $model->getCity($map,$this->userExtra('log,lat'),$page,$max);
                cache('city_'.$this->user_id.'_'.$page,$rows,30);
            }

        }else{

            $rows  = $model->getUsers($map,$order);

        }


//        print_r($rows);exit;

        api_return(1,'获取成功',$rows);

    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 获取与当前用户相反的性别数字
     */
    protected function sex(){

        if ($this->userInfo('sex') == 1){

            return 2;

        }else{

            return 1;

        }

    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 颜值筛选
     */
    public function vod()
    {

        $map = [];
        $map['a.status'] = 1;

        $model = new \app\common\model\Vod();

        $rows = $model->getRows($map);

        api_return(1,'获取成功',$rows);

    }











}