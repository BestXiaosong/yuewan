<?php
namespace rongyun\api;


class Chat
{
    public $appKey = '';
    public $appSecret = '';
    public $RongCloud;

    public function __construct()
    {
        $config = config('rongyun');
        $this->appKey = $config['appKey'];
        $this->appSecret = $config['appSecret'];
        $this->RongCloud = new RongCloud($this->appKey,$this->appSecret);
    }


    public function returnHttp($result){
        $result = json_decode($result,true);
        if(isset($result['code']) && $result['code'] == 200) {
            return $result;
        }else{
            return ['code'=>0,'msg'=>$result['errorMessage']];
        }
    }

    /**
     * 获取 Token 方法
     * int      userID  用户ID
     * string   username  用户名称
     * string   head  用户头像
     * */
    public function getToken($userId,$username,$head){
        $result = $this->RongCloud->user()->getToken($userId, $username, $head);
        return $this->returnHttp($result);
    }
    //**************** message **************

    // 发送单聊消息方法（一个用户向另外一个用户发送消息，单条消息最大 128k。每分钟最多发送 6000 条信息，每次发送用户上限为 1000 人，如：一次发送 1000 人时，示为 1000 条消息。）
    public function sendMessageOne($userId,$touserId,$content){
        $result = $this->RongCloud->message()->publishPrivate($userId,$touserId,'RC:VcMsg',$content, 'thisisapush', '{\"pushData\":\"hello\"}', '4', '0', '0', '0', '0');
        echo "publishPrivate    ";
        print_r($result);
        echo "\n";
    }

    // 发送聊天室消息方法（一个用户向聊天室发送消息，单条消息最大 128k。每秒钟限 100 次。）
    public function sendMessageChatroom($userId,$Chatroom,$content){
        $result = $this->RongCloud->message()->publishChatroom($userId, $Chatroom, 'RC:TxtMsg',$content);
        return $this->returnHttp($result);
    }


    // 消息历史记录下载地址获取 方法消息历史记录下载地址获取方法。获取 APP 内指定某天某小时内的所有会话消息记录的下载地址。（目前支持二人会话、讨论组、群组、聊天室、客服、系统通知消息历史记录下载）
    public function getHistory(){
        $result = $this->RongCloud->message()->getHistory('2015010101');
        echo "getHistory    ";
        print_r($result);
        echo "\n";

    }

    //**************** chatroom **************
    //创建聊天室
    public function chatroom($chatRoom){
        $result = $this->RongCloud->chatroom()->create($chatRoom);
        return $this->returnHttp($result);
    }

    // 查询聊天室信息方法
    public function chatroomInfo($chatroomId){
        $result = $this->RongCloud->chatroom()->query($chatroomId);
        return $this->returnHttp($result);
    }

    // 加入聊天室方法
    public function chatroomJoin($userId,$chatroomId){
        $result =  $this->RongCloud->chatroom()->join($userId,$chatroomId);
        return $this->returnHttp($result);
    }

    // 判断用户是否在聊天室
    public function chatroomExist($userId,$chatroomId){
        $result =  $this->RongCloud->chatroom()->exist($userId,$chatroomId);
        return $this->returnHttp($result);
    }

    // 查询聊天室信息方法
    public function chatroomMassge($chatroomId){
        $result = $this->RongCloud->chatroom()->query($chatroomId);
        return $this->returnHttp($result);
    }

    // 查询聊天室内用户方法
    public function chatroomUser($chatroomId){
        $result = $this->RongCloud->chatroom()->queryUser($chatroomId, '500', '2');
        return $this->returnHttp($result);
    }


}