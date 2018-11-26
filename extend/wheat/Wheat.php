<?php
/**
 *抢麦功能
 *
 *redis缓存
 * ①：wheat_is_open_.{$room_id}  是存储房间是否开启抢麦  1：开启 ； 0：关闭
 * ②：wheat_bit_.{$room_id}  是存储房间麦位   格式：[['id'=>1,'st'=>1],...] id:用户id  st:麦位状态 1开启 0关闭
 *
 *Chat类
 * up() 方法里面用到了融云  判断用户是否在聊天室
 * */
namespace wheat;

use rongyun\api\Chat;
use think\Cache;


class Wheat
{

    /**
     * 上麦/换麦
     *string user_id 用户ID
     *string room_id 房间ID
     *string wheat_id 麦ID
     * */
    public function on($user_id,$room_id,$wheat_id){
        return $this->up($user_id,$room_id,$wheat_id);
    }

    /**
     * 下麦/踢麦
     *string room_id 房间ID
     *string wheat_id 麦ID
     * */
    public function down($room_id,$wheat_id){
        //判断麦位是否存在 (从Redis缓存中读取麦位)
        if($wheat_id<1){
            return $this->error('麦位有误');
        };
        //获取麦位
        $arrWheat =$this->getWheat($room_id);
        $wheat_id_key = $wheat_id-1;
        if(!is_array($arrWheat) || !array_key_exists($wheat_id_key,$arrWheat)){
            return $this->error('该麦位不存在');
        }
        //更新麦位
        $arrWheat[$wheat_id_key]['id'] = 0;
        $ret = $this->setWheat($room_id,$arrWheat);
        if($ret){
            return $this->success('下麦成功',['wheat'=>$arrWheat]);
        }else{
            return $this->error('下麦失败');
        }

    }

    /**
     * 锁麦
     *string room_id//房间ID
     *string wheat_id//麦ID
     * */
    public function lock($room_id,$wheat_id){
        //判断麦位是否存在 (从Redis缓存中读取麦位)
        if($wheat_id<1){
            return $this->error('麦位有误');
        };
        //获取麦位
        $arrWheat =$this->getWheat($room_id);
        $wheat_id_key = $wheat_id-1;
        if(!is_array($arrWheat) || !array_key_exists($wheat_id_key,$arrWheat)){
            return $this->error('该麦位不存在');
        }
        //更新麦位
        $arrWheat[$wheat_id_key]['id'] = 0;
        $arrWheat[$wheat_id_key]['st'] = 0;
        $ret = $this->setWheat($room_id,$arrWheat);
        if($ret){
            return $this->success('锁麦成功',['wheat'=>$arrWheat]);
        }else{
            return $this->error('锁麦失败');
        }
    }

    /**
     * 抱麦Embrace
     *string user_id  用户ID
     *string room_id 房间ID
     *string wheat_id 麦ID
     * */
    public function embrace($user_id,$room_id,$wheat_id){
        return $this->up($user_id,$room_id,$wheat_id,false);
    }

    /**
     * 上麦/换麦 公共方法
     *string user_id 用户ID
     *string room_id 房间ID
     *string wheat_id 麦ID
     *string wheat_id 麦ID
     * */
    private function up($user_id,$room_id,$wheat_id,$is_embrace=true){
        //判断房间开启上麦功能
        if(!$this->isOPenWheat($room_id)){
            return $this->error('抢麦还没开始');
        }

        //判断用户是否在房间
        $chat = new Chat(); //此处用融云判断用户是否在房间
        $ret = $chat->chatroomExist($user_id,$room_id);
        if(!$ret['code'] || !$ret['isInChrm']){
            return $this->error('该用户不在房间中');
        }
        //判断麦位是否关闭 (从Redis缓存中读取麦位)
        if($wheat_id<1){
            return $this->error('麦位有误');
        };
        //获取麦位
        $arrWheat = $this->getWheat($room_id);

        $wheat_id_key = $wheat_id-1;

        if($is_embrace){  //判断是不是抱麦  false:为抱麦
            if(!is_array($arrWheat) || !array_key_exists($wheat_id_key,$arrWheat) || !$arrWheat[$wheat_id_key]['st']){
                return $this->error('该麦位已关闭');
            }
        }
        //判断麦位是否被占
        if($arrWheat[$wheat_id_key]['id']){
            if($arrWheat[$wheat_id_key]['id'] == $user_id){
                return $this->error('已在该麦位上');
            }else{
                return $this->error('该麦位已被占领');
            }
        }
        //判断是否在其他麦位 并进行换麦
        $oldId = -1;
        foreach ($arrWheat as $k=>$v){
            if($v['id'] && $v['id'] == $user_id){
                $oldId = $k;
                break;
            }
        }
        //上麦成功
        $arrWheat[$wheat_id_key]['id'] = $user_id;
        if($oldId>=0){ //更新换麦
            $arrWheat[$wheat_id_key]['id'] = 0;
        }
        $ret = $this->setWheat($room_id,$arrWheat);//更新麦位
        if($ret){
            return $this->success('抢麦成功',['wheat'=>$arrWheat]);
        }else{
            return $this->error('抢麦失败');
        }
    }

    /**
     *判断房间是否抢麦
     * */
    private function isOPenWheat($room_id){
        return Cache::store('redis')->get('wheat_is_open_'.$room_id) == 1 ? true : false;
    }
    /**
     * 获取房间麦位
     * int  room_id 房间ID
     *
     * [['id'=>1,'st'=>1],...] id:用户id  st:麦位状态 1开启 0关闭
     * */
    private function getWheat($room_id){
        return Cache::store('redis')->get('wheat_bit_'.$room_id);
    }

    /**
     * 更新房间麦位
     * int  room_id 房间ID
     * array  data 麦位集合
     * */
    private function setWheat($room_id,$data){
        return Cache::store('redis')->set('wheat_bit_'.$room_id,$data);
    }

    /**
     *失败返回
     * */
    private function error($msg){
        return ['code'=>0,'msg'=>$msg];
    }

    /**
     *成功返回
     * */
    private function success($msg,$data=[]){
        return ['code'=>200,'msg'=>$msg,'data'=>$data];
    }
}