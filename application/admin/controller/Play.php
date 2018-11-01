<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/30
 * Time: 17:45
 */

namespace app\admin\controller;
use app\common\model\PlayCategory;
use app\common\logic\PlayCategory as cate;
use app\common\model\Room;

use think\Db;
class Play extends Base
{







    /**
     * 直播列表
     */
    public function index()
    {

        $where = [];
        if (!empty($_GET['room_name'])) $where['room_name'] = ['like','%'.trim(input('get.room_name')).'%'];
        if (!isEmpty($_GET['status'])) $where['status'] = input('get.status');
        if (!isEmpty($_GET['cid'])) $where['cid'] = input('get.cid');
        $model  = new Room();
        $rows = $model->getList($where);
        $data = Db::name('play_category')->order('create_time DESC')->select();
        $cate = array_key($data,'cid');
        $this->assign([
            'rows' => $rows,
            'title' => '直播列表',
            'cate' => $cate,
            'pageHTML' => $rows->render(),
        ]);
        return view();

    }

    /**
     * 改变推荐状态
     */
    public function cate_top()
    {
        $data = input();
        $result = Db::name('play_category')->update(['top'=>$data['val'],'update_time'=>time(),'cid'=>$data['id']]);
        if($result !== false){
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }

    /**
     * 改变推荐状态
     */
    public function top()
    {
        $data = input();
        $result = Db::name('room')->update(['top'=>$data['val'],'update_time'=>time(),'room_id'=>$data['id']]);
        if($result !== false){
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }

    public function edit()
    {
        if(request()->isPost()){
            $data  = input('post.');
            $name = Db::name('room')
                ->where('room_name',$data['room_name'])
                ->where('room_id','<>',$data['id'])
                ->value('room_id');
            if ($name){
                $this->error('该昵称已存在');
            }
            $model = new \app\common\logic\Room();
            $result = $model->saveChange($data);
            if($result !== false){
                $this->success('操作成功',url('index'));
            }
            $this->error($model->getError());
        }
        $cate = Db::name('play_category')->order('create_time DESC')->select();
        $data = (new Room())->detail(['room_id'=>input('id')]);
        $this->assign([
            'title' => '直播编辑',
            'cate' => $cate,
            'data'=>$data,
        ]);
        return view();
    }

    public function change()
    {
        $data = input();
        $result = Db::name('room')->update(['status'=>$data['type'],'room_id'=>$data['id']]);
        if($result !== false){
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }

    public function delete()
    {
        $id     = input('id');
        $result = Db::name('room')->delete($id);
        if ($result !== false){
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }








    /**
     * 直播分类
     */
    public function cate()
    {
        $where = [];
        if (!empty($_GET['cate_name']))$where['cate_name'] = ['like','%'.trim(input('get.cate_name')).'%'];
        if (!isEmpty($_GET['status']))$where['status'] = input('get.status');
        $model = new PlayCategory();
        $rows = $model->getList($where);
        $this->assign([
            'rows' => $rows,
            'pageHTML' => $rows->render(),
            'title' => '直播分类管理',
        ]);
        return view();
    }
    public function cate_edit()
    {
        if(request()->isPost()){
            $data  = input('post.');
            $model = new cate();
            $cid = $model
                ->where('cate_name',$data['cate_name'])
                ->where('cid','<>',$data['id'])
                ->value('cid');
            if ($cid){
                $this->error('该分类已存在');
            }
            $result = $model->saveChange($data);
            if($result !== false){
                $this->success('操作成功',url('cate'));
            }
            $this->error($model->getError());
        }
        $this->_show('play_category','cid');
        $this->assign([
            'title' => '直播分类编辑',
        ]);
        return view();
    }

    public function cate_change()
    {
        $data = input();
        $result = Db::name('play_category')->update(['status'=>$data['type'],'cid'=>$data['id']]);
        if($result !== false){
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }

    public function cate_del()
    {
        $id     = input('id');
        $result = Db::name('play_category')->delete($id);
        if ($result !== false){
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }
}