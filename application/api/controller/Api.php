<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/14 0014
 * Time: 16:34
 */

namespace app\api\controller;

use app\common\model\Banner;
use app\common\model\Coin;
use app\common\model\PlayCategory;
use rongyun\api\RongCloud;
use think\Db;

class Api extends User
{


    /**
     * 获取banner及广告图
     */
    public function getBanner()
    {
        $model = new Banner();
        $where['status'] = 1;
        $where['cid'] = input('post.cid');
        $limit = is_numeric(input('post.num')) ? input('post.num') : 6;
        $rows = $model->getBanner($where, $limit);
        if (!empty($rows)) api_return(1, '获取成功', $rows);
        api_return(0, '暂无数据');
    }



    /**
     * 获取融云token
     */
    public function token()
    {
        $token = $this->R_token($this->role_id);
        if ($token !== false) api_return(1, '获取成功', $token);
        api_return(0, '服务器繁忙,请稍后重试');
    }


    /**
     * 获取说明文档
     */
    public function explain()
    {
        $id = input('post.id');
        if (!is_numeric($id)) api_return(0, '参数错误');
        $data = Db::name('explain')->where('id', $id)->value('content');
        if (!empty($data)) api_return(1, '获取成功', $data);
        api_return(0, '暂无数据');
    }


    /**
     * 封禁成员
     */
    public function block_user()
    {
        $RongCloud = new \rongyun\api\RongCloud('m7ua80gbmjrnm','cWPdDytpyx4');
        $room_id = dehashid($this->request->post('room_id'));
        $user = dehashid($this->request->post('role_id'));
        $time = 43200;
        if(!is_numeric($room_id)||empty($room_id)||!is_numeric($user)||empty($user)||empty($time)) api_return(0,'参数错误');
        $where['room_id'] = $room_id;
        $where['status'] = 1;
        $data = db('room')->where($where)->field('role_id as Homeowner,cid,detail,is_mobile,img,is_close,user_id,room_id,money,is_charge,room_name,VIP,play_status,status,fans,official,create_time')->cache(5)->find();
        $data['role_id'] = hashid($this->role_id);
        $data['Homeowner'] = hashid($data['Homeowner']);
        if ($data['user_id'] == $this->user_id){
            $yes = 1;
        }else{
            $term['role_id'] = $this->role_id;
            $term['room_id'] = $room_id;
            $term['status']  = 2;
            $yes = Db::name('room_follow')->where($term)->value('follow_id')?1:0; //是否为房间管理员
        }
        if($yes){
            $result = $RongCloud->chatroom()->addBlockUser(hashid($user), 'play_'.hashid($room_id), $time);
            $result1 = $RongCloud->chatroom()->addBlockUser(hashid($user), 'chat_'.hashid($room_id), $time);
            $result = json_decode($result,true);
            $result1 = json_decode($result1,true);
            $code = $result['code'];
            $code1 = $result1['code'];
            if($code == '200'&&$code1 == 200){
                api_return(1,'封禁成功');
            }else{
                api_return(0,'封禁失败');
            }
        }else{
            api_return(0,'没有权限进行封禁');
        }
    }




    /**
     * 解除封禁
     */
    public function over_block_user()
    {
        $RongCloud = new \rongyun\api\RongCloud('m7ua80gbmjrnm','cWPdDytpyx4');
        $room_id = dehashid($this->request->post('room_id'));
        $user = dehashid($this->request->post('role_id'));
        if(!is_numeric($room_id)||empty($room_id)||!is_numeric($user)||empty($user)) api_return(0,'参数错误');
        $where['room_id'] = $room_id;
        $where['status'] = 1;
        $data = db('room')->where($where)->field('role_id as Homeowner,cid,detail,is_mobile,img,is_close,user_id,room_id,money,is_charge,room_name,VIP,play_status,status,fans,official,create_time')->cache(5)->find();
        $data['role_id'] = hashid($this->role_id);
        $data['Homeowner'] = hashid($data['Homeowner']);
        if ($data['user_id'] == $this->user_id){
            $yes = 1;
        }else{
            $term['role_id'] = $this->role_id;
            $term['room_id'] = $room_id;
            $term['status']  = 2;
            $yes = Db::name('room_follow')->where($term)->value('follow_id')?1:0; //是否为房间管理员
        }
        if($yes){
            $result = $RongCloud->chatroom()->rollbackBlockUser(hashid($user), 'play_'.hashid($room_id));
            $result1 = $RongCloud->chatroom()->rollbackBlockUser(hashid($user), 'chat_'.hashid($room_id));
            $result = json_decode($result,true);
            $result1 = json_decode($result1,true);
            $code = $result['code'];
            $code1 = $result1['code'];
            if($code == '200'&&$code1 == 200){
                api_return(1,'解封成功');
            }else{
                api_return(0,'解封失败');
            }
        }else{
            api_return(0,'没有权限进行解封');
        }
    }

    /**
     * 封禁列表
     */

    public function block_list(){
        $room_id = dehashid($this->request->post('room_id'));
        if(!is_numeric($room_id)||empty($room_id)) api_return(0,'参数错误');
        $where['room_id'] = $room_id;
        $where['status'] = 1;
        $data = db('room')->where($where)->field('role_id as Homeowner,cid,detail,is_mobile,img,is_close,user_id,room_id,money,is_charge,room_name,VIP,play_status,status,fans,official,create_time')->cache(5)->find();
        $data['role_id'] = hashid($this->role_id);
        $data['Homeowner'] = hashid($data['Homeowner']);
        if ($data['user_id'] == $this->user_id){
            $yes = 1;
        }else{
            $term['role_id'] = $this->role_id;
            $term['room_id'] = $room_id;
            $term['status']  = 2;
            $yes = Db::name('room_follow')->where($term)->value('follow_id')?1:0; //是否为房间管理员
        }
        if($yes){
            $user_list = block_list($room_id);

            if($user_list){
                foreach($user_list as $k=>$v){
                    $datas[] = db('role')->where(['role_id'=>dehashid($v['userId'])])->field('role_id,role_name,header_img')->find();
                }
                foreach ($datas as $k=>$v){
                    $datas[$k]['role_id'] = hashid($v['role_id']);
                }
                api_return(1,'查询成功',$datas);
            }else{
                api_return(0,'暂无数据');
            }
        }else{
            api_return(0,'没有权限进行列表查询');
        }
    }
}