<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/27 0027
 * Time: 17:10
 */

namespace app\common\model;
use think\Model;


class Gift extends Model
{





    public function giftList($where = [])
    {
        return $this->where($where)->field('gift_id,gift_name,thumbnail,img,price')->order('price')->cache(30)->select();
    }



    //后台获取礼物列表
    public function giftListAdmin($where = [])
    {
        return $this
            ->where($where)
            ->order('create_time desc')
            ->paginate('',false,['query'=>request()->param()]);

    }


    public function saveChange($data){
        if(is_numeric($data['id'])){
            return $this->allowField(true)->isUpdate(true)->save($data,['gift_id'=>$data['id']]);
        }else{
            return $this->allowField(true)->save($data);
        }
    }

    //获取我接受的礼物
    public function getGift($userId,$time){
        if($time){
            $where['g.create_time']=['like','%'.$time.'%'];
        }
            $where['g.to_user']=$userId;
        $result= db('gift_record')
            ->alias('g')
            ->join('role r','r.user_id=g.to_user','LEFT')
            ->join('gift gt','gt.gift_id=g.gift_id','LEFT')
            ->field('r.role_name,r.header_img,gt.gift_name,g.num,g.create_time')
            ->where($where)
            ->cache(60)
            ->order('g.create_time desc')
            ->paginate('',false,['query'=>request()->param()]);

        $items=$result->items();
        if(empty($items)) return false;
        return ['thisPage'=>$result->currentPage(),'hasNext'=>$result->hasMore(),'data'=>$items];
    }
    //获取我送出的礼物
    public function sendGift($userId,$time){
        if($time){
            $where['g.create_time']=['like','%'.$time.'%'];
        }
        $where['g.user_id']=$userId;
        $result= db('gift_record')
            ->alias('g')
            ->join('role r','r.user_id=g.to_user','LEFT')
            ->join('gift gt','gt.gift_id=g.gift_id','LEFT')
            ->field('r.role_name,r.header_img,gt.gift_name,g.num,g.create_time')
            ->where($where)
            ->cache(60)
            ->order('g.create_time desc')
            ->paginate('',false,['query'=>request()->param()]);

        $items=$result->items();
        if(empty($items)) return false;
        return ['thisPage'=>$result->currentPage(),'hasNext'=>$result->hasMore(),'data'=>$items];
    }
    //统计我收到的礼物总数
     public function giftCount($where){
            return db('gift_record')->where($where)->count();
     }
     //赠送礼物
     public function  giveGift($data){
            $data['create_time']=time();
            $data['type']=1;
            return db('gift_record')->strict(false)->insert($data);
     }
     //扣除积分
      public function  endMoney($userId,$money){
            $total=db('users')->where('user_id',$userId)->value('money');
            if ($total < $money) api_return(0,'余额不足');
           return db('users')->where('user_id',$userId)->setDec('money',$money);
}

       public function sendReport($data){
               $data['imgs']=str_replace(',','[]',$data['imgs']);
               return db('reason')->insert($data);
       }

       /*
        * 后台举报列表
        */
       public function adminReportList($map){

              $row= db('reason')->alias('r')
                ->where($map)
                ->field('r.type,r.report_id,r.reason,r.status,r.create_time,r.update_time,r.role_id,r.id')
                ->order('r.create_time desc')
                ->paginate('',false,['query'=>request()->param()]);
              $items=$row->items();
           if (empty($items)) return false;

           foreach ($items as $k=>$v){
               $items[$k]['role_name']=db('role')->where(['role_id'=>$v['role_id']])->value('role_name');
               if($v['type']==1){

                   $items[$k]['name']=db('room')->where(['room_id'=>$v['id']])->value('room_name');
               }else if($v['type']==2){

                   $items[$k]['name']=db('role')->where(['role_id'=>$v['id']])->value('role_name');
               }
           }
           return $items;
       }


            /*
             * 后台举报详情
             */
            public function details($id){
                $row=db('reason')->where(['report_id'=>$id])->find();
                if($row['type']==1){
                    $row['name']=db('room')->where(['room_id'=>$row['id']])->value('room_name');
                }elseif ($row['type']==2){
                    $row['name']=db('role')->where(['role_id'=>$row['id']])->value('role_name');
                }
                $row['role_name']=db('role')->where('role_id',$row['role_id'])->value('role_name');
                $row['img']=explode('[]',$row['imgs']);
                return $row;
            }

            /*
             * 后台举报修改状态
             */
            public function  updateReason($report_id,$status){
                $row=db('reason')->where(['report_id'=>$report_id])->find();
                if($status==1){
                    if($row['type']==1){
                        db('room')->where('room_id',$row['id'])->update(['status'=>0]);
                    }elseif($row['type']==2){
                        db('role')->where(['role_id'=>$row['id']])->update(['status'=>0]);
                    }
                   return db('reason')->where(['report_id'=>$report_id])->update(['status'=>$status]);
                }else{
                   return db('reason')->where(['report_id'=>$report_id])->update(['status'=>$status]);
                }
            }

}