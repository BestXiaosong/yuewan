<?php
namespace app\common\model;




class ChatUsers extends Base
{

    public function getRows($map = [])
    {

        $rows =  $this->alias('a')
            ->join('chat_group c','c.group_id = a.group_id','LEFT')
            ->where($map)
            ->field("a.group_id,c.group_name,c.img,a.chat_id")
            //可根据group筛选唯一值
//            ->group('a.group_id')
            ->cache(5)
            ->paginate()->each(function ($item){

                $item['img'] = explode(',',$item['img']);

            });
        $items = $rows->items();
        return ['thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$items];

    }

    public function items($map = [])
    {

        return $this->alias('a')
            ->join('chat_group c','c.group_id = a.group_id','LEFT')
            ->where($map)
            ->field("a.group_id,c.group_name,c.img,a.chat_id,c.num")
            //可根据group筛选唯一值
//            ->group('a.group_id')
            ->cache(5)
            ->select();
    }



    //前端群管理页面获取群成员
    public function getList($map = [],$user_id = 0)
    {
        $rows =  $this->alias('a')
            ->join('users u','u.user_id = a.user_id','LEFT')
            ->where($map)
            ->field("a.chat_id,u.nick_name,u.header_img,a.user_id,a.type")
            ->cache(3)
            ->select();
        foreach ($rows as $k => $v){
            //如果当前循环的角色id和当前操作者角色id一致   清除当前循环数据
            if ($v['user_id'] == $user_id){
                unset($rows[$k]);
            }else{
                $rows[$k]['user_id'] = hashid($v['user_id']);
            }
        }
        return array_values($rows);
    }



    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 获取单独一个详情
     */
    public function getDetail($map = [])
    {
        return  $this->alias('a')
            ->join('chat_group c','c.group_id = a.group_id','LEFT')
            ->where($map)
            ->field("a.group_id,c.group_name,c.img,a.chat_id,c.group_user,c.type")
            ->cache(5)
            ->find();
    }




}