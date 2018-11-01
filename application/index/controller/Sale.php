<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/5 0005
 * Time: 16:12
 */

namespace app\index\controller;


use app\common\model\Role;
use app\common\model\Room;
use app\common\model\SaleSuccess;

class Sale extends User
{
    public function index(){
        $model = new SaleSuccess();
        $room_model = new Room();
        $role_model = new Role();
        $role_name = $this->request->get('role_name');
        if(!empty($role_name)){
            $data = $role_model->roles($role_name);
            $role['data'] = $data;
        }else{
            $role = $model->getRows(0);
        }
        $rule = $this->httpPost(array('id'=>1),'/api/explain');
        $room_name = $this->request->get('room_name');
        if(!empty($room_name)){
            $data = $room_model->rooms($room_name);
            $room['data'] = $data;
        }else{
            $room = $model->getRows(1);
        }
//        var_dump($role);exit;
//        api_return($role);exit;
        return view('', [
            'role'=>$role,
            'rule'=>$rule,
            'room'=>$room,
        ]);
    }


}