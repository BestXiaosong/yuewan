<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/30 0030
 * Time: 16:41
 */
namespace app\common\model;




class RoleCheck extends Base
{
    public function getList($where = [])
    {
        $join = [
            ['cl_role r', 's.role_id = r.role_id', 'left']
        ];
        $file = 'r.role_name,s.check_id,s.status,s.create_time,s.money';

        return $this->alias('s')
            ->join($join)
            ->where($where)
            ->order('s.create_time desc')
            ->field($file)
            ->paginate(15, false, ['query' => request()->param()]);

//        return $this->getLastSql();
    }


    public function getRequest($id)
    {
        $must = db('users')->where(['user_id' => $id])->find();
        $check = $this->where(['role_id' => $must['role_id']])->find();
        $min = db('extend')->where(['id' => 1])->value('role_check');
        if ($check['status'] != 0) {
            return 2;
        }else{
            return $min;
        }
    }


    public function getCheck($id, $money)
    {
        $min = db('extend')->where(['id'=>1])->value('role_check');
        $must = db('users')->where(['user_id' => $id])->find();
        $check = $this->where(['role_id' => $must['role_id']])->find();
        if($money<$min){
            $msg = '您抵押的资产不足最低额度';
            return $msg;
        }
        if ($must['money'] < $money) {
            $msg = '您的余额不足';
            return $msg;
        } else {
            if ($check) {
                if ($check['status'] != 0) {
                    $msg = '您的该角色不在可审核范围';
                    return $msg;
                } else {
                    $this->where(['check_id'=>$check['check_id']])->update(['status'=>2,'update_time'=>time(),'money'=>$money]);
                    db('users')->where(['user_id' => $id])->setDec('money',$money);
                    money($id, 7, 0 - $money, 1, '认证抵押资产');
                }
            } else {
                $data['role_id'] = $must['role_id'];
                $data['money'] = $money;
                $data['status'] = 2;
                $data['create_time'] = time();
                $this->insert($data);
                db('users')->where(['user_id' => $id])->setDec('money',$money);
                money($id, 7, 0 - $money, 1, '认证抵押资产');

            }
            return 1;
        }
    }

    public function turn($id){
        $where['rc.check_id'] = $id;
        $join = [
          ['cl_role r','rc.role_id = r.role_id','left'],
          ['cl_users u','r.user_id = u.user_id','left']
        ];
        $file = 'rc.money,u.user_id';
        $result = $this->alias('rc')->field($file)->join($join)->where($where)->find();
        db('users')->where(['user_id'=>$result['user_id']])->setInc('money',$result['money']);
        money($result['user_id'], 7,  $result['money'], 1, '认证失败返还抵押资产');
    }
}