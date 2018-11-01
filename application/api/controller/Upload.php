<?php

namespace app\api\controller;

use Qiniu\Auth as Auth;
use Qiniu\Storage\UploadManager;
use think\Controller;
use think\Db;

class Upload extends User
{
    /**
     * @return \think\response\Json
     * @throws \Exception
     * 图片上传到七牛
     */
    public function index()
    {
        if(request()->isPost()){
            $qiniu_config = config('qiniu');
            $accessKey = $qiniu_config['ACCESSKEY'];
            $secretKey = $qiniu_config['SECRETKEY'];
            $file = request()->file("file");
//            api_return(0,'test',['file'=>$file,'files'=>request()->file(),'test'=>$_FILES]);
            if (empty($file)) api_return(0,'文件为空');
            $info = $file->validate(['size'=>1024*1024*3,'ext'=>'jpeg,jpg,png,gif']);
            if (!$info) api_return(0,$file->getError());
            $filePath = $file->getRealPath();
            $ext = pathinfo($file->getInfo('name'), PATHINFO_EXTENSION);  //后缀
            $key = substr(md5($file->getRealPath()) , 0, 5). date('YmdHis') . rand(0, 9999) .'.'.$ext;
            $auth = new Auth($accessKey, $secretKey);
            $token = $auth->uploadToken($qiniu_config['bucket']);
            $uploadMgr = new UploadManager();
            list($ret, $err) = $uploadMgr->putFile($token,$key,$filePath);
            if ($err !== null) {
                api_return(0,'上传失败');
            } else {
//                api_return(0,'test',['ret'=>$ret,'err'=>$err]);
                api_return(1,'上传成功',config('qiniu.domain').'/'.$ret['key']);
            }
        }else{
            api_return(0,'上传失败');
        }
    }


    public function token()
    {
        $qiniu_config = config('qiniu');
        $accessKey = $qiniu_config['ACCESSKEY'];
        $secretKey = $qiniu_config['SECRETKEY'];
        $auth = new Auth($accessKey, $secretKey);
        $token = $auth->uploadToken($qiniu_config['bucket']);
        api_return(1,"获取成功",array('token'=>$token,'url'=>config('qiniu.domain')));
    }



    /**
     * 回放视频上传获取token
     */
    public function file()
    {
        if(request()->isPost()){
            $data = input('post.');
            $ext  = ['mp4','avi','m3u8'];
            if (!in_array($data['ext'],$ext)) api_return(0,'文件类型错误');
            $user = Db::name('users')->where('user_id',$this->user_id)->field('bucket_space,use_space')->find();
            $size = bcdiv($data['size'],1024,2);//获得GB单位数据
            if ($size == 0){
                $size = 0.01;
            }
            if ($user['bucket_space'] < ($size + $user['use_space'])){
                api_return(0,'空间不足');
            }
            $qiniu_config = config('qiniu');
            $accessKey = $qiniu_config['ACCESSKEY'];
            $secretKey = $qiniu_config['SECRETKEY'];
            $auth = new Auth($accessKey, $secretKey);
            $token = $auth->uploadToken($qiniu_config['bucket']);
            api_return(1,'获取成功',array('token'=>$token,'url'=>config('qiniu.domain')));
        }else{
            api_return(0,'获取失败');
        }
    }





    /**
     * @return \think\response\Json
     * tp5图片上传到本地
     */
    public function index1()
    {
        if(request()->isPost()){
            $file = request()->file("file");
            if(empty($file)){
                api_return(0,'上传失败(101)');
            }
            $info = $file->move(ROOT_PATH.'public'.DS.'upload'.DS.'images');
            if ($info) {
                api_return(1,'上传成功',DS.'public'.DS.'upload'.DS.'images'.DS.$info->getSaveName());
            } else {
                api_return(0,'上传失败(102)');
            }
        }else{
            api_return(0,'上传失败(103)');
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