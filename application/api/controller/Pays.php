<?php
/**
 * Created by xiaosong
 * E-mail:306027376@qq.com
 * Date: 2018/11/12
 * Time: 17:37
 */

namespace app\api\controller;


use Yansongda\Pay\Pay;

class Pays extends User
{

    protected $alipay = [
        'app_id' => '2018052160244001',
        'notify_url' => 'http://api.tikuzhuanjia.com/pays/alipay',
        'return_url' => 'http://api.tikuzhuanjia.com/pays/alipay',
        'ali_public_key'   => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAiF4Y1bF3uVl6+xdUE+HqeG6HkiRsmnvnszkqv4v9iHrQJHpp7r9GXd1QO51MKjvU3OBOI6I/RLxADtaL8RmQ4AIHYdye5GreITFivbXIMtTprG0AF8gCw8tjWTD+iwuYgbXDh+OniKR3Dks2IEDiwOHilnNGCbib0CIwXDodPxq/avOMMguVxHfsVVIVvzxmaFKun9h8IVC4JGuXaLgV0AHrmqgk7zsTdMQ1pn9AGAh/tW4MC+wb4VPSuDCexU+GSktZZEQU5DBk/mTR+9VuLWDLyEU34sspvlb3TnmDMvPsnA+WxSXSGQeyyk+T2u1tz5/YLTnJ4KzusKwUz+MPSQIDAQAB',
        // 加密方式： **RSA2**
        'private_key' => 'MIIEowIBAAKCAQEAqao5TmnoaPuaDKwzzfPDxqPHTcdkooGSNQXz7lrStlFzqugo/aG+rKPDuvSMY2108X+jfp67mK0/3bNANN0aLhiolbEvobd4U3faH+L8fv++M5IJdbH0ln7vJKfzInShZVDoCzwpXh3N6FQZ8z9MLemIk8udvOUavEJf2hSZWLy3MbmioL8QYhnTGqP5tcJjIC7ufzrKxIiJyeoT/ciYZ6RYor9TXkGZ3porVk+M764RtXoiV3nqKnG9DozZU+dF3Cgkcb4s7BrCxiWHRcl+FJ2mKVAf6RsEd1OSJ7aupexyUqH7fWLsRZ1Tn9RO4wneQ1EJgbZXHHeBcbsbjECu5wIDAQABAoIBAD6tBXJ0KUju+R+JVbHVRRNSWUPgTsrBdtNjmZMJtiFnwYT3Mn1PjPKVpK6hvGLWgobcEfeqh76E8bzihOuCajNxJIX36JKjBi4/bjKtVX1M2GSQpDH4RVR7G7i82lJ2J1EYLEBKPzXnaLNUrilvzqJ/TNbcNy8aq1+0XVhgl61xn1tX3iR2vvd+1xxsWOsB8L0aPnAsoSH7dJ7AwDZO/blOLeoz9CuBUU7GYiQLGYuZTlScr3N3O5dsGuL+8vigrgstXAzckpb9yAnpGgepWFBTTvWkSS0crsSCLdznbcRBkQtgKRF3t9ScFC1wFeTr/ueslE07QvMZ1CFiR7FRm4ECgYEA2JYzqaqgLI1LXkgAOAzt33fhjyFWGmlpsyWFYSptMyhlotHv4j6tgPowqviSQ5mDEP66sOdT1D1o2o5SPvDNfjRk1BfhkzG4Os+efgHJ1BhuyF5bBwz3etaBuYTfFUFrAu1grbH/cmf0K3GH9TBXuhiQiYrVlmjt7H4hidvArIcCgYEAyIoly6iEqVqM3nzIilI9bwFz+RbYTaaCBH1L/LTensnUqOlCVio+HvCN845uR+QgnqZuh3OsXbeo3kfe10x0Xz9scePOLuQ7wWJEFNNemVLft0WP321zPyxzq0L2ndu0U0eM10MknA8mElGSICn+mBr7FLPHb7MVyC5YcTBT4qECgYEAkqJOY+ZC/ybCChjRHSGTwqHVMiQtuT/48fLLNJeWyvXkqbFcqV4p9ZJtdLNJwz6hf9YV60MSfDT/Ukjc4gQB/BnY0cdBT3hv9FEwSrtHO7M2/az0D/f1bVLhDQsqRae+nYK825wRCBHdO7Rnidaq7jFHWfeG14g+3MggSMdg0O0CgYAC2IMEytVnGdPZ7GdkHxqkEp80r7BOGcjKi4Sih2aJVk/gPb8lPeA3zC4XgLPr7T7RQYdcALY3dj29OcPdxkX4fAvr6dGpNK/sZJqWuREkl9p43VHXV9RE1zqk+YRKZS2/6MoE2/0PAeAGboXmUvI78lYRyyNPYHk0qAO1R3xJQQKBgHxTdByHm4qL/GInVLxYF8/8XXAlUalXzyB/g/ne2uUf3Xr7Iks9+O6hdiZQ9as6oVMSGT14nlBfurEsjF7DDtNuCPQ8P7I94RwY++1O3RHrQfrKdOo4u68ADfPU49dvjhQevnljM++33hB2yQ840Rg+tUGdDazqVwkuKlEuQKZi',
        'log' => [ // optional
            'file' => './public/logs/alipay.log',
            'level' => 'debug', // 建议生产环境等级调整为 info，开发环境为 debug
            'type' => 'single', // optional, 可选 daily.
            'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
        ],

    ];






    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 支付宝余额充值签名获取
     */
    public function aliPayRecharge()
    {

        $order = [
            'out_trade_no' => time(),
            'total_amount' => '1',
            'subject' => 'test subject - 测试',
            'attach'=> $this->user_id,
        ];

        $alipay = Pay::alipay($this->alipay)->app($order);

        api_return(1,'获取成功',$alipay);

    }
    
    
    
}