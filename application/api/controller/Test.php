<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/15
 * Time: 16:35
 */

namespace app\api\controller;


//use Qiniu\Pili\Client;
//use function Qiniu\Pili\HDLPlayURL;
//use function Qiniu\Pili\HLSPlayURL;
//use Qiniu\Pili\Mac;
//use function Qiniu\Pili\RTMPPlayURL;
//use function Qiniu\Pili\RTMPPublishURL;
//use function Qiniu\Pili\SnapshotPlayURL;
use app\api\command\Red;
use think\Controller;

class Test extends Controller
{


    public function cash()
    {
        $data['phone'] = '17688868666';
        $data['id'] = '11';
        $data['token'] = '8cc567da9f8ec99395860eb063694709';
        $data['money'] = '-817.6';
        for ($i = 0; $i <= 10000; $i++) {
            $res = curl_post('http://api.dk918.cn//user/cash', $data);
            var_dump($res);
        }
    }











//$mac = new Qiniu\Pili\Mac($ak, $sk);
//$client = new Qiniu\Pili\Client($mac);
//$hub = $client->hub($hubName);
//print_r($hub);

    public function index()
    {
        $ak = config('qiniu.ACCESSKEY');
        $sk = config('qiniu.SECRETKEY');
        $hubName = config('qiniu.hubName');
        $qiNiu = config('qiniu');
        $mac = new Mac($ak, $sk);
        $client = new Client($mac);
        $hub = $client->hub($hubName);
        $streamKey = "xiaosongtest5";
        $time = 3600 * 100;
        $push = RTMPPublishURL($qiNiu['pushDomain'], $qiNiu['hubName'], $streamKey, $time, $ak, $sk);
        $data['push'] = $push;
        var_dump($data);
        exit;
//        print_r($stream);
        //创建streamexit;
//        exit;
        echo "================Create stream\n";
//        $resp = $hub->create($streamKey);
//        print_r($resp);
        //获取stream info
        echo "================Get stream info\n";
//        $resp = $stream->info();
//        print_r($resp);
//        $url = Qiniu\Pili\RTMPPublishURL("publish-rtmp.test.com", $hubName, $streamKey, 3600, $ak, $sk);
        //推流地址
        $push = RTMPPublishURL("pili-publish.play.51soha.com", $hubName, $streamKey, 3600, $ak, $sk);
        dump($push);
        $url = RTMPPlayURL("pili-live-rtmp.play.51soha.com", $hubName, $streamKey);
        dump($url);
        $url = HDLPlayURL("pili-live-hdl.play.51soha.com", $hubName, $streamKey);
        echo $url, "\n";
        $url = HLSPlayURL("pili-live-hls.play.51soha.com", $hubName, $streamKey);
        dump($url);
        $url = SnapshotPlayURL("pili-live-snapshot.play.51soha.com", $hubName, $streamKey);
        echo $url, "\n";
        //更改流的实时转码规格
        echo "================Update converts:\n";
//        $info = $stream->info();
//        echo "before update converts. info:\n";
//        dump($info);
//        $stream->updateConverts(array("480p", "720p"));
//        $info = $stream->info();
//        dump($info);
        //        $resp = $stream->info();
//        print_r($resp);
    }

    public function test()
    {
//        $arr['Account'] = "8618780106307";
        $arr['Pwd'] = "Wo@123456";
        $data = MBerryApi($arr, 'api.Charge', '123456');
        dump($data);
        exit;
        $data = my_sort($arr);
        dump($data);
        exit;

        $data = hash_hmac('md5', 'a=1&b=22', 'BigFoolYouAreTheBest');
        dump($data);
        echo md5($data);
    }

    public function test1()
    {
        return view();
    }

    public function redBack()
    {
        $red = new Red();
        $red->doCron();
    }


}