<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6 0006
 * Time: 10:55
 */

namespace app\admin\controller;


use app\common\model\Helpers;
use app\common\model\Opinion;
use think\Db;
use think\Request;

class Message extends Base
{
    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 帮助文档列表
     */
    public function helpers()
    {

        $map   = [];
        $model = new Helpers();
        $rows  = $model->getList($map);

        $this->assign([
            'title'=>'帮助文档列表',
            'pageHTML'=>$rows->render(),
            'rows'=>$rows
        ]);
        return view();

    }

    public function change()
    {
        $field = input('str')?input('str'):'status';
        $this->_change('helpers',$field);
    }

    /**
     * 帮助文档编辑
     */
    public function edit()
    {

        $this->_edit('helpers','帮助文档编辑',url('helpers'),'helpers');

        return view();
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