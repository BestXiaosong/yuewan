<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/26
 * Time: 15:46
 */

namespace app\common\model;


use think\Db;
use think\Model;

class Role extends Model
{
    public function userRoles($where = [],$role_id)
    {
        $rows = $this->where($where)->field('header_img,status,role_id,role_name,fans_num,sign,birthday,sex')->select();
        if (empty($rows)) return false;
        foreach ($rows as $k => $v){
            $rows[$k]['sex'] = $v['sex'] == 1?'男':'女';
            if ($role_id == $v['role_id']){
                $rows[$k]['select'] = 1;
            }else{
                $rows[$k]['select'] = 0;
            }
             $rows[$k]['role_id'] = hashid($v['role_id']);
            if ($v['status'] == 1){
                $rows[$k]['msg'] = '正常';
            }elseif ($v['status'] == 2){
                $rows[$k]['msg'] = '拍卖中';
            }else{
                $rows[$k]['msg'] = '禁用';
            }
        }
        return $rows;
    }

    public function roles($name)
    {
        $where['r.role_name'] = $name;
        $where['r.status'] = array('neq',0);
//      $where['s.sale_status'] = array('neq',2);
        $results = db('role')->alias('r')->where($where)->find();
        $where['s.type'] = 0;
        $where['r.role_name'] = array('like','%'.$name.'%');
        $join = [
            ['cl_sale_success s','s.g_id = r.role_id','left'],
        ];
        $file = 's.sale_id,s.money,r.role_name,s.update_time,r.status';
        $result = db('role')->alias('r')->join($join)->where($where)->order('s.create_time desc')->field($file)->select();

        if($result){
            foreach ($result as $key => $value) {
                if($value['status'] == 1){
                    $result[$key]['status_cn'] = '已占用';
                }elseif($value['status'] == 2){
                    $result[$key]['status_cn'] = '拍卖中';
                }
                if($value['money'] == null){
                    $result[$key]['money'] = "0";
                }
                if(empty($value['update_time'])){
                    $result[$key]['update_time'] = time();
                }

                $result[$key]['update_time'] = date("Y-m-d H:i:s", $result[$key]['end_time']);
                $result[$key]['sale_id'] = hashid($value['sale_id']);
            }

            
        }

        if(!$results)
        {
            $money = db('extend')->where(['id'=>1])->value('sale_role');
            $data = array(array('role_name'=>$name,'status'=>0,'status_cn'=>'无人竞拍','update_time'=>date('Y-m-d H:i:s',time()+24*60*60),'money'=>$money));
            if($result){
                $result =  array_merge($data,$result);
            }else{
                $result = $data;
            }
        }
        return $result;
    }



    public function getList($where = [])
    {
        return $this->alias('a')
            ->join('users u','u.user_id = a.user_id','LEFT')
            ->where($where)
            ->order('a.role_id')
            ->field('a.*,u.phone')
            ->paginate(15,false,['query'=>request()->param()]);
    }

    public function getOne($where = [],$role_id = 0)
    {
        $data = $this
            ->alias('a')
            ->where($where)
            ->join('users u','u.user_id = a.user_id','left')
            ->cache(60)
            ->field('a.role_id,a.role_name,a.header_img,a.place,a.official,a.sign,a.birthday,a.first_time,a.sex,a.fans_num,a.create_time,u.check')
            ->find();
        if (empty($data)) return false;
        $data['fans_num'] = num($data['fans_num']);
        $map['role_id'] = $data['role_id'];
        $map['follow_role_id'] = $role_id;
        $status = db('role_follow')->where($map)->value('status');
        if ($status){
            $data['is_follow'] = 1;
        }else{
            $data['is_follow'] = 0;
        }
        $room['role_id'] = $role_id;
        $room['status']  = ['between','1,2'];
        $data['follow_room'] = num(Db::name('room_follow')->where($room)->cache(60)->count('follow_id'));
        $role['follow_role_id'] = $role_id;
        $role['status'] = 1;
        $data['follow_role'] = num(Db::name('role_follow')->where($role)->cache(60)->count('follow_id'));
        $data['role_id'] = hashid($data['role_id']);
        return $data;
    }

}