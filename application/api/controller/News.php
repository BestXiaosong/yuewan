<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/1 0001
 * Time: 9:08
 */

namespace app\api\controller;

use app\common\logic\NewsCollect;
use app\common\logic\NewsReply;
use app\common\model\News as NewsModel;
use app\common\logic\News as NewsLogic;
use app\common\model\NewsCollect as NewsCollectModel;
use think\Config;
use think\Request;

class News extends User
{
    //咨讯列表
    public function newsList()
    {
        $newModel = new NewsModel();
        $top = $newModel->getNewsTop();
        $newslist = $newModel->apiNewsList();
        $type = request()->param('type');
        $num = count($top);
        if ($type == 'pc') {
            if($num == 0){
                $num += 1;
            }else{
                unset($top[0]);
            }
            if ($num < 3) {
                foreach ($newslist['data'] as $k => $va) {
                    if ($num == 3) {
                        break;
                    }
                    $top[] = $va;
                    $num += 1;
                }
            }else{
                $arr[] = $top[1];
                $arr[] = $top[2];
//                unset($top);
                $top = $arr;
            }
        } else {
            if ($num < 5) {
                foreach ($newslist['data'] as $k => $va) {
                    if ($num == 5) {
                        break;
                    }
                    $top[] = $va;
                    $num += 1;
                }

            }

        }
        $top ? api_return(1, '获取成功', $top) : api_return(0, '获取失败');
    }


    //top文章
    public function topic()
    {
        $accessKey = Config::get('coding_access_key');
        $secretKey = Config::get('coding_secret_key');

        $httpParams = array(
            'access_key' => $accessKey,
            'date' => time(),

        );
        $last_id = Request::instance()->post('last_id');
        if (!empty($last_id)) {
            $httpParams['last_id'] = $last_id;
        }
        $signParams = array_merge($httpParams, array('secret_key' => $secretKey));

        ksort($signParams);
        $signString = http_build_query($signParams);

        $httpParams['sign'] = strtolower(md5($signString));

        $url = 'http://api.coindog.com/topic/list?' . http_build_query($httpParams);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $curlRes = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($curlRes, true);


        if (array_key_exists('status_code', $json)) {
            api_return(0, $json['message']);
        } else {
            if (empty($json)) {
                api_return(0, '没有数据');
            }
            foreach ($json as $k=>$v){
                if($k>8){
                    unset($json[$k]);
                }
            }
            api_return(1, '获取成功', $json);
        }

    }

    /**
     * 咨讯详情
     */

    public function newsDetails()
    {
        $news_id = Request::instance()->param('news_id');
        if (!is_numeric($news_id)) {
            api_return(0, 'news_id必须是数字');
        }

        $model = new NewsModel();
        $detail = $model->getNewsDetailsById($news_id);
        //利好利空状态
        $user_id = $this->user_id;
        $replymodel = new \app\common\model\NewsReply();
        $type = $replymodel->getReplyStatus($news_id, $user_id);
        $detail['type'] = $type;
        //收藏状态
        $collectmodel = new NewsCollectModel();
        $colect_status = $collectmodel->getCollectByUser($user_id, $news_id);
        if (!empty($colect_status)) {
            $detail['collect_id'] = $colect_status['collect_id'];
            $detail['collect_status'] = 1;
        } else {
            $detail['collect_id'] = 0;
            $detail['collect_status'] = 0;
        }

        //咨讯详情页推荐
        $recommend = $model->getNewsListByCid($detail->cid, 3);
        $arr = array();
        $arr['detail'] = $detail;
        $arr['list'] = $recommend;
        $detail ? api_return(1, '获取成功', $arr) : api_return(0, '获取失败');
    }

    //咨讯收藏

    public function collect()
    {
        $user_id = $this->user_id;
        $news_id = Request::instance()->post('news_id');
        //收藏状态
        $collectmodel = new NewsCollectModel();
        $colect_status = $collectmodel->getCollectByUser($user_id, $news_id);
        if (!empty($colect_status)) {
            api_return(0, '不能重复收藏');
        }
        $model = new NewsCollect();
        $res = $model->addCollect($user_id, $news_id);
        if ($res) {
            api_return(1, '收藏成功', ['collect_id' => $res]);
        }
        api_return(0, '收藏失败');
    }

    //咨讯评论
    public function comments()
    {
        $user_id = $this->user_id;
        $param = Request::instance()->post();
        $param['user_id'] = $user_id;
        $model = new NewsReply();
        $res = $model->saveChange($param);
        if ($res) {
            $newsLogic = new NewsLogic();
            if ($param['type'] == 1) {
                //利好数加一
                $r = $newsLogic->changeLihao($param['news_id']);

            } else {
                //利空加一
                $r = $newsLogic->changeLikong($param['news_id']);

            }
            if ($r) {
                api_return(1, '评论成功');
            }
        }
        api_return(0, '评论失败');
    }

    //我的收藏
    public function myCollect()
    {
        $user_id = $this->user_id;
//        $user_id = 1;
        $model = new NewsCollectModel();
        $res = $model->myCollect($user_id);
        if ($res) {
            api_return(1, '获取成功', $res);
        }
        api_return(0, '无数据');
    }

    //我的收藏删除
    public function delCollect()
    {
        $user_id = $this->user_id;
        $ids = Request::instance()->post('cids');
        $ids = explode(',', $ids);
        $model = new NewsCollectModel();
        $res = $model->updateStatus($ids, $user_id);
        if ($res) {
            api_return(1, '删除成功');
        }
        api_return(0, '删除失败');

    }


}