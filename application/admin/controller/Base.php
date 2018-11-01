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

class Base extends  Controller
{

    public function _initialize()
    {
        $session = session('user_id');
        if (empty($session)){
            echo "<script>top.location.href = '/login/login'</script>";exit;
        }
        $menuList = session('menuList');
        if(empty($menuList)){
            $menuList = $this->getMenuList();
            session('menuList',$menuList);
        }


        if ($session != 1){
            $url =strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', request()->controller())).'/'.strtolower(preg_replace('/(?<=[a-z])([A-Z])/', '_$1', request()->action()));

            //不需要验证权限的数组
            $array = ['index/index','demo/index','index/logout'];

            if (!in_array($url,$array)){
                $rules = session('rules');
                if (!in_array($url,$rules)){
                    $this->error('权限不足');
                };
            }
        }

        parent::_initialize();
        $this->assign('stamp',123);
    }



    //获取单独数据或列表用于添加或修改的返显或需要查询分类的
    protected function _show($db = '',$id = 0,$db2 = '',$field2 = '',$where = []){
        //分类表查询
        if (!empty($db2)){
            $result = Db::name($db2)->where($where)->field($field2)->select();
            $this->assign('result',$result);
        }
        //修改返显查询
        $get_id = input('id');

        if(is_numeric($get_id)){
            $data = Db::name($db)->where($id,$get_id)->find();
            if(!is_null($data)){
                $this->assign('data',$data);
            }
        }
    }

    //获取单独数据 对应配置分类  及用户昵称
    protected function _show_name($db = '',$id = 0,$db2 = '',$field2 = '',$where = []){
        //分类表查询
        if (!empty($db2)){
            $result = Db::name($db2)->where($where)->field($field2)->select();
            $this->assign('result',$result);
        }
        //修改返显查询
        $get_id = input('id');

        if(is_numeric($get_id)){
            $data = Db::name($db)
                ->alias('a')
                ->join('users u','u.user_id = a.user_id','LEFT')
                ->field('a.*,u.nike_name')
                ->where('a.'.$id,$get_id)->find();
            if(!is_null($data)){
                $this->assign('data',$data);
            }
        }
    }





    //修改时单独一条数据left join
    protected function _showJoin($db = '',$id = 0,$join = [],$field = '',$type = ''){
        $get_id = input('id');
        if(is_numeric($get_id)){
            $data = Db::name($db)->alias('a')->field($field,$type)->join($join)->where('a.'.$id,$get_id)->find();
            if(!is_null($data)){
                $this->assign('data',$data);
            }
        }
    }

    //修改时单独一条数据left join
    protected function special($db = '',$id = 0,$join = [],$field = '',$type = ''){
        $get_id = input('id');
        if(is_numeric($get_id)){
            $data = Db::name($db)->alias('a')->field($field,$type)->join($join)->where('a.'.$id,$get_id)->find();
            if(!is_null($data)){
                $this->assign('data',$data);
                if ($data['is_read'] == 0){
                    Db::name('opinion')->where('oid',$data['oid'])->update(['is_read'=>1]);
                }
            }
        }
    }



    //根据id查询多条数据
    protected function _shows($db = '',$id = '',$db2 = '',$field2 = ''){
        if (!empty($db2)){
            $result = Db::name($db2)->field($field2)->select();
            $this->assign('result',$result);
        }
        $get_id = input('id');
        if(is_numeric($get_id)){
            $data = Db::name($db)->where($id,$get_id)->select();
            if(!is_null($data)){
                $this->assign('data',$data);
            }
        }
    }


    //获取无关联表列表
    protected function _list($db = '',$order = '',$where = [],$field = '',$type = ''){
        if (isEmpty($where['status'])){
            $where['status'] = 1;
        }
        $rows = Db::name($db)->where($where)->order($order)->field($field,$type)->paginate(15,false,['query'=>request()->param()]);
        $this->assign('rows',$rows);
        $this->assign('pageHTML',$rows->render());
    }

    //不论状态查询数据
    protected function _list2($db = '',$order = '',$where = [],$field = '',$type = ''){
        $rows = Db::name($db)->where($where)->order($order)->field($field,$type)->paginate(15,false,['query'=>request()->param()]);
        $this->assign('rows',$rows);
        $this->assign('pageHTML',$rows->render());
    }

    /**
     * 获取菜单列表
     * @return array
     */
    public function getMenuList(){
        $uid = session('user_id');
        if($uid==1){
            $menu_list = $this->getAllMenu();
        }else {

            $group_ids = Db::name('auth_group_access')->where(array('uid' => $uid))->column('group_id');

            $group_rules = Db::name('auth_group')->where('id', 'in', $group_ids)->select();

            $rules = "";
            foreach ($group_rules as $k => $v) {
                if ($k == 0) {
                    $rules = $v['rules'];
                }
                $rules .= "," . $v['rules'];
            }
            $rules = explode(',', $rules);
            $rules = array_unique($rules);
            $rule_name = Db::name('auth_rule')->where('id', 'in', $rules)->column('name');
            $rule_use = [];
            foreach ($rule_name as $vv) {
                $rule_use[] = strtolower($vv);
            }

            session('rules',$rule_name);
            $menu_list = $this->getAllMenu();
            foreach ($menu_list as $k => $v) {
                //判断是否有主菜单权限
                $url = $v['control'];
                if (!in_array($url, $rule_use)) {
                    unset($menu_list[$k]);
                }else{
                    foreach ($menu_list[$k]['sub_menu'] as $m => $i){
                        //判断是否有子菜单权限
                        $href = $i['control'] . '/' . $i['act'];
                        if (!in_array($href, $rule_use)) {
                            unset($menu_list[$k]['sub_menu'][$m]);
                        }
                    }
                }
            }

        }
        return $menu_list;
    }
    /**
     * 菜单列表详情
     * @return array
     */
    public function getAllMenu(){
        $data = Db::name('right')->where(array('pid'=>0,'status'=>1))->order('sort asc')->select();
        foreach($data as $k=>$v){
            $child = Db::name('right')->where(array('pid'=>$v['id'],'status'=>1))->select();
            if($child){
                $data[$k]['sub_menu'] = $child;
            }
        }
        return $data;
    }



}