<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/31 0031
 * Time: 13:40
 */

namespace app\admin\controller;


use think\Db;
use app\common\model\News as NewsModel;
use app\common\logic\News as NewsLogic;


class News extends Base
{
    /**
     * @return \think\response\View
     * 咨讯列表
     */
    public function index()
    {
        $where = [];
        if (!empty($_GET['title'])) {
            $where['n.title'] = ['like', '%' . trim(input('get.title')) . '%'];
        }
        $model = new NewsModel();
        $rows = $model->newsList($where,false);
        $this->assign([
            'title' => '咨询管理',
            'rows' => $rows,
            'pageHTML' => $rows->render(),
        ]);
        return view();

    }

    /**
     * @return \think\response\View
     * 咨讯编辑
     */
    public function edit()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $model = new NewsLogic();
            $result = $model->saveChange($data);
            if ($result !== false) {
                $this->success('操作成功', url('/news/index'));
            }
            $this->error($model->getError());
        }
        $where['status'] = 1;
        $this->_show('news', 'news_id');
        $this->assign([
            'title' => '咨讯编辑',
        ]);
        return view();
    }

    /**
     * @throws \think\Exception
     * 咨讯推荐
     */
    public function change()
    {
        $data = input();
        $result = Db::name('news')->update(['is_top'=>$data['is_top'],'news_id'=>$data['id']]);
        if($result !== false){
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }


    /**
     * @throws \think\Exception
     * 咨讯禁用
     */
    public function changeStatus()
    {
        $data = input();
        $result = Db::name('news')->update(['status'=>$data['status'],'news_id'=>$data['id']]);
        if($result !== false){
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }

    /**
     * @throws \think\Exception
     * 咨讯删除
     */
    public function del()
    {
        $id     = input('param.id');
        $result = Db::name('news')->where('news_id',$id)->delete();
        if ($result){
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }


}