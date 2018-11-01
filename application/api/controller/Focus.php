<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/3 0003
 * Time: 9:46
 * 房间/角色关注接口
 */

namespace app\api\controller;
use app\common\model\FocusRoom;
use think\Controller;

class Focus extends User
{
      //关注的房间
      public function focusRoom()
      {
         $model=new FocusRoom();
         $result=$model->getFocusRoom($this->role_id);
          if ($result !== false) api_return(1,'获取成功',$result);
          api_return(0,'暂无数据');

      }

      public function webFocusRoom(){
          $model=new FocusRoom();
          $result=$model->getFocusRoom1($this->role_id);
          if ($result !== false) api_return(1,'获取成功',$result);
          api_return(0,'暂无数据');

      }
      //关注的角色
       public function  focusRole()
       {
           $model=new FocusRoom();
           $result=$model->getFocusRole($this->role_id);
           if ($result !== false) api_return(1,'获取成功',$result);
           api_return(0,'暂无数据');
       }
       //直播间关注房间
       public function addFocusRoom()
       {
           $data['room_id']=dehashid(input('room_id'));
           if (!is_numeric($data['room_id'])) api_return(0,'参数错误');
           $data['role_id']=$this->role_id;
           $model=new FocusRoom();

           $res = $model->addFocusRoom($data);
           if ($res !== false){
               api_return(1,'关注成功');
           }else{
               $error = $model->getError();
               if (empty($error)) $error = '操作失败';
               api_return(0,$error);
           }
       }
       //直播间关注角色
        public function  addFocusRole()
        {
            $data['role_id']=dehashid(input('role_id'));
            if (!is_numeric($data['role_id'])) api_return(0,'参数错误');
            $data['follow_role_id']=$this->role_id;
            $model=new FocusRoom();
            $model->addFocusRole($data)?api_return(1,'关注成功'):api_return(0,'关注失败');
        }
        //取消关注房间
       public function hiddenRoom()
       {
             $room_id=dehashid(input('room_id'));
             $model=new FocusRoom();
             $model->hiddenRoom($room_id,$this->role_id)?api_return(1,'取消成功'):api_return(0,'取消失败');
       }
        //取消关注角色
        public function hiddenRole()
        {
            $follow_id=dehashid(input('role_id'));
            if(!is_numeric($follow_id))api_return(0,'非法ID');
            $model=new FocusRoom();
            $model->hiddenRole($follow_id,$this->role_id)?api_return(1,'取消成功'):api_return(0,'取消失败');
        }


}