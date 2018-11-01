<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/26
 * Time: 15:19
 */

namespace app\common\logic;


use think\Model;

class Role extends Model
{



    public function saveChange($data){
        if(is_numeric($data['id'])){
            return $this->validate('role.edit')->allowField(true)->isUpdate(true)->save($data,['role_id'=>$data['id']]);
        }else{
            return $this->validate('role.add_role')->allowField(true)->save($data);
        }
    }
    public function add($name,$id,$money)
    {
        $where['role_name'] = $name;
        $result = $this->where($where)->find();
        $people_money = db('users')->where(['user_id'=>$id])->value('money');
        $min_money = db('extend')->where(['id'=>1])->value('sale_role');
        $rule = db('extend')->where(['id'=>1])->value('price_limit');
        if($result){
            $res1 = db('sale_success')->where(['type'=>0,'g_id'=>$result['role_id'],'sale_status'=>0])->find();
            if($res1){
                $min_money = $res1['money']+$res1['money']*$rule;
            }

        }
//        api_return($res);
        if($min_money>$money){
            return 4;
        }
        if($people_money < $money){
            return 6;
        }
        if($result)
        {
            if($result['status'] == 2){
                $res = db('sale_success')->where(['type'=>0,'g_id'=>$result['role_id'],'sale_status'=>0])->find();
                if(empty($res['start_time'])){
                    $res['user_id'] = $id;
                    $res['money'] = $money;
                    $res['start_time'] = time();
                    $res['create_time'] = time();
//                    $res['end_time'] = time() + 24*60*60;
                    $res['end_time'] = time() + 3*60;
                    $res['update_time'] = time();
                    $res['status'] = 0;
                    $result1 = db('sale_success')->update($res);
                    money($id,4,0-$money,1,'拍卖角色'.$name.'加价成功，冻结竞拍金额 ');
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

                        money($res['user_id'],4,$res['money'],1,'拍卖角色'.$name.'竞拍价格被超越，返还冻结金额');
                        $result = db('users')->where(['user_id'=>$res['user_id']])->setInc('money',$res['money']);
                        $res['user_id'] = $id;
                        $res['money'] = $money;
                        $res['update_time'] = time();
                        $result1 = db('sale_success')->update($res);
                        $result2 = db('users')->where(['user_id'=>$id])->setDec('money',$money);
                        money($id,4,0-$money,1,'拍卖角色'.$name.'加价成功，冻结竞拍金额 ');
                        $data['sale_id'] = $res['sale_id'];
                        $data['user_id'] = $id;
                        $results = db('sale_history')->where($data)->find();
                        if(empty($results)){
                            $data['create_time'] = time();
                            $data['update_time'] = time();
                            $data['status'] = 0;
                            $data['money'] = $money;
                            $result3 = db('sale_history')->insert($data);
                        }else{
                            $results['money'] = $money;
                            $results['update_time'] = time();
                            $result3 = db('sale_history')->update($results);
                        }
                            return 1;

                    }
                }

            }

        }else{
            $data['role_name'] = $name;
            $data['status'] = 2;
            $data['user_id'] = 0;
            $data['create_time'] = time();
            $data['header_img'] = config('default_img');
            $g_id = $this->insertGetId($data);
            $datas['type'] = 0;
            $datas['g_id'] = $g_id;
            $datas['user_id'] = $id;
            $datas['money'] = $money;
            $datas['start_time'] = time();
//            $datas['end_time'] = time()+24*60*60;
            $datas['end_time'] = time()+3*60;
            $datas['create_time'] = time();
            $datas['update_time'] = time();
            $datas['status'] = 0;
            $sale_id = db('sale_success')->insertGetId($datas);
            $wheres['sale_id'] = $sale_id;
            $wheres['user_id'] = $id;
            $results = db('sale_history')->where($wheres)->find();
            $result2 = db('users')->where(['user_id'=>$id])->setDec('money',$money);
            money($id,4,0-$money,4,'拍卖角色'.$name.'加价成功，冻结竞拍金额 ');
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



    public function admin_add_role($name){
        $where['role_name'] = $name;
        $result = $this->where($where)->find();
        if($result){
            return false;
        }else{
            $data['role_name'] = $name;
            $data['status'] = 2;
            $data['sale_status'] = 0;
            $data['user_id'] = 0;
            $data['create_time'] = time();
            $data['header_img'] = config('default_img');
            $g_id = $this->insertGetId($data);
            return $g_id;
        }

    }
}