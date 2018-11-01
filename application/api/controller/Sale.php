<?php

namespace app\api\controller;



use app\common\model\SaleSuccess;

class Sale extends User
{


    public function _initialize()
    {
        parent::_initialize();
    }


    /**
     * 进入角色拍卖时可拍卖角色列表
     */
    public function sale_role()
    {
        $type = 0;
        $model = new \app\common\model\SaleSuccess();
        $result = $model->getRows($type);
        if($result){
            api_return(1,'成功',$result);
        }else{
            api_return(0,'暂无数据');
        }
        
    }
    /**
     * 进入房间拍卖时可拍卖房间列表
     * @return 
     */
    public function sale_room()
    {
        $type = 1;
        $model = new \app\common\model\SaleSuccess();
        $result = $model->getRows($type);
        if($result){
            api_return(1,'成功',$result);
        }else{
            api_return(0,'暂无数据');
        }
    }
    /**
     * [房间拍卖时点击搜索框进行查询]
     * @return [name]  [前端传递的输入框中的内容]
     */
    public function search_room()
    {
        $room_name = trim(input('post.name'));
//        api_return(strlen($room_name));
        if(strlen($room_name) < 2*3) api_return(1,'暂无数据',array());
        $model = new \app\common\model\Room();
        $result = $model->rooms($room_name);
        api_return(1,'成功',$result);
    }
    /**
     * [角色拍卖时点击搜索框进行查询]
     * @return [name] [前端传递的输入框中的内容]
     */
    public function search_role()
    {
        $role_name = trim(input('post.name'));
        if(strlen($role_name)<2) api_return(1,'成功',array());
        $model = new \app\common\model\Role();
        $result = $model->roles($role_name);
        api_return(1,'成功',$result);
    }
    /**
     * [sale_room_add 房间拍卖点击加价按钮执行此方法]
     * @return [name] [拍卖房间的名称]
     *         [user_id] [请求此接口的用户]
     *         [money] [当前用户出价价格]
     */
    public function sale_room_add(){
        $room_name = trim(input('post.name'));
        $cache = cache('sale_'.$room_name);
        if ($cache){
            api_return(0,'服务器繁忙,请稍后重试');
        }else{
            cache('sale_'.$room_name,1,0.3);
        }
        if(empty($room_name)) api_return(0,'拍卖房间名不能为空');
        $money = trim(input('post.money'));
        $user_id = $this->user_id;
        $role_id= $this->role_id;
        $model = new \app\common\logic\Room();
        $result = $model->add($room_name,$user_id,$money,$role_id);
        if($result == 1){
            api_return(1,'竞价成功');
        }elseif($result == 2){
            api_return(0,'您是拍卖发起者，不能进行竞拍，很抱歉');
        }elseif($result == 3){
            api_return(0,'您当前是出价最高者，请勿重复加价');
        }elseif($result == 4){
            api_return(0,'您的出价过低，请提高出价进行拍卖');
        }elseif($result == 6){
            api_return(0,'您的出价已经超过您的账户余额');
        }else{
            api_return(0,'该拍卖品加价通道已关闭，请您选择其他拍卖品');
        }
    }
    /**
     * [sale_role_add 角色拍卖点击加价按钮执行此方法]
     * @return [name] [拍卖角色的名称]
     *         [user_id] [请求此接口的用户]
     *         [money] [当前用户出价价格]
     */
    public function sale_role_add(){
        $role_name = trim(input('post.name'));

        if(empty($role_name)) api_return(0,'拍卖角色名不能为空');
        $cache = cache('sale_'.$role_name);
        if ($cache){
            api_return(0,'服务器繁忙,请稍后重试');
        }else{
            cache('sale_'.$role_name,1,0.3);
        }
        $money = trim(input('post.money'));
        $user_id = $this->user_id;
        $model = new \app\common\logic\Role();
        $result = $model->add($role_name,$user_id,$money);
        if($result == 1){
            api_return(1,'竞价成功');
        }elseif($result == 2){
            api_return(0,'您是拍卖发起者，不能进行竞拍，很抱歉');
        }elseif($result == 3){
            api_return(0,'您当前是出价最高者，请勿重复加价');
        }elseif($result == 4){
        api_return(0,'您的出价过低，请提高出价进行拍卖');
        }elseif($result == 6){
        api_return(0,'您的出价已经超过您的账户余额');
        }elseif($result == 5){
            api_return(0,'该拍卖品加价通道已关闭，请您选择其他拍卖品');
        }
    }
    /**
     *  点击我的拍卖，我参与的请求接口
     */
    public function join(){
        $user_id = $this->user_id;
        $model = new SaleSuccess();
        $result = $model->join($user_id);
        if($result){
            api_return(1,'成功',$result);
        }else{
            api_return(0,'暂无数据');
        }
    }
    /**
     * 点击我的拍卖，我发起的请求接口
     */
    public function initiate(){
        $user_id = $this->user_id;
        $model = new SaleSuccess();
        $result = $model->initiate($user_id);
        if($result){
            api_return(1,'成功',$result);
        }else{
            api_return(0,'暂无数据');
        }
    }


    /**
     * 发起房间拍卖
     */

    public function initiate_sale_room(){
        $room_id = dehashid($this->request->post('room_id'));
        if(!is_numeric($room_id)) api_return(0,'参数错误');
        $psw = $this->request->post('psw');
        $user_id = $this->user_id;
        $money = $this->request->post('money');
        $model = new \app\common\logic\SaleSuccess();
        $result = $model->sale($psw,$user_id,$money,$room_id,1);
        if($result == 1){
            api_return(1,'发起成功');
        }else{
            api_return(0,$result);
        }
    }
    /**
     * 发起角色拍卖
     */

    public function initiate_sale_role(){
        $role_id = dehashid($this->request->post('role_id'));
        if(!is_numeric($role_id)) api_return(0,'参数错误');
        $psw = $this->request->post('psw');
        $user_id = $this->user_id;
        $money = $this->request->post('money');
        $model = new \app\common\logic\SaleSuccess();
        $result = $model->sale($psw,$user_id,$money,$role_id,0);
        if($result == 1){
            api_return(1,'发起成功');
        }else{
            api_return(0,$result);
        }
    }
}
