<?php
/**
 * Created by xiaosong
 * E-mail:4155433@gmail.com
 * Date: 2018/11/28
 * Time: 14:10
 */

namespace app\common\job;


class testController
{
    public function actionWithHelloJob(){


        for ($i = 1;$i <= 100000;$i++){

            $data = [];
            $data['user_id'] = $i;
            $data['follow_user'] = $i;
            Db::name('user_follow')->insert($data);

        };exit;
        // 1.当前任务将由哪个类来负责处理。
        //   当轮到该任务时，系统将生成一个该类的实例，并调用其 fire 方法
        $jobHandlerClassName  = 'app\job\Hello';
        // 2.当前任务归属的队列名称，如果为新队列，会自动创建
        $jobQueueName  	  = "helloJobQueue";
        // 3.当前任务所需的业务数据 . 不能为 resource 类型，其他类型最终将转化为json形式的字符串
        //   ( jobData 为对象时，需要在先在此处手动序列化，否则只存储其public属性的键值对)
        $jobData       	  = [ 'ts' => time(), 'bizId' => uniqid() , 'a' => 1 ] ;
        // 4.将该任务推送到消息队列，等待对应的消费者去执行
//        $jobData = function (){
//            echo 'xxx';
//        };
        $isPushed = Queue::push($jobHandlerClassName,$jobData);
        // database 驱动时，返回值为 1|false  ;   redis 驱动时，返回值为 随机字符串|false
        if( $isPushed !== false ){
            echo date('Y-m-d H:i:s') . " a new Hello Job is Pushed to the MQ"."<br>";
        }else{
            echo 'Oops, something went wrong.';
        }
    }
}