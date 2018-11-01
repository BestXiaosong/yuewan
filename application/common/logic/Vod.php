<?php
namespace app\common\logic;

use app\admin\controller\Upload;
use think\Model;

class Vod extends Model
{

    protected $autoWriteTimestamp = true;

    public function saveChange($data){
        if(is_numeric($data['id'])){
            return $this->validate(true)->allowField(true)->save($data,['pid'=>$data['id']]);
        }else{
             return $this->validate(true)->allowField(true)->save($data);
        }
    }

    public function up($where = [],$id){
        $where['role_id'] = db('users')->where(['user_id'=>$id])->value('role_id');
//        return db('users')->getLastSql();
        $result = db('vod_up')->where($where)->find();
        if($result){
            $result['update_time'] = time();
            if($result['status'] == 0){
                $result['status'] = 1;
                $this->where(['pid'=>$where['pid']])->setInc('up');
            }else{
                $result['status'] = 0;
                $this->where(['pid'=>$where['pid']])->setDec('up');
            }
            $result = db('vod_up')->update($result);
        }else{
            $data['pid'] = $where['pid'];
            $data['role_id'] = $where['role_id'];
            $data['status'] = 1;
            $data['create_time'] = time();
            $this->where(['pid'=>$where['pid']])->setInc('up');
            $result = db('vod_up')->insert($data);
        }
        if($result){
            return true;
        }else{
            return false;
        }
    }


    public function reply($data,$id,$pid){
        $data['role_id']  = db('users')->where(['user_id'=>$id])->value('role_id');
        $data['create_time'] = time();
        $data['status'] = 1;
        $data['pid'] = $pid;
        $result = db('vod_reply')->insert($data);
        $res = $this->where(['pid'=>$pid])->setInc('reply_num');
        if($result){
            return true;
        }else{
            return false;
        }
    }

    public function share($pid){
        $res = $this->where(['pid'=>$pid])->setInc('share_num');
        if($res){
            return true;
        }else{
            return false;
        }
    }


    public function click($pid,$id){
        $film = 'v.create_time,v.reply_id,v.pid,v.p_id,v.content,r.role_name,r.header_img';
        $joins = [
            ['cl_role r','v.role_id = r.role_id','left']
        ];
        $this->where(['pid'=>$pid])->setInc('num');
        $res = $this->where(['pid'=>$pid])->find();
        $people = db('users')->where(['user_id'=>$id])->find();
        $result = db('vod_reply')->alias('v')->field($film)->join($joins)->where(['v.pid'=>$pid,'v.p_id'=>0])->select();
        foreach($result as $k=>$v){
            $result[$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
            $result[$k]['detail'] = db('vod_reply')->alias('v')->field($film)->join($joins)->where(['v.pid'=>$pid,'v.p_id'=>$v['reply_id']])->select();
            foreach ($result[$k]['detail'] as $key=>$val){
                $result[$k]['detail'][$key]['create_time'] = date('Y-m-d H:i:s',$val['create_time']);
            }

        }
        $data['reply'] = $result;

        $files = 'pid,title,num,reply_num,share_num,up,img,detail,play_url,create_time';
        $details = db('vod')->field($files)->where(['pid'=>$pid])->find();
        $details['create_time'] = date('Y-m-d H:i:s',$details['create_time']);
        $details['num'] = num($details['num']);
        $details['share_num'] = num($details['share_num']);
        $details['reply_num'] = num($details['reply_num']);
        $details['up'] = num($details['up']);
        $data['vod'] = $details;
        $join = [
            ['cl_role r','u.role_id = r.role_id','left']
        ];
        $file = 'r.role_id,u.check,r.role_name,r.header_img,r.official,r.sex,r.fans_num,r.sign';

        $data['information'] = db('users')->alias('u')->field($file)->where(['u.user_id'=>$res['user_id']])->join($join)->find();
        $url = db('explain')->where(['id'=>12])->cache(86400)->value('content');
        $data['information']['shareUrl'] = $url.'index/share_vod_detail/pid/'.hashid($pid).'/user_id/'.hashid($id);
        $data['information']['fans_status'] = 0;
        $results = db('role_follow')->where(array('role_id'=>$data['information']['role_id'],'follow_role_id'=>$people['role_id']))->find();
        $up_status = db('vod_up')->where(['pid'=>$pid,'role_id'=>$people['role_id']])->value('status');
        $data['information']['role_id'] = hashid($data['information']['role_id']);
//        return db('role_follow')->getLastSql();
        $data['information']['fans_num'] = num($data['information']['fans_num']);
        if($results){
            if($results['status'] == 1){
                $data['information']['fans_status'] = 1;
            }
        }
        $data['information']['up_status'] = 0;
        if($up_status&&$up_status == 1){
            $data['information']['up_status'] = 1;
        }

        $history = db('play_history')->where(['play_id'=>$pid,'type'=>1,'user_id'=>$id])->find();
        if($history){
            db('play_history')->where(['play_id'=>$pid,'type'=>1,'user_id'=>$id])->update(['update_time'=>time()]);
        }else{
            $his['play_id'] = $pid;
            $his['type'] = 1;
            $his['user_id'] = $id;
            $his['status'] = 1;
            $his['create_time'] = time();
            $his['update_time'] = time();
            $his['role_id'] = $people['role_id'];
            db('play_history')->insert($his);
        }
        return $data;
    }


//    public function fans($pid,$id)
//    {
//        $res = $this->where(['pid'=>$pid])->find();
//        $people = $this->where(['pid'=>$id])->find();
//        $results = db('role_follow')->where(['role_id'=>$res['role_id'],'follow_role_id'=>$people['role_id']])->find();
//        if($results){
//            if($results['status'] == 1){
//              $results['status'] = 0;
//              db('role')->where(['role_id'=>$res['role_id']])->setDec('fans_num');
//            }elseif($results['status'] == 0){
//                $results['status'] = 1;
//                db('role')->where(['role_id'=>$res['role_id']])->setInc('fans_num');
//            }
//            $result = db('role_follow')->update($results);
//        }else{
//            $data['role_id'] = $res['role_id'];
//            $data['follow_role_id'] = $people['role_id'];
//            $data['status'] = 1;
//            $data['create_time'] = time();
//            $result = db('role_follow')->insert($data);
//            db('role')->where(['role_id'=>$res['role_id']])->setInc('fans_num');
//        }
//        if($result){
//            return true;
//        }else{
//            return false;
//        }
//    }

    public function upload($id,$space,$data){
        $info = db('users')->where(['user_id'=>$id])->find();
        if(($space+$info['use_space']) > $info['bucket_space']){
            return 0;
        }else{
            $data['user_id'] = $id;
            $data['create_time'] = time();
            $result = $this->insert($data);
            db('users')->where(['user_id'=>$id])->setInc('use_space',$space);
            if($result){
                return 1;
            }else{
                return 2;
            }
        }
    }

    public function del($id){
        $result = db('vod')->where(['user_id'=>$id])->select();
        foreach ($result as $k=>$v){
            $model = new Upload();
            $model->delete1($v['play_url']);

        }
       db('vod')->where(['user_id'=>$id])->delete();


        $results = db('users')->where(['user_id'=>$id])->update(['use_space'=>0]);
        if($results != false){
            return true;
        }else{
            return false;
        }
    }




    public function share_click($pid){
        $film = 'v.create_time,v.reply_id,v.pid,v.p_id,v.content,r.role_name,r.header_img';
        $joins = [
            ['cl_role r','v.role_id = r.role_id','left']
        ];
        $this->where(['pid'=>$pid])->setInc('num');
        $res = $this->where(['pid'=>$pid])->find();
        if(empty($res)){
            return false;
        }
        $result = db('vod_reply')->alias('v')->field($film)->join($joins)->where(['v.pid'=>$pid,'v.p_id'=>0])->select();
        foreach($result as $k=>$v){
            $result[$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
            $result[$k]['detail'] = db('vod_reply')->alias('v')->field($film)->join($joins)->where(['v.pid'=>$pid,'v.p_id'=>$v['reply_id']])->select();
            foreach ($result[$k]['detail'] as $key=>$val){
                $result[$k]['detail'][$key]['create_time'] = date('Y-m-d H:i:s',$val['create_time']);
            }

        }
        $data['reply'] = $result;
        $files = 'pid,title,num,reply_num,share_num,up,img,detail,play_url,create_time';
        $details = db('vod')->field($files)->where(['pid'=>$pid])->find();
        $details['create_time'] = date('Y-m-d H:i:s',$details['create_time']);
        $details['num'] = num($details['num']);
        $details['share_num'] = num($details['share_num']);
        $details['reply_num'] = num($details['reply_num']);
        $details['up'] = num($details['up']);
        $data['vod'] = $details;
        $join = [
            ['cl_role r','u.role_id = r.role_id','left']
        ];
        $file = 'r.role_id,u.check,r.role_name,r.header_img,r.official,r.sex,r.fans_num,r.sign';
        $data['information'] = db('users')->alias('u')->field($file)->where(['u.user_id'=>$res['user_id']])->join($join)->find();
        $data['information']['fans_status'] = 0;
        $data['information']['role_id'] = hashid($data['information']['role_id']);
//        return db('role_follow')->getLastSql();
        $data['information']['fans_num'] = num($data['information']['fans_num']);
        $data['information']['up_status'] = 0;
        return $data;
    }




    public function no_login_click($pid){
        $film = 'v.create_time,v.reply_id,v.pid,v.p_id,v.content,r.role_name,r.header_img';
        $joins = [
            ['cl_role r','v.role_id = r.role_id','left']
        ];
        $this->where(['pid'=>$pid])->setInc('num');
        $res = $this->where(['pid'=>$pid])->find();
        $result = db('vod_reply')->alias('v')->field($film)->join($joins)->where(['v.pid'=>$pid,'v.p_id'=>0])->select();
        foreach($result as $k=>$v){
            $result[$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
            $result[$k]['detail'] = db('vod_reply')->alias('v')->field($film)->join($joins)->where(['v.pid'=>$pid,'v.p_id'=>$v['reply_id']])->select();
            foreach ($result[$k]['detail'] as $key=>$val){
                $result[$k]['detail'][$key]['create_time'] = date('Y-m-d H:i:s',$val['create_time']);
            }

        }
        $data['reply'] = $result;
        $files = 'pid,title,num,reply_num,share_num,up,img,detail,play_url,create_time';
        $details = db('vod')->field($files)->where(['pid'=>$pid])->find();
        $details['create_time'] = date('Y-m-d H:i:s',$details['create_time']);
        $details['num'] = num($details['num']);
        $details['share_num'] = num($details['share_num']);
        $details['reply_num'] = num($details['reply_num']);
        $details['up'] = num($details['up']);
        $data['vod'] = $details;
        $join = [
            ['cl_role r','u.role_id = r.role_id','left']
        ];
        $file = 'r.role_id,u.check,r.role_name,r.header_img,r.official,r.sex,r.fans_num,r.sign';
        $data['information'] = db('users')->alias('u')->field($file)->where(['u.user_id'=>$res['user_id']])->join($join)->find();
        $url = db('explain')->where(['id'=>12])->cache(86400)->value('content');
        $data['information']['shareUrl'] = $url.'index/share_vod_detail/pid/'.hashid($pid).'/user_id/'.hashid($id);
        $data['information']['fans_status'] = 0;
        $data['information']['role_id'] = hashid($data['information']['role_id']);
//        return db('role_follow')->getLastSql();
        $data['information']['fans_num'] = num($data['information']['fans_num']);
        $data['information']['fans_status'] = 0;
        $data['information']['up_status'] = 0;
        return $data;
    }
}