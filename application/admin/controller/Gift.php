<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/27 0027
 * Time: 14:41
 */

namespace app\admin\controller;
use app\common\model\Gift  as Gifts;

class Gift extends  Base
{


            public function  index()
            {

                $map=[];
                if(!empty($_GET['gift_name'])){
                    $map['gift_name']=['like','%'.trim(input('get.gift_name')).'%'];
                }
                if(!isEmpty($_GET['status'])){
                    $map['status']=trim(input('get.status'));
                }

                $model=new \app\common\model\Gift();
                $rows=$model->giftListAdmin($map);
                $this->assign([
                    'rows'=>$rows,
                    'pageHTML'=>$rows->render()
                ]);
                return view();
            }

            //添加礼物类型
           public function  gift_edit()
           {
               if(request()->isPost())
               {
                   $data=request()->param();
                   $model=new \app\common\model\Gift();
                   $model->saveChange($data)?$this->success('成功'):$this->error('失败');

               }elseif(request()->isGet()){
                   $gift_id=input('id');
                   $details=db('gift')->where('gift_id',$gift_id)->find();
                   $this->assign('data',$details);
               }

               return view();
           }
            //后台删除礼物
           public function  gift_delete(){
                $id=input('id');
                db('gift')->where('gift_id',$id)->delete()?$this->success('成功'):$this->error('失败');
           }

           //礼物赠送记录
           public  function  gift_record_list()
           {
               $map=[];
               if(!empty($_GET['gift_name'])){
                   $map['g.gift_name']=['like','%'.trim(input('get.gift_name')).'%'];
               }
               if(!empty($_GET['startDate'])){
                   $startDate=strtotime(trim(input('get.startDate')));
               }
               if(!empty($_GET['endDate'])){
                   $endDate=strtotime(trim(input('get.endDate')));
               }
               if($startDate&&$endDate){
                   $map['gr.create_time']=['between',$startDate,$endDate];
               }else if($startDate&&empty($endDate)){
                   $map['gr.create_time']=['>',$startDate];
               }else if($endDate&&empty($startDate)){
                   $map['gr.create_time']=['<',$endDate];
               }

               $model=new \app\common\model\Gift();
               $rows=$model->recordList($map);
               $this->assign([
                   'rows'=>$rows,
                   'pageHTML'=>$rows->render()
               ]);
                return view();
           }
}