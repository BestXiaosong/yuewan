<?php
/**
 *抢麦功能
 *
 *redis缓存
 * ①：wheat_is_open_.{$room_id}  是存储房间是否开启抢麦  1：开启 ； 0：关闭
 * ②：wheat_bit_.{$room_id}  是存储房间麦位   格式：[['user_id'=>1,'st'=>1],...] user_id:用户id  st:麦位状态 1开启 0关闭
 *
 *Chat类
 * up() 方法里面用到了融云  判断用户是否在聊天室
 * */
namespace wheat;

use app\api\controller\User;
use rongyun\api\Chat;
use think\Cache;


class Wheat
{


    public function test()
    {
        [
            ['id'=>1,'st'=>1],
            ['id'=>1,'st'=>1],
            ['id'=>1,'st'=>1],
            ['id'=>1,'st'=>1],
            ['id'=>1,'st'=>1],
            ['id'=>1,'st'=>1],
            ['id'=>1,'st'=>1],
        ];

    }



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
        $arrWheat[$wheat_id_key]['user_id'] = 0;
        $arrWheat[$wheat_id_key]['header_img'] = '';
        $arrWheat[$wheat_id_key]['nick_name'] = '';
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
        $arrWheat[$wheat_id_key]['user_id'] = 0;
        $arrWheat[$wheat_id_key]['st'] = 0;
        $arrWheat[$wheat_id_key]['header_img'] = '';
        $arrWheat[$wheat_id_key]['nick_name'] = '';
        $ret = $this->setWheat($room_id,$arrWheat);
        if($ret){
            return $this->success('锁麦成功',['wheat'=>$arrWheat]);
        }else{
            return $this->error('锁麦失败');
        }
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 开麦
     */
    public function open($room_id,$wheat_id)
    {
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
        $arrWheat[$wheat_id_key]['user_id'] = 0;
        $arrWheat[$wheat_id_key]['st'] = 1;
        $arrWheat[$wheat_id_key]['header_img'] = '';
        $arrWheat[$wheat_id_key]['nick_name'] = '';
        $ret = $this->setWheat($room_id,$arrWheat);
        if($ret){
            return $this->success('开麦成功',['wheat'=>$arrWheat]);
        }else{
            return $this->error('开麦失败');
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

        if (is_numeric($user_id)){
            $user_id = hashid($user_id);
        }


        //判断用户是否在房间
        $chat = new Chat(); //此处用融云判断用户是否在房间
        $ret = $chat->chatroomExist($user_id,$room_id);
        if(!$ret['code'] || !$ret['isInChrm']){
//            return $this->error('该用户不在房间中');
        }
        //判断麦位是否关闭 (从Redis缓存中读取麦位)
        if($wheat_id<1){
            return $this->error('麦位有误');
        };
        //获取麦位
        $arrWheat = $this->getWheat($room_id);

        $wheat_id_key = $wheat_id-1;
        if(!is_array($arrWheat) || !array_key_exists($wheat_id_key,$arrWheat) || !$arrWheat[$wheat_id_key]['st']){
            return $this->error('该麦位已关闭');
        }

        if($arrWheat[$wheat_id_key]['user_id'] == $user_id){
            return $this->error('已在该麦位上');
        }

        if($is_embrace){  //判断是不是抱麦  false:为抱麦
            //判断麦位是否被占
            if($arrWheat[$wheat_id_key]['user_id']){
                return $this->error('该麦位已被占领');
            }
        }

        //判断是否在其他麦位 并进行换麦
        $oldId = -1;

        foreach ($arrWheat as $k=>$v){
            if($v['user_id'] && $v['user_id'] == $user_id){
                $oldId = $k;
                break;
            }
        }

        //上麦成功
        $arrWheat[$wheat_id_key]['user_id'] = $user_id;

        $id = dehashid($user_id);

        $arrWheat[$wheat_id_key]['header_img'] = User::staticInfo('header_img',$id);
        $arrWheat[$wheat_id_key]['nick_name']  = User::staticInfo('nick_name',$id);

        if($oldId>=0){ //更新换麦
            $arrWheat[$oldId]['user_id'] = 0;
            $arrWheat[$oldId]['header_img'] = '';
            $arrWheat[$oldId]['nick_name']  = '';
        }
        $ret = $this->setWheat($room_id,$arrWheat);//更新麦位
        if($ret){
            return $this->success('上麦成功',['wheat'=>$arrWheat]);
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
     * [['user_id'=>1,'st'=>1],...] user_id:用户id  st:麦位状态 1开启 0关闭
     * */
    public function getWheat($room_id){
        return Cache::store('redis')->get('wheat_bit_'.$room_id);
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 获取房间下指定麦位信息
     */
    public function getWheatId($room_id,$wheat_id){

        return Cache::store('redis')->get('wheat_bit_'.$room_id)[$wheat_id];

    }
    public static function staticWheatId($room_id,$wheat_id){

        return Cache::store('redis')->get('wheat_bit_'.$room_id)[$wheat_id];

    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * @param $room_id |要获取的房间id
     * @param int $wheat |麦位数 房间不存在时根据麦位数创建麦位
     * @return array|mixed
     * 获取或设置房间当前麦位
     */
    public static function wheat($room_id,$wheat = 5){
        $data = Cache::store('redis')->get('wheat_bit_'.$room_id);
        if (!is_array($data)){
            $data = self::wheatInit($room_id,$wheat);
        }
        $is_open = Cache::store('redis')->get('wheat_is_open_'.$room_id);

        if (!is_numeric($is_open)){
            Cache::store('redis')->set('wheat_is_open_'.$room_id,1);
        }
        return $data;
    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 麦位初始化
     */
    public static function wheatInit($room_id,$wheat){
        $data = [];
        for ($i = 1;$i <= $wheat;$i++){
            $arr['wheat_id'] = $i;
            $arr['user_id'] = 0;
            $arr['st'] = 1;
            $arr['header_img'] = '';
            $arr['nick_name']  = '';
            $data[] = $arr;
        }
        Cache::store('redis')->set('wheat_bit_'.$room_id,$data);
        return $data;
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