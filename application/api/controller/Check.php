<?php

namespace app\api\controller;



use app\common\logic\UserId;
use app\common\model\RoleCheck;
use Cloudauth\Request\V20180703\CompareFacesRequest;
use Ecs\Request\V20140526\DescribeInstancesRequest;
require_once './extend/aliyun/aliyun-php-sdk-cloudauth/Cloudauth/Request/V20180703/CompareFacesRequest.php';

use think\Exception;
use think\Loader;
require_once './extend/aliyun/aliyun-php-sdk-core/Config.php';

class Check extends User
{


    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     *      申请官方认证进入接口
*/
    public function requests()
    {
        $model = new RoleCheck();
        $user_id = $this->user_id;
        $result = $model->getRequest($user_id);
        if($result != 2){
            api_return(1,'成功',$result);
        }else{
            api_return(0,'当前此角色不可进行认证,请确定此帐号处于未认证和未审核状态再进行认证申请');
        }

    }

    /**
     *      申请官方认证接口
     */
    public function check()
    {
        $id = $this->user_id;
        $money = $this->request->post('money');
        if(!$money){
            api_return(0,'缺少参数');
        }
        $model = new RoleCheck();
        $result = $model->getCheck($id,$money);
        if($result == 1){
            api_return(1,'申请成功');
        }else{
            api_return(0,$result);
        }

    }
    public function person_check()
    {
        $model = new UserId();
        $user_id = $this->user_id;
        $result = $model->getCan($user_id);
        if($result&&$result['status']==1){
            api_return(0,'当前用户已实名认证,请勿重复认证');
        }elseif($result&&$result['status'] == 2){
            api_return(1,'当前用户自动实名认证失败,是否进行人工审核',2);
        }elseif($result&&$result['status'] == 3){
            api_return(0,'当前用户人工实名认证中,请勿重复提交认证');
        }else{
            api_return(1,'当前用户可以进行实名认证',1);
        }

    }

    /**
     *      人脸比对接口
     */

    /*   此为阿里云获取身份证信息成功后返回值
    stdClass Object
    (
        [address] => xxxxx
        [birth] => 1111111
        [config_str] => {"side":"face"}
        [face_rect] => stdClass Object
            (
                [angle] => -90
                [center] => stdClass Object
                    (
                        [x] => 392
                        [y] => 922.5
                    )

                [size] => stdClass Object
                    (
                        [height] => 142
                        [width] => 155
                    )

            )

        [name] => xx
        [nationality] => xx //民族
        [num] => 11111111111111111//身份证号
        [request_id] => 20180808163556_a83ddf881fad5bdc8701d20a769e81a4
        [sex] => 男
        [success] => 1//信息获取成功
    */
    public function face(){
        //阿里云获取身份证信息
        $results = false;
        $res = false;
        $url = "https://dm-51.data.aliyun.com/rest/160601/ocr/ocr_idcard.json";
        $appcode = "d8a374ee16bc4396858379eb5eb54672";

        $file = $this->request->post('file');
        $back = $this->request->post('back');
        $img = $this->request->post('img');
        if(empty($file)||empty($back)||empty($img)) api_return(0,'参数错误');
//        $file = './public/upload/images/test.png';
        $contents = file_get_contents($file);
//         echo $contents;exit;
        //如果输入带有inputs, 设置为True，否则设为False
        $is_old_format = false;
        //如果没有configure字段，config设为空
        $config = array(
            "side" => "face"
        );
        if($contents) {
            $base64 = base64_encode($contents); // 转码
        }
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type".":"."application/json; charset=UTF-8");
        $querys = "";
        if($is_old_format == TRUE){
            $request = array();
            $request["image"] = array(
                "dataType" => 50,
                "dataValue" => "$base64"
            );

            if(count($config) > 0){
                $request["configure"] = array(
                    "dataType" => 50,
                    "dataValue" => json_encode($config)
                );
            }
            $body = json_encode(array("inputs" => array($request)));
        }else{
            $request = array(
                "image" => "$base64"
            );
            if(count($config) > 0){
                $request["configure"] = json_encode($config);
            }
            $body = json_encode($request);
        }
        $method = "POST";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$".$url, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        $result = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $rheader = substr($result, 0, $header_size);
        $rbody = substr($result, $header_size);
        $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
        if($httpCode == 200){
            if($is_old_format){
                $output = json_decode($rbody, true);
                $result_str = $output["outputs"][0]["outputValue"]["dataValue"];
            }else{
                $result_str = $rbody;
            }

            $results = json_decode($result_str);
        }else{
            api_return(0,'审核失败,请重新提交数据');
        }
        if($results){
            if($results->success){
                $data['real_name'] = $results->name;
                $data['user_id'] = $this->user_id;
                $data['address'] = $results->address;
                if($results->sex == '男'){
                    $data['sex'] = 1;
                }else{
                    $data['sex'] = 0;
                }
                $data['ID_num'] = $results->num;
                $res = db('user_id')->where(['ID_num'=>$data['ID_num'],'status'=>1])->select();
                if(!empty($res)){
                api_return(0,'该身份证已进行过实名认证，请不要使用已认证过的身份证进行认证');
                }
                $data['face'] = $file;
                $data['back'] = $back;
                $data['img'] = $img;
                $idcard = $results->num;
                $realname = urlencode($results->name);
                $urls = "http://op.juhe.cn/idcard/query?key=f34e0944b85b5c11ff2e95d140176f39&idcard=$idcard&realname=$realname";
                $res = curl_get($urls);
            }else{
                api_return(0,'审核失败，请重新提交数据');
            }
        }
        if($res){
            if($res['error_code'] == 0){
                //阿里云人脸比对
                //创建DefaultAcsClient实例并初始化
                $iClientProfile = \DefaultProfile::getProfile(
                    "cn-huadong2",            //默认
                    "LTAIj85NUg3vb1Uf",        //您的Access Key ID
                    "igp6QM1NAbm6f8VZeXg1WbBUFxODLA");    //您的Access Key Secret
                $iClientProfile::addEndpoint("cn-huadong2", "cn-huadong2", "Cloudauth", "cloudauth.aliyuncs.com");
                $client = new \DefaultAcsClient($iClientProfile);
                //创建API请求并设置参数
                //CompareFaces接口文档：https://help.aliyun.com/document_detail/59317.html
                $request = new CompareFacesRequest();
                //若使用base64上传图片, 需要设置请求方法为POST
                $request->setMethod("POST");
                //传入图片资料，请控制单张图片大小在 2M 内，避免拉取超时
                $request->setSourceImageType("FacePic");
                $request->setSourceImageValue("$file"); //base64方式上传图片, 格式为"base64://图片base64字符串", 以"base64://"开头且图片base64字符串去掉头部描述(如"data:image/png;base64,"), 并注意控制接口请求的Body在8M以内
                $request->setTargetImageType("FacePic");
                $request->setTargetImageValue("$img"); //http方式上传图片, 此http地址须可公网访问
                //发起请求并处理异常
                try {
                    $response = $client->getAcsResponse($request);
                    if (!$response) api_return(0,'认证失败,请上传清晰正确的照片');
                    // 后续业务处理

                    if($response->Code == 1){
                        $point = db('extend')->where(['id'=>1])->value('compare_num');
                        if($response->Data->SimilarityScore >= $point){
                            $data['status']  =  1;
                        }else{
                            $data['status']  =  2;
                        }
                        $model = new UserId();
                        $result = $model->saves($data);
                        if($result&&$data['status'] == 1){
                            api_return(1,'审核成功',1);
                        }elseif($result&&$data['status'] == 2){
                            api_return(1,'当前用户自动实名认证失败,是否进行人工审核',2);
                        }
                    }else{
                        api_return(0,'审核失败,请重新提交数据');
                    }
                } catch (Exception $e) {
                    api_return(0,'审核失败,请重新提交数据');
                } catch (Exception $e) {
                    api_return(0,'审核失败,请重新提交数据');
                }
            }else{
                api_return(0,'审核失败,请重新提交数据');
            }
        }else{
            api_return(0,'审核失败,请重新提交数据');
        }

    }

    /**
     * 实名认证接口转人工审核
     */

    public  function  people_check(){
        $model = new UserId();
        $user_id = $this->user_id;
        $result = $model->changStatus($user_id);
        if($result){
            api_return(1,'操作成功');
        }else{
            api_return(0,'请重试');
        }
    }
}
