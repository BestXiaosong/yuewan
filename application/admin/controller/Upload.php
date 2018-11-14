<?php

namespace app\admin\controller;

use Qiniu\Config;
use Qiniu\Storage\BucketManager;
use think\Controller;
use Qiniu\Auth as Auth;
use Qiniu\Storage\UploadManager;
use think\Image;

class Upload extends Controller
{

    /**
     * 图片上传
     */
    public function index()
    {
        if(request()->isPost()){
            $qiniu_config = config('qiniu');
            $accessKey    = $qiniu_config['ACCESSKEY'];
            $secretKey    = $qiniu_config['SECRETKEY'];
            $file = request()->file('file');

            $info = $file->validate(['size'=>1024*1024*3,'ext'=>'jpeg,jpg,png,gif']);
            if (!$info) api_return(0,$file->getError());
            $filePath = $file->getRealPath();

            $fileName = input('fileName');
            if (!$fileName){
                $ext   = pathinfo($file->getInfo('name'), PATHINFO_EXTENSION);  //后缀
                $fileName   = substr(md5($file->getRealPath()) , 0, 5). date('YmdHis') . rand(0, 9999) . '.' . $ext;
            }

            $auth  = new Auth($accessKey, $secretKey);
            $token = $auth->uploadToken($qiniu_config['bucket']);
            $uploadMgr = new UploadManager();
            list($ret, $err) = $uploadMgr->putFile($token, $fileName, $filePath);
            if ($err !== null) {
                return json(['status' => 0, 'msg' => '上传失败']);
            } else {
                return json(['status' => 1, 'msg' => config('qiniu.domain').$ret['key']]);
            }
        }else{
            return json(['status' => 0, 'msg' => '上传失败']);
        }
    }








    /**
     * 七牛云图片删除
     */
    public function delete1()
    {
        $qiniu_config = config('qiniu');
        $accessKey = $qiniu_config['ACCESSKEY'];
        $secretKey = $qiniu_config['SECRETKEY'];
        $bucket = $qiniu_config['bucket'];

        $domain = config('qiniu.domain');

        $file = explode($domain,input('file'))[1];
        $auth = new Auth($accessKey, $secretKey);
        $config = new Config();
        $bucketManager = new BucketManager($auth,$config);
//        $bucketManager->delete($bucket,$file);
        $data = $bucketManager->delete($bucket,$file);
        if ($data == null || $data == ''){
            $this->success('删除成功');
        }
        $this->error('删除失败');
    }

    /**
     * 本地图片删除
     */
    public function delete()
    {
//        substr(input('file'),(strripos(input('file'),'/')+1));
        $file = '.'.DS.input('file');
//        dump($file);exit;
//        dump();exit;
        if (unlink($file)){
            $this->success();
        }else{
            $this->error();
        }
    }

    /**
     * @return \think\response\Json
     * tp5图片上传到本地处图片
     */
//    public function index()
//    {
//        //tp5图片上传到本地代码
//        if(IS_POST){
//            $file = request()->file("file");
//            if(empty($file)){
//                return json(['status' => 0, 'msg' => '上传失败']);
//            }
//            $ext = '.'.pathinfo($file->getInfo('name'),PATHINFO_EXTENSION);
//            $image = Image::open(request()->file("file"));
//            $name = md5(time().rand(10000,99999));
//            $return = 'public'.DS.'upload'.DS.'images'.DS.date('Ymd');
//            $dir = ROOT_PATH.$return;
//            if (!is_dir($dir)) mkdir($dir, 0755, true);
//            $path = $dir.DS.$name.$ext;
//            $image->thumb(150, 150)->save($path);
//            return json(['status' => 1, 'msg' => DS.$return.DS.$name.$ext]);
//        }else{
//            return json(['status' => 0, 'msg' => '上传失败']);
//        }
//    }

    /**
     * @return \think\response\Json
     * tp5图片上传到本地 不进行处理
     */
    public function index111()
    {
        //tp5图片上传到本地代码
        if(request()->isPost()){
            $file = request()->file("file");
            if(empty($file)){
                return json(['status' => 0, 'msg' => '上传失败']);
            }
            $image = Image::open(request()->file("file"));
            $image->thumb(150, 150)->save('./thumb.png');

            $info = $file->move(ROOT_PATH.'public'.DS.'upload'.DS.'images');
            if ($info) {
                return json(['status' => 1, 'msg' => DS.'public'.DS.'upload'.DS.'images'.DS.$info->getSaveName()]);
            } else {
                return json(['status' => 0, 'msg' => '上传失败']);
            }
        }else{
            return json(['status' => 0, 'msg' => '上传失败']);
        }
    }


    public function editor()
    {
        if(request()->isPost()){
            $qiniu_config = config('qiniu');
            $accessKey = $qiniu_config['ACCESSKEY'];
            $secretKey = $qiniu_config['SECRETKEY'];
            $file = request()->file('file');
            $filePath = $file->getRealPath();
            $ext = pathinfo($file->getInfo('name'), PATHINFO_EXTENSION);  //后缀

            $key =substr(md5($file->getRealPath()) , 0, 5). date('YmdHis') . rand(0, 9999) . '.' . $ext;

            $auth = new Auth($accessKey, $secretKey);

            $token = $auth->uploadToken($qiniu_config['bucket']);

            $uploadMgr = new UploadManager();
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
            if ($err !== null) {
                return json(['success' => false, 'msg' => '上传失败']);
            } else {
                return json(['success' => true,'msg'=>'成功', 'file_path' => 'http://'.config('qiniu.domain').'/'.$ret['key']]);
                //api_return(1,'上传成功',['url'=>'http://oyvfqfbc9.bkt.clouddn.com/'.$ret['key']]);
            }
        }else{
            return json(['success' => false, 'msg' => '上传失败']);
        }
    }


}