<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/3
 * Time: 9:51
 */

namespace app\index\controller;
use app\common\model\Banner;
use think\Db;

class Web extends User
{

    /**
     * pc首页
     */
    public function index()
    {

        if (input('id')){
            $row['id'] = input('id');
        }else{
            $id = Db::name('room')->where(['status'=>1,'play_status'=>1])->order('top','desc')->value('room_id');
            $row['id'] = hashid($id);
        }

        $data = $this->httpPost($row,'/index/joinRoom');
//        if ($data === false){dump($this->errorMsg);dump($this->json);exit();}

        $top = $this->httpPost(['type'=>'hot' ],'/index/top');

        $official=$this->httpPost(['type'=>'official' ],'/index/top');

        $hpbanner = Banner::where(['cid'=>8,'status'=>1])->find();
        $dbbanner=Banner::where(['cid'=>9,'status'=>1])->field('img,url')->select();
        $roomlist=$this->httpPost('','/index/roomList');

        $newstop=$this->httpPost('','/index/newslist');

        $newlist=$this->httpPost(['type'=>'pc'],'/index/newslist');

        $zdnew=$newstop[0];
        $newtopic=$this->httpPost('','/index/topic');

        $type=$this->httpPost(['top'=>1],'/index/cate');


        $this->assign([
            'data' => $data,
            'top'=>$top,
            'official'=>$official,
            'type'=>$type,
            'new'=>$newlist,
            'zdnew'=>$zdnew,
            'newtopic'=>$newtopic,
            'hp'=>$hpbanner,
            'db'=>$dbbanner,
            'typecount'=>db('play_category')->where('status',1)->count(),
            'room'=>$roomlist['data'],
            'wx_qrcode'=>db('extend')->value('wx_qrcode'),
            'download'=>db('explain')->where(['id'=>9])->value('content')
        ]);
        return view();
    }

    /**
     * 直播播放页
     */
    public function play()
    {
        return view();
    }


    /**
     * app下载页面
     */

    public function download(){
        $android = db('explain')->where(['id'=>14])->value('content');
        $ios = db('explain')->where(['id'=>15])->value('content');
        if($this->request->isMobile()){
            return view('wap/download',[
                'android'=>$android,
                'ios'=>$ios,
            ]);
        }else{
            return view('',[
                'android'=>$android,
                'ios'=>$ios,
            ]);
        }

    }

}