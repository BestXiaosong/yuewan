<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6 0006
 * Time: 10:09
 * 个人中心房间模块接口
 */

namespace app\api\controller;
use app\common\logic\Room;
use think\Db;
use think\Exception;

class SelfRoom extends User
{
         //我创建的房间接口
        public function  addRoomList()
        {
             $model=new \app\common\model\SelfRoom();

             $data=$model->getRoom($this->user_id);
             $data?api_return(1,'获取成功',$data):api_return(0,'暂无数据');
        }
        //我管理的房间
        public function glRoom()
        {

             $model=new \app\common\model\SelfRoom();

             $data=$model->glRoom($this->role_id);
            $data?api_return(1,'获取成功',$data):api_return(0,'暂无数据');

        }
        //视频回放
          public function  vod()
          {
              $model=new \app\common\model\SelfRoom();
              $data=$model->vod($this->user_id,$this->role_id);
              $data?api_return(1,'请求成功',$data):api_return(0,'暂无数据');
          }

          //可用空间大小
         public function space(){
             $model=new \app\common\model\SelfRoom();
             $data=$model->selfPlace($this->user_id);
             if($data){
                 api_return(1,'请求成功',$data);
             }else{
                 api_return(0,'请求失败');
             }
         }
          //删除回放视频
        public function delVod()
        {

            $id=intval(input('pid'));
            $file = db('vod')->where('pid', $id)->value('play_url');
            if(!$file) api_return(0,'所需删除的视频错误,请重试');
            $data=$this->fileInfo($file);
            if($data['code']!==1)api_return(0,'暂无文件信息');
            $space=round($data['size']/1024,2);//视频文件大小GBecho $space;exit;
           $model=new \app\common\model\SelfRoom();
            Db::startTrans();
               try{
                   $a=delVod($file);
                   $a=null;
                   if($a == null || $a == '') {
                       $a=db('vod')->where('pid', $id)->delete();
                       db('vod_reply')->where('pid', $id)->delete();
                       db('vod_up')->where('pid', $id)->delete();
                       $model->saveSpace($this->user_id, $space);
                       Db::commit();
                   }else {
                       api_return(0, '删除失败');
                   }
                       api_return(1, '删除成功');
               }catch (Exception $e){
                   Db::rollback();
                   api_return(0,'操作失败');
               }

        }

        /**
         *  房间及活动收费接口
         */
        public function give_money(){
            $data['user_id'] = $this->user_id;
            $data['role_id'] = $this->role_id;
            $data['room_id'] = dehashid($this->request->post('room_id'));
            if(!is_numeric($data['room_id'])) api_return(0,'参数错误');
            $data['type'] = $this->request->post('type');
            $month = $this->request->post('month');
            $model = new  Room();
            $result = $model->VIP_Room($data,$month);
            if($result == 1){
                api_return(1,'付费成功');
            }else{
                api_return(0,$result);
            }
        }
}