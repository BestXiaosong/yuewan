<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/3 0003
 * Time: 10:04
 */

namespace app\common\model;


use think\Db;
use think\Model;

class FocusRoom  extends  Model
{
         /*
          * 关注的角色
          */
            public function getFocusRole($role_id){
                $result= db('role_follow rf')->where(['rf.follow_role_id'=>$role_id,'rf.status'=>1])
                  ->join('role r','rf.role_id=r.role_id','left')
                  ->field('rf.follow_id,r.role_id,r.role_name,r.header_img,r.sign,r.official,r.fans_num')
                  ->order('rf.create_time desc')
                  ->paginate();
                   $items = $result->items();
                   foreach ($items as $k=>$v){
                       $items[$k]['role_id']=hashid($v['role_id']);
                   }
                if (empty($items)) return false;
                return ['thisPage'=>$result->currentPage(),'hasNext'=>$result->hasMore(),'data'=>$items];
            }
            /*
        * 关注的房间
        */
            public function getFocusRoom($role_id){
                  $result=db('room_follow rf')->where(['rf.role_id'=>$role_id,'rf.status'=>1])
                         ->join('room r','rf.room_id=r.room_id')
                         ->join('role rl','r.role_id=rl.role_id')
                         ->field('r.room_name,r.img,r.official,r.play_status,rl.role_name,rf.room_id,rl.header_img')
                         ->order('rf.create_time desc')
                         ->paginate();
                  $items=$result->items();
                   foreach ($items as $k=>$v){
                       if ($v['play_status'] == 1){
                           $guess['status'] = ['between','1,2'];
                           $guess['room_id'] = $v['room_id'];
                           $num = Db::name('guess')->where($guess)->count('guess_id');
                           if ($num){
                               $items[$k]['remark'] = '竞猜';
                           }else{
                               $items[$k]['remark'] = '直播中';
                           }
                       }elseif ($v['play_status'] == 0){
                           $items[$k]['remark'] = '聊天开放中';
                       }elseif ($v['play_status'] == 2){
                           $items[$k]['remark'] = '直播预告';
                       }
                       $items[$k]['room_id'] = hashid($v['room_id']);
                       $items[$k]['hotval']  = hotValue($v['room_id']);
                   }
                if (empty($items)) return false;
                return ['thisPage'=>$result->currentPage(),'hasNext'=>$result->hasMore(),'data'=>$items];
            }

    /**
     * @param $role_id
     * @return array|bool
     * web 关注的房间
     */
    public function getFocusRoom1($role_id){
        $result=db('room_follow rf')->where(['rf.role_id'=>$role_id,'rf.status'=>1])
            ->join('room r','rf.room_id=r.room_id')
            ->join('role rl','r.role_id=rl.role_id')
            ->field('r.room_name,r.img,r.official,r.play_status,rl.role_name,rl.header_img,r.title,r.brief,rl.fans_num')
            ->order('rf.create_time desc')
            ->paginate();
        $items=$result->items();

        if (empty($items)) return false;
        return ['thisPage'=>$result->currentPage(),'hasNext'=>$result->hasMore(),'data'=>$items];
    }
            /*
             * 添加关注房间
             */
            public function addFocusRoom($data){
                   $result=db('room_follow')->where($data)->find();
                   if(!empty($result)){
                       if($result['status'] == 0){
                           Db::name('room')->where('room_id',$data['room_id'])->setInc('fans');
                           return Db::name('room_follow')->where('follow_id',$result['follow_id'])->update(['status'=>1,'update_time'=>time()]);
                       }else{
                           $this->error = '您已关注该房间,请勿重复操作';
                           return false;
                       }
                   }else{
                       $data['create_time']=time();
                       $data['status']=intval(1);
                       Db::name('room')->where('room_id',$data['room_id'])->setInc('fans');
                       return Db::name('room_follow')->strict(false)->insert($data);
                   }
            }
            /*
         * 添加关注角色
         */
            public function addFocusRole($data){
                $result=db('role_follow')->where($data)->find();
                if(!empty($result)){
                    if($result['status']!=1){
                        db('role')->where('role_id',$data['role_id'])->setInc('fans_num');
                        return db('role_follow')->where('follow_id',$result['follow_id'])->update(['status'=>1,'update_time'=>time()]);
                    }else{
                        api_return(0,'您已关注该用户');
                    }
                }else{

                     db('role')->where('role_id',$data['role_id'])->setInc('fans_num');
                    $data['create_time']=time();
                    $data['status']=intval(1);
                    return db('role_follow')->strict(false)->insert($data);
                }

            }
            /*
             * 取消房间关注
             */
            public function  hiddenRoom($room_id,$role_id){
                   $fans=db('room')->where('room_id',$room_id)->value('fans');
                   if($fans>0){
                       $a=db('room')->where('room_id',$room_id)->setDec('fans');
                   }

                return db('room_follow')->where(['role_id'=>$role_id,'room_id'=>$room_id])->update(['status'=>0,'update_time'=>time()]);

            }
            /*
         * 取消角色关注
         */
            public function  hiddenRole($role_id,$self){
                $fans=db('role')->where('role_id',$role_id)->value('fans_num');
                    if($fans>0){
                        $a=db('role')->where('role_id',$role_id)->setDec('fans_num');
                    }
                return db('role_follow')->where(['role_id'=>$role_id,'follow_role_id'=>$self])->update(['status'=>0,'update_time'=>time()]);

            }


}