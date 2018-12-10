<?php
/**
 * Created by xiaosong
 * E-mail:306027376@qq.com
 * Date: 2018/11/12
 * Time: 17:37
 */

namespace app\api\controller;


use think\Db;
use Yansongda\Pay\Pay;

class Pays extends User
{

    protected $alipay = [
        'app_id' => '2018052160244001',
        'notify_url' => 'http://api.tikuzhuanjia.com/pays/alipay',
        'ali_public_key'   => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAiF4Y1bF3uVl6+xdUE+HqeG6HkiRsmnvnszkqv4v9iHrQJHpp7r9GXd1QO51MKjvU3OBOI6I/RLxADtaL8RmQ4AIHYdye5GreITFivbXIMtTprG0AF8gCw8tjWTD+iwuYgbXDh+OniKR3Dks2IEDiwOHilnNGCbib0CIwXDodPxq/avOMMguVxHfsVVIVvzxmaFKun9h8IVC4JGuXaLgV0AHrmqgk7zsTdMQ1pn9AGAh/tW4MC+wb4VPSuDCexU+GSktZZEQU5DBk/mTR+9VuLWDLyEU34sspvlb3TnmDMvPsnA+WxSXSGQeyyk+T2u1tz5/YLTnJ4KzusKwUz+MPSQIDAQAB',
        'return_url' => 'http://api.tikuzhuanjia.com/pays/alipay1',
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

                //获取要赠送的人数
                $people = count(explode(',',$data['to_user']));

                //根据人数及每人赠送数量判断赠送总数
                $num = $data['num'] * $people;

                if ($num <= 0) api_return(0,'请选择要赠送的的人');

                $price = bcmul($gift['price'],$num,2);

                $subject = '萌趴礼物('.$gift['gift_name'].')赠送';

                $body = json_encode([
                    'user_id' => hashid($this->user_id),
                    'id' => $data['id'],
                    'type' => $data['type'],
                    'to_user' => $data['to_user']
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
    
    
    
}