<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/30
 * Time: 17:45
 */

namespace app\admin\controller;
use app\common\model\RoomCategory;
use app\common\logic\PlayCategory as cate;
use app\common\model\Room;

use think\Db;
class Play extends Base
{

    protected static $room_type = [
//        1=>电台 2=>娱乐  3=>点单 4=>聊天
        '1' =>['type_name'=>'电台','type'=>'1'],
        '2' =>['type_name'=>'娱乐','type'=>'2'],
        '3' =>['type_name'=>'点单','type'=>'3'],
        '4' =>['type_name'=>'聊天','type'=>'4'],
    ];




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
        $data = Db::name('room_category')->order('create_time DESC')->select();
        $cate = array_key($data,'cid');
        $this->assign([
            'rows' => $rows,
            'title' => '直播列表',
            'cate' => $cate,
            'pageHTML' => $rows->render(),
            'type' => self::$room_type,
        ]);
        return view();

    }

    /**
     * 改变推荐状态
     */
    public function cate_top()
    {

        $this->_change('room_category','top');

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
        $cate = Db::name('room_category')->order('create_time DESC')->select();
        $data = (new Room())->detail(['room_id'=>input('id')]);
        $this->assign([
            'title' => '房间编辑',
            'cate' => array_key($cate,'cid'),
            'data'=>$data,
            'type' => self::$room_type,
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
     * 房间分类标签
     */
    public function cate()
    {
        $where = [];
        if (!empty($_GET['cate_name']))$where['cate_name'] = ['like','%'.trim(input('get.cate_name')).'%'];
        if (!isEmpty($_GET['status']))$where['status'] = input('get.status');
        $model = new RoomCategory();
        $rows = $model->getList($where);
        $this->assign([
            'rows' => $rows,
            'pageHTML' => $rows->render(),
            'title' => '房间分类标签',
        ]);
        return view();
    }

    public function cate_edit()
    {
        $this->_edit('room_category','房间分类标签编辑',url('cate'),false);
        return view();
    }

    public function cate_change()
    {

        $this->_change('room_category');

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