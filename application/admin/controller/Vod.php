<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/9
 * Time: 10:17
 */

namespace app\admin\controller;


use app\common\model\VodCategory;
use app\common\logic\VodCategory as cate;
use app\common\model\Vod as model;
use Qiniu\Auth;
use Qiniu\Config;
use Qiniu\Storage\BucketManager;
use think\Db;
use think\Exception;

class Vod extends Base
{
    /**
     * 视频列表
     */
    public function index()
    {
        $where = [];
        if (!empty($_GET['nick_name'])) $where['u.nick_name'] = ['like','%'.trim(input('get.nick_name')).'%'];
        if (!isEmpty($_GET['status'])) $where['a.status'] = input('get.status');
        $model  = new model();
        $rows   = $model->getList($where);
        $this->assign([
            'rows' => $rows,
            'title' => '视频列表',
            'pageHTML' => $rows->render(),
        ]);
        return view();

    }

    /**
     * 改变推荐状态
     */
    public function top()
    {
        $data = input();
        $result = Db::name('vod')->update(['top'=>$data['val'],'update_time'=>date('Y-m-d H:i:s'),'pid'=>$data['id']]);
        if($result !== false){
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }

    public function edit()
    {
        if(request()->isPost()){
            $data = input('post.');
            $model = new \app\common\logic\Vod();
            $result = $model->saveChange($data);
            if($result !== false){
                $this->success('操作成功',url('index'));
            }
            $this->error($model->getError());
        }

        $auth  = new Auth(config('qiniu')['ACCESSKEY'], config('qiniu')['SECRETKEY']);
        $token = $auth->uploadToken(config('qiniu')['bucket']);
        $cate  = Db::name('play_category')->order('create_time DESC')->select();
        $domain = config('qiniu')['domain'];
        $this->_show('vod','pid');
        $this->assign([
            'title' => '点播编辑',
            'cate' => $cate,
            'token'=>$token,
            'domain'=> $domain,
        ]);
        return view();
    }

    public function change()
    {
        $data = input();
        $result = Db::name('vod')->update(['status'=>$data['type'],'pid'=>$data['id']]);
        if($result !== false){
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }

    protected function fileInfo($url = '')
    {
        $array['code'] = 0;
        if (empty($url)){
            $array['msg'] = '文件不存在';
        } else{
            $key = substr($url,(strripos($url,'/')+1));
            $accessKey = config('qiniu.ACCESSKEY');
            $secretKey = config('qiniu.SECRETKEY');
            $bucket = config('qiniu.bucket');
            $auth = new Auth($accessKey, $secretKey);
            $config = new Config();
            $bucketManager = new BucketManager($auth, $config);
            list($fileInfo, $err) = $bucketManager->stat($bucket, $key);
            if ($err) {
                $array['msg'] = '文件信息查询失败';
                $array['data'] = $err;
            } else {
                $array['code'] = 1;
                $array['msg'] = '文件信息查询成功';
                $array['size'] = round($fileInfo['fsize']/1024/1024,2);
                $array['data'] = $fileInfo;
            }
        }
        return $array;
    }

    public function delete()
    {
        $id = input('id');
        $file = Db::name('vod')->where(['pid'=>$id])->find();

        if (!$file){
            $this->error('视频不存在');
        }

        Db::startTrans();
        try{

            $result = Db::name('vod')->where('pid', $id)->delete();

            if ($result){

                delVod($file['play_url']);

            }

            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            $this->error('操作失败');
        }
        $this->success('删除成功');
    }







    /**
     * 点播分类
     */
    public function cate()
    {
        $where = [];
        if (!empty($_GET['cate_name']))$where['cate_name'] = ['like','%'.trim(input('get.cate_name')).'%'];
        if (!isEmpty($_GET['status']))$where['status'] = input('get.status');
        $model = new VodCategory();
        $rows = $model->getList($where);
        $this->assign([
            'rows' => $rows,
            'pageHTML' => $rows->render(),
            'title' => '点播分类管理',
        ]);
        return view();
    }

    public function cate_edit()
    {
        if(request()->isPost()){
            $data  = input('post.');
            $model = new cate();
            $cid = $model
                ->where('cate_name',$data['cate_name'])
                ->where('cid','<>',$data['id'])
                ->value('cid');
            if ($cid){
                $this->error('该分类已存在');
            }
            $result = $model->saveChange($data);
            if($result !== false){
                $this->success('操作成功',url('cate'));
            }
            $this->error($model->getError());
        }
        $this->_show('vod_category','cid');
        $this->assign([
            'title' => '点播分类编辑',
        ]);
        return view();
    }

    public function cate_change()
    {
        $data = input();
        $result = Db::name('vod_category')->update(['status'=>$data['type'],'cid'=>$data['id']]);
        if($result !== false){
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }

    public function cate_del()
    {
        $id     = input('id');
        $result = Db::name('vod_category')->delete($id);
        if ($result !== false){
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }



    public function expired_list(){
        $phone = $this->request->param('phone');
        if(!empty($phone)){
            $where['phone'] = $phone;
        }
        $where['space_time'] = 0;
        $model = new model();
        $result = $model->expired_list($where);
//        var_dump($result);exit;
        $this->assign([
            'rows' => $result,
            'title' => '空间过期用户列表',
        ]);
        return view();

    }

    public function del(){
        $user_id = $this->request->param('id');
        $model = new \app\common\logic\Vod();
        $result = $model->del($user_id);
//        var_dump($result);exit;
        if($result){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }
    }


}