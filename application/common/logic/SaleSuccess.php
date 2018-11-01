<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/30 0030
 * Time: 17:29
 */
namespace app\common\logic;

use think\Model;


class SaleSuccess extends Base
{
    public function saveChange($data){
        if(is_numeric($data['id'])){

            $data['start_time'] = strtotime($data['start_time']);
            $data['end_time'] = strtotime($data['end_time']);
            return $this->validate(true)->allowField(true)->save($data,['sale_id'=>$data['id']]);
        }else{
            $data['create_time'] = time();
            $data['start_time'] = strtotime($data['start_time']);
            $data['end_time'] = strtotime($data['end_time']);
            return $this->validate(true)->allowField(true)->save($data);
        }
    }
    public function sale($psw,$id,$money,$g_id,$type)
    {
            $info = db('users')->where(['user_id'=>$id])->find();
            if($type == 0){
                $count = db('role')->where(['user_id'=>$id])->count('role_id');
                if($g_id == $info['role_id']){
                    return '当前正在使用该角色,不可进行拍卖';
                }
            }else{
                $count = db('room')->where(['user_id'=>$id])->count('room_id');
                $room = db('room')->where(['room_id'=>$g_id])->find();

                if($room['play_status'] != 0){
                    return '当前该房间未关闭,不可进行拍卖';
                }
            }

//        if($count == 1){
//
//        }
            $check = md5(md5($psw).$info['salt']);
            if($check == $info['trade_password']){
                $data['type'] = $type;
                $data['g_id'] = $g_id;
                $data['money'] = $money;
                $data['sale_user_id'] = $id;
                $data['start_time'] = time();
                $data['end_time'] = time()+3*60;
//                $data['end_time'] = time()+24*60*60;
                $data['status'] = 0;
                $data['create_time'] = time();
                $data['update_time'] = time();
                $this->insert($data);
                if($type == 1){
                    db('room')->where(['room_id'=>$g_id])->update(['status'=>2]);
                }else{
                    db('role')->where(['role_id'=>$g_id])->update(['status'=>2]);
                }

                return 1;
            }else{
                return '密码错误';
            }


    }


    public function admin_add($g_id,$type,$money){
        $datas['type'] = $type;
        $datas['g_id'] = $g_id;
        $datas['sale_user_id'] = 0;
        $datas['money'] = $money;
        $datas['status'] = 1;
        $result = db('sale_success')->insert($datas);
        if($result){
            return true;
        }else{
            return false;
        }
    }

}