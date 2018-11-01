<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/1 0001
 * Time: 11:33
 */
namespace app\common\model;
use think\Model;
use think\Db;
class Guess extends Model
{
      //发起竞猜
      public function  addGuess($data){
             $data['create_time']=time();
              $this->allowField(true)->save($data);
             return $this->getLastInsID();
      }

      //竞猜下注
      public function  addGuessRecord($data){
          $data['create_time']=time();
          if (is_numeric($data['id'])){
              return db('guess_record')->where('record_id',$data['id'])->setInc('money',$data['money']);
          }
          return db('guess_record')->strict(false)->insert($data);
      }
      //直播间竞猜列表接口
      public function  getGuess($where = []){
          $rows =  $this->where($where)->order('create_time desc')->limit(3)->select();
          if (empty($rows)) return false;
          foreach ($rows as $k => $v){
              $map['guess_id']   = $v['guess_id'];
              $map['answer']     = 'A';
              $rows[$k]['a_num']   = Db::name('guess_record')->where($map)->cache(5)->count('record_id');
              $rows[$k]['a_money'] = Db::name('guess_record')->where($map)->cache(5)->sum('money');
              $map['answer']     = 'B';
              $rows[$k]['b_num']   = Db::name('guess_record')->where($map)->cache(5)->count('record_id');
              $rows[$k]['b_money'] = Db::name('guess_record')->where($map)->cache(5)->sum('money');
              $rows[$k]['guess_id']=hashid($v['guess_id']);
          }
          return $rows;
      }
      //竞猜详情
        public function  detailsGuess($guessId,$type = 0){
          if ($type){
              return $this->alias('a')
                  ->join([
                      ['room r','a.room_id = r.room_id','left']
                  ])
                  ->where(['a.guess_id'=>$guessId])->find();
          }
              return $this->where(['guess_id'=>$guessId])->find();
        }





        //我参与的竞猜
        public function selfJoinGuess($user_id){
             $result=db('guess_record as gr')->where(['gr.user_id'=>$user_id])
                 ->join('guess g','gr.guess_id=g.guess_id','LEFT')
                 ->order('gr.create_time desc')
                 ->field('gr.guess_id,gr.answer,gr.money,gr.status as rstatus,gr.s_money,g.*')
                 ->paginate();

             $items=$result->items();
            if(empty($items)) return false;
            foreach ($items as $k=>$v){
                $items[$k]['acount']=db('guess_record')->where(['answer'=>'A','guess_id'=>$v['guess_id']])->count();
                $items[$k]['bcount']=db('guess_record')->where(['answer'=>'B','guess_id'=>$v['guess_id']])->count();
                $items[$k]['amoney']=db('guess_record')->where(['answer'=>'A','guess_id'=>$v['guess_id']])->sum('money');
                $items[$k]['bmoney']=db('guess_record')->where(['answer'=>'B','guess_id'=>$v['guess_id']])->sum('money');
                $items[$k]['guess_id']=hashid($v['guess_id']);
            }
            return ['thisPage'=>$result->currentPage(),'hasNext'=>$result->hasMore(),'data'=>$items];

        }

        //我发起的竞猜
        public function  selfSendGuess($user_id,$status = 0){
            $map['user_id']=$user_id;

            if ($status == 1){
                $map['status'] = ['between','1,2'];
            }
//            else{
//                $map['status']=['between','1,3'];
//            }

              $result=$this->where($map)
                  ->field('')
                   ->order('create_time desc')
                   ->paginate();

            $items = $result->items();
            foreach ($items as $k=>$v){
                $items[$k]['guess_id']=hashid($v['guess_id']);
            }
            if(empty($items)) return false;
            return ['thisPage'=>$result->currentPage(),'hasNext'=>$result->hasMore(),'data'=>$items];

        }
        //竞猜赔率换算
        public function guessOdds($guess_id){
              $guess = db('guess')->where('guess_id',$guess_id)
                   ->field('amoney,bmoney')
                   ->find();
              if($guess){
                  $guess_ratio = Db::name('extend')->where('id',1)->value('guess_ratio');
                  //系统抽成
                  $ratio = $guess_ratio / 100;
                  $data['ratio'] = $ratio.'%';
                  //A赢系统抽成金额
                  $data['A_ratio'] = bcmul($guess['amoney'],$ratio,2);
                  //A抽成后金额
                  $Amoney = $guess['amoney'] - $data['A_ratio'];
                  //B赢系统抽成金额
                  $data['B_ratio'] = bcmul($guess['bmoney'],$ratio,2);
                  //B抽成后金额
                  $Bmoney = $guess['bmoney'] - $data['B_ratio'];

                  $odds_A = $Bmoney/$Amoney;
                  if ($odds_A < 0.01) $odds_A = 0.01;
                  $data['odds_A'] = $odds_A;
                  $odds_B = $Amoney/$Bmoney ;
                  if ($odds_B < 0.01) $odds_B = 0.01;
                  $data['odds_B'] = $odds_B;
              }
              return $data;
        }
        //平台抽成比例获取
        public function platformOdds(){
            return  Db::name('extend')->where('id',1)->value('guess_ratio');
        }

        //结算竞猜公布答案
       public function releaseAnswer($data){
               $odds = $this->guessOdds($data['guess_id']);
               $data['odds_A'] = $odds['odds_A'];
               $data['odds_B'] = $odds['odds_B'];
               $data['update_time'] = time();
               $data['status'] = 3;
               return db('guess')->where('guess_id',$data['guess_id'])->update($data);
       }
       /*
        * 后台获取竞猜话题列表
        */
        public function getGuessList($where=[]){
            return $this->alias('g')->where($where)
                    ->join('room r','g.room_id=r.room_id')
                    ->field('g.guess_id,r.room_name,g.title,g.status,g.right,g.answer_A,g.answer_B,g.odds_A,g.odds_B,g.create_time')
                    ->order('g.create_time desc')
                    ->paginate('',false,['query'=>request()->param()]);
        }

        /*
         * 后台竞猜日志
         */
        public function  getGuessLog($guess_id){
             return db('guess_record')->alias('gr')->where('gr.guess_id',$guess_id)
                    ->join('role ro','ro.role_id=gr.role_id','LEFT')
                    ->join('guess g','g.guess_id=gr.guess_id','LEFT')
//                    ->join('room r','g.room_id=r.room_id','LEFT')
                    ->field('ro.role_name,gr.record_id,gr.answer,gr.money,gr.status,gr.s_money,gr.create_time')
                    ->order('gr.create_time desc')
                    ->paginate('',false,['query'=>request()->param()]);
        }

        /*
         * 直播间封盘接口
         */
       public function endGuess($guess_id){
             return $this->where('guess_id',$guess_id)->update(['status'=>2]);

       }

       /*
        * 竞猜结算接口
        * 一次查三千条  先结算赢的钱
        */
       public function  settlementMoney($guess_id,$right){
                    cache('wait_guess_'.$guess_id,$right);
               }






}
