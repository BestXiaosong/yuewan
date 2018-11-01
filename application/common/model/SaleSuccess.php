<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/30 0030
 * Time: 16:41
 */
namespace app\common\model;




class SaleSuccess extends Base
{
    public function getList($where = []){
        $wheres = array();
        $wheres['s.status'] = $where['s.status'];
        if($where['s.type']  == 0){
            $wheres['s.type'] = 0;
            if(isset($where['s.title'])){
                $wheres['r.role_name'] = ['like','%'.$where['s.title'].'%'];
            }
            $join = [
                ['cl_role r','s.g_id = r.role_id','left'],
                ['cl_users u','s.user_id = u.user_id','left']
            ];
            $file = 's.*,r.role_name as name,u.phone';
        }elseif($where['s.type'] == 1){
            $wheres['s.type'] =  1;
            if(isset($where['s.title'])){
                $wheres['rm.room_name'] = ['like','%'.$where['s.title'].'%'];
            }
            $join = [
                ['cl_room rm','s.g_id = rm.room_id','left'],
                ['cl_users u','s.user_id = u.user_id','left']
            ];
            $file = 's.*,rm.room_name as rname,u.phone';
        }else{
            if(isset($where['s.title'])){
                $wheres['r.role_name|rm.room_name'] = ['like','%'.$where['s.title'].'%'];
            }
            $join = [
                ['cl_role r','s.g_id = r.role_id','left'],
                ['cl_room rm','s.g_id = rm.room_id','left'],
                ['cl_users u','s.user_id = u.user_id','left']
            ];
            $file = 's.*,r.role_name as name,rm.room_name as rname,u.phone';
        }
//        return $wheres;exit;

         return $this->alias('s')
            ->join($join)
            ->where($wheres)
            ->order('s.create_time desc')
            ->field($file)
            ->paginate(15,false,['query'=>request()->param()]);

//        return $this->getLastSql();
    }
    public function getSale($type){
        if($type == 0){
            $result =  db('role')->where(['user_id'=>0])->order('create_time desc')->select();
            return $result;
        }else{
            $result = db('room')->where(['user_id'=>0])->order('create_time desc')->select();
            return $result;
        }

    }
    public function getGoods($id){
        $result = $this->where(['sale_id'=>$id])->find();
        if($result['type'] == 0){
            $results =  db('role')->where(['user_id'=>0])->order('create_time desc')->select();
            return $results;
        }else{
            $results = db('room')->where(['user_id'=>0])->order('create_time desc')->select();
            return $results;
        }
    }


    //获取拍卖信息
    public function getRows($type,$name='')
    {
        $where['s.type'] = $type;
        $where['s.sale_status'] = 0;
        $where['s.status'] = 0;
        if($type == 0){
            if($name){
                $where['r.role_name'] = array('like','%'.$name.'%');
            }
            $join = [
                ['cl_role r','s.g_id = r.role_id','left'],
            ];
            $file = 's.sale_id,s.money,r.role_name,s.end_time as update_time';
        }else{
            if($name){
                $where['rm.room_name'] = array('like','%'.$name.'%');
            }
            $join = [
                ['cl_room rm','s.g_id = rm.room_id','left'],
            ];
            $file = 's.sale_id,s.money ,rm.room_name,s.end_time as update_time';
        }

        $rows = $this->alias('s')->join($join)->where($where)->order('s.create_time desc')->field($file)->paginate();
        $items = $rows->items();
        if (empty($items)) return false;
        foreach ($items as $k=>$v){
            $items[$k]['sale_id'] = hashid($v['sale_id']);
        }
        return ['thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$items];
    }
    //查询该id所参与的竞拍
    public function join($id){
        $data['h.user_id'] = $id;
        $join = [
            ['cl_sale_success s','h.sale_id = s.sale_id','left'],
            ['cl_role r','s.g_id = r.role_id','left'],
            ['cl_room rm','s.g_id = rm.room_id','left']
        ];

        $files = 'h.*,s.money,s.type,r.role_name,rm.room_name,s.update_time';
        $result = db('sale_history')->alias('h')->field($files)->where($data)->join($join)->paginate();

        $items = $result->items();
        if (empty($items)) return false;
        foreach ($items as $k=>$v){
            if($v['type'] == 1){
                $results[$k]['name'] = $v['room_name'];
                $results[$k]['type'] = 1;
            }else{
                $results[$k]['name'] = $v['role_name'];
                $results[$k]['type'] = 0;
            }
            if($v['status'] == 0){
                $results[$k]['status'] = 0;
                $results[$k]['status_cn'] = '竞拍中';
            }else{
                $results[$k]['status'] = 1;
                $results[$k]['status_cn'] = '已结束';
            }
                $results[$k]['money'] = $v['money'];
                $results[$k]['time'] = $v['update_time'];
        }
        return ['thisPage'=>$result->currentPage(),'hasNext'=>$result->hasMore(),'data'=>$results];
    }
    //查询该id发起的竞拍
    public function initiate($id){
        $where['sale_user_id'] = $id;
        $join = [
            ['cl_role r','s.g_id = r.role_id','left'],
            ['cl_room rm','s.g_id = rm.room_id','left']
        ];
        $file = 's.*,r.role_name,rm.room_name';
        $result = $this->alias('s')->field($file)->join($join)->where($where)->paginate();
        $items = $result->items();
        if(empty($items)) return false;
        foreach ($items as $k=>$v){
            if($v['type'] == 1){
                $results[$k]['name'] = $v['room_name'];
                $results[$k]['type'] = 1;
            }else{
                $results[$k]['name'] = $v['role_name'];
                $results[$k]['type'] = 0;
            }
            if($v['sale_status'] == 0){
                $results[$k]['status'] = 0;
                $results[$k]['status_cn'] = '竞拍中';
            }else{
                $results[$k]['status'] = 1;
                $results[$k]['status_cn'] = '已结束';
            }
            $results[$k]['money'] = $v['money'];
        }
        return ['thisPage'=>$result->currentPage(),'hasNext'=>$result->hasMore(),'data'=>$results];
    }
}