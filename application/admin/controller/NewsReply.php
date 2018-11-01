<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/31 0031
 * Time: 13:40
 */

namespace app\admin\controller;


use think\Db;
use app\common\model\NewsReply as NewsReplyModel;
use app\common\logic\NewsReply as NewsReplyLogic;

class NewsReply extends Base
{
    /**
     * @return \think\response\View
     * 评论列表
     */
    public function index()
    {
        $where = [];
        if (!empty($_GET['content'])) {
            $where['content'] = ['like', '%' . trim(input('get.content')) . '%'];
        }

        if (!empty($_GET['news_id'])) {
            $where['news_id'] = trim(input('get.news_id'));
        }

        $model = new NewsReplyModel();
        $rows = $model->newsReplyList($where);
        $this->assign([
            'title' => '咨询评论列表',
            'rows' => $rows,
            'pageHTML' => $rows->render(),
        ]);
        return view();

    }

    /**
     * @return \think\response\View
     * 咨讯评论编辑
     */
    public function edit()
    {
        if(request()->isPost()){
            $data  = input('post.');
            $model = new NewsReplyLogic();
            $result = $model->saveChange($data);
            if($result !== false){
                $this->success('操作成功',url('/news_reply/index'));
            }

            $this->error($model->getError());
        }
        $where['status'] = 1;
        $this->_show('news_reply','reply_id');
        $this->assign([
            'title' => '咨讯编辑',
        ]);
        return view();
    }

    /**
     * @throws \think\Exception
     * 咨讯评论禁用
     */
    public function changeStatus()
    {
        $data = input();
        $result = Db::name('news_reply')->update(['status'=>$data['status'],'reply_id'=>$data['id']]);
        if($result !== false){
            $this->success('操作成功');
        }
        $this->error(Db::name('news')->getError());
    }


}