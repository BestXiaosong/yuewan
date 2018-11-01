<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/27 0027
 * Time: 15:29
 */

namespace app\admin\controller;


use app\common\model\RedHistory;
use think\Request;

class RedPackage extends Base
{
    protected $status = [
        1 => ['money_type' => '1' ,'msg' => '积分'],
        2 => ['money_type' => '2' ,'msg' => '比特币'],
        3 => ['money_type' => '3' ,'msg' => '以太币'],
        4 => ['money_type' => '4' ,'msg' => 'bcdn'],
    ];

    protected $red_type = [
        1=>['red_type'=>'1','msg'=>'普通红包'],
        2=>['red_type'=>'2','msg'=>'拼手气'],
    ];


    public function index()
    {

        $where = [];
        if (!empty($_GET['nick_name'])) {
            $where['nick_name'] = ['like', '%' . trim(input('get.nick_name')) . '%'];
        }
        if (!empty($_GET['phone'])) {
            $where['phone'] = ['like', '%' . trim(input('get.phone')) . '%'];
        }
        if (!empty($_GET['user_id'])) {
            $where['user_id'] = trim(input('get.user_id'));
        }
        if (!empty($_GET['cid'])) {
            $where['cid'] = trim(input('get.cid'));
        }
        if (!empty($_GET['money_type'])) {
            $where['money_type'] = trim(input('get.money_type'));
        }
        if (!empty($_GET['red_type'])) {
            $where['red_type'] = trim(input('get.red_type'));
        }
        $model = new \app\common\model\RedPackage();
        $rows = $model->getRedPackageList($where);
        $this->assign([
            'title' => '红包发送记录管理',
            'rows' => $rows,
            'pageHTML' => $rows->render(),
            'money_type'=>$this->status,
            'red_type'=>$this->red_type
        ]);
        return view();
    }


    public function edit()
    {
        if(Request::instance()->isPost()){
        }

        $id = Request::instance()->param('id');
        $model = new \app\common\model\RedPackage();
        $row = $model->getRedPackageOne($id);
        $redhistorymodel = new RedHistory();
        $histroy = $redhistorymodel->getHistoryByRedId($id);
        $this->assign([
            'title' => '红包详情',
            'data' => $row,
            'record'=>$histroy['data']
        ]);
        return view();

    }


}