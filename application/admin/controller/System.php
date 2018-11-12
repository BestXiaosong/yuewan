<?php
/**
 * Created by xiaosong
 * E-mail:306027376@qq.com
 * Date: 2018/11/12
 * Time: 13:42
 */

namespace app\admin\controller;


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


}