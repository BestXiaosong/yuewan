<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/4 0004
 * Time: 16:51
 */

namespace app\api\command;


use app\common\logic\Users;
use app\common\model\RedPackage;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\Exception;

/**
 * 红包处理类
 */
class Red extends Command
{
    protected function configure()
    {
        $this->setName('red')->setDescription('php_command_red');
    }

    protected function execute(Input $input, Output $output)
    {
        /* 永不超时 */
        ini_set('max_execution_time', 0);
        $this->doCron();
    }


    public function doCron()
    {
        $fileName = __DIR__.'/red.txt';
        $fp = fopen($fileName, "r");
        if(flock($fp,LOCK_EX | LOCK_NB)) {
        $model = new RedPackage();
        $red = $model->getExpireRed();
        $redis = redisConnect();
        foreach ($red as $k => $v) {
        //money_type == coin表里的coin_id;
            $coinInfo = Db::name('coin')->where('coin_id',$v['money_type'])->find();
            $len = $redis->lLen('red_package_'.$v['red_id']);
            $sum_money = 0;
            for ($i = 0; $i < $len; $i++) {
                $sum_money += $redis->lpop('red_package_'.$v['red_id']);
            }

            Db::startTrans();
            try{
                Db::name($coinInfo['table'])->where('user_id',$v['user_id'])->setInc($coinInfo['field'],$sum_money);

                if($sum_money>0){
                    money($v['user_id'],7, $sum_money,$v['money_type'],$coinInfo['coin_name'].'红包未领完退款'.$sum_money);
                    $j_push_id = Db::name('users')->where('user_id',$v['user_id'])->value('j_push_id');

                    Push(3,$j_push_id,$coinInfo['coin_name'].'红包未领完退款'.$sum_money.'已到账');
                }

                Db::name('red_package')->where('red_id',$v['red_id'])->update(['status'=>2]);



                Db::commit();
            }catch (Exception $e){
                Db::rollback();
            }

        }
            flock($fp,LOCK_UN);
        }
        fclose($fp);
    }


    public function doCron2()
    {
        $fileName = __DIR__.'/red.txt';
        $fp = fopen($fileName, "r");
        if(flock($fp,LOCK_EX | LOCK_NB)) {
            $logic = new \app\common\logic\RedPackage();
            $model = new RedPackage();
            $red = $model->getExpireRed();
            $redis = redisConnect();
            foreach ($red as $k => $va) {


                if ($va['money_type'] == 1) {
                    //返还到积分账户
                    $len = $redis->lLen('red_package_' . $va['red_id']);
                    $sum_money = 0;
                    for ($i = 0; $i < $len; $i++) {
                        $sum_money += $redis->lpop('red_package_' . $va['red_id']);
                    }
                    $user = new Users();
                    $res = $user->where('user_id', $va['user_id'])->setInc('money', $sum_money);
                    //返还失败再次修改
                    if ($res === false) {
                        $res = $user->where('user_id', $va['user_id'])->setInc('money', $sum_money);
                    }
                    if($res){
                        $logic->updateStatus($va['red_id'],2);
                    }
                } elseif ($va['money_type'] == 2) {
                    //返还到比特币
                    $len = $redis->lLen('red_package_' . $va['red_id']);
                    $sum_money = 0;
                    for ($i = 0; $i < $len; $i++) {
                        $sum_money += $redis->lpop('red_package_' . $va['red_id']);
                    }
                    $user = new Users();

                    $res = $user->where('user_id', $va['user_id'])->setInc('btc', $sum_money);
                    //返还失败再次修改
                    if ($res === false) {
                        $res = $user->where('user_id', $va['user_id'])->setInc('btc', $sum_money);
                    }
                    if($res){
                        $logic->updateStatus($va['red_id'],2);
                    }
                } elseif ($va['money_type'] == 3) {
                    //返还到以太币
                    $len = $redis->lLen('red_package_' . $va['red_id']);
                    $sum_money = 0;
                    for ($i = 0; $i < $len; $i++) {
                        $sum_money += $redis->lpop('red_package_' . $va['red_id']);
                    }
                    $user = new Users();

                    $res = $user->where('user_id', $va['user_id'])->setInc('eth', $sum_money);
                    //返还失败再次修改
                    if ($res === false) {
                        $res = $user->where('user_id', $va['user_id'])->setInc('eth', $sum_money);
                    }
                    if($res){
                        $logic->updateStatus($va['red_id'],2);
                    }
                }elseif($va['money_type'] == 4){
                    //返还到BCDN
                    $len = $redis->lLen('red_package_' . $va['red_id']);
                    $sum_money = 0;
                    for ($i = 0; $i < $len; $i++) {
                        $sum_money += $redis->lpop('red_package_' . $va['red_id']);
                    }
                    $user = new Users();

                    $res = $user->where('user_id', $va['user_id'])->setInc('BCDN', $sum_money);
                    //返还失败再次修改
                    if ($res === false) {
                        $res = $user->where('user_id', $va['user_id'])->setInc('BCDN', $sum_money);
                    }
                    if($res){
                        $logic->updateStatus($va['red_id'],2);
                    }
                }
                //钱包明细

                if($sum_money>0){
                    money($va['user_id'],7, $sum_money,$va['money_type'],'红包未领完返还');
                }

            }
            flock($fp,LOCK_UN);
        }
        fclose($fp);
    }


}