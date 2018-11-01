<?php
namespace app\admin\controller;

use app\common\logic\BannerCate;
use think\Db;


class Banner extends Base
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
            $where['b.title'] = ['like','%'.trim(input('get.title')).'%'];
        }
        if(!empty($_GET['cid'])){
            $where['b.cid'] = trim(input('get.cid'));
        }
        if(!isEmpty($_GET['status'])){
            $where['b.status'] = trim(input('get.status'));
        }
        $model = new \app\common\model\Banner();
        $rows = $model->getList($where);
        $data = Db::name('banner_cate')->where('status',1)->select();
        $this->assign([
            'data'=>$data,
            'pageHTML'=>$rows->render(),
            'rows'=>$rows
        ]);
        return view();
    }

    public function banner_edit()
    {
        if(request()->isPost()){
            $data   = input('post.');
            $model  = new \app\common\logic\Banner();
            $result = $model->saveChange($data);
            if($result !== false){
                $this->success('操作成功',url('/banner/index'));
            }
            $this->error($model->getError());
        }
        $where['status'] = 1;
        $this->_show('banner','bid','banner_cate','',$where);
        $this->assign('title','图片编辑');
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

    public function banner_del()
    {
        $id     = input('id');
        $result = Db::name('banner')->delete($id);
        if ($result !== false){
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }

    public function cate()
    {
        $this->_list2('banner_cate','cid DESC');
        $this->assign('title','图片列表');
        return view();
    }


    public function cate_edit()
    {
        if(request()->isPost()){
            $data  = input('post.');
            $model = new BannerCate();
            $result = $model->saveChange($data);
            if($result !== false){
                $this->success('操作成功',url('/banner/cate'));
            }
            $this->error($model->getError());
        }
        $this->_show('banner_cate','cid');
        return view();
    }

    public function cate_change()
    {
        $data = input();
        $result = Db::name('banner_cate')->update(['status'=>$data['type'],'cid'=>$data['id']]);
        if($result !== false){
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }

    public function cate_del()
    {
        $id     = input('id');
        $result = Db::name('banner_cate')->delete($id);
        if ($result !== false){
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }



}
