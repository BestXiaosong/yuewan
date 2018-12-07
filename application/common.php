<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use Qiniu\Config;
use Qiniu\Storage\BucketManager;
use Qiniu\Auth as Auth;
use Qiniu\Storage\UploadManager;
use think\Image;
// 应用公共文件

function curl_post($uri, $data = [])
{
// 参数数组
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // 跳过证书检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
    curl_setopt($ch, CURLOPT_URL, $uri);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    //curl_setopt($ch, CURLOPT_PORT, 8081);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function ischeck($data)
{
    echo($data == 0 ? '' : 'checked');
}


//将数组生成下拉列表
function arr2select($name, $rows, $title = '--请选择--', $default, $valueField = 'id', $textField = 'name', $class = 'form-control')
{
    //  name       =>数据库对应字段名
    //  rows       =>要循环的数组
    //  title      =>默认的无选择状态
    //  default    => 默认被选中的
    //  valueField => value的值
    //  textField  => 要输入的字段
    $html = "<select class='{$class}' name='{$name}'>";
    if (!empty($title)) {
        $html .= "<option value=''>$title</option>";
    }
    if (empty($rows)) {
        echo $html .= "</select>";
        return;
    }
    foreach ($rows as $row) {
        $selected = '';
        if ($row[$valueField] == $default) {
            $selected = "selected=selected";
        }
        $html .= "<option {$selected} value='{$row[$valueField]}'>{$row[$textField]}</option>";
    }
    $html .= "</select>";
    echo $html;
}


//将数组生成下拉列表
function arr4select($name, $rows, $title = '--请选择--', $default, $valueField = 'id', $textField = 'name', $class = 'form-control')
{
    //  name       =>数据库对应字段名
    //  rows       =>要循环的数组
    //  title      =>默认的无选择状态
    //  default    => 默认被选中的
    //  valueField => value的值
    //  textField  => 要输入的字段
    $html = "<select class='{$class}' name='{$name}'>";
    if (!empty($title)) {
        $html .= "<option value=''>$title</option>";
    }
    if (empty($rows)) {
        echo $html .= "</select>";
        return;
    }
    foreach ($rows as $row) {
        $selected = '';
        if ($row[$valueField] == $default) {
            if (!isEmpty($default)) {
                $selected = "selected=selected";
            }
        }
        $html .= "<option {$selected} value='{$row[$valueField]}'>{$row[$textField]}</option>";
    }
    $html .= "</select>";
    echo $html;
}


function arr3select($name, $rows, $title = '--请选择--', $default, $valueField = 'id', $textField = 'name', $class = 'form-control')
{
    //  name       =>数据库对应字段名
    //  rows       =>要循环的数组
    //  title      =>默认的无选择状态
    //  default    => 默认被选中的
    //  valueField => value的值
    //  textField  => 要输入的字段
    $html = "<select class='{$class}' name='{$name}'>";
    if (!empty($title)) {
        $html .= "<option value='0'>$title</option>";
    }
    if (empty($rows)) {
        echo $html .= "</select>";
    }
    foreach ($rows as $row) {
        $selected = '';
        if ($row[$valueField] == $default) {
            $selected = "selected=selected";
        }
        $html .= "<option {$selected} value='{$row[$valueField]}'>{$row[$textField]}</option>";
    }
    $html .= "</select>";
    echo $html;
}

//将数组生成下拉列表
function list2select($name, $rows, $default, $class, $is_all = false)
{
    $html = "<select class='{$class}' name='{$name}'>";
    if ($is_all) {
        $html .= "<option value=''>--请选择--</option>";
    }
    foreach ($rows as $row => $v) {
        $selected = '';
        if ($row == $default) {
            $selected = "selected=selected";
        }
        $html .= "<option {$selected} value='{$row}'>{$v}</option>";
    }
    $html .= "</select>";
    return $html;
}


function url_exists($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    //不下载
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    //设置超时
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($http_code == 200) {
        return true;
    }
    return false;
}

//二级下拉列表
function list3select($name, $rows, $default, $class)
{
    $html = "<select class='{$class}' name='{$name}'>";
    $html .= "<option value=''>--请选择--</option>";
    foreach ($rows as $row) {
        $selected = '';
        if ($row == $default) {
            $selected = "selected=selected";
        }
        $html .= "<option {$selected} value='{$row}'>{$row}</option>";
    }
    $html .= "</select>";
    echo $html;
}

function cate2select($name, $rows, $default, $class, $valueField = 'id', $textField = 'name')
{
    $html = "<select class='{$class}' name='{$name}'>";
    $html .= "<option value='0'>--根分类--</option>";
    foreach ($rows as $row) {
        $selected = '';
        if ($row[$valueField] == $default) {
            $selected = "selected=selected";
        }
        $html .= "<option {$selected} value='{$row[$valueField]}'>{$row[$textField]}</option>";
    }
    $html .= "</select>";
    echo $html;
}

/**
 * Created by xiaosong
 * E-mail:306027376@qq.com
 * @param int $length
 * @param string $type
 * @return string
 * 获取随机字符串
 */
function generateStr($length = 5, $type = "all")
{
    //密码字符集，可任意添加你需要的字符
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?';
    if ($type == "num") {
        $chars = '0123456789';
    } elseif ($type == "char") {
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    }
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $password;
}


if (!function_exists('array_column')) {
    function array_column($rows, $column_name)
    {
        $values = array();
        foreach ($rows as $row) {
            $values[] = $row[$column_name];
        }
        return $values;
    }
}

function setPageCook()
{
    cookie('__forward__', $_SERVER['REQUEST_URI']);
}

function uploadHtml($name, $value, $type)
{
    $timestamp = time();
    $salt = md5('unique_salt' . $timestamp);
    $html = '';
    if ($type == 'single_img') {
        $html = '<div name="' . $name . '" class="4jUploader" value="' . $value . '" type="single" ext="img" timestamp="' . $timestamp . '" salt="' . $salt . '" app_path="" root_path="" upload="' . config('UPLOAD') . '"></div>';
    } elseif ($type == 'single_file') {
        $html = '<div name="' . $name . '" class="4jUploader" value="' . $value . '" type="single" ext="file" timestamp="' . $timestamp . '" salt="' . $salt . '" app_path="" root_path="" ></div>';
    } elseif ($type == 'multi_img') {
        $html = '<div name="' . $name . '" class="4jUploader" value="' . $value . '" type="multi" ext="img" timestamp="' . $timestamp . '" salt="' . $salt . '" app_path="" root_path="" upload="' . config('UPLOAD') . '"></div>';
    } elseif ($type == 'multi_file') {
        $html = '<div name="' . $name . '" class="4jUploader" value="' . $value . '" type="multi" ext="file" timestamp="' . $timestamp . '" salt="' . $salt . '" app_path="" root_path="" ></div>';
    }
    return $html;
}


function combineDika()
{
    $data = func_get_args();
    $data = current($data);
    $cnt = count($data);
    $result = array();
    $arr1 = array_shift($data);
    foreach ($arr1 as $key => $item) {
        $result[] = [$item];
    }
    foreach ($data as $key => $item) {
        $result = combineArray($result, $item);
    }
    return $result;
}


/**
 * 两个数组的笛卡尔积
 * @param unknown_type $arr1
 * @param unknown_type $arr2
 */
function combineArray($arr1, $arr2)
{
    $result = array();
    foreach ($arr1 as $item1) {
        foreach ($arr2 as $item2) {
            $temp = $item1;
            $temp[] = $item2;
            $result[] = $temp;
        }
    }
    return $result;
}






/**
 * 格式化时间
 */
function fmt_time($value, $type = "full_time")
{
    if (!empty($value) || $value != 0) {
        if ($type == "full_time") {
            return date("Y-m-d H:i:s", $value);
        } else {
            return date("Y-m-d", $value);
        }
    }
    return "";
}

/**
 * 判断是否为空，注意  0不为空，为解决 php内0为空问题
 */
function isEmpty($val)
{
    if ($val === 0 || $val === '0') {
        return false;
    } else {
        return empty($val);
    }
}


function sub_str($text, $length = 10)
{
    $end = '';
    if (mb_strlen($text) > $length) {
        $end = '…';
    }
    return mb_substr($text, 0, $length) . $end;
}

function api_return( $code = 0 ,$msg = '', $data = null, array $header = [])
{

    $result = [
        'code' => $code,
        'msg' => $msg,
        'time' => \think\Request::instance()->server('REQUEST_TIME'),
        'data' => $data,
    ];

    $response = \think\Response::create($result, 'json')->header($header);

    throw new think\exception\HttpResponseException($response);
}

function check_token()
{
    $token = input('post.token');
    $is_token_exist = config($token);
    if (empty($is_token_exist)) {
        return false;
    }
    config($token, time(), 14400);
    return true;
}

function makeToken()
{
    $encrypt_key = substr(md5(((float)date("YmdHis") + rand(100, 999)) . rand(1000, 9999)), 8, 16);
    config('token_' . $encrypt_key, time(), 14400);
    return $encrypt_key;
}


function hashid($str, $length = 8)
{
    $hash = new \Hashids\Hashids(config('hash_key'), $length);
    return $hash->encode($str);
}

function dehashid($str, $length = 8)
{
    $hash = new \Hashids\Hashids(config('hash_key'), $length);
    $rs = $hash->decode($str);
    if (is_null($rs)) {
        return '';
    } else {
        return $rs[0];
    }
}

/**
 * Created by xiaosong
 * E-mail:4155433@gmail.com
 * @param int $room_id
 * @return string
 * 获取房间热度
 */
function hotValue($room_id = 0){
    $hot = cache('hot'.$room_id);
    if (!isEmpty($hot)) return "$hot";
    $hot = 0;
    $cache = cache('hotVal'.$room_id);
    $now = time();
    foreach ($cache as $k => $v){

        if ($v['expiration_time'] > $now){
            $hot += $v['hot'];
        }
    }
    cache('hot'.$room_id,$hot,15);
    return "$hot";
}

/**
 * Created by xiaosong
 * E-mail:4155433@gmail.com
 * @param $room_id |房间id
 * @param int $num |要增加的热度
 * @param null $out_time |热度过期时间  若无过期时间  默认为房间人数增加的热度
 * 增加房间热度
 */
function addHot($room_id,$num = 1,$out_time = null){
    $hot = cache('hotVal'.$room_id);
    $time = todayEndTime()+7200;
    if ($out_time){

        $arr['hot'] = $num;
        $arr['expiration_time'] = time()+$out_time;
        $hot[] = $arr;
    }else{

        if (!array_key_exists('hot',$hot)){
            $hot['hot']['hot'] = $num;
            $hot['hot']['expiration_time'] = time()+$time;
        }else{
            $hot['hot']['hot'] = $hot['hot']['hot']+$num;
        }

    }
    cache('hotVal'.$room_id,$hot,$time);
}

/**
 * Created by xiaosong
 * E-mail:4155433@gmail.com
 * @param $room_id
 * @param int $num
 * 减少房间热度 只能减少房间人数产生的热度
 */
function delHot($room_id,$num = 1){
    $time = todayEndTime()+7200;
    $hot = cache('hotVal'.$room_id);
    $have = $hot['hot']['hot'] - $num;
    if ($have < 0){
        $have = 0;
    }
    $hot['hot']['hot'] = $have;
    cache('hotVal'.$room_id,$hot,$time);
}



/**
 * Created by xiaosong
 * E-mail:306027376@qq.com
 * 推送
 * @param $type 1 房间 2 资产 3钱包
 * @param string $j_push_id
 * @param string $title
 * @param $room_id
 * @return bool
 * 极光推送公用方法
 *
 */
 function Push($type = 0,$j_push_id = '',$title = '来自萌趴直播的推送消息',$room_id)
{
    if (!$j_push_id) return false;

    $extend = [];
    if ($type == 1){
        $extend['extras'] = [
            'type' => 'room',//跳转至房间
            'room_id' => $room_id,
        ] ;
    }elseif ($type == 2){
        $extend['extras'] = [
            'type' => 'assets',//跳转至资产
        ] ;
    }elseif ($type == 3){
        $extend['extras'] = [
            'type' => 'wallet',//跳转至钱包
        ] ;
    }

    j_push($title,$j_push_id,$extend);
}


/**
 * 极光及时推送
 */
function j_push($title = '', $reg_id, $extend = [])
{
    $config = config('jpush');
    $push = new \JPush\Client($config['ak'], $config['mk']);

    try {
        if ($reg_id == 'all') {
            $push->push()
                ->setPlatform('all')
                ->addAllAudience()
                ->setNotificationAlert($title)
                ->send();
        } else {
            $push_payload = $push->push()
                ->setPlatform('all')
                ->iosNotification($title, $extend)
                ->androidNotification($title, $extend)
                ->addRegistrationId($reg_id)
                ->setNotificationAlert($title);
            $push_payload->addRegistrationId($reg_id)->send();
        }
    } catch (\JPush\Exceptions\APIConnectionException $e) {
        return false;
    } catch (\JPush\Exceptions\APIRequestException $e) {
        return false;
    }
    return true;
}

/**
 * 极光定时推送
 */
function j_push_schedule($title = '', $reg_id, $time, $name)
{
    $config = config('jpush');
    $push = new \JPush\Client($config['ak'], $config['mk']);
    $payload = $push->push()
        ->setPlatform("all")
        ->setNotificationAlert($title);
    try {
        if ($reg_id == 'all') {
            $payload = $payload->addAllAudience()->build();
        } else {
            $payload = $payload->addRegistrationId($reg_id)->build();
        }
        $result = $push->schedule()
            ->createSingleSchedule($name, $payload, array("time" => $time));
    } catch (\JPush\Exceptions\APIConnectionException $e) {
        //dump($e);
        return false;
    } catch (\JPush\Exceptions\APIRequestException $e) {//dump($e);
        return false;
    }
    return $result['body']['schedule_id'];
}


/**
 * 聚合数据发送一条短信
 */
function sendSms($mobile = 0)
{
    ini_set("display_errors", "on");
    header('content-type:text/html;charset=utf-8');
    $sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
    $val = rand(111111, 999999);
    $smsConf = array(
        'key' => config('juhe.smskey'), //您申请的APPKEY
        'mobile' => $mobile, //接收短信的用户手机号码
        'tpl_id' => config('juhe.smsid'), //您申请的短信模板ID，根据实际情况修改
        'tpl_value' => "#code#=$val&#m#=5" //您设置的模板变量，根据实际情况修改
    );
    $content = juhecurl($sendUrl, $smsConf, 1); //请求发送短信
    if ($content) {
        $result = json_decode($content, true);
        $error_code = $result['error_code'];
        if ($error_code == 0) {
            cache('code'.$mobile, $val, 300);
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function curl_get($url)
{
    header('content-type:text/html;charset=utf-8');
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    //设置头文件的信息作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($data, true);
    return $result;
}

/**
 * Created by xiaosong
 * E-mail:306027376@qq.com
 * @param $url
 * @param bool $params
 * @param int $ispost
 * @return bool|mixed
 * 聚合curl请求
 */
function juhecurl($url, $params = false, $ispost = 0)
{
    $httpInfo = array();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($ispost) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_URL, $url);
    } else {
        if ($params) {
            curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
        }
    }
    $response = curl_exec($ch);
    if ($response === FALSE) {
        return false;
    }
    curl_getinfo($ch, CURLINFO_HTTP_CODE);
    array_merge($httpInfo, curl_getinfo($ch));
    curl_close($ch);
    return $response;
}

//重命名数组键值
function array_key($arr, $val)
{
    $temp_key = array_column($arr, $val);//数组指定值
    return array_combine($temp_key, $arr);//修改数组键
}

//人性化时间
function formatTime($time)
{

    if (!is_numeric($time)){
        $time = strtotime($time);
    }
    $time = strtotime($time);

    $rtime = date("m-d H:i", $time);

    $htime = date("H:i", $time);
    $time = time() - $time;
    if ($time < 60) {
        $str = '刚刚';
    } elseif ($time < 60 * 60) {
        $min = floor($time / 60);
        $str = $min . '分钟前';
    } elseif ($time < 60 * 60 * 24) {
        $h = floor($time / (60 * 60));
        $str = $h . '小时前 ';
    } elseif ($time < 60 * 60 * 24 * 3) {
        $d = floor($time / (60 * 60 * 24));
        if ($d == 1) {
            $str = '昨天 ' . $rtime;
        } else {
            $str = '前天 ' . $rtime;
        }
    } else {
        $str = $rtime;
    }
    return $str;
}

/**
 * Created by xiaosong
 * E-mail:4155433@gmail.com
 * 秒数转换为天数
 */
function secToDay($time){

    $day = bcdiv($time,86400,0);
    return $day.'天';

}


/**
 * Created by xiaosong
 * E-mail:306027376@qq.com
 * @param $time
 * @return false|string
 * 人性化时间
 */
function time_format($time)
{
    $publish_timestamp = strtotime($time);
    $now = date("Y-m-d H:i:s");
    $now_timestamp = strtotime($now);
    $lag = ceil(($now_timestamp - $publish_timestamp) / 60);
    $format_time = $lag . "分钟前";
    if ($lag >= 30) {
        switch ($lag) {
            case 30:
                $format_time = "半小时前";
                break;
            case $lag > 30 && $lag < 60:
                $format_time = $lag . "分钟前";
                break;
            case $lag >= 60 && $lag < 120:
                $format_time = "一小时前";
                break;
            case ceil($lag / 60) < 24:
                $format_time = (ceil($lag / 60) - 1) . "小时前";
                break;
            case ceil($lag / 60) > 24 && ceil($lag / 60) < 48:
                $format_time = "昨天" . date("H:i", $publish_timestamp);
                break;
            case ceil($lag / 60) > 48:
                $format_time = date("Y-m-d H:i", $publish_timestamp);
                break;
        }
    }
    return $format_time;
}

/**
 * Created by xiaosong
 * E-mail:306027376@qq.com
 * @param $str
 * @param $field
 * @return bool
 * 查出数据库不允许使用的字段进行判断
 */
function filterWord($str, $field)
{
    $arr = \think\Db::name('extend')->where('id', 1)->value($field);
    $array = explode(',', $arr);
    foreach ($array as $v) {
        if (strstr($str, trim($v)) != false) {
            return $v;
        }
    }
    return false;
}

function form_id_html($form_id = null){

    if (!$form_id){
        return;
    }
    $map['form_id'] = ['in',$form_id];
    $rows = \think\Db::name('skill_form')->where($map)->select();

    $str = '';

    foreach ($rows as $k => $v){

        $str .=
            '
            <div style="margin-top: 15px;">
             <input type="hidden" name="form_id[]" value="'.$v['form_id'].'">
                <input style="float: left"  class="form-control" name="form_name[]" type="text" value="'.$v['form_name'].'">
                 <input  style="margin-left: 5px" type="button" class="btn delStr" value="删除">
            <div style="clear: both"></div></div>
            
            ';
    }

    return $str;
    
}

function tag_id_html($tag = null){

    if (!$tag){
        return;
    }
    $map['tag'] = ['in',$tag];
    $rows = \think\Db::name('skill_tag')->where($map)->select();

    $str = '';

    foreach ($rows as $k => $v){

        $str .=
            '
            <div style="margin-top: 15px;">
             <input type="hidden" name="tag[]" value="'.$v['tag'].'">
                <input style="float: left"  class="form-control" name="tag_name[]" type="text" value="'.$v['tag_name'].'">
                 <input  style="margin-left: 5px" type="button" class="btn delStr" value="删除">
            <div style="clear: both"></div></div>
            
            ';
    }

    return $str;

}

function checkBox($name, $rows, $default, $text)
{
    $html = '<div class="checkbox i-checks">';
    $arr = explode(',', $default);
    foreach ($rows as $row) {
        if (in_array($row[$name], $arr)) {
            $checked = 'checked=""';
        } else {
            $checked = '';
        }
        $html .= '<label><input ' . $checked . ' name="' . $name . '[]" type="checkbox" value="' . $row[$name] . '"> <i></i> ' . $row[$text] . '</label>';
    }
    $html .= '</div>';
    return $html;
}

/**
 *  钱包明细写入
 */
function money($id, $money_type, $money,$coin_type = 1,$remark = '',$order_num= '')
{
    $data['user_id'] = $id;
    $data['remark'] = $remark;
    //1=>红包 2=>竞猜 3=>兑换 4=>拍卖 5=>直播间付费 6=>充值 7=>其它  8=>礼物赠送
    $data['money_type'] = $money_type;
    $data['money'] = $money;
    if ($money < 0) {
        $data['type'] = 2;
    } elseif ($money > 0) {
        $data['type'] = 1;
    }
    $data['create_time'] = time();
    $data['status'] = 1;
    //金币类型 1=>积分 2=>比特币  3=>以太币 4=>BCDN'
    $data['coin_type'] = $coin_type;
    $data['order_num'] = empty($order_num)?'RE'.hashid($id).date("Ymd").rand(1000,9999):$order_num;
    $result = db('money_detail')->insert($data);
    if ($result) {
        return true;
    } else {
        return false;
    }
}

/**
 * Created by xiaosong
 * E-mail:306027376@qq.com
 * @param $money  金额
 * @param int $money_type 金币类型 1=>积分 2=>比特币  3=>以太币 4=>BCDN'
 * @param int $status  状态 1=>平台流水 2=>用户充值 3=>用户提现
 * @param string $remark 备注
 * 资金流水写入
 */
function stream($money,$money_type = 1,$remark = '',$status = 1){
    if ($money <= 0) return false;
    $data['money'] = $money;
    $data['money_type'] = $money_type;
    $data['status'] = $status;
    $data['remark'] = $remark;
    $data['create_time'] = time();
    $result = \think\Db::name('capital_flow')->insert($data);
    if ($result !== false){
        return true;
    }else{
        return false;
    }
}



/**
 * Created by xiaosong
 * E-mail:306027376@qq.com
 * @param $chat_room
 * @return bool|int|mixed
 * 获取融云聊天室成员数量
 */
function chatRoomUserCount($chat_room){
    $num = cache('chatRoomUserCount_'.$chat_room);
    if ($num) return $num;
    $RongCloud = new \rongyun\api\RongCloud(config('rongyun')['appKey'],config('rongyun')['appSecret']);
    $result = $RongCloud->chatroom()->queryUser($chat_room, '500', '2');
    $num = $result['total'] < 100??100;
    if ($num == 100){
        $num += rand(1,99);
    }else{
        $num += rand(99,499);
    }
    cache('chatRoomUserCount_'.$chat_room,$num,300);
    return $num;
}


/**
 * Created by xiaosong
 * E-mail:306027376@qq.com
 * @return Redis
 * 原生redis连接
 */
function redisConnect()
{
    $redis = new \Redis();
    $redis->connect(\think\Env::get('redis_host','127.0.0.1'), \think\Env::get('redis_port',6379));
    $password = \think\Env::get('redis_password','');
    $redis->auth($password);
    return $redis;
}


//超过一万改为xx万，并返回字符串
function num($num){
    $num = abs($num);
    if($num/10000 >= 1){
        $nums = round($num/10000,1).'万';
    }else{
        $nums = "$num";
    }
    return $nums;
}

//删除七牛云视频文件
function delVod($file,$bucket = null,$domain = null)
{
    $qiniu_config = config('qiniu');
    $accessKey = $qiniu_config['ACCESSKEY'];
    $secretKey = $qiniu_config['SECRETKEY'];

    if (!$domain){
        $domain = config('qiniu.domain');
    }

    $file = explode($domain,$file)[1];


    if (!$bucket){
        $bucket = $qiniu_config['bucket'];
    }
    $auth = new Auth($accessKey, $secretKey);
    $config = new Config();
    $bucketManager = new BucketManager($auth,$config);
    $data=$bucketManager->delete($bucket,$file);
    return $data;
}



/**
 * 计算下级总人数
 */
function  junior($user_id = 0){
    $ClassA = \think\Db::name('users')->where('proxy_id',$user_id)->column('user_id');
    $num = count($ClassA);
    if (empty($num)) return $num;
    $map['proxy_id'] = ['in',$ClassA];
    $ClassB = \think\Db::name('users')->where($map)->column('user_id');
    $countB = count($ClassB);
    if (empty($countB)) return $num;
    $num += $countB;
    $where['proxy_id'] = ['in',$ClassB];
    $num += \think\Db::name('users')->where($where)->count('user_id');
    return $num;
}


/**
 *  生成邀请二维码
 */

function code($value = '',$codeName = ''){
    if (empty($codeName)) $codeName = time().rand(1000,9999);
    import('code.phpqrcode');
    $errorCorrectionLevel = 'L';//容错级别
    $matrixPointSize = 6;//生成图片大小
    $model = new \QRcode();
    $path =    "./public/upload/code/$codeName.png";
    //生成二维码图片
    $model->png($value, $path, $errorCorrectionLevel, $matrixPointSize, 2);
    $qiniu_config = config('qiniu');
    $accessKey = $qiniu_config['ACCESSKEY'];
    $secretKey = $qiniu_config['SECRETKEY'];
    $key = $codeName.'.png';
    $auth = new Auth($accessKey, $secretKey);
    $token = $auth->uploadToken($qiniu_config['bucket']);
    $uploadMgr = new UploadManager();
    list($ret, $err) = $uploadMgr->putFile($token,$key,$path);
    if ($err !== null) {
        return false;
    } else {
       return config('qiniu.domain').'/'.$ret['key'];
    }
}

/**
 * Created by xiaosong
 * E-mail:306027376@qq.com
 * 在left和right范围内获取不等于that的值
 */
function get_rand_num($left,$right,$that)
{
    $num = rand($left,$right);
    if($num == $that)
    {
        $num = get_rand_num($left,$right,$that);
    }
    return $num;
}

/**
 * Created by xiaosong
 * E-mail:306027376@qq.com
 * 融云检查用户是否在线
 */
function checkOnline($user_id){
    $model  = new \rongyun\api\RongCloud(config('rongyun')['appKey'],config('rongyun')['appSecret']);
    $result = $model->user()->checkOnline($user_id);
    return $result['status']??0;

}

/**
 * Created by xiaosong
 * E-mail:306027376@qq.com
 * 获取现在距离今天结束的时间差
 */
function todayEndTime(){
    return strtotime(date('Y-m-d 23:59:59'))-time();
}

/**
 * 根据生日计算星座
 */
function get_constellation($birthday)
{

    $month = intval(substr($birthday, 5, 2));
    $day = intval(substr($birthday, 8, 2));
    if ($month < 1 || $month > 12 || $day < 1 || $day > 31)
    {
        return NULL;
    }
    $signs = array(
        array('20' => '水瓶座'),
        array('19' => '双鱼座'),
        array('21' => '白羊座'),
        array('20' => '金牛座'),
        array('21' => '双子座'),
        array('22' => '巨蟹座'),
        array('23' => '狮子座'),
        array('23' => '处女座'),
        array('23' => '天秤座'),
        array('24' => '天蝎座'),
        array('22' => '射手座'),
        array('22' => '摩羯座')
    );
    list($start, $name) = fun_adm_each($signs[$month - 1]);
    if ($day < $start)
    {
        list($start, $name) = fun_adm_each($signs[($month - 2 < 0) ? 11 : $month - 2]);
    }

    return $name;
}


//PHP7.2中代替each()方法
if (!function_exists('fun_adm_each')){
    function fun_adm_each(&$array){
        $res = array();
        $key = key($array);
        if($key !== null){
            next($array);
            $res[1] = $res['value'] = $array[$key];
            $res[0] = $res['key'] = $key;
        }else{
            $res = false;
        }
        return $res;
    }
}

//根据年龄区间 获取日期区间
//$section 年龄区间
function ageDate($section = '1,2',$format = 'date',$type = 'array'){

    $data   = explode(',',$section);
    $endY   = date('Y') - $data[0];
    $end    = date($endY.'-m-d 23:59:59');
    $startY = date('Y') - $data[1];
    $start  = date($startY.'-m-d 00:00:00');

    if ($format != 'date'){
        $end   = strtotime($end);
        $start = strtotime($start);
    }

    if ($type != 'array'){
        return $start.','.$end;
    }else{
        return [$start,$end];
    }
}


//根据年月 获取当月时间区间
//$returnFirstDay 为true返回开始日期，否则返回结束日期
function getMonthRange($date,$format = 'date',$type = 'array'){

    $timestamp = strtotime( $date );

    $monthFirstDay = date( 'Y-m-1 00:00:00', $timestamp );

    $mdays = date( 't', $timestamp );
    $monthLastDay = date( 'Y-m-' . $mdays . ' 23:59:59', $timestamp );

    if ($format != 'date'){
        $monthFirstDay   = strtotime($monthFirstDay);
        $monthLastDay    = strtotime($monthLastDay);
    }

    if ($type != 'array'){
        return $monthFirstDay.','.$monthLastDay;
    }else{
        return [$monthFirstDay,$monthLastDay];
    }

}




//判断是否为非0整数
function isInt($num){

    if (!is_numeric($num)) return false;

    if ($num == 0) return false;

    if(floor($num)==$num){
        return true;
    }else{
        return false;
    }
}

/**
 * Created by xiaosong
 * E-mail:4155433@gmail.com
 * 将数字转换为最多$n位小数
 */
function numberDecimal($num,$n = 2){


    if (floor($num) != $num){

        $temp = explode ( '.', $num );

        $decimal = end ( $temp );

        $count = strlen ( $decimal );

        if ($count >= $n){

            $count = $n;

        }

        $numStr = number_format($num,$count);

    }else{

        $numStr = "$num";

    }

    return $numStr;

}




if (! function_exists('dd')) {
    function dd($args)
    {
        http_response_code(500);

        dump($args);

        die(1);
    }
}

function getAge($birthday){

    if (!is_numeric($birthday)){
        $birthday = strtotime($birthday);
    }

    //格式化出生时间年月日
    $byear=date('Y',$birthday);
    $bmonth=date('m',$birthday);
    $bday=date('d',$birthday);

    //格式化当前时间年月日
    $tyear=date('Y');
    $tmonth=date('m');
    $tday=date('d');

    //开始计算年龄
    $age=$tyear-$byear;
    if($bmonth>$tmonth || $bmonth==$tmonth && $bday>$tday){
        $age--;
    }
    return $age;
}
