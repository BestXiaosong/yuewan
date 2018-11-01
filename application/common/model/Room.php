<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/26
 * Time: 15:46
 */

namespace app\common\model;


use think\Db;
use think\Model;

class Room extends Model
{
    public function getRoom($where = [])
    {
        $rows = $this->where($where)->field('img,fans,status,room_id,room_name,fans')->select();
        if (empty($rows)) return false;
        foreach ($rows as $k => $v) {

            $map['status'] = 2;
            $map['room_id'] = $v['room_id'];
            $rows[$k]['admins']  = Db::name('room_follow')->where($map)->cache(60)->count('follow_id');
            $rows[$k]['room_id'] = hashid($v['room_id']);
            if ($v['status'] == 1) {
                $rows[$k]['remark'] = '正常';
            } elseif ($v['status'] == 2) {
                $rows[$k]['remark'] = '拍卖中';
            } else {
                $rows[$k]['remark'] = '禁用';
            }
        }
        return $rows;
    }
    public function rooms($name)
    {
        $where['rm.room_name'] = $name;
        $where['rm.status'] = array('neq',0);
//        $where['s.sale_status'] = array('neq',2);
        $results = db('room')->alias('rm')->where($where)->find();
        $where['s.type'] = 1;
        $where['rm.room_name'] = array('like','%'.$name.'%');
        $where['rm.status'] = array('neq',0);

        $join = [
            ['cl_sale_success s','s.g_id = rm.room_id','left'],
        ];
        $file = 's.sale_id,s.money ,rm.room_name,s.update_time,rm.status';

        $result = db('room')->alias('rm')->join($join)->where($where)->order('s.create_time desc')->field($file)->select();
        if($result){
            foreach($result as $k=>$v){
                if($v['status'] == 1){
                    $result[$k]['status_cn'] = '已占用';
                }elseif($v['status'] == 2){
                    $result[$k]['status_cn'] = '拍卖中';
                }
                if($v['money'] == null){
                    $result[$k]['money'] = "0";
                }
                if(empty($v['update_time'])){
                    $result[$k]['update_time'] = time();
                }
                $result[$k]['update_time'] = date("Y-m-d H:i:s", $result[$k]['end_time']);
                $result[$k]['sale_id'] = hashid($v['sale_id']);
            }
        }
        if(!$results)
        {
            $money = db('extend')->where(['id'=>1])->value('sale_room');
            $data = array(array('room_name'=>$name,'status_cn'=>'无人竞拍','status'=>0,'money'=>$money,'update_time'=>date('Y-m-d H:i:s',time()+24*60*60)));
            if($result){
              $result =  array_merge($data,$result);
           }else{
                $result = $data;
           }
        }
        return $result;
    }

    public function getList($where = []){
        return $this
            ->where($where)
            ->order('create_time desc')
            ->field('room_name,top,cid,room_id,img,status,create_time,update_time')
            ->paginate(15,false,['query'=>request()->param()]);
    }





    public function getRows($where = [])
    {
        $exp = new \think\db\Expression('field(a.play_status,1,2,0),a.update_time desc');
        $rows = $this->alias('a')->where($where)
            ->join([
                [ 'play_category b','b.cid = a.cid','LEFT'],
                [ 'role r','r.role_id = a.role_id','LEFT'],
            ])
            ->field('a.cid,b.cate_name,a.play_status,a.room_id,r.role_name,r.header_img,a.detail,a.img,a.room_name,a.VIP,a.official,a.fans')
            ->order($exp)
            ->cache(10)
            ->paginate();
//        var_dump( $rows);exit;
        $items = $rows->items();
        if (empty($items)) return false;
        foreach ($items as $k => $v){
            if ($v['play_status'] == 1){
                $guess['status'] = ['between','1,2'];
                $guess['room_id'] = $v['room_id'];
                $num = Db::name('guess')->where($guess)->count('guess_id');
                if ($num){
                    $items[$k]['remark'] = '竞猜';
                }else{
                    $items[$k]['remark'] = '直播中';
                }
            }elseif ($v['play_status'] == 0){
                $items[$k]['remark'] = '聊天开放中';
            }elseif ($v['play_status'] == 2){
                $items[$k]['remark'] = '直播预告';
            }
            $items[$k]['hot'] = hotValue($v['room_id']);
            $items[$k]['room_id'] = hashid($v['room_id']);
        }
        return ['thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$items,'lastPage'=>$rows->lastPage()];
    }
    
    
    
    
    public function detail($where = [])
    {
        return $this->alias('a')
            ->join('play_category b','b.cid = a.cid','LEFT')
            ->where($where)
            ->field('a.*,b.cate_name')->find();

    }


    public function top($where = [])
    {
        $rows =  $this->alias('a')
            ->where($where)
            ->join([
                [ 'play_category b','b.cid = a.cid','LEFT'],
                [ 'role r','r.role_id = a.role_id','LEFT'],
            ])
            ->order(['a.top'=>'desc','a.update_time'=>'desc'])
            ->limit(4)
            ->field('a.cid,a.play_status,b.cate_name,a.room_id,r.header_img,r.role_name,a.img,a.room_name,a.official,a.VIP,a.fans')
//            ->cache(60)
            ->select();
        if (empty($rows)) return false;
        foreach ($rows as $k => $v){
            if ($v['play_status'] == 1){
                $guess['status'] = ['between','1,2'];
                $guess['room_id'] = $v['room_id'];
                $num = Db::name('guess')->where($guess)->count('guess_id');
                if ($num){
                    $rows[$k]['remark'] = '竞猜';
                }else{
                    $rows[$k]['remark'] = '直播中';
                }
            }elseif ($v['play_status'] == 0){
                $rows[$k]['remark'] = '聊天开放中';
            }elseif ($v['play_status'] == 2){
                $rows[$k]['remark'] = '直播预告';
            }
            $rows[$k]['hot'] = hotValue($v['room_id']);
            $rows[$k]['room_id'] = hashid($v['room_id']);
        }
        return $rows;
    }

    /**
     * 获取房间信息
     */
    public function msg($room_id = 0,$user_id = 0,$role_id = 0,$type = 1)
    {
        $result = block_list($room_id,$role_id,1);
        if($result){
            $this->error = '您已被拉入黑名单，不能进入';
            return false;
        }
        $where['room_id'] = $room_id;
        $where['status'] = 1;
//        $where['play_status'] = ['neq',0];
        $data = $this->where($where)->field('role_id as Homeowner,cid,detail,is_mobile,img,is_close,user_id,room_id,money,is_charge,room_name,VIP,play_status,status,fans,official,create_time')->cache(5)->find();
        if (empty($data)){
            $this->error = '房间处于禁用或拍卖状态,不能开启直播';
            return false;
        }

        //        $data['Homeowner'] = hashid($role_id);
        $data['role_id'] = hashid($role_id);


        $data['Homeowner'] = hashid($data['Homeowner']);
        if ($data['user_id'] == $user_id){
            $data['is_Homeowner'] = 1;//是否为房主
            $data['is_admin'] = 1;
        }else{
            $data['is_Homeowner'] = 0;
            $term['role_id'] = $role_id;
            $term['room_id'] = $room_id;
            $term['status']  = 2;
            $data['is_admin'] = Db::name('room_follow')->where($term)->value('follow_id')?1:0; //是否为房间管理员
        }
        if ($data['VIP'] == 1 && $data['user_id'] != $user_id && $data['is_charge'] == 1 && $data['money'] != 0){

            $map['status']  = 1;
//            $map['user_id'] = $user_id;
            $map['role_id'] = $role_id;
            $map['room_id'] = $room_id;
            $map['end_time'] = ['>',time()];
            $map['type'] = 1;
            $charge_id = Db::name('room_charge')->where($map)->cache(3)->value('charge_id');
            if (!$charge_id){
               api_return(400,'该房间是付费房间，请支付后进行观看');
            }

        }

        $data['activity_id'] = 0;

        $act['room_id'] = $room_id;
        $act = Db::name('room_activity')->order('activity_id desc')->where($act)->field('status,charge,activity_id,money,start_time')->cache(3)->find();

        if ($act){
            if ($act['status'] == 2){
                $data['activity_id'] = $act['activity_id'];
            }
            $data['start_time'] = $act['start_time'];
            if ($act['charge'] == 1 && $act['status'] == 1){
                if ($data['user_id'] != $user_id){
                    $charge['room_id'] = $room_id;
                    $charge['activity_id'] = $act['activity_id'];
                    $charge['type']    = 2;
    //                $charge['user_id'] = $user_id;
                    $charge['role_id'] = $role_id;
                    $id = Db::name('room_charge')->where($charge)->cache(3)->value('charge_id');

                    if (!$id) api_return(300,'本次活动需收费,请先付费');
                }
            }
        }



        unset($data['user_id']);

        $data['room_id'] = hashid($data['room_id']);

        if ($type == 2){//房间主页信息

            $num['status'] = 2;
            $num['room_id'] = $room_id;
            $data['admin_num'] =  Db::name('room_follow')->where($num)->count('follow_id');//房间管理员数量
            $user['a.room_id'] = $room_id;
            $user['a.status']  = 1;
            $fans = [];
            $fans = Db::name('room_follow')
                ->alias('a')
                ->join([
                    ['role r','r.role_id = a.role_id','left'],
                ])
                ->where($user)
                ->field('r.role_name,r.header_img,r.role_id')
                ->cache(60)
                ->limit(5)
                ->select();
            if (!empty($fans)){
                foreach ($fans as $k => $v){
                    $fans[$k]['role_id'] = hashid($v['role_id']);
                }
            }
            $data['cate_name'] = Db::name('play_category')->where('cid',$data['cid'])->value('cate_name');
            $data['room_user'] = $fans;
            $notice['room_id'] = $room_id;
            $notice['status']  = 1;
            $data['notice_num'] = Db::name('room_notice')->where($notice)->cache(60)->count('notice_id');
            $data['notice'] = Db::name('room_notice')->where($notice)->order(['top'=>'desc','update_time'=>'desc'])->cache(60)->value('title');
        }else{
            $data['chat_id'] = 'room_'.$data['room_id']; //聊天室融云聊天室id
            $data['chat_id_play'] = 'play_'.$data['room_id']; //直播间融云聊天室id
            $data['hot']     = hotValue($data['room_id']);//房间热度
            $follow['role_id'] = $role_id;
            $follow['room_id'] = $room_id;

            $follow['status']  = ['between','1,2'];
            $data['is_follow'] = Db::name('room_follow')->where($follow)->value('follow_id')?1:0;
        }
        $history = db('play_history')->where(['play_id'=>$room_id,'type'=>2,'user_id'=>$user_id])->find();
        if($history){
            db('play_history')->where(['play_id'=>$room_id,'type'=>2,'user_id'=>$user_id])->update(['update_time'=>time()]);
        }else{
            $his['play_id'] = $room_id;
            $his['type'] = 2;
            $his['user_id'] = $user_id;
            $his['status'] = 1;
            $his['create_time'] = time();
            $his['update_time'] = time();
            $his['role_id'] = $role_id;
            db('play_history')->insert($his);
        }
        return $data;
    }


    public function no_login_msg($room_id = 0,$type = 1)
    {
        $where['room_id'] = $room_id;
        $where['status'] = 1;
//        $where['play_status'] = ['neq',0];
        $data = $this->where($where)->field('role_id as Homeowner,cid,detail,is_mobile,img,is_close,user_id,room_id,money,is_charge,room_name,VIP,play_status,status,fans,official,create_time')->cache(5)->find();
        if (empty($data)){
            $this->error = '房间处于禁用或拍卖状态,不能开启直播';
            return false;
        }

        $data['role_id'] = hashid(0);
        $data['Homeowner']    = hashid($data['Homeowner']);
        $data['is_Homeowner'] = 0;//是否为房主
        $data['is_admin']     = 0;

        $data['activity_id'] = 0;


        if ($data['VIP'] == 1 && $data['is_charge'] == 1 && $data['money'] != 0){
            api_return(0,'该房间为VIP房间,请先登录再进入');

        }

        $data['start_time'] = 0;

        $act['room_id'] = $room_id;

        $act = Db::name('room_activity')->order('activity_id desc')->where($act)->field('status,charge,activity_id,money,start_time')->cache(3)->find();

        if ($act) {
            $data['start_time'] = $act['start_time'];
            if ($act['charge'] == 1 && $act['status'] == 1) {
                api_return(300, '本次活动需收费,请先付费');
            }
        }



            unset($data['user_id']);

        $data['room_id'] = hashid($data['room_id']);

        if ($type == 2){//房间主页信息
            $num['status'] = 2;
            $num['room_id'] = $room_id;
            $data['admin_num'] =  Db::name('room_follow')->where($num)->count('follow_id');//房间管理员数量
            $user['a.room_id'] = $room_id;
            $user['a.status']  = 1;
            $fans = [];
            $fans = Db::name('room_follow')
                ->alias('a')
                ->join([
                    ['role r','r.role_id = a.role_id','left'],
                ])
                ->where($user)
                ->field('r.role_name,r.header_img,r.role_id')
                ->cache(60)
                ->limit(5)
                ->select();
            if (!empty($fans)){
                foreach ($fans as $k => $v){
                    $fans[$k]['role_id'] = hashid($v['role_id']);
                }
            }
            $data['cate_name'] = Db::name('play_category')->where('cid',$data['cid'])->value('cate_name');
            $data['room_user'] = $fans;
            $notice['room_id'] = $room_id;
            $notice['status']  = 1;
            $data['notice_num'] = Db::name('room_notice')->where($notice)->cache(60)->count('notice_id');
            $data['notice'] = Db::name('room_notice')->where($notice)->order(['top'=>'desc','update_time'=>'desc'])->cache(60)->value('title');
        }else{
            $data['chat_id'] = 'room_'.$data['room_id']; //聊天室融云聊天室id
            $data['chat_id_play'] = 'play_'.$data['room_id']; //直播间融云聊天室id
            $data['hot']     = hotValue($data['room_id']);//房间热度
            $follow['role_id'] = hashid(0);
            $follow['room_id'] = $room_id;
            $follow['status']  = ['between','1,2'];
            $data['is_follow'] = Db::name('room_follow')->where($follow)->value('follow_id')?1:0;
        }



        return $data;
    }








    /**
     * 获取房主及管理员
     */
    public function getAdmins($where = [])
    {
        $data = [];
        //房主信息
        $admin = $this
            ->alias('a')
            ->where($where)
            ->join([
                ['role r','r.role_id = a.role_id','left'],
            ])
            ->field('r.role_name,r.header_img,r.role_id')
            ->cache(60)
            ->find();
//        $admin = Db::name('role')->where('role_id',$role_id)->field('role_name,header_img,role_id')->find();
        if (!empty($admin)){
            $admin['role_id'] = hashid($admin['role_id']);
            $admin['status']  = 1;
            $admin['remark']  = '房主';
        }
        $data[] = $admin;
        //管理员状态为粉丝表状态2
        $where['a.status'] = 2;
        //管理员列表
        $admins = db('room_follow')
            ->alias('a')
            ->where($where)
            ->join([
                ['role r','r.role_id = a.role_id','left'],
            ])
            ->field('r.role_name,r.header_img,r.role_id,a.status')
            ->cache(2)
            ->select();
        if (!empty($admins)){
            foreach ($admins as $k => $v) {
                $admins[$k]['role_id'] = hashid($v['role_id']);
                $admins[$k]['remark'] = '管理员';
                $data[] = $admins[$k];
            }
        }
        return $data;
    }



    /**
     * 获取房间成员
     */
    public function getUsers($where = [])
    {
        $data = [];
        //房主信息
        $admin = $this
            ->alias('a')
            ->where($where)
            ->join([
                ['role r','r.role_id = a.role_id','left'],
            ])
            ->field('r.role_name,r.header_img,r.role_id')
            ->cache(60)
            ->find();
        if (!empty($admin)){
            $admin['role_id'] = hashid($admin['role_id']);
            $admin['remark']  = '房主';
        }
        $data[] = $admin;
        //管理员状态为粉丝表状态2
        $where['a.status'] = 2;
        //管理员列表
        $admins = db('room_follow')
            ->alias('a')
            ->where($where)
            ->join([
                ['role r','r.role_id = a.role_id','left'],
            ])
            ->field('r.role_name,r.header_img,r.role_id')
            ->cache(60)
            ->select();
        if (!empty($admins)){
            foreach ($admins as $k => $v) {
                $admins[$k]['role_id'] = hashid($v['role_id']);
                $admins[$k]['remark'] = '管理员';
                $data[] = $admins[$k];
            }
        }
        return $data;
    }



    public function share_room_info($room_id){
        $where['a.room_id'] = $room_id;
        $where['a.status'] = 1;
//        $where['play_status'] = ['neq',0];
        $join = [
            ['cl_role r','a.role_id = r.role_id','left']
        ];
        $data = $this->alias('a')->join($join)->where($where)->field('a.role_id as Homeowner,a.cid,a.brief,a.is_mobile,a.img,a.is_close,a.user_id,a.room_id,a.money,a.is_charge,a.room_name,a.VIP,a.play_status,a.status,a.official,a.create_time,r.header_img,r.sex,r.sign,`r`.`official` as `check`')->cache(60)->find();
        if (empty($data)){
            return false;
        }
        if ($data['is_charge'] == 1 ){
               return false;
        }
        $data['chat_id_play'] = 'play_'.hashid($room_id);
        $data['chat_id'] = 'room_'.hashid($room_id);
        return $data;
    }
}