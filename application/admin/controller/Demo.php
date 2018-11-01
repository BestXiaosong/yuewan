<?php
/**
 * Created by PhpStorm.
 * Users: Administrator
 * Date: 2018/3/10
 * Time: 9:08
 */
namespace app\admin\controller;
use think\Controller;
use think\Db;

class Demo extends  Base
{
    /**
     * @return \think\response\View
     * 不查询关联表时的数据列表
     */
    public function demoList()
    {
        $where = [];
        if(!empty($_GET['phone'])){
            $where['phone'] = ['like','%'.input('phone').'%'];
        }
        $this->_list('user_file','uid DESC',$where);
        return view();
    }

    public function index()
    {
        exit('后台首页');
    }
    /**
     * @return \think\response\View
     * 编辑
     */
    public function demoEdit()
    {
        if (IS_POST){
            $model = new StoreDetail();
            $data   = input('post.');
            $result = $model->saveChange($data);
            if($result!==false){
                $this->success('保存成功','/store/index');
            }
            $this->error($model->getError());
        }
        $this->_show('store_detail','sid');
        $this->assign('title','门店管理');
        return view();
    }

    /**
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * 批量删除
     */
    public function demoBatch()
    {
        if (IS_POST){
            $data = input('post.');
            $where['qid']  = ['in',$data['id']];
            $result = Db::table('questions')->where($where)->update(['status'=>0]);
            if ($result !== false){
                $this->success('删除成功');
            }
            $this->error('删除失败');
        }
    }

    /**
     * 单点登陆 单设备登陆
     */
    public function only()
    {
        Members::where('id',1)->update(['update_at'=>time(),'session_id'=>session_id()]);
        //单设备登陆验证
        $old_session = Db::name('member')->where('id',$this->uid)->value('session_id');
        if ($old_session != session_id()){
            session('member_info',null);
            $this->redirect('Login/index');
        }
    }



}