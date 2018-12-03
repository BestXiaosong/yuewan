<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/26
 * Time: 15:19
 */

namespace app\common\logic;


use think\Model;

class Room extends Model
{

    public function saveChange($data){

        if (empty($data['password'])){
            unset($data['password']);
        }
        if(is_numeric($data['id'])){
            return $this->validate('room.edit')->allowField(true)->isUpdate(true)->save($data,[$this->getPk()=>$data['id']]);
        }else{
            return  $this->validate('room.add_room')->allowField(true)->save($data);
        }
    }

    public function detail($room_id,$where){
        $result = db('room_notice')->where(['room_id'=>$room_id])->where($where)->order('create_time desc,top desc')->field('title,content,notice_id')->find();
        return $result;
    }


    public function changeStatus($id,$room_id,$notice_id,$del = 0,$up = 0,$user_id = 0){
        $user = $this->where(['room_id'=>$room_id])->value('user_id');
        $role_ids= db('room_follow')->where(['room_id'=>$room_id,'status'=>2])->column('role_id');
        $result = 0;
        if($user_id == $user||in_array($id,$role_ids)){
            if($del){
                $result = db('room_notice')->where(['notice_id'=>$notice_id])->update(['status'=>0]);
            }elseif($up){
                $res = db('room_notice')->where(['notice_id'=>$notice_id])->find();
                if($res['top'] == 0){
                    $res['top'] = 1;
                }else{
                    $res['top'] = 0;
                }
                $result = db('room_notice')->update($res);
            }

            if($result){
                return 1;
            }else{
                return 0;
            }
        }else{
            return -1;
        }
    }



}