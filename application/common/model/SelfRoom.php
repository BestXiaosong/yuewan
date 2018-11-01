<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6 0006
 * Time: 10:31
 */

namespace app\common\model;


use think\Model;

class SelfRoom extends  Base
{
         public function getRoom($user_id){
             $result=db('room r')->where(['r.user_id'=>$user_id])
                     ->join('role rl','r.role_id=rl.role_id','LEFT')
                     ->order('r.create_time desc')
                     ->field('r.status,r.room_name,r.room_id,r.img,r.fans,rl.header_img')
                     ->cache(120)
                     ->paginate();
             $items = $result->items();
             foreach ($items as $k=>$v){
                 if ($v['status'] == 1){
                     $items[$k]['remark'] = '正常';
                 }elseif ($v['status'] == 2){
                     $items[$k]['remark'] = '拍卖中';
                 }else{
                     $items[$k]['remark'] = '封禁';
                 }
                 $items[$k]['hotval']=hotValue($v['room_id']);
                 $items[$k]['room_id']=hashid($v['room_id']);
             }
             if(empty($items)) return false;
             return ['thisPage'=>$result->currentPage(),'hasNext'=>$result->hasMore(),'data'=>$items];
         }


         public function  glRoom($role_id){
              $result=db('room_follow')->alias('rf')->where(['rf.role_id'=>$role_id,'rf.status'=>2])
                   ->join('room r','rf.room_id=r.room_id','LEFT')
                   ->join('role rl','rl.role_id=rf.role_id','LEFT')
                   ->field('r.room_name,r.img,r.room_id,rl.header_img')
                   ->order('rf.create_time desc')
                   ->cache(60)
                   ->paginate();

             $items = $result->items();
             foreach ($items as $k=>$v){
                 $items[$k]['hotval']=hotValue($v['room_id']);
                 $items[$k]['room_id']=hashid($v['room_id']);
             }
             if(empty($items)) return false;
             return ['thisPage'=>$result->currentPage(),'hasNext'=>$result->hasMore(),'data'=>$items];

         }

       //个人中心视频回放
         public function vod($user_id,$role_id){
              $result = db('vod')->alias('v')->where(['v.user_id'=>$user_id,'v.status'=>1])
                        ->join('users u','u.user_id=v.user_id','LEFT')
                        ->join('role r','r.role_id=u.role_id','LEFT')
                        ->order('v.create_time desc')
                        ->field('v.pid,v.title,v.img,v.play_url,v.create_time,r.role_name')
                        ->paginate();
             $items = $result->items();

             if(empty($items)) return false;
             return ['thisPage'=>$result->currentPage(),'hasNext'=>$result->hasMore(),'data'=>$items];
         }
         //查询个人存放内容空间
        public function selfPlace($user_id){
             $row=db('users')->where('user_id',$user_id)->field('bucket_space,use_space')->find();
             $row['bucket_space']=$row['bucket_space'].'GB';
             $row['use_space']=$row['use_space'].'GB';
             return array($row);
         }
         /*更新个人内存占用空间
          *
          */
         public function saveSpace($user_id,$delspace){
                 $totalSpace=db('users')->where('user_id',$user_id)->field('bucket_space,use_space')->find();
                 $data['use_space']=$totalSpace['use_space']-$delspace;

                return db('users')->where('user_id',$user_id)->update($data);
         }



}