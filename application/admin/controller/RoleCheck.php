<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/30 0030
 * Time: 16:39
 */

namespace app\admin\controller;

use app\common\logic\BannerCate;
use think\Db;


class RoleCheck extends Base
{

    function _initialize()
    {
        parent::_initialize();
        $this->assign('stamp',123);
    }

    public function index()
    {
        $where = [];
//        if(!empty($_GET['title'])){
//            $where['s.title'] = trim(input('get.title'));
//        }

        if(!isEmpty($_GET['type'])){
            $where['s.status'] = $_GET['type'];
            $this->assign('type',$_GET['type']);
        }
        $model = new \app\common\model\RoleCheck();
        $rows = $model->getList($where);
//        echo $rows;exit;
//        $data = Db::name('banner_cate')->where('status',1)->select();
        $this->assign([
//            'data'=>$data,
            'pageHTML'=>$rows->render(),
            'rows'=>$rows
        ]);
        return view();
    }

//    public function check_edit()
//    {
//        if(request()->isPost()){
//            $data   = input('post.');
//            $model  = new \app\common\logic\RoleCheck();
//            $result = $model->saveChange($data);
//            if($result !== false){
//                $this->success('操作成功',url('/sale_success/index'));
//            }
//            $this->error($model->getError());
//        }
//        $this->_show('sale_success','sale_id');
//        $id = input('id');
//        if(!empty($id)){
//            $models = new \app\common\model\SaleSuccess();
//            $results = $models->getGoods($id);
//            $this->assign('result',$results);
//        }
//        $this->assign('title','添加拍卖品');
//        return view();
//    }

    public function check_edit()
    {
        $id     = input('id');
        $status = input('status');
        if($status == 0){
            $model = new \app\common\model\RoleCheck();
            $model->turn($id);
        }
        $result = Db::name('role_check')->where(['check_id'=>$id])->update(['status'=>$status]);
        if ($result !== false){
         $this->success('审核通过');
        }
       $this->error('审核失败');
    }

//    public function cate()
//    {
//        $this->_list2('banner_cate','cid DESC');
//        $this->assign('title','图片列表');
//        return view();
//    }
//
//
//    public function cate_edit()
//    {
//        if(request()->isPost()){
//            $data  = input('post.');
//            $model = new BannerCate();
//            $result = $model->saveChange($data);
//            if($result !== false){
//                $this->success('操作成功',url('/banner/cate'));
//            }
//            $this->error($model->getError());
//        }
//        $this->_show('banner_cate','cid');
//        return view();
//    }
//
//    public function cate_change()
//    {
//        $data = input();
//        $result = Db::name('banner_cate')->update(['status'=>$data['type'],'cid'=>$data['id']]);
//        if($result !== false){
//            $this->success('操作成功');
//        }
//        $this->error('操作失败');
//    }
//
//    public function cate_del()
//    {
//        $id     = input('id');
//        $result = Db::name('banner_cate')->delete($id);
//        if ($result !== false){
//            $this->success('删除成功');
//        }
//        $this->error('删除失败');
//    }



}
