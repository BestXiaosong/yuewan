<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/2
 * Time: 10:36
 */

namespace app\api\controller;


use app\common\model\Users;
use app\common\validate\UserId;
use think\Db;
use vod\Request\V20170321\AddEditingProjectRequest;

class Vod extends User
{
    /**
     * 获取回放推荐列表
     */
    public function lists()
    {
        $where['v.top'] = 1;
        $where['v.status'] = 1;
//        $id = 1;
        $id = $this->user_id;
        $model = new \app\common\model\Vod();
        $result = $model->getLists($where,$id);
//        echo $result;exit;
        if($result){
            api_return(1,'成功',$result);
        }else{
            api_return(0,'暂无数据');
        }
    }

    /**
     * 获取回放列表
     */
    public function vodlist()
    {
//        $where['v.top'] = 0;
        $where['v.status'] = 1;
        $id = $this->user_id;
        $cid =$this->request->post('cid');
        if(!empty($cid)){
            $where['v.cid'] = $cid;
        }
        $model = new \app\common\model\Vod();
        $result = $model->getRowss($where,$id);
        if($result){
            api_return(1,'成功',$result);
        }else{
            api_return(0,'暂无数据');
        }
    }
    /**
     * 获取回放分类列表
     */
    public function catelist()
    {
        $where['status'] = 1;
        $model = new \app\common\model\Vod();
        $result = $model->getCate($where);
        if($result){
            api_return(1,'成功',$result);
        }else{
            api_return(0,'暂无数据');
        }
    }
    /**
     * 回放视频搜索
     */
    public function vediosearch()
    {
        $where['v.title'] = ['like','%'.trim($this->request->post('title')).'%'];
//        $where['title'] = ['like','%数%'];
        $where['v.status'] = 1;
        $id = $this->user_id;
        $model = new \app\common\model\Vod();
        $result = $model->getVedio($where,$id);
        if($result){
            api_return(1,'成功',$result);
        }else{
            api_return(0,'暂无数据');
        }
    }

    /**
     * 回放视频点赞
     */
    public function up()
    {
        $id = $this->user_id;
        $where['pid'] = $this->request->post('pid');
        if(!is_numeric($where['pid']) || empty($where['pid'])) api_return(0,'参数错误');

//        $where['status'] = 1;
//        echo $id;exit;
        $model = new \app\common\logic\Vod();
        $result = $model->up($where,$id);
//        echo $result;exit;
        if($result){
            api_return(1,'操作成功');
        }else{
            api_return(0,'操作失败');
        }
    }

    /**
     * 回放视频评论
     */
    public function reply()
    {
        $data['p_id'] = $this->request->post('p_id');
        if(empty($data['p_id'])){
            $data['p_id'] = 0;
        }
        $pid = $this->request->post('pid');
        $id = $this->user_id;
        $data['content'] = $this->request->post('content');
        $model = new \app\common\logic\Vod();
        $result = $model->reply($data,$id,$pid);
        if($result){
            api_return(1,'操作成功');
        }else{
            api_return(0,'操作失败');
        }
    }

    /**
     * 回放视频分享
     */
    public function share()
    {
        $pid = $this->request->post('pid');
        if(!is_numeric($pid) || empty($pid)) api_return(0,'参数错误');
        $model = new \app\common\logic\Vod();
        $result = $model->share($pid);
        if($result){
            api_return(1,'操作成功');
        }else{
            api_return(0,'操作失败');
        }
    }

    /**
     * 回放视频点击次数
     */
    public function click()
    {
        $pid = $this->request->post('pid');
//        $pid = 1;
        $id = $this->user_id;
//        $id = 1;
        $model = new \app\common\logic\Vod();
        $result = $model->click($pid,$id);
//        print_r($result);exit;
        if($result){
            api_return(1,'操作成功',$result);
        }else{
            api_return(0,'操作失败');
        }
    }


    /**
     * 当前主播回访视频列表
     */

    public function vodlist_now(){
        $role_id = dehashid(input('post.role_id'));
        if (!is_numeric($role_id)) api_return(0,'参数错误');
        $id = db('role')->where(['role_id'=>$role_id])->value('user_id');
        $where['v.status'] = 1;
        $model = new \app\common\model\Vod();
        $result = $model->getRowss($where,$id);
        if($result){
            api_return(1,'成功',$result);
        }else{
            api_return(0,'暂无数据');
        }
    }


    /**
     * 当前用户的观看历史
     */
    public function history(){
        $user_id = $this->user_id;
        $type = $this->request->post('type');
        $model = new \app\common\model\Vod();
        $result = $model->getHistoryList($user_id,$type);
        if($result){
            api_return(1,'成功',$result);
        }else{
            api_return(0,'暂无数据');
        }
    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 清空观看历史
     */
    public function clear()
    {
        $type = input('post.type');
        if ($type != 1 && $type != 2) api_return(0,'参数错误');
        $map['user_id'] = $this->user_id;
        $map['type'] = $type;
        $result = Db::name('play_history')->where($map)->delete();
        if ($result) api_return(1,'删除成功');
        api_return(0,'操作失败');
    }


    /**
     * 获取回放列表
     */
    public function vodlist_all(){
          $where['v.status'] = 1;
//        $id = 1;
          $id = $this->user_id;
          $model = new \app\common\model\Vod();
          $cates_id = $model->getCateId(array('status'=>1));
          $cate_id = implode(',',$cates_id['data']);
          $where['v.cid'] = array('in',$cate_id);
          $result = $model->getLists_all($where,$id);
//        var_dump($result);exit;
          if($result){
              api_return(1,'成功',$result);
          }else{
              api_return(0,'暂无数据');
          }
      }

}