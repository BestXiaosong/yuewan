<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/26
 * Time: 15:19
 */

namespace app\common\logic;


use think\Db;
use think\Model;
use traits\controller\Jump;

class Room extends Model
{



    public function saveChange($data){
//        $config = config('tencent');
//        if (empty($data['time'])){
//            $this->error = '请选择直播过期时间';
//            return false;
//        }
        if(is_numeric($data['id'])){
//            $data['push_url'] = getPushUrl($config['bizid'],$data['id'],$config['key'],$data['time']);
//            $data['play_url'] = getPlayUrl($config['bizid'],$data['id']);
            return $this->validate('room.edit')->allowField(true)->isUpdate(true)->save($data,['room_id'=>$data['id']]);
        }else{
            return  $this->validate('room.add_room')->allowField(true)->save($data);
//            if($this->getLastInsID()){
////                $data['push_url'] = getPushUrl($config['bizid'],$id,$config['key'],$data['time']);
////                $data['play_url'] = getPlayUrl($config['bizid'],$id);
//                return $this->validate(true)->allowField(true)->save($data,['room_id'=>$id]);
//            }else{
//                $this->error = $this->getError();
//                return false;
//            }
        }
    }
    public function add($name,$id,$money,$role_id)
    {
    	$where['room_name'] = $name;
    	$result = $this->where($where)->find();
    	$people_money = db('users')->where(['user_id'=>$id])->value('money');
    	$min_money = db('extend')->where(['id'=>1])->value('sale_room');
        $rule = db('extend')->where(['id'=>1])->value('price_limit');
        if($result){
            $res1 = db('sale_success')->where(['type'=>1,'g_id'=>$result['room_id'],'sale_status'=>0])->find();
            if($res1&&!empty($res1['start_time'])){
                $min_money = $res1['money']+$res1['money']*$rule;
            }else{
                $min_money = $res1['money'];
            }

        }
    	if($min_money>$money){
    	    return 4;
        }
        if($people_money < $money){
            return 6;
        }
    	if($result)
    	{
    		if($result['status'] == 2){
    			$res = db('sale_success')->where(['type'=>1,'g_id'=>$result['room_id'],'sale_status'=>0])->find();
    			if(empty($res['start_time'])){
                    $res['user_id'] = $id;
                    $res['money'] = $money;
                    $res['start_time'] = time();
//                    $res['end_time'] = time() + 24*60*60;
                    $res['end_time'] = time() + 3*60;
                    $res['update_time'] = time();
                    $res['status'] = 0;
                    $result1 = db('sale_success')->update($res);
                    money($id,4,0-$money,1,'拍卖房间'.$name.'加价成功，冻结竞拍金额 ');
                    $data['create_time'] = time();
                    $data['user_id'] = $id;
                    $data['sale_id'] = $res['sale_id'];
                    $data['update_time'] = time();
                    $data['status'] = 0;
                    $data['money'] = $money;
                    $result3 = db('sale_history')->insert($data);
                    return 1;
                }
    			if($res['start_time']>time()||$res['end_time']<time()||$res['end_time']-time()<=30)
    			{
    				return 5;
    			}else{
    				if($res['sale_user_id'] == $id){
    					return 2;
    				}elseif($res['user_id'] == $id){
    					return 3;
    				}elseif($res['money']>=$money){
    					return 4;
    				}else{
                        money($res['user_id'],4,$res['money'],1,'拍卖房间'.$name.'竞拍价格被超越，返还冻结金额');
                        db('users')->where(['user_id'=>$res['user_id']])->setInc('money',$res['money']);
    					$res['user_id'] = $id;
    					$res['money'] = $money;
    					$res['update_time'] = time();
    					db('sale_success')->update($res);
                        db('users')->where(['user_id'=>$id])->setDec('money',$money);
                        money($id,4,0-$money,1,'拍卖房间'.$name.'加价成功，冻结竞拍金额 ');
    					$data['sale_id'] = $res['sale_id'];
    					$data['user_id'] = $id;
    					$results = db('sale_history')->where($data)->find();
    					if(empty($results)){
    					    $data['create_time'] = time();
    					    $data['update_time'] = time();
    					    $data['status'] = 0;
    					    $data['money'] = $money;
    					    db('sale_history')->insert($data);
                        }else{
    					    $results['money'] = $money;
    					    $results['update_time'] = time();
                            db('sale_history')->update($results);
                        }
    					return 1;
    				}
    			}
    			
    		}
    	}else{
    		$data['room_name'] = $name;
    		$data['status'] = 2;
    		$data['create_time'] = time();
    		$data['user_id'] = 0;
    		$data['img'] = config('default_room_img');
    		$data['cid'] = db('play_category')->order('update_time desc  top asc')->limit(0,1)->value('cid');
    		$data['role_id'] = $role_id;
    		$g_id = $this->insertGetId($data);
    		$datas['type'] = 1;
    		$datas['g_id'] = $g_id;
    		$datas['user_id'] = $id;
    		$datas['money'] = $money;
    		$datas['start_time'] = time();
//    		$datas['end_time'] = time()+60*60*24;
    		$datas['end_time'] = time()+60*3;
    		$datas['create_time'] = time();
    		$datas['update_time'] = time();
    		$datas['status'] = 0;
    		$sale_id = db('sale_success')->insertGetId($datas);
            $wheres['sale_id'] = $sale_id;
            $wheres['user_id'] = $id;
            $results = db('sale_history')->where($wheres)->find();
            db('users')->where(['user_id'=>$id])->setDec('money',$money);
            money($id,4,0-$money,1,'拍卖房间'.$name.'加价成功，冻结竞拍金额 ');
            if(empty($results)){
                $wheres['create_time'] = time();
                $wheres['update_time'] = time();
                $wheres['status'] = 0;
                $wheres['money'] = $money;
               db('sale_history')->insert($wheres);
            }
            return 1;
    	}
    }

    public function detail($room_id,$where){
        $result = db('room_notice')->where(['room_id'=>$room_id])->where($where)->order('create_time desc,top desc')->field('title,content,notice_id')->find();
        return $result;
    }
    public function new_notice($room_id){
        $join = [
            ['cl_role r','r.role_id = rm.role_id','left'],
        ];
        $file = 'rm.*,r.role_name';
        $result = db('room_notice')->alias('rm')->field($file)->join($join)->where(['rm.room_id'=>$room_id])->order('rm.create_time desc,rm.top desc')->find();
        return $result;
    }
    public function notice($id,$room_id,$data,$user_id = 0,$notice_id = 0){
        $user = $this->where(['room_id'=>$room_id])->value('user_id');
        $role_ids= db('room_follow')->where(['room_id'=>$room_id,'status'=>2])->column('role_id');
        if($user_id == $user||in_array($id,$role_ids)){
            if($notice_id){
                $result['update_time'] = time();
                if(empty($data)){
                    return -2;
                }else{
                    $res = db('room_notice')->where(['notice_id'=>$notice_id])->update($data);
                }
            }else{
                $data['room_id'] = $room_id;
                $data['role_id'] = $id;
                $data['create_time'] = time();
                $data['update_time'] = time();
                $data['status'] = 1;
                $res = db('room_notice')->insert($data);
            }
//            return db('room_notice')->getLastSql();
            if($res){
                return 1;
            }else{
                return 0;
            }
        }else{
            return -1;
        }
    }


    public function changeStatus($id,$room_id,$notice_id,$del = 0,$up = 0,$user_id = 0){
        $user = $this->where(['room_id'=>$room_id])->value('user_id');
        $role_ids= db('room_follow')->where(['room_id'=>$room_id,'status'=>2])->column('role_id');
        $result = 0;
        if($user_id == $user||in_array($id,$role_ids)){
            if($del){
                $result = db('room_notice')->where(['notice_id'=>$notice_id])->update(['status'=>0]);
            }elseif($up){
                $res = db('room_notice')->where(['notice_id'=>$notice_id])->find();
                if($res['top'] == 0){
                    $res['top'] = 1;
                }else{
                    $res['top'] = 0;
                }
                $result = db('room_notice')->update($res);
            }

            if($result){
                return 1;
            }else{
                return 0;
            }
        }else{
            return -1;
        }
    }


    public function notice_list($room_id){
        $join = [
            ['cl_role r','r.role_id = rm.role_id','left'],
        ];
        $file = 'rm.*,r.role_name';
       $result = db('room_notice')->alias('rm')->order('rm.create_time desc')->field($file)->join($join)->where(['rm.room_id'=>$room_id,'rm.status'=>1])->select();
       foreach ($result as $k=>$v){
           $result[$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
           $result[$k]['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
           $result[$k]['room_id'] = hashid($v['room_id']);
           $result[$k]['role_id'] = hashid($v['role_id']);
       }
       return $result;
    }





    public function VIP_Room($where,$month){
        $result = db('room_charge')->where($where)->find();
        $money = db('users')->where(['user_id'=>$where['user_id']])->value('money');
        if($where['type'] == 1){
            $type = 5;
            $remark = '直播间付费';
            $where['update_time'] = time();
            $results = db('room')->where(['room_id'=>$where['room_id']])->find();
            if($results['VIP'] == 1 && $results['is_charge'] == 1){
                $where['money'] = $month * $results['money'];
            }else{
                return '该房间不满足收费条件';
            }
            if($money<$where['money']){
                return '余额不足,无法进行支付';
            }
            $where['start_time'] = time();
            $where['end_time'] = time() + $month*30*24*60*60;
            $where['status'] = 1;
        }else{
            $type = 7;
            $remark = '活动付费';
            $where['money'] = db('room_activity')->where(['room_id'=>$where['room_id']])->order('activity_id desc ')->value('money');
            $activity = db('room_activity')->where(['room_id'=>$where['room_id']])->order('activity_id desc ')->find();
            $where['activity_id'] = $activity['activity_id'];
            if($activity['status'] == 0){
                api_return(0,'该活动已过期,付费失败');
            }
        }

        if($result){
            $res = db('room_charge')->where(['charge_id'=>$result['charge_id']])->update($where);
        }else{
            $where['create_time'] = time();
            $res = db('room_charge')->insert($where);
        }
        if($res){
            db('users')->where(['user_id'=>$where['user_id']])->setDec('money',$where['money']);
            money($where['user_id'],$type,0-$where['money'],1,$remark);
            return 1;
        }else{
            return '操作失败，请重试';
        }
    }




    public function admin_add_room($name){
        $where['room_name'] = $name;
        $result = $this->where($where)->find();
        if($result){
            return false;
        }else{
            $data['room_name'] = $name;
            $data['status'] = 2;
            $data['create_time'] = time();
            $data['sale_status'] = 0;
            $data['user_id'] = 0;
            $data['img'] = config('default_room_img');
            $data['cid'] = db('play_category')->order('update_time desc  top asc')->limit(0,1)->value('cid');
            $data['role_id'] = 0;
            $g_id = $this->insertGetId($data);
            return $g_id;
        }

    }
}