<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/4
 * Time: 9:33
 */

namespace app\api\command;

use rongyun\api\methods\Push;
use think\console\Command;
use think\console\Input;
use think\console\Output;

/**
 * 拍卖处理类
 */

class Sale extends Command
{
    protected function configure()
    {
        $this->setName('Sale')->setDescription('php_commandapi');
    }

    protected function execute(Input $input, Output $output)
    {
        /* 永不超时 */
        ini_set('max_execution_time', 0);
        $this->doCron();
    }


    public function doCron()
    {
        $fileName = __DIR__.'/sale.txt';
        $fp = fopen($fileName, "r");
        if(flock($fp,LOCK_EX | LOCK_NB)) {
        $result = db('sale_success')->where(['sale_status'=>0,'status'=>0])->select();
//
        foreach($result as $k=>$v){
            if($v['end_time']<= time()){
                $data['sale_status'] = 1;
                if($v['type'] == 0){
                    if($v['user_id'] == ''){

                        db('role')->where(['role_id'=>$v['g_id']])->update(['status'=>1,'user_id'=>$v['sale_user_id']]);
                        $role_name = db('role')->where(['role_id'=>$v['g_id']])->value('role_name');
                        $j_push_id = db('users')->where(['user_id'=>$v['sale_user_id']])->value('j_push_id');
                        \Push(2,$j_push_id,'您拍卖的角色名为'.$role_name.'的角色无人竞价，已返回您的资产中');

                    }else{
                        db('role')->where(['role_id'=>$v['g_id']])->update(['status'=>1,'user_id'=>$v['user_id']]);
                        $role_name = db('role')->where(['role_id'=>$v['g_id']])->value('role_name');
                        $j_push_id = db('users')->where(['user_id'=>$v['user_id']])->value('j_push_id');
                        \Push(2,$j_push_id,'您拍卖的角色名为'.$role_name.'的角色已成功拍下');
                        db('users')->where(['user_id'=>$v['sale_user_id']])->setInc('money',$v['money']);
                        $j_push_id = db('users')->where(['user_id'=>$v['sale_user_id']])->value('j_push_id');
                        money($v['sale_user_id'],4,$v['money'],1,'拍卖成功,获得拍卖价'.$v['money']);
                        \Push(3,$j_push_id,'您拍卖的角色名为'.$role_name.'的角色已经拍卖完成');
                    }

                }else{

                    if($v['user_id'] == ''){

                        $role_id = db('users')->where(['user_id'=>$v['sale_user_id']])->value('role_id');
                        db('room')->where(['room_id'=>$v['g_id']])->update(['user_id'=>$v['sale_user_id'],'role_id'=>$role_id,'start_time'=>time(),'status'=>1]);
                        $room_name = db('room')->where(['room_id'=>$v['g_id']])->value('room_name');
                        $j_push_id = db('users')->where(['user_id'=>$v['sale_user_id']])->value('j_push_id');
                        \Push(2,$j_push_id,'您拍卖的房间名为'.$room_name.'的房间无人竞价，已返回您的资产中');

                    }else{
                        $role_id = db('users')->where(['user_id'=>$v['user_id']])->value('role_id');
                        db('room')->where(['room_id'=>$v['g_id']])->update(['user_id'=>$v['user_id'],'role_id'=>$role_id,'start_time'=>time(),'status'=>1]);
                        $room_name = db('room')->where(['room_id'=>$v['g_id']])->value('room_name');
                        $j_push_id = db('users')->where(['user_id'=>$v['user_id']])->value('j_push_id');
                        \Push(2,$j_push_id,'您拍卖的房间名为'.$room_name.'的房间已成功拍下');
                        db('users')->where(['user_id'=>$v['sale_user_id']])->setInc('money',$v['money']);
                        $j_push_id = db('users')->where(['user_id'=>$v['sale_user_id']])->value('j_push_id');
                        money($v['sale_user_id'],4,$v['money'],1,'拍卖成功,获得拍卖价'.$v['money']);
                        \Push(3,$j_push_id,'您拍卖的房间名为'.$room_name.'的房间已经拍卖完成');
                    }
                }
                db('sale_success')->where(['sale_id'=>$v['sale_id']])->update(['sale_status'=>1]);
                db('sale_history')->where(['sale_id'=>$v['sale_id']])->update(['status'=>1]);

            }
        }

            flock($fp,LOCK_UN);
        }
        fclose($fp);
    }

}