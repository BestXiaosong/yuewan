<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/6 0006
 * Time: 14:58
 */

namespace app\index\controller;


class Vod extends User
{
    //回放列表
    public function index(){
        $model = new \app\common\model\Vod();
        $cate = $model->getCate(array('status'=>1));
        $cid = $this->request->param('cid');
        $info = $this->httpPost(array(),'/index/getUser');
        $url = '/Index/vodlist';
        $data = $this->httpPost(array('cid'=>$cid),$url);
        $url = '/index/getBanner';
        $result = $this->httpPost(array('cid'=>10,'num'=>3),$url);
        $this->assign('cid',$cid);
        $this->assign('ad',$result);
//        dump($info);exit;
        return view('',[
            'cate'=>$cate,
            'data'=>$data,
            'info'=>$info,
        ]);
    }


    public function detail()
    {
        $pid = $this->request->param('pid');
        $result = $this->httpPost(array('pid'=>$pid),'/Index/click');
        if($this->token == ''){
            $this->assign('no_login',1);
        }else{
            $this->assign('no_login',0);
        }
//        dump($result);exit;
        return view('',[
            'result'=>$result,
        ]);
    }

}