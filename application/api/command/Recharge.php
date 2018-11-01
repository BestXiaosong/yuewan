<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/4
 * Time: 9:33
 */

namespace app\api\command;

use app\common\model\Bankroll;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
use think\Exception;

/**
 * 提现查询处理类
 */
class Recharge extends Command
{
    protected function configure()
    {
        $this->setName('recharge')->setDescription('php_command_recharge');
    }

    protected function execute(Input $input, Output $output)
    {
        /* 永不超时 */
        ini_set('max_execution_time', 0);
        $this->doCron();
    }


    public function doCron()
    {
        $fileName = __DIR__.'/recharge.txt';
        $fp = fopen($fileName, "r");
        if(flock($fp,LOCK_EX | LOCK_NB)) {
            try{
                $model = new Bankroll();
                $page  = cache('rechargePage');
                $rows  = $model->getList($page);
//                echo count($rows);
                if ($rows){
                    cache('rechargePage',$page + 1);
                    foreach ($rows as $k => $v){
                        $arr['hash'] = $v['TxHash'];
                        $data = MBerryApi($arr,'api.QueryTXByHash');
                        if ($data['Msg'] == 'success' && $data['Data']['Status'] == 1){ //莓果api请求成功且交易已成功
                            Db::name('bankroll')->where('b_id',$v['b_id'])->update(['status'=>5]);
                            $j_push_id = Db::name('users')->where('user_id',$v['user_id'])->value('j_push_id');
                            if ($j_push_id){
                                j_push('您的提现申请已通过,'.$v['money'].'BCDN已到账');
                            }
                        }
                    }
                }else{
                    cache('rechargePage',1);
                }
            }catch (Exception $e){

            }
            flock($fp,LOCK_UN);
        }
        fclose($fp);
    }

}