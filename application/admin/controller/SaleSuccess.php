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


class SaleSuccess extends Base
{

    function _initialize()
    {
        parent::_initialize();
        $this->assign('stamp',123);
    }

    public function index()
    {
        $where = [];
        if(!empty($_GET['title'])){
            $where['s.title'] = trim(input('get.title'));
        }

        if(!isEmpty($_GET['type'])){
            $where['s.type'] = $_GET['type'];
            $this->assign('type',$_GET['type']);
        }else{
            $where['s.type'] = -1;
        }
        $where['s.status'] = 0;

        $model = new \app\common\model\SaleSuccess();
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

    public function sale_edit()
    {
        if(request()->isPost()){
            $data   = input('post.');

            if($data['type'] == 0){
                $model = new \app\common\logic\Role();
                $g_id = $model->admin_add_role($data['name']);
                if(!$g_id){
                    $this->error('角色名已存在,不能添加为新的拍卖品');
                }
            }else{
                $model = new \app\common\logic\Room();
                $g_id = $model->admin_add_room($data['name']);
                if(!$g_id){
                    $this->error('房间名已存在,不能添加为新的拍卖品');
                }
            }
            $model  = new \app\common\logic\SaleSuccess();
            $result = $model->admin_add($g_id,$data['type'],$data['money']);
            if($result !== false){
                $this->success('操作成功',url('/sale_success/admin'));
            }
            $this->error($model->getError());
        }
        $this->_show('sale_success','sale_id');
        $id = input('id');
        if(!empty($id)){
            $models = new \app\common\model\SaleSuccess();
            $results = $models->getGoods($id);
            $this->assign('result',$results);
        }
        $this->assign('title','添加拍卖品');
        return view();
    }

    public function change()
    {
        $data = input();
        $result = Db::name('banner')->update(['status'=>$data['type'],'bid'=>$data['id']]);
        if($result !== false){
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }

    public function sale_del()
    {
        $id     = input('id');
        $status = 1;
        $result = Db::name('sale_success')->where(['sale_id'=>$id])->update(['status'=>$status]);
        if ($result !== false){
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }



    public function admin(){
        $where = [];
        if(!empty($_GET['title'])){
            $where['s.title'] = trim(input('get.title'));
        }

        if(!isEmpty($_GET['type'])){
            $where['s.type'] = $_GET['type'];
            $this->assign('type',$_GET['type']);
        }else{
            $where['s.type'] = -1;
        }
        if(!isEmpty($_GET['status'])){
            $where['s.sale_status'] = $_GET['status'];
        }
        $where['s.status'] = 1;

        $model = new \app\common\model\SaleSuccess();
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

        public function change_status(){
            $data = input();
            $result = Db::name('sale_success')->update(['sale_status'=>$data['val'],'sale_id'=>$data['id']]);
            if($result !== false){
                $this->success('操作成功');
            }
            $this->error('操作失败');
        }


}
