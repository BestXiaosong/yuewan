<?php

namespace app\api\controller;

use app\common\model\Banner;
use app\common\model\Helpers;
use think\Db;
use app\common\model\Users;

class Index extends Base
{


    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $ip = request()->ip();
        echo 'api:';
        echo  $ip;exit('endTest');
    }



    /**
     * 获取banner及广告图
     */
    public function getBanner()
    {
        $model = new Banner();
        $where['status'] = 1;
        $where['cid'] = input('post.cid');
        $limit = is_numeric(input('post.num')) ? input('post.num') : 6;
        $rows = $model->getBanner($where,$limit);
        if (!empty($rows)) api_return(1, '获取成功', $rows);
        api_return(0, '暂无数据');
    }





    /**
     * 发送短信
     */
    public function sms()
    {
        if (request()->isPost()){
            $phone = input('post.phone');
            $has   = cache('code_num'.$phone);
            $endToday = strtotime(date('Y-m-d 23:59:59'));
            $time = $endToday - time();
            if ($has){
                if ($has['num'] >= 15)api_return(0,'短信发送过多');
                if ($has['time']+60 > time())api_return(0,'短信发送过于频繁,请一分钟后重试');
            }else{
                cache('code_num'.$phone,['num'=>1,'time'=>time()],$time);
            }
            $result = sendSms($phone);
            if ($result){
                cache('code_num'.$phone,['num'=>$has['num']+1,'time'=>time()],$time);
                api_return(1,'发送成功');
            }else{
                api_return(0,'发送失败');
            }
        }
        api_return(666,999);
    }


    //生成微信分享二维码

    public function shareQrcode(){
        $data = request()->param('url');
        $res = code($data);
        api_return(1,'成功',$res);
    }



    /**
     * 版本控制
     */
    public function version()
    {
        $new = Db::name('version')->field('versionCode,versionName,force,url,detail')->order('id desc')->cache(300)->find();
        if (!empty($new)) api_return(1,'获取成功',$new);
        api_return(0,'系统错误');
    }





    /**
     * 获取说明文档
     */
    public function explain()
    {
        $id = input('post.id');
        if (!is_numeric($id)) api_return(0,'参数错误');
        $data = Db::name('explain')->where('id',$id)->value('content');
        if (!empty($data)) api_return(1,'获取成功',$data);
        api_return(0,'暂无数据');
    }


    /**
     * 获取融云token
     */
    public function token()
    {
        $token = $this->R_token($this->role_id);
        if ($token !== false) api_return(1,'获取成功',$token);
        api_return(0,'服务器繁忙,请稍后重试');
    }




    public function img()
    {
        getVideoCover('http://file.51soha.com/vod0v21072460.mp4');
    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 广场
     */
    public function square()
    {

        $data = input('post.');

        $map = [];

        if ($data['sex']){

            $map['a.sex'] = $data['sex'];

        }

        $model = new Users();

        $rows  = $model->rows($map);

        api_return(1,'获取成功',$rows);

    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 分类获取
     */
    public function cate(int $type)
    {

        $model = new \app\common\model\Skill();

        $map['type'] = $type;

        $rows  = $model->getRows($map);

        api_return(1,'获取成功',$rows);

    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 分类详情
     */
    public function detail(int $id)
    {

        $map['skill_id'] = $id;
//        $map['status']   = 1;

        $model = new \app\common\model\Skill();

        $data  = $model->getDetail($map);

        api_return(1,'获取成功',$data);

    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 获取帮助文章
     */
    public function helpers()
    {
        $model = new Helpers();
        $rows  = $model->getRows(['status'=>1]);
        api_return(1,'获取成功',$rows);
    }
    
    
    
    
}
