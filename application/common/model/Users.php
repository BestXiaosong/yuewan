<?php
namespace app\common\model;

use think\Db;
use think\Model;


class Users extends Model
{
    protected $dateFormat = 'Y-m-d H:i';

    public function profile()
    {
        return $this->hasOne('users', 'user_id', 'proxy_id')->field('nick_name');
    }

    public function getDetail($where = [])
    {
        return $this->alias('a')
            ->where($where)
            ->field('a.header_img,a.nick_name,a.sex,a.uuid,a.grade')
            ->find();
    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 编辑个人资料页面返显
     */
    public function getInfo($user_id = 0)
    {
        $map['user_id'] = $user_id;
        $data = $this->where($map)
            ->field('header_img,nick_name,sex,tag,sign,birthday,job')
            ->find();
        $map['type']   = 1;
        $map['status'] = 1;
        $data['imgs'] = Db::name('user_img')->where($map)->field('img_id,img')->select();
        $data['img_max'] = Db::name('extend')->where('id',1)->cache(60)->value('img_max');
        return $data;
    }




    /**
     * 后台获取用户列表
     */
    public function userList($where = [])
    {
        return $this
            ->where($where)
            ->order('user_id desc')
            ->paginate('', false, ['query' => request()->param()]);
    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 家族申请列表获取用户  房间管理获取用户
     */
    public function getItems($map = [])
    {
        $join = [
            ['user_extend e','e.user_id = a.user_id','left']
        ];

        $field = 'a.user_id,a.header_img,a.sex,a.nick_name,a.tag,e.noble_id,e.noble_time,e.level';

        $rows =  $this->alias('a')
            ->where($map)
            ->join($join)
            ->field($field)
            ->order('e.online_status desc')
            ->cache(15)
            ->paginate()->each(function ($item){
                $item['noble_id'] = \app\api\controller\Base::checkNoble($item);
                $item['user_id'] = hashid($item['user_id']);

            });
        return ['thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$rows->items()];
    }

    public function getNoble($map = [])
    {
        $join = [
            ['user_extend e','e.user_id = a.user_id','left']
        ];

        $field = 'a.user_id,a.header_img,a.nick_name,a.sign,e.noble_id,e.noble_time,e.online_time,e.online_status';

        $rows =  $this->alias('a')
            ->where($map)
            ->join($join)
            ->field($field)
            ->order(['e.noble_id'=>'desc','noble_time'=>'desc'])
            ->cache(15)
            ->paginate()->each(function ($item){
                $item['noble_id'] = \app\api\controller\Base::checkNoble($item);

                if ($item['online_status']){
                    $item['status'] = '当前在线';
                }else{
                    $item['status'] = formatTime($item['online_time']);
                }

                $item['user_id'] = hashid($item['user_id']);
            });
        return ['thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$rows->items()];
    }



    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 广场获取
     */
    public function rows($map = [])
    {
        $join = [
            ['user_extend e','e.user_id = a.user_id','left']
        ];

        $field = 'a.user_id,a.header_img,a.sex,a.nick_name,a.sign,e.online_time,e.online_status,e.room_id,e.noble_id,e.noble_time,e.level';

        $rows =  $this->alias('a')->where($map)->join($join)
            ->field($field)
            ->order('e.online_status desc')
            ->cache(15)
            ->paginate()->each(function ($item){

                if ($item['online_status']){

                    $item['status'] = '当前在线';

                }else{

                    $item['status'] = formatTime($item['online_time']);

                }
                $item['noble_id'] = \app\api\controller\Base::checkNoble($item);


                if ($item['room_id']){

                    $item['skill']['apply_id']   = 0;
                    $item['skill']['skill_name'] = '';

                }else{
                    //用户不在房间中   查询是否有技能
                    $skill['a.user_id'] = $item['user_id'];
//                    $skill['a.status']  = 1;
                    $skill['a.is_use']  = 1;

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
     */
    public function search($map = [],$user_id = 0,$distance = false,$userExtra = [])
    {

        if ($distance){

            $max  = Db::name('extend')->where('id',1)->cache(60)->value('distance');

            $join = [
                ['user_extend e','e.user_id = a.user_id','left'],
            ];

            $log = $userExtra['log'];
            $lat = $userExtra['lat'];

            $field = "e.place,(st_distance (point (e.log,e.lat),point($log,$lat) ) / 0.0111) AS distance,a.user_id,a.uuid,a.nick_name,a.header_img";
            $items =  $this->alias('a')->where($map)->join($join)
                ->field($field)
                ->order('distance')
                ->having("distance < $max")
                ->cache(15)
                ->select();
            $thisPage = 1;
            $hasNext  = false;

        }else{

            $rows = $this->alias('a')->where($map)->field('a.user_id,a.uuid,a.nick_name,a.header_img')->paginate();
            $thisPage = $rows->currentPage();
            $hasNext = $rows->hasMore();
            $items = $rows->items();

        }

        $where['user_id'] = $user_id;
        $where['status']  = 1;

        foreach ($items as $k => $v){

            $where['follow_user']   = $v['user_id'];
            $items[$k]['is_follow'] = Db::name('user_follow')->where($where)->value('follow_id')?1:0;
            $items[$k]['user_id']   = hashid($v['user_id']);

        }

        return ['thisPage'=>$thisPage,'hasNext'=>$hasNext,'data'=>$items];
    }
    
    
    
    

}