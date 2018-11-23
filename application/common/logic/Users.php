<?php
namespace app\common\logic;

use think\Db;
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
//            if (!empty($data['password'])) {
//                $salt = $this->where('user_id', $data['id'])->value('salt');
//                $data['password'] = md5(md5($data['password']).$salt);
//            } else {
//                unset($data['password']);
//            }
            $data['token_expire'] = 0;
            return $this->allowField(true)->save($data, [$this->getPk() => $data['id']]);
        } else {
            $validate = validate('users');
            if (!$validate->scene('back_add')->check($data)) api_return(0, $validate->getError());
            $data['header_img'] = !empty($data['header_img']) ? $data['header_img'] : config('default_img');
//            $data['salt'] = generateStr(); //密码盐
//            $data['password'] = md5(md5($data['password']).$data['salt']);
//            $data['create_time'] = time();
            $result =  $this->allowField(true)->save($data);
            if ($result) {

                //注册user_extend
                $data['user_id'] = $this->getLastInsID();
                Db::name('user_extend')->strict(false)->insert($data);
                //修改用户uuid
                $uuid = 101088888+$data['user_id']*1000+rand(1,999);
                $this->where('user_id',$data['user_id'])->update(['uuid'=>$uuid]);

            }
            return $result;
        }
    }

    /**
     * 前端修改用户信息
     */
    public function change($data)
    {
        if (is_numeric($data['id'])) {
            $validate = validate('users');
            if (!$validate->scene('edit')->check($data)) api_return(0, $validate->getError());
//            if (!empty($data['password'])) {
//                $salt = $this->where('user_id',$data['id'])->value('salt');
//                $data['password'] = md5(md5($data['password']).$salt);
//            } else {
//                unset($data['password']);
//            }
            return $this->allowField(true)->save($data, ['user_id' => $data['id']]);
        } else {
            $validate = validate('base');
            if (!$validate->scene('front_user_add')->check($data)) api_return(0, $validate->getError());
            $data['header_img'] = config('default_img');
            $data['nick_name']  = '萌趴用户'.substr($data['phone'],-4);
//            $data['salt'] = generateStr(); //密码盐
//            $data['password'] = md5(md5($data['password']).$data['salt']);
            $result = $this->allowField(true)->save($data);
            if ($result){
                //注册user_extend
                $data['user_id'] = $this->getLastInsID();
                Db::name('user_extend')->strict(false)->insert($data);
                //修改用户uuid
                $uuid = 101088888+$data['user_id']*1000+rand(1,999);
                $this->where('user_id',$data['user_id'])->update(['uuid'=>$uuid]);

            }
            return $result;
        }
    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 第三方登录创建角色
     */
    public function userAdd($data)
    {
        $validate = validate('base');
        if (!$validate->scene('third_user_add')->check($data)) api_return(0, $validate->getError());
        $result =  $this->allowField(true)->save($data);
        if ($result){
            //注册user_extend
            $data['user_id'] = $this->getLastInsID();
            Db::name('user_extend')->strict(false)->insert($data);
            //修改用户uuid
            $uuid = 101088888+$data['user_id']*1000+rand(1,999);
            $this->where('user_id',$data['user_id'])->update(['uuid'=>$uuid]);

        }
        return $result;
    }



}