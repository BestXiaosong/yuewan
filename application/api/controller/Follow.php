<?php
/**
 * Created by xiaosong
 * E-mail:4155433@gmail.com
 * Date: 2018/11/28
 * Time: 9:56
 */

namespace app\api\controller;

use app\common\logic\Logic;
use app\common\logic\UserFollow;
use \app\common\model\UserFollow as model;
use think\Db;
use think\Exception;

class Follow extends User
{

    
    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 关注用户
     */
    public function follow()
    {
        $id = dehashid(input('post.id'));

        if (!is_numeric($id)){
            api_return(0,'用户id错误');
        }
        if ($id == $this->user_id){
            api_return(0,'您不能关注自己');
        }

        $map['follow_user'] = $id;
        $map['user_id']     = $this->user_id;

        $model = new UserFollow();

        $data = $model->get($map);

        //查询我关注的用户是否关注了我  如果关注了 type改为1
        $type['follow_user'] = $this->user_id;
        $type['user_id']     = $id;
        $type['status']      = 1;
        $follow = $model->get($type);
        if ($data){
            if ($data['status'] == 1) api_return(0,'您已关注该用户,请勿重复操作');
            $next = $data;
        }else{
            $next = $model;
        }
        $map['status'] = 1;
        Db::startTrans();
        try{
            if ($follow){
                $map['type']  = 1;
                $follow->save(['type'=>1]);
            }
            $next->save($map);
            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            api_return(0,$e->getMessage());
        }
        api_return(1,'关注成功');

    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 取消关注
     */
    public function cancel()
    {
        $id = dehashid(input('post.id'));
        if (!is_numeric($id)){
            api_return(0,'用户id错误');
        }
        $map['follow_user'] = $id;
        $map['user_id']     = $this->user_id;
        $model = new UserFollow();
        $data = $model->get($map);
        if ($data && $data['status'] == 1){

            Db::startTrans();
            try{
                //如果对方和我互为好友  取消好友关系
                if ($data['type'] == 1){
                    $type['follow_user'] = $this->user_id;
                    $type['user_id']     = $id;
                    $model->where($type)->update(['type'=>0]);
                }
                $save['status'] = 0;
                $save['type']   = 0;
                $data->save($save);
                Db::commit();
            }catch (Exception $e){
                Db::rollback();
                api_return(0,'服务器繁忙,请稍后重试',$e->getMessage());
            }
            api_return(1,'取消关注成功');
        }else{
            api_return(0,'您未关注该用户!');
        }
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 获取我的粉丝
     */
    public function fans()
    {

        $model = new model();

        $map['a.status'] = 1;
        $map['a.follow_user'] = 1;

        $rows = $model->getRows($map);

        api_return(1,'获取成功',$rows);

    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 获取我的关注
     */
    public function following()
    {
        $model = new model();

        $map['a.status']  = 1;
        $map['a.user_id'] = $this->user_id;

        $rows = $model->getRows($map);

        api_return(1,'获取成功',$rows);
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 获取我的好友
     */
    public function friends()
    {
        $model = new model();

        $map['a.status']  = 1;
        $map['a.user_id'] = $this->user_id;
        $map['a.type']    = 1;

        $rows = $model->getRows($map);

        api_return(1,'获取成功',$rows);
    }
    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 我的黑名单列表
     */
    public function blackList()
    {
        $model = new model();

        $map['a.status']  = 2;
        $map['a.user_id'] = $this->user_id;

        $rows = $model->getRows($map);

        api_return(1,'获取成功',$rows);
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 拉黑
     */
    public function pullBlack()
    {
        $id = dehashid(input('post.id'));

        if (!is_numeric($id)){
            api_return(0,'用户id错误');
        }
        if ($id == $this->user_id){
            api_return(0,'您不能拉黑自己');
        }

        $map['follow_user'] = $id;
        $map['user_id']     = $this->user_id;

        $model = new UserFollow();

        $data = $model->get($map);

        //查询拉黑的用户是否和我互相关注  如果关注了 type改为0
        $type['follow_user'] = $this->user_id;
        $type['user_id']     = $id;
        $type['status']      = 1;
        $follow = $model->get($type);
        if ($data){
            if ($data['status'] == 2) api_return(0,'您已拉黑该用户,请勿重复操作');
            $next = $data;
        }else{
            $next = $model;
        }
        $map['status'] = 2;
        Db::startTrans();
        try{
            if ($follow && $follow['type'] == 1){
                $map['type']  = 0;
                $follow->save(['type'=>0]);
            }

            $next->save($map);
            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            api_return(0,$e->getMessage());
        }
        api_return(1,'拉黑成功');
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 取消拉黑
     */
    public function removeBlack()
    {
        $id = dehashid(input('post.id'));
        if (!is_numeric($id)){
            api_return(0,'用户id错误');
        }
        $map['follow_user'] = $id;
        $map['user_id']     = $this->user_id;
        $model = new UserFollow();
        $data  = $model->get($map);
        if ($data && $data['status'] == 2){
            Db::startTrans();
            try{

                $save['status'] = 0;
                $save['type']   = 0;
                $data->save($save);
                Db::commit();
            }catch (Exception $e){
                Db::rollback();
                api_return(0,'服务器繁忙,请稍后重试',$e->getMessage());
            }
            api_return(1,'移除成功');
        }else{
            api_return(0,'您未拉黑该用户!');
        }
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 关注房间
     */
    public function roomFollow()
    {
        $id = input('post.id');

        $roomInfo = $this->roomInfo($id);

        if (!$roomInfo) api_return(0,'房间id错误');
        if ($roomInfo['user_id'] == $this->user_id){
            api_return(0,'您不能关注自己的房间');
        }

        $map['room_id'] = $id;
        $map['user_id'] = $this->user_id;

        $model = new Logic();
        $model->changeTable('room_follow');

        $data = $model->where($map)->find();

        if ($data && $data['status'] != 0){
            api_return(0,'您已关注该房间');
        }

        if ($data){

            $result = $data->save(['status'=>1]);

        }else{
            $result =$model->save($map);
        }

        if ($result){
            api_return(1,'关注成功');
        }

        api_return(0,$data->getError().$model->getError());

    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 取消关注
     */
    public function roomCancel()
    {
        $id = input('post.id');
        $map['room_id'] = $id;
        $map['user_id'] = $this->user_id;
        $model = new Logic();
        $model->changeTable('room_follow');
        $data = $model->where($map)->find();
        if (!$data || $data['status'] == 0){
            api_return(0,'您未关注该房间');
        }
        if ($data['status'] != 1){
            api_return(0,'管理员不能取消关注');
        }
        $result =  $data->save(['status'=>0]);

        if ($result) api_return(1,'取消关注成功');
        api_return(0,'操作失败');
    }



}