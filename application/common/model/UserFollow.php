<?php
namespace app\common\model;




use think\Model;

class UserFollow extends Model
{

    public function getRows($map = [])
    {

        $rows =  $this->alias('a')
            ->join('user_extend e','e.user_id = a.user_id','LEFT')
            ->join('users u','u.user_id = a.user_id','LEFT')
            ->where($map)
            ->field('u.user_id,u.nick_name,u.header_img,u.tag,e.room_id,e.online_status,e.online_time')
            ->paginate()->each(function ($item){

                if ($item['online_status']){

                    $item['status'] = '当前在线';

                }else{

                    $item['status'] = formatTime($item['online_time']);

                }

                $item['user_id'] = hashid($item['user_id']);

            });

        return ['thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$rows->items()];

    }
    


}