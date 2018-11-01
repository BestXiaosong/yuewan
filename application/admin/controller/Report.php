<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/11 0011
 * Time: 10:26
 */

namespace app\admin\controller;


class Report extends Base
{
          public function  index(){
              if(!isEmpty($_GET['status'])){
                  $map['r.status']=trim(input('get.status'));
              }
              if(!isEmpty($_GET['type'])){
                  $map['r.type']=trim(input('get.type'));
              }

              $model=new \app\common\model\Gift();
              $rows=$model->adminReportList($map);
              $this->assign([
                  'rows'=>$rows,
//                  'pageHTML'=>$rows->render()
              ]);
              return view();
          }


          public function delete(){
              $id=input('id');
              db('reason')->where('report_id',$id)->delete()?$this->success('成功'):$this->error('失败');
          }

          public function  report_edit(){
              $model=new \app\common\model\Gift();
              if(request()->isGet()){
                  $id=input('id');
                  $row=$model->details($id);
                  $this->assign('data',$row);
              }elseif(request()->isPost()){
                  $report_id=input('report_id');
                  $status=input('status');
                   $row=$model->updateReason($report_id,$status);
                   if($row!==false)$this->success('审核成功',url('/report/index'));
                   $this->error('审核失败',url('/report/index'));
              }
              return view();
          }
}