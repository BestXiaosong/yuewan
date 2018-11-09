<?php
/**
 * Created by xiaosong
 * E-mail:306027376@qq.com
 * Date: 2018/11/7
 * Time: 10:09
 */

namespace app\api\controller;


use think\Db;

class Test extends  Base
{
    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 距离查询测试
     */
    public function test()
    {
        $distance = 5;
        $log = 104.06656;
        $lat = 30.617767;

//        dump(config());exit;

        $result = Db::query(
            "SELECT    
                 u.user_id,u.nick_name,
                 (st_distance (point (log, lat),point($log,$lat) ) / 0.0111) AS distance    
                 FROM    
                 cl_users u    
                 HAVING distance < $distance
                 ORDER BY distance"
        );

        $data = Db::name('users')->field("user_id,nick_name,(st_distance (point (log, lat),point($log,$lat) ) / 0.0111) AS distance")
            ->having("distance < $distance")->select();

        dump($data);

        dump($result);
    }

}