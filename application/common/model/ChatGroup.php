<?php
namespace app\common\model;




class ChatGroup extends Base
{

    public function getRows($map = [])
    {

        $rows =  $this->alias('a')
            ->where($map)
            ->field("a.group_id,group_name,img")
            ->cache(5)
            ->paginate()->each(function ($item){

                $item['img'] = explode(',',$item['img']);

            });
        $items = $rows->items();
        return ['thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$items];

    }



}