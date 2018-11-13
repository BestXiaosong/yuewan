<?php
/**
 * Created by xiaosong
 * E-mail:306027376@qq.com
 * Date: 2018/11/12
 * Time: 13:42
 */

namespace app\admin\controller;


use app\common\logic\UserLevel;
use app\common\model\Job;
use app\common\model\RechargeConfig;
use think\Db;

class System extends Base
{

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 充值配置列表
     */
    public function recharge()
    {

        $map   = [];
        $model = new RechargeConfig();
        $rows  = $model->getList($map);
        $this->assign([
            'rows'=>$rows,
            'pageHTML'=>$rows->render(),
            'title' => '充值配置列表',
        ]);
        return view();
    }


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 充值配置编辑
     */
    public function edit_recharge()
    {
        if(request()->isPost()){
            $data   = input('post.');
            $model  = new \app\common\logic\RechargeConfig();
            $result = $model->saveChange($data);
            if($result !== false){
                $this->success('操作成功',url('/system/recharge'));
            }
            $this->error($model->getError());
        }
        $this->_show('recharge_config','r_id');
        $this->assign('title','充值配置编辑');
        return view();
    }


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * 状态修改
     */
    public function change_recharge()
    {
        $data = input();
        $result = Db::name('recharge_config')->update(['status'=>$data['type'],'r_id'=>$data['id']]);
        if($result !== false){
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 工作列表
     */
    public function job()
    {

        $model = new Job();

        $data  = $model->where('pid',0)->order('sort')->select();
        
        $pid = 0;

        if (!isEmpty(input('pid'))) $pid = input('pid');

        $rows = $model->cate($pid);

        $this->assign([
            'title' => '工作列表',
            'rows' => $rows,
            'data' => $data,
        ]);

        return view();

    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 工作编辑
     */
    public function jobEdit()
    {

        if (request()->isPost()){
            $data = input('post.');

            $model = new \app\common\logic\Job();

            $result = $model->saveChange($data);

            if ($result){
                $this->success('操作成功');
            }
            $this->error($model->getError());
        }

    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 会员等级
     */
    public function level()
    {

        $rows = Db::name('user_level')->order('level')->select();

        $this->assign([
            'title' => '会员等级',
            'rows'  => $rows,
        ]);

        return view();
    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 会员等级编辑
     */
    public function levelEdit()
    {
        if (request()->isPost()){
            $data = input('post.');

            $model = new UserLevel();

            $map['level'] = $data['level'];
            if (is_numeric($data['id'])){

                $map[$model->getPk()] = ['neq',$data['id']];

            }

            $has = $model->where($map)->value($model->getPk());

            if ($has){
                $this->error('等级已存在,请勿重复添加');
            }

            $result = $model->saveChange($data);


            if ($result){
                $this->success('操作成功');
            }
            $this->error($model->getError()??'系统错误');
        }
    }



}