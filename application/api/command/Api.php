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

class Api extends Command
{
    protected function configure()
    {
        $this->setName('api')->setDescription('php_command_api');
    }

    protected function execute(Input $input, Output $output)
    {
        /* 永不超时 */
        ini_set('max_execution_time', 0);
        $this->doCron();
    }


    public function doCron()
    {
        echo 'xxxx';
        //TODO do somethings
    }

}