<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/30 0030
 * Time: 16:39
 */

namespace app\admin\controller;



class UserCheck extends Base
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
        $model = new \app\common\model\UserId();
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

    public function user_check()
    {
        if(request()->isPost()){
            $data   = input('post.');
            $model  = new \app\common\logic\UserId();
            $result = $model->saveChange($data);
            if($result !== false){
                $this->success('操作成功',url('/user_check/index'));
            }
            $this->error($model->getError());
        }
        $this->_show('user_id','ID');
        $id = input('id');
        $look = input('look');
        if(!empty($look)){
            $this->assign('look',$look);
        }
        if(!empty($id)){
            $models = new \app\common\model\UserId();
            $results = $models->detail($id);
            $this->assign('result',$results);
        }
        $this->assign('title','认证审核');
        return view();
    }



}
