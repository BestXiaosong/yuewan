<?php
/**
 * Created by xiaosong
 * E-mail:4155433@gmail.com
 * Date: 2018/12/5
 * Time: 16:53
 */

namespace app\common\model;


use think\Model;

class RoomBlacklist extends Model
{
    public function getRows($map = [])
    {
        $join = [
            ['user_extend e','e.user_id = a.user_id','left'],
            ['users u','u.user_id = a.user_id','left']
        ];

        $field = 'a.update_time,a.minute,u.birthday,u.user_id,u.header_img,u.sex,u.nick_name,u.tag,e.noble_id,e.noble_time,e.level';

        $rows =  $this->alias('a')
            ->where($map)
            ->join($join)
            ->field($field)
            ->cache(15)
            ->paginate()->each(function ($item){
                $item['noble_id'] = \app\api\controller\Base::checkNoble($item);
                $item['user_id']  = hashid($item['user_id']);
            });
        return ['thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$rows->items()];
    }



}