<?php
namespace app\admin\controller;


use app\common\logic\Users;
use app\common\model\LoginLog;
use app\common\model\Role;
use think\Db;
use think\Validate;


class User extends Base
{

    function _initialize()
    {
        parent::_initialize();
        $this->assign('stamp',123);
    }

    public function index()
    {
        $where = [];
        if(!empty($_GET['nick_name'])){
            $where['nick_name'] = ['like','%'.trim(input('get.nick_name')).'%'];
        }
        if(!empty($_GET['phone'])){
            $where['phone'] = ['like','%'.trim(input('get.phone')).'%'];
        }
        if(!empty($_GET['user_id'])){
            $where['user_id'] = trim(input('get.user_id'));
        }

        $model = new \app\common\model\Users();
        $rows = $model->userList($where);
        $this->assign([
            'title' => '用户管理',
            'rows' => $rows,
            'pageHTML' => $rows->render(),
        ]);
        return view();
    }


    /**
     * @return \think\response\View
     * 用户信息编辑
     */
    public function user_edit()
    {
        if(request()->isPost()){
            $data  = input('post.');
            $model = new Users();
            $phone = Db::name('users')
                ->where('phone',$data['phone'])
                ->where('user_id','<>',$data['id'])
                ->value('user_id');
            if ($phone){
                $this->error('该手机号已注册');
            }
//            dump($data);exit;
            $result = $model->saveChange($data);
            if($result){
                $this->success('操作成功',url('user/index'));
            }
            $this->error($model->getError());
        }
        $this->_show('users','user_id');
        $this->assign([
            'title' => '用户信息编辑',
        ]);
        return view();
    }

    public function change()
    {
        $data = input();
        $result = Db::name('users')->update(['status'=>$data['type'],'user_id'=>$data['id']]);
        if($result !== false){
            //改变用户状态后清空前端token
            cache('token_'.hashid($data['id']),null);
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }

    public function user_pass()
    {
        $id     = input('param.id');
        $result = Db::name('users')->where('user_id',$id)->delete();
        if ($result){
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }








    /**
     * 登陆日志列表
     */
    public function log()
    {
        $where = [];
        if(!empty($_GET['nick_name'])){
            $where['c.nick_name'] = ['like','%'.trim(input('get.nick_name')).'%'];
        }
        $model = new LoginLog();
        $rows = $model->getList($where);
        $this->assign([
            'rows' => $rows,
            'pageHTML' => $rows->render(),
        ]);
        return view();
    }

    public function del()
    {
        $id     = input('param.id');
        $result = Db::name('login_log')->delete($id);
        if ($result){
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }

    public function cate()
    {
        $this->_list2('users_category','create_time DESC');
        $this->assign([
            'title'=>'用户类型'
        ]);
        return view();
    }

    public function cate_edit()
    {
        if (request()->isPost()){
            $data   = input('post.');
            $result = Db::name('users_category')->strict(false)->where('cid',$data['id'])->update($data);
            if($result!==false){
                $this->success('保存成功','/user/cate');
            }
            $this->error('操作失败');
        }
        $this->_show('users_category','cid');
        return view();
    }

    /**
     * 角色列表
     */
    public function role()
    {
        $where = [];
        if(!empty($_GET['role_name'])){
            $where['a.role_name'] = ['like','%'.trim(input('get.role_name')).'%'];
        }
        if(!isEmpty($_GET['status'])){
            $where['a.status'] = trim(input('get.status'));
        }
        $where['u.status'] = 1;
        $model = new Role();
        $rows = $model->getList($where);
        $this->assign([
            'title' => '角色列表',
            'rows' => $rows,
            'pageHTML' => $rows->render(),
            'status' => array_key($this->status,'status'),
        ]);
        return view();
    }

    protected $status = [
      ['status'=>1,'name'=>'正常'],
      ['status'=>0,'name'=>'禁用'],
      ['status'=>2,'name'=>'拍卖中'],
    ];

    public function role_change()
    {
        $data = input();
        $result = Db::name('role')->update(['status'=>$data['type'],'role_id'=>$data['id']]);
        if($result !== false){
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }

    public function role_del()
    {
        $id     = input('param.id');
        $result = Db::name('role')->where('role_id',$id)->delete();
        if ($result){
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }

    /**
     * 角色信息编辑
     */
    public function role_edit()
    {
        if(request()->isPost()){
            $data  = input('post.');
            $model = new \app\common\logic\Role();
            $name = $model
                ->where('role_name',$data['role_name'])
                ->where('role_id','<>',$data['id'])
                ->value('role_id');
            if ($name){
                $this->error('该昵称已存在');
            }
            $result = $model->saveChange($data);
            if($result !== false){
                $this->success('操作成功',url('user/role'));
            }
            $this->error($model->getError());
        }
        $this->_show('role','role_id');
        $this->assign([
            'title' => '角色信息编辑',
        ]);
        return view();
    }
    /**
     * 用户资金明细列表
     */
    public function detail()
    {
        $id = $this->request->param('id');
        $where = [];
        $where['user_id'] = $id;
        $where['status'] = 1;
        if(!empty($_GET['start'])&&empty($_GET['end'])){
            $where['create_time'] = ['>=',strtotime($_GET['start'])];
        }elseif(!empty($_GET['end'])&&empty($_GET['start'])){
            $where['create_time'] = ['<=',strtotime($_GET['end'])];

        }elseif(!empty($_GET['start'])&&!empty($_GET['end'])){
            $where['create_time'] = ['between',array(strtotime($_GET['start']),strtotime($_GET['end']))];
        }
//        print_r($_GET);exit;
        $model = new \app\common\model\MoneyDetail();
        $rows = $model->money_detail_list($where);
//        echo $rows;exit;
//        print_r($rows);exit;
        $this->assign([
            'title' => '资金明细',
            'rows' => $rows,
            'pageHTML' => $rows->render(),
        ]);
        return view('money_detail');
    }


    /**
     * 角色推荐设置
     */
    public function top()
    {
        $data = input();
        $result = Db::name('role')->update(['top'=>$data['val'],'update_time'=>time(),'role_id'=>$data['id']]);
        if($result !== false){
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 发送极光推送
     */
    public function push()
    {
        if (request()->isPost()){
            $data = input('post.');
            $push_id = 'all';
            if ($data['phone']){
                $push_id = Db::name('users')->where('phone',$data['phone'])->value('j_push_id');
                if (empty($push_id)) $this->error('推送失败,用户无推送id');
            }
            $result = j_push($data['title'],$push_id);
            if ($result){
                $this->success('发布成功');
            }
        }
        $this->error('系统繁忙,请稍后重试');
    }



}
