<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 20$this->user_id8/7/3$this->user_id 003$this->user_id
 * Time: $this->user_id6:43
 */

namespace app\api\controller;
use app\common\model\GiftRecord;
use think\Controller;
use think\Db;
use think\Cache;
use app\common\model\Gift as Gifts;
use think\Exception;

class Gift extends  User
{
        //直播间赠送礼物显示接口
        public function  giftList()
        {
              $model=new Gifts();
              $showList=$model->giftList(['status'=>1]);
              $showList?api_return(1,'获取成功',$showList):api_return(0,'获取失败');
        }
            //我收到的礼物接口
        public function  recordGift()
        {
             $time=trim(input('time'));   //时间筛选

             $userId=$this->user_id;
             $model=new Gifts();
             $where['to_user']=$userId;
             $recordList=$model->getGift($userId,$time);
             $recordList['count']=$model->giftCount(['to_user'=>$userId]);
             $recordList?api_return(1,'获取成功',$recordList):api_return(0,'获取失败');
        }
        //我的资产礼物
        public function  selfGift()
        {
               $userId=$this->user_id;
               $model=new Gifts();
               $data=[];
               $data['record_gift']=$model->giftCount(['to_user'=>$userId]);
               $data['send_gift']=$model->giftCount(['user_id'=>$userId]);
               $data?api_return(1,'请求成功',$data):api_return(0,'获取失败');
        }
        //我送出的礼物
        public function  sendGift()
        {
            $time=trim(input('time'));   //时间筛选
            $userId=$this->user_id;
            $model=new Gifts();

            $sendList=$model->sendGift($userId,$time);
            $sendList['count']=$model->giftCount(['user_id'=>$userId]);
            $sendList?api_return(1,'获取成功',$sendList):api_return(0,'获取失败');
        }

        /**
         * 我的资产 -> 礼物 统计列表
         */
        public function myList()
        {
            $where['a.to_user'] = $this->user_id;
            $map['a.user_id']   = $this->user_id;
            $model = new GiftRecord();
            $rows['get']  = $model->myList($where);
            $rows['send'] = $model->myList($map);
            if (empty($rows['get']) && empty($rows['send'])) api_return(0,'暂无数据');
            api_return(1,'获取成功',$rows);
        }


        /**
         * 我收到|发出的礼物详情
         */
        public function giftDetail()
        {
            $where['a.gift_id'] = input('post.id');
            if (!empty(input('post.time'))){
                $time = input('post.time');
                $info = date_parse_from_format('Y-m',$time);
                if ( 0 != $info['warning_count'] || 0 != $info['error_count']) api_return(0,'时间错误');
                $start = strtotime($time);
                $end   = strtotime($time.'-'.date('t', strtotime($start)).' 23:59:59');
                $where['a.create_time'] = ['between time',[$start,$end]];
            }
            $type = input('post.type');
            if (!is_numeric($where['a.gift_id'])) api_return(0,'参数错误');
            if ($type == 1){
                $where['a.to_user'] = $this->user_id;
            }elseif ($type == 2){
                $where['a.user_id'] = $this->user_id;
            }else{
                api_return(0,'参数错误');
            }
            $model = new GiftRecord();
            $rows  = $model->myDetail($where,$type);
            if ($rows !== false) api_return(1,'获取成功',$rows);
            api_return(0,'暂无数据');

        }






        //赠送礼物接口
        public function giveGift()
        {
            $cache = cache('giveGift_'.$this->user_id);
            if ($cache){
                api_return(0,'访问过于频繁,请稍后重试');
            }else{
                cache('giveGift_'.$this->user_id,1,1);
            }

             $data=request()->only(['room_id','gift_id','num'],'post');
             $data['room_id']=dehashid($data['room_id']);
            if(!is_numeric($data['room_id']))api_return(0,'参数错误');
            $data['role_id']=$this->role_id;
            $data['user_id']=$this->user_id;
            $data['to_user']=db('room')->where('room_id',$data['room_id'])->value('user_id');
            // 启动事务
            Db::startTrans();
            try{
                $model=new Gifts();
                $map['room_id'] = $data['room_id'];
                $map['status']  = 1;
                $activity_id = Db::name('room_activity')->where($map)->value('activity_id');
                if ($activity_id){
                    $data['activity_id'] = $activity_id;
                }
                $model->endMoney($this->user_id,getMoney($data['gift_id'],$data['num']));
                money($this->user_id,8,"-".getMoney($data['gift_id'],$data['num']));
                $model->giveGift($data);
                Db::commit();
            }catch (Exception $e){
                // 回滚事务
                Db::rollback();
                api_return(0,'赠送失败');
            }

            $row = [];
            $gift=db('gift')->where('gift_id',$data['gift_id'])->field('gift_name,img')->cache(3600)->find();
            $row['img']=$gift['img'];
            $row['gift_name']=$gift['gift_name'];
            $row['num']=$data['num'];
            $row['role_name']=db('role')->where('role_id',$this->role_id)->cache(3600)->value('role_name');
            $row['role_id']=hashid($this->role_id);
            $this->sendMsg('play_'.hashid($data['room_id']),1,$row);
            api_return(1,'赠送成功');


        }
            //礼物兑换接口
          public function changeMoney(){
                  $gift_id=intval(input('gift_id'));
                  $where['to_user']=$this->user_id;
                  $where['gift_id']=$gift_id;
                  $where['type']=1;
                  $model=new GiftRecord();
                  $row=$model->giftChange($where);

                  if ($row !== false) api_return(1,'获取成功',$row);
                  api_return(0,'暂无数据');
          }

          //礼物兑换完成接口
          public function  complateMoney(){
              $num=input('num');
              $money=input('money');

              $gift_id = intval(input('gift_id'));
              $where['to_user'] = $this->user_id;
              $where['gift_id'] = $gift_id;
              $where['type'] = 1;
              $model = new GiftRecord();
              $data  = $model->giftChange($where);

              $order = 'RE'.hashid($this->user_id).date("Ymd").rand(1000,9999);
              Db::startTrans();
              try{
                  $writeDetails = money($this->user_id,3,$money,1,'',$order);
                  $update = db('users')->where('user_id',$this->user_id)->setInc('money',$data['money']);
                  $updateStatus=db('gift_record')->where(['to_user'=>$this->user_id,'type'=>1,'gift_id'=>$gift_id])->update(['type'=>0]);
                  //写入资金流水
                  stream($data['rate_money'],1,'用户('.$this->phone.')礼物兑换扣除手续费'.$data['rate'].'('.$data['rate_money'].'积分)');

                  Db::commit();
              }catch (Exception $e){
                  Db::rollback();
                  api_return(0,'兑换失败');
              }
              $row = [];
              $row['num'] = $data['num'];
              $row['money'] = $data['money'].'积分';
              $row['order_num']=$order;
              $row['create_time']=date('Y-m-d i:s',time());
              api_return(1,'兑换成功',$row);
          }

          /*
           * 举报房间角色接口
           */
          public function report(){
              $data=request()->only(['reason','detail','imgs','type','id'],'post');
              if ($data['type'] != 1 && $data['type'] != 2) api_return(0,'举报类型错误');
              $data['id']=dehashid($data['id']);
              if (!is_numeric($data['id'])) api_return(0,'角色id错误');
              $data['user_id']=$this->user_id;
              $data['role_id']=$this->role_id;
              $data['create_time']=time();
              $data['status']=0;
              $model=new Gifts();
              $row=$model->sendReport($data);
              if($row!==false)api_return(1,'举报成功');
              api_return(0,'举报失败');
          }


}