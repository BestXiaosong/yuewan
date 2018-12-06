<?php
namespace app\common\model;




use think\Model;

class UserGuard extends Model
{

    public function getRows($map = [])
    {
        $field = 'a.user_id,u.nick_name,u.header_img,a.guard_user,a.end_time';

        $rows =$this->alias('a')
            ->join([
                ['users u','u.user_id = a.user_id','left'],
                ['user_extend e','e.user_id = a.user_id','left'],
            ])
            ->where($map)
            ->order('a.type desc')
            ->field($field)
            ->cache(30)
            ->paginate()->each(function ($item){
                $item['end_time'] = secToDay($item['end_time'] - time());
                $item['guard_nick_name'] = \app\api\controller\Base::staticInfo('nick_name',$item['guard_user']);
                $item['guard_header_img'] = \app\api\controller\Base::staticInfo('header_img',$item['guard_user']);
                $item['guard_user'] = hashid($item['guard_user']);
                $item['user_id'] = hashid($item['user_id']);
            });
        return ['thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$rows->items()];
    }
    


}