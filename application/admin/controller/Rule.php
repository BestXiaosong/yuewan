<?php
namespace app\admin\controller;
use app\common\logic\Explain;
use app\common\model\Admins;
use app\common\model\AuthGroup;
use app\common\model\AuthGroupAccess;
use app\common\model\AuthRule;
use app\common\model\Coin;
use app\common\model\Version;
use think\Db;
use think\Request;

/**
 * 后台权限管理
 */
class Rule extends \app\admin\controller\Base {

//******************权限***********************
    /**
     * 权限列表
     */
    public function index(){
        $AuthRule = new AuthRule();
        $data =  $AuthRule->getTreeData('tree','id','title');

        $assign=array(
            'data'=>$data
            );
        $this->assign($assign);
        return view();
    }

    /**
     * 说明文档
     */
    public function explain()
    {
        if (request()->isPost()){
            $model = new Explain();
            $content = input('post.content/a');
            $id = input('post.id/a');
            foreach ($content as $k => $v){
                $data[$k]['id'] = $id[$k];
                $data[$k]['content'] = $v;
            }
            $result = $model->change($data);
           if ($result) $this->success('修改成功');
           $this->error($model->getError());
        }
        $rows = Db::name('explain')->select();
        $this->assign([
            'title' => '说明文档',
            'rows' => $rows,
        ]);
        return view();
    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 货币详情
     */
    public function coin()
    {

        $model = new Coin();
        $rows  = $model->getList();
        $this->assign([
            'title' => '货币管理',
            'rows' => $rows,
        ]);
        return view();
    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 货币编辑
     */
    public function coin_edit()
    {
        if (\request()->isPost()){
            $data = \request()->only('id,coin_name,red_mini,length,status,img,sort');
            $model = new \app\common\logic\Coin();
            $result = $model->saveChange($data);
            if ($result){
                $this->success('修改成功',url('rule/coin'));
            }else{
                $this->error($model->getError());
            }
        }
        $id   = input('id');
        $data = Db::name('coin')->where('coin_id',$id)->find();
        $this->assign([
            'data' => $data,
        ]);
        return view();
    }

    public function ex_add()
    {
        $data = input('post.');
        if (empty($data['msg'])) $this->error('介绍不能为空');
        if (empty($data['content'])) $this->error('内容');
        $res = Db::name('explain')->insert($data);
        if ($res) $this->success('添加成功');
        $this->error('添加失败');
    }


    /**
     * 添加权限
     */
    public function add(Request $request){
        $data = $request->post();
        unset($data['id']);
        $AuthRule = new AuthRule();

        $result=$AuthRule->addData($data);
        if ($result) {
            $this->success('添加成功',url('Rule/index'));
        }else{
            $this->error('添加失败');
        }
    }

    /**
     * 修改权限
     */
    public function edit(Request $request){
        $data = $request->post();
        $AuthRule = new AuthRule();
        $map=array(
            'id'=>$data['id']
            );
        $result=$AuthRule->editData($map,$data);
        if ($result) {
            $this->success('修改成功',url('Rule/index'));
        }else{
            $this->error('修改失败');
        }
    }

    /**
     * 删除权限
     */
    public function delete(Request $request){
        $id = $request->param('id');
        $map=array(
            'id'=>$id
            );
        $AuthRule = new AuthRule();
        $result=$AuthRule->deleteData($map);
        if($result){
            $this->success('删除成功',url('Rule/index'));
        }else{
            $this->error('请先删除子权限');
        }

    }
//*******************用户组**********************
    /**
     * 用户组列表
     */
    public function group(){
        $AuthGroup = new AuthGroup();
        $data= $AuthGroup->select();
        $assign=array(
            'data'=>$data
            );

        $this->assign($assign);
       return view();
    }

    /**
     * 添加用户组
     */
    public function add_group(Request $request){
        $data = $request->post();
        unset($data['id']);
        $AuthRule = new AuthGroup();
        $result=$AuthRule->addData($data);
        if ($result) {
            $this->success('添加成功',url('Rule/group'));
        }else{
            $this->error('添加失败');
        }
    }

    /**
     * 修改用户组
     */
    public function edit_group(Request $request){
        $data = $request->post();
        $map=array(
            'id'=>$data['id']
            );
        $AuthRule = new AuthGroup();
        $result=$AuthRule->editData($map,$data);
        if ($result) {
            $this->success('修改成功',url('Rule/group'));
        }else{
            $this->error('修改失败');
        }
    }

    /**
     * 删除用户组
     */
    public function delete_group(Request $request){
        $id = $request->param('id');
        $map=array(
            'id'=>$id
            );
        $AuthRule = new AuthGroup();
        $result=$AuthRule->deleteData($map);
        if ($result >= 0) {
            $this->success('删除成功',url('Rule/group'));
        }else{
            $this->error('删除失败');
        }
    }

//*****************权限-用户组*****************
    /**
     * 分配权限
     */
    public function rule_group(Request $request){
        $data = $request->post();
        if($data){
            $map=array(
                'id'=>$data['id']
                );
            $data['rules']=implode(',', $data['rule_ids']);
            unset( $data['rule_ids']);
            $AuthGroup = new AuthGroup();
            $result=$AuthGroup->editData($map,$data);
            if ($result) {
                $this->success('操作成功',url('Rule/group'));
            }else{
                $this->error('操作失败');
            }
        }else{
            $id = $request->param('id');
            $AuthGroup = new AuthGroup();
            // 获取用户组数据
            $group_data=$AuthGroup->where(array('id'=>$id))->find();
            $group_data['rules']=explode(',', $group_data['rules']);
            // 获取规则数据
            $AuthRule = new AuthRule();
            $rule_data=$AuthRule->getTreeData('level','id','title');
            $assign=array(
                'group_data'=>$group_data,
                'rule_data'=>$rule_data
                );

           $this->assign($assign);
           return view();
        }

    }
//******************用户-用户组*******************
    /**
     * 添加成员
     */
    public function check_user(Request $request){
        $group_id  = $request->param('group_id');
        $group_name = Db::name('auth_group')->where(array('id'=>$group_id))->value('title');
        $AuthGroupAccess = new AuthGroupAccess();
        $uids = $AuthGroupAccess->getUidsByGroupId($group_id);

        // 判断用户名是否为空
        $user_data = Db::name('admins')->select();
        $assign=array(
            'group_name'=>$group_name,
            'uids'=>$uids,
            'user_data'=>$user_data,
            'group_id'=>$group_id,

            );
        $this->assign($assign);
       return view();
    }

    /**
     * 添加用户到用户组
     */
    public function add_user_to_group(Request $request){
        $data  = $request->param();
        $map=array(
            'uid'=>$data['uid'],
            'group_id'=>$data['group_id']
            );
        $AuthGroupAccess = new AuthGroupAccess();
        $count=Db::name('auth_group_access')->where($map)->count();
        if($count==0){
            $AuthGroupAccess->addData($data);
        }
        $this->success('操作成功',url('Rule/check_user',array('group_id'=>$data['group_id'])));
    }

    /**
     * 将用户移除用户组
     */
    public function delete_user_from_group(Request $request){
        $data  = $request->param();
        $AuthGroupAccess = new AuthGroupAccess();
        $result=$AuthGroupAccess->deleteData($data);
        if ($result) {
            $this->success('操作成功',url('admin_user_list'));
        }else{
            $this->error('操作失败');
        }
    }

    /**
     * 管理员列表
     */
    public function admin_user_list(){
        $AuthGroupAccess = new AuthGroupAccess();
        $data=$AuthGroupAccess->getAllData();
        $assign=array(
            'data'=>$data
            );
        $this->assign($assign);
       return view();
    }

    /**
     * 添加管理员
     */
    public function add_admin(Request $request){
        $data = $request->post();
        if($data){
            if(!isset($data['group_ids'])){
                $this->error('请选择管理组');exit;
            }
            $daa = $data;
            unset($daa['group_ids']);
            $User = new Admins();
            $User->save($daa);

            $id = $User->user_id;

            $daa_s['user_id'] = $id;
//            $daa_s['id']  = $id;
            $User->save($daa_s);
            $AuthGroupAccess = new AuthGroupAccess();
            if($id){
                if (!empty($data['group_ids'])) {
                    foreach ($data['group_ids'] as $k => $v) {
                        $group=array(
                            'uid'=>$id,
                            'group_id'=>$v
                            );
                        $AuthGroupAccess->addData($group);
                    }
                }
                // 操作成功
                $this->success('添加成功',url('Rule/admin_user_list'));
            }else{
                // 操作失败
                $this->error('操作失败');
            }
        }else{
            $data=Db::name('auth_group')->select();
            $assign=array(
                'data'=>$data
                );
            $this->assign($assign);
           return view();
        }
    }

    /**
     * 修改管理员
     */
    public function edit_admin(Request $request){
        $data = $request->post();

        if($data){
            // 组合where数组条件
            $uid=$data['id'];
            $map=array(
                'user_id'=>$uid
                );
            // 修改权限
            $AuthGroupAccess = new AuthGroupAccess();
            $User = new Admins();
            $AuthGroupAccess->deleteData(array('uid'=>$uid));
            foreach ($data['group_ids'] as $k => $v) {
                $group=array(
                    'uid'=>$uid,
                    'group_id'=>$v
                    );
                $AuthGroupAccess->addData($group);
            }
            $data1['user_name']  = $data['user_name'];
            $data1['status']     = $data['status'];
            $data1['phone']      = $data['phone'];
            $data1['nick_name']  = $data['nick_name'];
            $data1['header_img'] = $data['header_img'];
            $data1['is_service'] = $data['is_service'];
            // 如果修改密码则md5
            if (!empty($data['password'])) {
                $data1['password']=md5($data['password'].'tz');
            }

            $result=$User->editData($map,$data1);
            if($result){
                // 操作成功
                $this->success('编辑成功',url('Rule/edit_admin',array('id'=>$uid)));
            }else{

                if (empty($error_word)) {
                    $this->success('编辑成功',url('Rule/edit_admin',array('id'=>$uid)));
                }else{
                    // 操作失败
                    $this->error('编辑失败');
                }

            }
        }else{
            $id  = $request->param('id');
            // 获取用户数据
            $user_data=Db::name('admins')->find($id);
            // 获取已加入用户组
            $group_data=Db::name('auth_group_access')
                ->where(array('uid'=>$id))
                ->select();
            foreach($group_data as $k=>$v){
                $group_ids[] = $v['group_id'];
            }
            // 全部用户组
            $data=Db::name('auth_group')->select();
            $assign=array(
                'data'=>$data,
                'user_data'=>$user_data,
                'group_data'=>$group_ids
                );

            $this->assign($assign);
           return view();
        }
    }
    /**
     * 删除管理员
     */
    public function del_admin(Request $request){
        $id  = $request->param('id');
        if($id==1){
            $this->error('超级管理员不能删除');
        }
        // 获取用户数据
        $result=Db::name('admins')->delete($id);
        if ($result) {
            $this->success('操作成功',url('Rule/admin_user_list'));
        }else{
            $this->error('操作失败');
        }
    }

    /**
     * 系统设置
     */
    public function extend()
    {
        if (request()->isPost()){
            $row = input('post.');
            $validate = validate('base');
            if (!$validate->scene('extend')->check($row)) api_return(0,$validate->getError());
            $result = Db::name('extend')->where('id',1)->update($row);
            if ($result !== false)$this->success('修改成功');
            $this->error('修改失败');
        }
        $data = Db::name('extend')->find();
        $this->assign([
            'data' => $data,
            'title' => '系统设置',
        ]);
        return view();
    }

    /**
     * 版本控制
     */
    public function version()
    {
        $where = [];
        $model = new Version();
        $rows = $model->getList($where);
        $this->assign([
            'title' => '版本控制',
            'rows' => $rows,
            'pageHTML' => $rows->render(),
        ]);
        return view();
    }


    public function version_edit()
    {
        if(request()->isPost()){
            $data  = input('post.');
            $model = new Version();
            $result = $model->saveChange($data);
            if($result !== false){
                $this->success('操作成功',url('/rule/version'));
            }
            $this->error($model->getError());
        }

        $this->_show('version','id');
        $this->assign([
            'title' => '版本控制',
        ]);
        return view();
    }


    /**
     * 改变推荐状态
     */
    public function top()
    {
        $data = input();
        $model = new Version();
        $result = $model->update(['force'=>$data['val'],'id'=>$data['id']]);
        if($result !== false){
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }


    public function change()
    {
        $data = input();
        $model = new Version();
        $result = $model->update(['status'=>$data['type'],'id'=>$data['id']]);
        if($result !== false){
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }

//    public function del()
//    {
//        $id     = input('id');
//        $result = Db::name('version')->delete($id);
//        if ($result !== false){
//            $this->success('删除成功');
//        }
//        $this->error('删除失败');
//    }


}
