<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6 0006
 * Time: 10:55
 */

namespace app\admin\controller;


use app\common\model\Opinion;
use think\Db;
use think\Request;

class Message extends Base
{
    //系统消息列表
    public function index()
    {
        $where['user_id'] = 0;
        if (!empty($_GET['title'])) {
            $where['title'] = ['like', '%' . input('get.title') . '%'];
        }
        if (!isEmpty($_GET['status'])) {
            $where['status'] = input('get.status');
        }
        if (!isEmpty($_GET['type'])) {
            $where['type'] = input('get.type');
        }
        $this->_list2('message', 'mid DESC', $where, '', true);
        $this->assign('title', '系统公告列表');
        return view();
    }

    public function edit()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $model = new \app\common\logic\Message();
            $data['user_id'] = 0;
            $result = $model->saveChange($data);
            if ($result !== false) {
                $this->success('操作成功', url('/message/index'));
            }
            $this->error($model->getError());
        }
        $this->_show('message', 'mid');
        $this->assign('title', '系统公告编辑');
        return view();
    }


    public function add()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $model = new \app\common\logic\Message();
            $data['user_id'] = 0;
            $result = $model->saveChange($data);
            if ($result !== false) {
                $this->success('操作成功', url('/message/index'));
            }
            $this->error($model->getError());
        }
        $this->assign('title', '系统公告编辑');
        return view();
    }

    public function change()
    {
        $data = input();
        $result = Db::name('message')->update(['status' => $data['type'], 'mid' => $data['id']]);
        if ($result !== false) {
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }

    public function del()
    {
        $id = input('id');
        $result = Db::name('message')->delete($id);
        if ($result !== false) {
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }

    /////////////////反馈/////////////////////

    /**
     * 用户反馈信息列表
     */
    public function opinion()
    {
        $model = new Opinion();
        $where = [];
        if (!isEmpty($_GET['status'])) $where['a.status'] = trim(input('get.status'));
        if (!isEmpty($_GET['is_read'])) $where['a.is_read'] = trim(input('get.is_read'));
        if (!isEmpty($_GET['type'])) $where['a.type'] = trim(input('get.type'));

        $rows = $model->getList($where);
        $this->assign('rows', $rows);
        $this->assign('pageHTML', $rows->render());
        $this->assign('title', '用户反馈');
        return view();
    }


    /**
     * 用户反馈处理
     */
    public function op_edit()
    {
        if (request()->isPost()) {
            $id = input('post.id');
            $data = Db::name('opinion')->where('oid', $id)->update(['status' => 1, 'update_time' => time()]);
            if ($data !== false) $this->success('操作成功', url('message/opinion'));
            $this->error('操作失败');
        }

        $id = Request::instance()->param('id');
        $model = new Opinion();
        $data = $model->getOne($id);
        //修改读取状态
        Db::table('cl_opinion')->where('oid', $id)->update(['is_read' => 1]);

        $this->assign('data', $data);
        $this->assign('title', '用户反馈');
        return view();
    }

    public function op_del()
    {
        $id = input('id');
        $result = Db::name('cl_opinion')->delete($id);
        if ($result !== false) {
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }


}