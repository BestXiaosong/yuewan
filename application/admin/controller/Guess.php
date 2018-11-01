<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/27 0027
 * Time: 14:43
 */

namespace app\admin\controller;


class Guess extends Base
{
      /*
       * 竞猜话题首页
       */
      public  function  index(){
          $map=[];
          if(!empty($_GET['status'])){
              $map['g.status']=intval(trim(input('get.status')));
          }
          if(!empty($_GET['startDate'])){
              $startDate=strtotime(trim(input('get.startDate')));
          }
          if(!empty($_GET['endDate'])){
              $endDate=strtotime(trim(input('get.endDate')));
          }
          if($startDate&&$endDate){
              $map['g.create_time']=['between',$startDate,$endDate];
          }else if($startDate&&empty($endDate)){
              $map['g.create_time']=['>',$startDate];
          }else if($endDate&&empty($startDate)){
              $map['g.create_time']=['<',$endDate];
          }

          $model=new \app\common\model\Guess();
          $rows=$model->getGuessList($map);
          $this->assign([
              'rows'=>$rows,
              'pageHTML'=>$rows->render()
          ]);
          return view();
      }

      /*
       * 竞猜日志查看
       */
      public function guess_index(){
            $guessid =intval(input('id'));
            $model   = new \app\common\model\Guess();
            $rows    = $model->getGuessLog($guessid);
            $data    = $model->detailsGuess($guessid,1);
            $this->assign([
              'rows'=>$rows,
              'pageHTML'=>$rows->render(),
              'data' => $data,
            ]);
            return view();
      }



}