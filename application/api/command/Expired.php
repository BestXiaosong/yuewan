<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/4
 * Time: 9:33
 */

namespace app\api\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

/**
 * 空间过期处理类
 */
class Expired extends Command
{
    protected function configure()
    {
        $this->setName('sale')->setDescription('php_command_sale');
    }

    protected function execute(Input $input, Output $output)
    {
        /* 永不超时 */
        ini_set('max_execution_time', 0);
        $this->doCron();
    }


    public function doCron()
    {
        $fileName = __DIR__.'/expired.txt';
        $fp = fopen($fileName, "r");
        if(flock($fp,LOCK_EX | LOCK_NB)) {
            $result = db('users')->select();
            foreach ($result as $k=>$v){
                if($v['space_time'] !=0 && (time()>$v['space_time'])){
                    $data['space_time'] = 0;
                    $data['bucket_space'] = 5;
                    db('users')->where(['user_id'=>$v['user_id']])->update($data);
                    $j_push_id = db('users')->where(['user_id'=>$v['user_id']])->value('j_push_id');
                    \Push(0,$j_push_id,'您的升级空间已过期,请进行续费');
                }
            }
            flock($fp,LOCK_UN);
        }
        fclose($fp);
    }

}