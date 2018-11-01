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

//    /**
//     * 回放视频时点击粉丝关注
//     */
//
//    public function fans(){
//        $pid = $this->request->post('pid');
//        $id = $this->user_id;
//        $model = new \app\common\logic\Sale();
//        $result = $model->fans($pid,$id);
//        if($result){
//            api_return(1,'操作成功');
//        }else{
//            api_return(0,'操作失败');
//        }
//    }


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

      /**
       * 视频上传验证
       */
      public function upload_verify(){
          $data['play_url'] = $this->request->post('file');
          $data['cid'] = $this->request->post('cid');
          $data['title'] = $this->request->post('title');
          $data['img'] = $this->request->post('img');
          $data['detail'] = $this->request->post('detail');
//          api_return(1,'xxx',$data);
          if(in_array('',$data)) api_return(0,'参数缺失,请补全参数之后提交');
          $space = $this->fileInfo( $data['play_url']);
          if($space['code'] == 0) api_return(0,'视频路径错误,请重试');
          $user_id = $this->user_id;
          $ture_space = bcdiv($space['size'],1024,2);
          if ($ture_space == 0) $ture_space = 0.01;
          $model = new \app\common\logic\Vod();
          $result = $model->upload($user_id,$ture_space,$data);
          if($result == 0){
                $up = new \app\admin\controller\Upload();
                $results = db('vod')->where(['play_url'=>$data['play_url']])->select();
                if($results){
                    $up->delete1($data['play_url']);
                }
                api_return(0,'您的可用空间不足，您的视频上传失败');
          }elseif($result == 2){
                api_return(0,'上传失败,请确认信息无误后重新上传');
          }else{
                api_return(1,'上传成功');
          }
      }

      /**
       * 升级空间
       */

      public function space_level(){
          $model = new Users();
          $user_id = $this->user_id;
          $result = $model->space_up($user_id);
          if($result){
              $time = $this->request->post('date');
              if(empty($time)){
                  api_return(1,'当前用户可以进行空间升级');
              }else{
                  $month = $time;
                  $size = $this->request->post('size');
                  if(!$size) api_return(0,'升级空间大小未选择,请选择空间大小');
                  if(!$month) api_return(0,'升级时间未选择,请选择升级时间');
                  $result = $model->money($month,$size);
                  api_return(1,'总价格获取成功',$result);
              }
          }else{
              api_return(0,'您当前处于空间已升级状态,不能进行空间升级');
          }
      }

    /**
     * 当前用户支付升级空间所需积分
     */
    public function need(){
        $model = new \app\common\logic\Users();
        $models = new Users();
        $time = $this->request->post('date');
        $user_id = $this->user_id;
        $month = $time;
        $size = $this->request->post('size');
        if(!$size) api_return(0,'升级空间大小未选择,请选择空间大小');
        if(!$month) api_return(0,'升级时间未选择,请选择升级时间');
        $result = $models->money($month,$size);
        $psw = $this->request->post('psw');
        $results = $model->pay($user_id,$psw,$result,$size,$time);
//        var_dump($results);exit;
        if($results === 0){
            api_return(0,'交易密码错误，请重试');
        }elseif($results === 2){
            api_return(0,'您当前账户余额不足，请充值后再开通');
        }else{
            api_return(1,'升级成功',$results);
        }
    }
    /**
     * 支付成功调用接口
     */
    public function order_detail(){
        $order_num = $this->request->post('order_num');
        $model = new Users();
        $result = $model->order_detail($order_num);
        if($result){
            api_return(1,'查询成功',$result);
        }else{
            api_return(0,'暂无数据');
        }
    }
    /**
     * @param $date1  当前日期
     * @param $date2  日期大的
     * @return float|int  两个日期相差几个月
     */
    public function getMonthNum($date1,$date2){
          $date1_stamp=strtotime($date1);
          $date2_stamp=strtotime($date2);
          list($date_1['y'],$date_1['m'])=explode("-",date('Y-m',$date1_stamp));
          list($date_2['y'],$date_2['m'])=explode("-",date('Y-m',$date2_stamp));
          return abs($date_1['y']-$date_2['y'])*12 +$date_2['m']-$date_1['m'];
      }

}