<?php
/**
 * Created by PhpStorm.
 * Users: Administrator
 * Date: 2018/3/20
 * Time: 9:51
 */

namespace app\api\controller;

use org\wechat\Jssdk;
use think\Controller;
use think\Db;


class Demo extends Controller
{


    public function login()
    {
        if(!empty(session('eid'))){
            $this->assign('title','已登录');
            return view('404');
        }
        if (request()->isPost()){
            $data = input('post.');
            if (!empty($data['phone']) and !empty($data['password'])){
                $row = Db::table('examiner')->where('phone',$data['phone'])->field('eid,password,status')->find();
                if (!empty($row)){
                    if($row['status'] == 1){
                        $password = md5($data['password'].'tz');
                        if ($password == $row['password']){
                            session('eid',$row['eid']);
                            $this->redirect('/check/index');
                        }
                        $this->assign('title','账号或密码错误');
                        return view('404');
                    }
                }
                $this->assign('title','账号被禁用或不存在');
                return view('404');
            }
            $this->assign('title','账号或密码不能为空');
            return view('404');
        }
        return view();
    }




    /**
     * @return \think\response\View
     * 获取微信验证
     */

    public function GetSignPackage()
    {
         $jssdkobj = new Jssdk('wx5f29872122a50cb2','6795123374edf3007030b6c3785ae643');
         $signPackage = $jssdkobj->GetSignPackage();
         $this->assign('signPackage',$signPackage);
         return view();
    }


    /**
     * 利用mysql做到附近门店功能
     */
    public function Near()
    {

//        使用附近功能mysql需要大于5.6版本

         $result = Db::query(
             'SELECT    
                 s.sid,s.store_name,s.lng,s.lat,     
                 (st_distance (point (lng, lat),point(104.06656,30.617767) ) / 0.0111) AS distance    
                 FROM    
                 store_detail s    
                 HAVING distance < 6
                 ORDER BY distance '
         );
        echo round(5.0912881983072,2);
//         print_r($result);
    }


    /**
     * @return int
     * 数据库随机查询
     */
    public function Rand()
    {
        //获取随机100道题
        $numbers = Db::table('Questions')->value('id');
//        $numbers = range (1,1000);
        //shuffle 将数组顺序随即打乱
        shuffle ($numbers);
        //array_slice 取该数组中的某一段
        $num=100;
        $result = array_slice($numbers,0,$num);
        $where['id'] = ['in',$result];
        $rows = Db::table('Questions')->order('type')->where($where)->value();

        //对比答案对错
        if (IS_POST) {
            //order("field(tid,$local_value)")
            //$post [
            //['题目id','选择的答案']
            //]
            $post = input('post.');
            $mark = 0;
            $num = 1;
            foreach ($post as $k => $v) {
                $RightKey = Db::table('Questions')->where(['id' => $post[$k][0]])->value('RightKey');
                if ($RightKey == $post[$k][1]) {
                    $mark += $num;
                }
            }
            return $num;
        }
    }


}