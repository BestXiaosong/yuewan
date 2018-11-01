<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/1 0001
 * Time: 10:40
 * 竞猜接口控制器
 *
 */

namespace app\api\controller;
use app\common\logic\GuessRecord;
use think\Db;
use app\common\model\Guess as Guesss;
use app\common\model\Gift as Gifts;
use think\Exception;

class Guess extends User
{

        //直播房间发起竞猜接口
        public function addGuess(){
          $data=request()->only(['room_id','title','answer_A','answer_B'],'post');
          $data['room_id']=dehashid($data['room_id']);
          if(!is_numeric($data['room_id']))api_return(0,'参数错误');
          $data['user_id']=$this->user_id;
          $model=new \app\common\model\Guess();

           $guess_id=$model->addGuess($data);
                $row=[];
                $row['guess_id']=$guess_id;
                $row['title']=$data['title'];
                $row['answer_A']=$data['answer_A'];
                $row['answer_B']=$data['answer_B'];
              $this->sendMsg('play_'.hashid($data['room_id']),3,$row);
          $guess_id?api_return(1,'发起成功'):api_return(0,'发起失败');

        }

        //竞猜下注接口
         public function addRecord(){

            /**
            * 参数验证
            */
            $data = request()->only(['guess_id','answer','money'],'post');

             /**
              * 访问控制
              */
             $cache = cache('guess_'.$data['guess_id']);
             if ($cache){
                 api_return(0,'服务器繁忙,请稍后重试');
             }else{
                 cache('guess_'.$data['guess_id'],1,0.1);
             }

            if ($data['answer'] != 'A' && $data['answer'] != 'B') api_return(0,'答案参数错误');
            $data['guess_id']=dehashid($data['guess_id']);
            if(!is_numeric($data['guess_id']))api_return(0,'参数错误');
            $userInfo = Db::name('users')->where('user_id',$this->user_id)->field('money')->find();
            if ($userInfo['money'] < $data['money']) api_return(0,'积分余额不足');

            $guess = Db::name('guess')->where('guess_id',$data['guess_id'])->field('status,amoney,bmoney')->find();
            if ($guess['status'] != 1) api_return(0,'当前竞猜已封盘或结束,不能下注');
            if ($data['answer'] == 'A'){
                $money = $guess['amoney']+$data['money'];
                $total = $guess['bmoney'];
            }else{
                $money = $guess['bmoney']+$data['money'];
                $total = $guess['amoney'];
            }
            if ($money > 9999999999){
                api_return(0,'投注总额超过限制,禁止投注');
            }
            $guess_ratio = Db::name('extend')->where('id',1)->cache(60)->value('guess_ratio');
            $ratio = 100 - $guess_ratio;
             /**
              * 下注时若下注的另一方有人投注，且另一方扣除手续费后每一注返点低于0.01时不能下注
              */
            if ($money > $total * $ratio && $total != 0){
                api_return(0,'投注总额超过限制,禁止投注');
            }

            $data['user_id'] = $this->user_id;
            $data['role_id'] = $this->role_id;
            $map['guess_id'] = $data['guess_id'];
            $map['role_id']  = $this->role_id;
            $has = Db::name('guess_record')->where($map)->order('record_id','desc')->field('money,answer,record_id')->find();
            if ($has){
                $max = Db::name('extend')->where('id',1)->cache(60)->value('guess_max');

                if ($has['answer'] != $data['answer']){
                    api_return(0,'追投注只能投注同一边');
                }else{
                    $data['id'] = $has['record_id'];

                }
            }
             /**
              * 逻辑处理
              */
            $model=new Guesss();
            $giftModel=new Gifts();
            //开启事物
             Db::startTrans();
             try{
                 $a = $giftModel->endMoney($this->user_id,$data['money']);
                 if($a == false)api_return(0,'当前余额不足');
                 $b = $model->addGuessRecord($data);

                 if ($data['answer'] == 'A'){
                     Db::name('guess')->where('guess_id',$data['guess_id'])->setInc('bmoney',$data['money']);
                 }else{
                     Db::name('guess')->where('guess_id',$data['guess_id'])->setInc('amoney',$data['money']);

                 }

                 if (!$data['id']){
                     if ($data['answer'] == 'A'){
                         Db::name('guess')->where('guess_id',$data['guess_id'])->setInc('acount');
                     }else{
                         Db::name('guess')->where('guess_id',$data['guess_id'])->setInc('bcount');
                     }
                 }
                 // 提交事务
                 if($a&&$b){
                     Db::commit();
                     api_return(1,'下注成功');
                 }else{
                     // 回滚事务
                     Db::rollback();
                     api_return(0,'下注失败');
                 }
             }catch (Exception $e){
                 Db::rollback();
                 api_return(0,'下注失败');
             }

         }

         /*
          * 竞猜赔率
          */


         //直播间竞猜列表
         public function getGuess(){
            $roomId = dehashid(trim(input('room_id')));
            if(!is_numeric($roomId))api_return(0,'参数错误');
            $model = new Guesss();
            $where['room_id'] = $roomId;
            $where['status']  = ['between','1,2'];
            $listGuess=$model->getGuess($where);
            if ($listGuess !== false){
              api_return(1,'获取成功',$listGuess);
            }else{
              api_return(0,'暂无数据');
            }
         }
         //竞猜详情
         public function  detailsGuess(){
             $guessId=dehashid(trim(input('guess_id')));
             if(!is_numeric($guessId))api_return(0,'参数错误');
             $model=new Guesss();
             $details=$model->detailsGuess($guessId);
             $details?api_return(1,'获取成功',$details):api_return(0,'获取失败');
         }
         /*
          * 我参与的竞猜
          */
        public function selfJoinGuess(){
             $user_id=$this->user_id;
             $model=new Guesss();
             $joinList=$model->selfJoinGuess($user_id);
             $joinList?api_return(1,'获取成功',$joinList):api_return(0,'暂无数据');
        }

        //我发起的竞猜
        public function  selfSendGuess(){

               $user_id=$this->user_id;
               $model=new Guesss();
               if (input('post.status') == 1){
                   $status = 1;
               }else{
                   $status = 0;
               }

               $sendGuessList=$model->selfSendGuess($user_id,$status);

               $sendGuessList?api_return(1,'获取成功',$sendGuessList):api_return(0,'暂无数据');

        }
        //公布答案竞猜接口
       public function resultGuess(){
            $data['guess_id'] = dehashid(trim(input('guess_id')));
            $data['right']    = trim(input('right'));
            if ($data['right'] != 'A' && $data['right'] != 'B') api_return(0,'正确答案只能是A或B');
            if(!is_numeric($data['guess_id']))api_return(0,'参数错误');
            $model = new Guesss();
           Db::startTrans();
           try{
               $odds = $model->guessOdds($data['guess_id']);
               $data['odds_A'] = $odds['odds_A'];
               $data['odds_B'] = $odds['odds_B'];
               $data['update_time'] = time();
               $data['status'] = 3;
               $row = $model->where('guess_id',$data['guess_id'])->update($data);

               if ($row){
                   //资金流水写入
                   if($data['right'] == 'A'){ //A赢抽成
                       stream($odds['A_ratio'],1,"竞猜平台抽成".$odds['ratio'].'('.$odds['A_ratio'].')积分');
                   }else{ //B赢系统抽成
                       stream($odds['B_ratio'],1,"竞猜平台抽成".$odds['ratio'].'('.$odds['B_ratio'].')积分');
                   }

                   $map['guess_id'] = $data['guess_id'];
                   $map['status']   = 0;
                   Db::name('guess_record')
                       ->field('user_id,answer,money,record_id')
                       ->where($map)
                       ->chunk(1000, function($items)use($data) {
                           foreach ($items as $k => $v) {
                               if ($v['answer'] == $data['right']){ //猜中
                                    $money_num = bcmul($data['odds_'.$data['right']],$v['money'],2);//赢得积分
                                    $money[$k]['user_id']     = $v['user_id'];
                                    $money[$k]['remark']      = '竞猜获胜,赢得'.$money_num.'积分';
                                    $money[$k]['money_type']  = 2;
                                    $money[$k]['type']        = 1;
                                    $money[$k]['money']       = $money_num;
                                    $money[$k]['create_time'] = time();
                                    $money[$k]['status']      = 1;
                                    $money[$k]['coin_type']   = 1;
                                    $money[$k]['order_num']   = 'RE'.hashid($v['user_id']).date("Ymd").rand(1000,9999);
                                    $guess[$k]['record_id']   = $v['record_id'];
                                    $guess[$k]['status']      = 1;
                                    $guess[$k]['s_money']     = $money_num;
                                    Db::name('users')->where('user_id',$v['user_id'])->setInc('money',$money_num);

                                   $j_push_id = db('users')->where('user_id',$v['user_id'])->value('j_push_id');
                                   Push(0,$j_push_id,$money[$k]['remark']);


                               }else{//未猜中
                                   $guess[$k]['record_id']   = $v['record_id'];
                                   $guess[$k]['status']      = 2;
                                   $guess[$k]['s_money']     = -$v['money'];
                               }
                           }
                           if (!empty($money)){
                               //资金明细写入
                                Db::name('money_detail')->insertAll($money);
                           }
                           if (!empty($guess)){
                               //结算信息写入
                               $model = new GuessRecord();
                               $model->saveAll($guess);
                           }
                   });
               }
               Db::commit();
           } catch (\Exception $e) {
               Db::rollback();
               api_return(0,'结算失败');
           }
           api_return(1,'结算成功');

       }
       /*
        * 直播间封盘接口
        */
       public function endGuess(){
           $guess_id=dehashid(input('guess_id'));
           if(!is_numeric($guess_id))api_return(0,'参数错误');
           $model=new \app\common\model\Guess();
           $row=$model->endGuess($guess_id);
           if($row!==false)api_return(1,'封盘成功');
           api_return(0,'封盘失败');
       }

}