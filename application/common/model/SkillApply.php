<?php

namespace app\common\model;

use Monolog\Handler\IFTTTHandler;
use think\Db;
use think\Model;

class SkillApply extends Model
{
    public function getList($where = []){
        return $this->alias('a')
            ->join('skill s','s.skill_id = a.skill_id','LEFT')
            ->join('users u','u.user_id = a.user_id','LEFT')
            ->where($where)
            ->field('a.*,s.skill_name,s.img as skill_img,u.nick_name,u.header_img')
            ->order('a.apply_id desc')
            ->paginate('',false,['query'=>request()->param()]);
    }

    public function getDetail($where = [])
    {
        return $this->alias('a')
            ->join('skill s','s.skill_id = a.skill_id','LEFT')
            ->join('users u','u.user_id = a.user_id','LEFT')
            ->where($where)
            ->field('a.*,s.skill_name,s.img as skill_img,u.nick_name,u.header_img')
            ->find();
    }


    public function getMy($map = [])
    {
        return $this->alias('a')
            ->join('skill s','s.skill_id = a.skill_id','LEFT')
            ->join('users u','u.user_id  = a.user_id','LEFT')
            ->where($map)
            ->field('s.skill_name,s.img,s.skill_id,a.is_use')
            ->select();
    }


    public function getRows($map = [])
    {

        $rows = $this->alias('a')
            ->join('skill s','s.skill_id = a.skill_id','LEFT')
            ->where($map)
            ->field('s.skill_name,a.apply_id,a.my_form,a.my_gift_id,s.form_id,s.gift_id,a.mini_price,s.spec')
            ->select();
        foreach ($rows as $k => $v){
            $form['form_id']  = ['in',$v['form_id']];
            $form['status']   = 1;
            $rows[$k]['form'] = Db::name('skill_form')->where($form)->field('form_id,form_name')->cache(10)->select();

            $gift['gift_id']  = ['in',$v['gift_id']];
            $gift['status']   = 1;
            $rows[$k]['gift'] = Db::name('gift')->where($gift)->field('gift_id,gift_name,thumbnail,img,price')->order('price')->cache(10)->select();

        }

        return $rows;
    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * @param array $map
     * @param string $order
     * 用户筛选
     */
    public function getUsers($map = [],$order = '')
    {

        $join = [
            ['user_extend e','e.user_id = a.user_id','left'],
            ['users u','u.user_id = a.user_id','left']
        ];

        $field = 'e.place,a.skill_id,a.mini_price,a.num,u.user_id,u.header_img,u.sex,u.nick_name,u.sign,e.online_time,e.online_status,e.room_id,e.noble,e.level';

        $rows =  $this->alias('a')->where($map)->join($join)
            ->field($field)
            ->order($order)
            ->cache(15)
            ->paginate()->each(function ($item){

                $item['distance'] = 0;

                if ($item['online_status']){

                    $item['status'] = '当前在线';

                }else{

                    $item['status'] = formatTime($item['online_time']);

                }

                if ($item['noble']){
                    //无贵族身份 不查询等级颜色
                    $item['color'] = '';
                }else{

                    //用户拥有等级  查询等级颜色
                    if ($item['level'] > 0){

                        $level['level'] = $item['level'];

                        $item['color'] = Db::name('user_level')->where($level)->cache(15)->value('color');

                    }else{
                        $item['color'] = '';
                    }

                }


                if ($item['room_id']){

                    $item['skill']['apply_id']   = 0;
                    $item['skill']['skill_name'] = '';

                }else{
                    //用户不在房间中   查询是否有技能
                    $skill['a.skill_id'] = $item['skill_id'];
                    $skill['a.user_id']  = $item['user_id'];

                    $item['skill'] = Db::name('skill_apply')
                        ->alias('a')
                        ->join( [

                            ['skill s','s.skill_id = a.skill_id','left']

                        ])
                        ->where($skill)
                        ->field('a.apply_id,s.skill_name')
                        ->find();

                }

                $item['user_id'] = hashid($item['user_id']);

            });

        return ['thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$rows->items()];
    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 同城查询
     */
    public function getCity($map = [],$userExtra = [] ,$distance = 5,$max = 50)
    {

        $join = [
            ['user_extend e','e.user_id = a.user_id','left'],
            ['users u','u.user_id = a.user_id','left']
        ];

        $log = $userExtra['log'];
        $lat = $userExtra['lat'];

        $field = "e.place,(st_distance (point (e.log,e.lat),point($log,$lat) ) / 0.0111) AS distance,a.skill_id,a.mini_price,a.num,u.user_id,u.header_img,u.sex,u.nick_name,u.sign,e.online_time,e.online_status,e.room_id,e.noble,e.level";

        $rows =  $this->alias('a')->where($map)->join($join)
            ->field($field)
            ->order('distance')
            ->having("distance < $distance")
            ->select();

        if (count($rows) <= 15 && $distance < $max){

            $distance += 5;

            return $this->getCity($map,$userExtra,$distance,$max);

        }else{

            if ($distance >= $max){
                $hasNext = false;
            }else{
                $hasNext = true;
            }
            return ['data'=>$rows,'hasNext'=>$hasNext,'thisPage'=>$distance];

        }

    }





    
    



}
