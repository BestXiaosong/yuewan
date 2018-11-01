<?php
namespace app\common\logic;

use think\Db;
use think\helper\hash\Md5;
use think\Model;


class Users extends Model
{


    /**
     * 后台修改用户信息
     */
    public function saveChange($data)
    {
        $data['update_time'] = time();
        if (is_numeric($data['id'])) {
            $validate = validate('users');
            if (!$validate->scene('back_edit')->check($data)) api_return(0, $validate->getError());
            if (!empty($data['password'])) {
                $salt = $this->where('user_id', $data['id'])->value('salt');
                $data['password'] = md5(md5($data['password']).$salt);
            } else {
                unset($data['password']);
            }
            $data['token_expire'] = 0;
            return $this->allowField(true)->save($data, ['user_id' => $data['id']]);
        } else {
            $validate = validate('users');
            if (!$validate->scene('back_add')->check($data)) api_return(0, $validate->getError());
            $data['header_img'] = !empty($data['header_img']) ? $data['header_img'] : config('default_img');
            $data['salt'] = generateStr(); //密码盐
            $data['password'] = md5(md5($data['password']).$data['salt']);
            $data['create_time'] = time();
            $result = $this->allowField(true)->save($data);
            if ($result) {
                $user_id = $this->getLastInsID();
                //私钥
                $key = md5($user_id.$data['salt'].rand(1, 99));
                $map['private_key'] = $key;
                $res = $this->where('user_id', $user_id)->update($map);
                $extend = Db::name('user_extend')->insert(['user_id'=>$user_id]);
                if (!$extend){
                    Db::name('user_extend')->insert(['user_id'=>$user_id]);
                }
                if (!$res) {
                    $this->where('user_id', $user_id)->update($map);
                }
            }
            return $result;
        }
    }

    /**
     * 前端修改用户信息
     */
    public function change($data)
    {
        $data['update_time'] = time();
        if (is_numeric($data['id'])) {
            $validate = validate('users');
            if (!$validate->scene('edit')->check($data)) api_return(0, $validate->getError());
            if (!empty($data['password'])) {
                $salt = $this->where('user_id', $data['id'])->value('salt');
                $data['password'] = md5(md5($data['password']).$salt);
            } else {
                unset($data['password']);
            }
            if (!empty($data['trade_password'])) {
                $salt = $this->where('user_id', $data['id'])->value('salt');
                $data['trade_password'] = md5(md5($data['trade_password']).$salt);
            } else {
                unset($data['trade_password']);
            }
            $data['update_time'] = time();
            return $this->allowField(true)->save($data, ['user_id' => $data['id']]);
        } else {
            $validate = validate('base');
            if (!$validate->scene('front_user_add')->check($data)) api_return(0, $validate->getError());
            $data['create_time'] = time();
            $data['salt'] = generateStr(); //密码盐
            $data['password'] = md5(md5($data['password']).$data['salt']);
            $data['trade_password'] = md5(md5($data['trade_password']).$data['salt']);
            $result = $this->allowField(true)->save($data);
            if ($result) {
                $user_id = $this->getLastInsID();
                //私钥
                $key = md5($user_id.$data['salt'].rand(1, 99));
                $map['private_key'] = $key;
                $res = $this->where('user_id', $user_id)->update($map);
                $extend = Db::name('user_extend')->insert(['user_id'=>$user_id]);
                if (!$extend){
                    Db::name('user_extend')->insert(['user_id'=>$user_id]);
                }
                if (!$res) {
                    $this->where('user_id', $user_id)->update($map);
                }
            }
            return $result;
        }
    }


    public function resetPhone($phone,$newphone)
    {
        return $this->isUpdate(true)->save(['phone'=>$newphone],['phone'=>$newphone]);
    }

//支付升级空间所需积分
    public function pay($id,$psw,$money,$size,$time){
        $info = db('users')->where(['user_id'=>$id])->find();
        $check = md5(md5($psw).$info['salt']);
        if($check == $info['trade_password']){
            if($info['money']>=$money){
                $data['bucket_space'] = $size + $info['bucket_space'];
//                $data['space_time'] =time()+$time*30*24*60*60;
                $data['space_time'] =time()+24*60*60;
                $data['money'] =  $info['money'] - $money;
                $order_num = "RE".hashid($id).date("Ymd").rand(1000,9999);
                money($id,7,0-$money,1,'升级空间扣除'.$money,$order_num);
                db('users')->where(['user_id'=>$id])->update($data);
                return $order_num;
            }else{
                return 2;
            }
        }else{
            return 0;
        }
    }
}