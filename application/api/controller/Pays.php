<?php
/**
 * Created by xiaosong
 * E-mail:306027376@qq.com
 * Date: 2018/11/12
 * Time: 17:37
 */

namespace app\api\controller;


use app\common\logic\Logic;
use think\Db;
use think\Exception;
use Yansongda\Pay\Pay;

class Pays extends User
{

    protected $alipay = [
        'app_id' => '2018052160244001',
        'notify_url' => 'http://amp.xingzhuosong.com/pays/notify',
        'ali_public_key'   => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAiF4Y1bF3uVl6+xdUE+HqeG6HkiRsmnvnszkqv4v9iHrQJHpp7r9GXd1QO51MKjvU3OBOI6I/RLxADtaL8RmQ4AIHYdye5GreITFivbXIMtTprG0AF8gCw8tjWTD+iwuYgbXDh+OniKR3Dks2IEDiwOHilnNGCbib0CIwXDodPxq/avOMMguVxHfsVVIVvzxmaFKun9h8IVC4JGuXaLgV0AHrmqgk7zsTdMQ1pn9AGAh/tW4MC+wb4VPSuDCexU+GSktZZEQU5DBk/mTR+9VuLWDLyEU34sspvlb3TnmDMvPsnA+WxSXSGQeyyk+T2u1tz5/YLTnJ4KzusKwUz+MPSQIDAQAB',
//        'return_url' => 'http://api.tikuzhuanjia.com/pays/alipay1',
        // 加密方式： **RSA2**
        'private_key' => 'MIIEowIBAAKCAQEAqao5TmnoaPuaDKwzzfPDxqPHTcdkooGSNQXz7lrStlFzqugo/aG+rKPDuvSMY2108X+jfp67mK0/3bNANN0aLhiolbEvobd4U3faH+L8fv++M5IJdbH0ln7vJKfzInShZVDoCzwpXh3N6FQZ8z9MLemIk8udvOUavEJf2hSZWLy3MbmioL8QYhnTGqP5tcJjIC7ufzrKxIiJyeoT/ciYZ6RYor9TXkGZ3porVk+M764RtXoiV3nqKnG9DozZU+dF3Cgkcb4s7BrCxiWHRcl+FJ2mKVAf6RsEd1OSJ7aupexyUqH7fWLsRZ1Tn9RO4wneQ1EJgbZXHHeBcbsbjECu5wIDAQABAoIBAD6tBXJ0KUju+R+JVbHVRRNSWUPgTsrBdtNjmZMJtiFnwYT3Mn1PjPKVpK6hvGLWgobcEfeqh76E8bzihOuCajNxJIX36JKjBi4/bjKtVX1M2GSQpDH4RVR7G7i82lJ2J1EYLEBKPzXnaLNUrilvzqJ/TNbcNy8aq1+0XVhgl61xn1tX3iR2vvd+1xxsWOsB8L0aPnAsoSH7dJ7AwDZO/blOLeoz9CuBUU7GYiQLGYuZTlScr3N3O5dsGuL+8vigrgstXAzckpb9yAnpGgepWFBTTvWkSS0crsSCLdznbcRBkQtgKRF3t9ScFC1wFeTr/ueslE07QvMZ1CFiR7FRm4ECgYEA2JYzqaqgLI1LXkgAOAzt33fhjyFWGmlpsyWFYSptMyhlotHv4j6tgPowqviSQ5mDEP66sOdT1D1o2o5SPvDNfjRk1BfhkzG4Os+efgHJ1BhuyF5bBwz3etaBuYTfFUFrAu1grbH/cmf0K3GH9TBXuhiQiYrVlmjt7H4hidvArIcCgYEAyIoly6iEqVqM3nzIilI9bwFz+RbYTaaCBH1L/LTensnUqOlCVio+HvCN845uR+QgnqZuh3OsXbeo3kfe10x0Xz9scePOLuQ7wWJEFNNemVLft0WP321zPyxzq0L2ndu0U0eM10MknA8mElGSICn+mBr7FLPHb7MVyC5YcTBT4qECgYEAkqJOY+ZC/ybCChjRHSGTwqHVMiQtuT/48fLLNJeWyvXkqbFcqV4p9ZJtdLNJwz6hf9YV60MSfDT/Ukjc4gQB/BnY0cdBT3hv9FEwSrtHO7M2/az0D/f1bVLhDQsqRae+nYK825wRCBHdO7Rnidaq7jFHWfeG14g+3MggSMdg0O0CgYAC2IMEytVnGdPZ7GdkHxqkEp80r7BOGcjKi4Sih2aJVk/gPb8lPeA3zC4XgLPr7T7RQYdcALY3dj29OcPdxkX4fAvr6dGpNK/sZJqWuREkl9p43VHXV9RE1zqk+YRKZS2/6MoE2/0PAeAGboXmUvI78lYRyyNPYHk0qAO1R3xJQQKBgHxTdByHm4qL/GInVLxYF8/8XXAlUalXzyB/g/ne2uUf3Xr7Iks9+O6hdiZQ9as6oVMSGT14nlBfurEsjF7DDtNuCPQ8P7I94RwY++1O3RHrQfrKdOo4u68ADfPU49dvjhQevnljM++33hB2yQ840Rg+tUGdDazqVwkuKlEuQKZi',
    ];

    protected $wechat = [
        'appid' => 'wxa24476b6ccbf5b5e', // APP APPID
//        'app_id' => 'wxb3fxxxxxxxxxxx', // 公众号 APPID
//        'miniapp_id' => '1504314791', // 小程序 APPID
        'mch_id' => '1504314791',
        'key' => 'f103c37dfff18ce23687a65f47730e13',
        'notify_url' => 'http://api.tikuzhuanjia.com/pays/wechat',
        'cert_client' => './cert/apiclient_cert.pem', // optional，退款等情况时用到
        'cert_key' => './cert/apiclient_key.pem',// optional，退款等情况时用到
    ];


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 支付签名获取
     */
    public function paySign()
    {

        $this->ApiLimit(1,$this->user_id);

        $data = input('post.');

        $order_num = 'YW'.hashid($this->user_id).time().rand(1000,9999);

        switch ($data['type']){
            case 'recharge': //钻石充值
                $map['r_id']   = $data['id'];
                $map['status'] = 1;
                $config = Db::name('recharge_config')->where($map)->find();
                if (!$config || $config['price'] <= 0) api_return(0,'参数错误');

                $price = $config['price'];

                $subject = '萌趴'.$config['money'].'钻充值';

                $body = json_encode([
                    'user_id' => hashid($this->user_id),
                    'id'=>$data['id'],
                    'type' => $data['type']
                ]);

                break;
            case 'gift': //礼物赠送
                $map['gift_id'] = $data['id'];
                $map['status']  = 1;
                $gift = Db::name('gift')->where($map)->find();

                if (!$gift || $gift['price'] <= 0 ) api_return(0,'参数错误');

                if (!isInt($data['num'])) api_return(0,'赠送数量错误');

                if (empty($data['to_user'])) api_return(0,'请选择要赠送的的人');

                //获取要赠送的人数
                $people = count(explode(',',$data['to_user']));


                //根据人数及每人赠送数量判断赠送总数
                $total = bcmul($gift['price'],$data['num'],2);

                if ($total <= 0)

                $price = bcmul($total,$people,2);

                $subject = '萌趴礼物('.$gift['gift_name'].')赠送';

                //TODO 传入房间id
                $body = json_encode([
                    'user_id' => hashid($this->user_id),
                    'id' => $data['id'],
                    'type' => $data['type'],
                    'to_user' => $data['to_user'],
                    'room_id' => $data['room_id']??0,
                    'total'=> $total,
                    'num' => $data['num'],
                ]);

                break;
            case 'invite': //邀约订单付款

                $map['order_id']   = $data['order_id'];
                $map['status'] = 0;
                $order = Db::name('order')->where($map)->find();

                if (!$order) api_return(0,'订单号错误');

                $price = $order['price'];

                $subject = '萌趴邀约';

                $body = json_encode([
                    'user_id' => hashid($this->user_id),
                    'order_id'=>$data['order_id'],
                    'type' => $data['type']
                ]);

                break;
            case 'noble': //贵族购买付款

                $noble = $this->noblePrice($this->user_id);

                $price = $data['price'];

                $subject = '萌趴贵族购买';

                $body = json_encode([
                    'user_id' => hashid($this->user_id),
                    'noble' => $noble['noble'],
                    'type' => $data['type']
                ]);

                break;
            default:
                api_return(0,'类型错误');
                break;

        }

        switch ($data['payType']){

            case 'alipay'://支付宝支付

                $order = [
                    'out_trade_no' => $order_num,
                    'total_amount' => '0.01', //单位元
//                'total_amount' => $price, //单位元
                    'subject' => $subject,
                    'body' =>  $body,//自定义参数

                ];

                $sign = Pay::alipay($this->alipay)->app($order)->getContent();
                break;
            case 'wechat': //微信支付

                $order = [
                    'out_trade_no' => $order_num,
                    'body' => $subject,
                    'total_fee' => '1',
//                    'total_fee' => $price, //单位 分
                    'attach'=> $body,
                ];

                $json = Pay::wechat($this->wechat)->app($order)->getContent();
                $sign = json_decode($json,true);

                break;

            default:
                api_return(0,'支付类型错误');
                break;

        }
        print_r($sign);exit;
        api_return(1,'获取成功',$sign);
    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 支付宝回调
     */
    public function notify()
    {
//        $alipay = Pay::alipay($this->alipay);
//        try{
//            $data = $alipay->verify(); // 是，验签就这么简单！
//            cache('aliTest',$data);
            $data = cache('aliTest');


            // 请自行对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
            // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
            // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；
            // 3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）；
            // 4、验证app_id是否为该商户本身。
            // 5、其它业务逻辑情况


            if ($data['trade_status'] == 'TRADE_SUCCESS' || $data['trade_status'] == 'TRADE_FINISHED'){

                $body = json_decode($data['body'],true);




                switch ($body['type']){
                    case 'recharge'://充值
                        $this->recharge($body);
                        break;
                    case 'gift': //礼物赠送
                        $this->gift($body);

                        break;
                    default:
                        api_return(0,'类型错误');
                        break;
                }


            }





//            Log::debug('Alipay notify', $data->all());
//        } catch (Exception $e) {
//
//             cache('message',$e->getMessage());
//        }

//        return $alipay->success()->send();// laravel 框架中请直接 `return $alipay->success()`
    }

    public function test()
    {
       $data = cache('aliTest');

       dd($data);
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 充值回调成功处理
     */
    protected function recharge($data)
    {
        $user_id = dehashid($data['user_id']);

        if (!is_numeric($user_id))  return false;

        $map['r_id']   = $data['id'];
        $map['status'] = 1;
        $config = Db::name('recharge_config')->where($map)->cache(3)->find();

        return Db::name('users')->where('user_id',$user_id)->setInc('money',$config['money']);
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 礼物赠送回调成功处理
     */
    protected function gift($data){
        $userIds = explode(',',$data['to_user']);

        $item['room_id'] = $data['room_id']??0;
        $item['gift_id'] = $data['id'];
        $item['num']     = $data['num']??1;
        $item['user_id'] = dehashid($data['user_id']);
        $item['total']   = $data['total']??1;
        $item['create_time'] = time();
        $item['update_time'] = time();

        $gift = Db::name('gift')->where('gift_id',$data['id'])->cache(3)->find();

        $detail['type'] = 1;
        $detail['title'] = '来自'.$this->userInfo('nick_name',$item['user_id']).'的'.$gift['gift_name'].'x'.$item['num'];
        $detail['remark'] = '收入';
        $detail['create_time'] = time();
        $detail['update_time'] = time();
        $ratio = $this->extend('gift_ratio') / 100;
        $detail['money'] = bcmul($item['total'],$ratio,2);

        $detail2['type']   = 2;
        $detail2['remark']  = '消费';
        $detail2['money']   = $item['total'];
        $detail2['user_id'] = $item['user_id'];
        $detail2['create_time'] = time();
        $detail2['update_time'] = time();

        $model = new Logic();
        $model->changeTable('gift_record');

        $array    = [];
        $details  = [];
        $details2 = [];
        foreach ($userIds as $k => $v){

            $item['to_user'] = dehashid($v);
            if (is_numeric($item['to_user'])){
                $array[] = $item;

                $detail['user_id'] = $item['to_user'];
                $detail2['title']  = '打赏'.$this->userInfo('nick_name',$item['to_user']).$gift['gift_name'].'x'.$item['num'];
                $details[] = $detail;
                $details2[] = $detail2;
                Db::name('users')->where('user_id',$item['to_user'])->setInc('money',$detail['money']);

                //trest
                //TODO 发送融云消息
            }
        }
//        var_dump($array);
//        var_dump($details);
//        var_dump($details2);exit;
        $result = $model->insertAll($array);
        Db::name('money_detail')->insertAll($details);
        Db::name('money_detail')->insertAll($details2);

       cache('msg','success');







    }


}