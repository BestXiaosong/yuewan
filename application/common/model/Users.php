<?php
namespace app\common\model;

use think\Model;


class Users extends Model
{
    protected $dateFormat = 'Y-m-d H:i';

    public function profile()
    {
        return $this->hasOne('users', 'user_id', 'proxy_id')->field('nick_name');
    }

    public function getDetail($where = [])
    {
        $user = $this->alias('a')->join('role r','r.role_id = a.role_id','left')
            ->where($where)
            ->field('a.check,a.btc,a.eth,a.money,a.user_id,a.proxy_id,r.role_name,r.sex,r.first_time,a.BCDN,r.sign,r.birthday,r.place,r.official,r.fans_num,r.role_id,a.phone,r.header_img')
            ->find();
        if (empty($user['proxy_id'])){
            $user['proxy_id'] = '平台';
        }else{
            $phone = $this->where('user_id', $user['proxy_id'])->cache(60)->value('phone');
            if (!empty($phone)){
                $user['proxy_id'] = '平台';
            }else{
                $user['proxy_id'] =  substr($phone,0,3).'****'.substr($phone,-4);
            }
        }
        $user['public']  = hashToken($user['user_id']);
        $user['user_id'] = hashid($user['user_id']);
        $user['role_id'] = hashid($user['role_id']);
        return $user;
    }

    /**
     * 后台获取用户列表
     */
    public function userList($where = [])
    {
        return $this
            ->where($where)
            ->order('user_id desc')
            ->paginate('', false, ['query' => request()->param()]);
    }


    public function getRows($where = [])
    {
        $rows = $this->where($where)->order('create_time desc')->field('real_name,user_id')->paginate();
        $items = $rows->items();
        if (empty($items)) return false;
        return ['thisPage' => $rows->currentPage(), 'hasNext' => $rows->hasMore(), 'data' => $items];
    }

    //查询用户积分余额
    public function getMoneyByUserId($user_id)
    {
        return $this->where('user_id',$user_id)->field('money,BCDN,eth,btc')->find();
    }

    //获取我的粉丝
    public function fans($role_id,$list_num = ''){
        $join = [
            ['cl_role r','r.role_id = rf.follow_role_id','left']
        ];
        $file = 'r.role_id,r.role_name,r.header_img';
        $where['rf.role_id'] = $role_id;
        $where['rf.status']  = 1;
        $data = db('role_follow')->alias('rf')->field($file)->join($join)->where($where)->paginate($list_num);
        $count = db('role_follow')->alias('rf')->field($file)->join($join)->where($where)->count();
        $items = $data->items();
        foreach ($items as $k=>$v){
            $items[$k]['role_id'] = hashid($v['role_id']);
        }
        if (empty($items)) return false;
        return ['thisPage' => $data->currentPage(), 'hasNext' => $data->hasMore(),'total_num'=>$count, 'data' => $items];

    }


    //获取是否能够升级
    public function space_up($id){
       $space = db('users')->where(['user_id'=>$id])->find();
       if($space['space_time'] != 0){
           return 0;
       }else{
           return 1;
       }
    }

    // 获取升级空间所需金额
    public function money($month,$size){
        $unit = db('extend')->where(['id'=>1])->value('space_money');
        $total_money = $unit*$month*$size;
        return $total_money;
    }



    public function order_detail($order_num){
        $join = [
            ['cl_users u','u.user_id = md.user_id','left'],
        ];
        $file = 'md.money,md.create_time,md.order_num,u.bucket_space';
       $result =  db('money_detail')->alias('md')->field($file)->where(['md.order_num'=>$order_num])->join($join)->find();
       $result['create_time'] = date("Y/m/d H:i",$result['create_time']);
       $result['money'] = abs($result['money']);
       $result['bucket_space'] = $result['bucket_space'].'G';
       return $result;
    }


    public function binding($type,$open_id,$data){
        $result = db('users')->where($data)->find();
        if(empty($result)) return '该手机号尚未注册';
        //type 为 1 代表微信第三方  2 代表QQ  3 代表莓果
        if($type == 'web_qq'){
            $results = db('user_extend')->where(['user_id'=>$result['user_id']])->value('web_qq');
        }elseif($type == 'web_wx'){
            $results = db('user_extend')->where(['user_id'=>$result['user_id']])->value('web_wx');
        }elseif($type == 'mg'){
            $results = db('user_extend')->where(['user_id'=>$result['user_id']])->value('UID');
        }else{
            $results = '';
        }
        if(empty($results)){
            if($type == 'web_qq'){
                db('user_extend')->where(['user_id'=>$result['user_id']])->update(['web_qq'=>$open_id]);
            }elseif($type == 'web_wx'){
                db('user_extend')->where(['user_id'=>$result['user_id']])->update(['web_wx'=>$open_id]);
            }elseif($type == 'mg'){
                db('user_extend')->where(['user_id'=>$result['user_id']])->update(['UID'=>$open_id]);

            }else{
                db('users')->where(['user_id'=>$result['user_id']])->update(array($type=>$open_id));
            }
            return 1;
        }else{
            return '该账号已绑定手机号,请勿重复绑定';
        }
    }
    /**
     * 获取下级用户
     */
    public function getList($where = [])
    {
        $join = [
            ['cl_role r','r.role_id = u.role_id','left']
        ];
        $rows = $this->alias('u')->where($where)->join($join)
            ->field('u.user_id,r.role_name,r.header_img')
            ->paginate();
        if (empty($rows->items())) return false;
        $result = $rows->items();
        foreach ($result as $k=>$v){
            $res = db('money_detail')->where(['user_id'=>$v['user_id'],'money_type'=>8,'type'=>2])->field('create_time as c,money')->order('create_time desc')->find();
            $result[$k]['user_id'] = hashid($v['user_id']);
            $result[$k]['money'] = abs($res['money']);
            $result[$k]['send_time'] = date('m/d H:i',$res['c']);
        }
        return ['total'=>$rows->total(),'thisPage'=>$rows->currentPage(),'hasNext'=>$rows->hasMore(),'data'=>$result];
    }
}