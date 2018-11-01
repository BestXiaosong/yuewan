<?php
/**
 * workman 处理类
 * 接收充值信息
 */
namespace app\api\controller;


use think\Controller;
use think\Db;
use think\Exception;
use think\Request;
use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;

class Socket extends Controller
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $worker = new Worker();

        $worker->onWorkerStart = function($worker){

            $con = new AsyncTcpConnection(config('websocketUrl'));

            $con->onConnect = function($con) {
                echo  'onConnect';
                $con->send('testMsg');
            };

            $con->onMessage = function($con, $data) {
                echo $data;
//                echo $data->Currency;
                $rows = json_decode($data,true);
                if (!is_array($rows)){
                  echo  'not array';
                } else{
                    // TODO 正式上线后解开注释
//                    if (isset($rows['Currency']) && $rows['Currency']  == 'BCDN'){

                        if (isset($rows['Currency'])){

                        $row['ETHAddr'] = $rows['TO'];
                        $row['TxHash']  = $rows['TxHash'];
                        $row['money']   = $rows['Value'];
//                    $user_id = Db::name('user_extend')->where('ETHAddr',$row['ETHAddr'])->value('user_id');
                        $user_id = 2;
                        if ($user_id){
                            Db::startTrans();
                            try{
                                Db::name('users')->where('user_id',$user_id)->setInc('BCDN',$row['money']);
                                $row['user_id'] = $user_id;
                                $row['create_time'] = time();
                                $row['money_type'] = 'BCDN';
                                $row['status'] = 1;
                                $row['type'] = 1;
                                $row['order_num'] = 'RE'.hashid($user_id).date("Ymd").rand(1000,9999);
                                Db::name('bankroll')->insert($row);
                                $phone = Db::name('user_extend')->where('user_id',$user_id)->value('phone');
                                //写入平台流水
                                stream($row['money'],4,"用户(".$phone.")充值".$row['money'].'BCDN',2);
                                Db::commit();
                            }catch (Exception $e){
                                Db::rollback();
                                $error = cache('error');
                                $error[] = $e;
                                cache('error',$error);
                            }
                            Db::name('users')->where('user_id',$user_id)->setInc('BCDN',$row['money']);

                        }
                    }else{

                    }
                }

            };

            $con->onClose = function($con) {
                // 如果连接断开，则在1秒后重连
                $con->reConnect(1);
            };
            $con->connect();
        };

        Worker::runAll();

    }
}