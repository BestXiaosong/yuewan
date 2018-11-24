<?php
/**
 * Created by xiaosong
 * E-mail:306027376@qq.com
 * Date: 2018/11/14
 * Time: 16:14
 */

namespace app\api\controller;
use app\common\logic\Logic;
use app\common\logic\SkillApply;
use app\common\model\Order;
use app\common\model\Skill as model;
use think\Db;
use think\Exception;

class Skill extends User
{


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 添加资质
     */
    public function skill()
    {

        $model = new model();

        $data['my']   = (new  \app\common\model\SkillApply)->getMy(['u.user_id' => $this->user_id]);

        $data['game'] = $model->getRows(['type'=>2]);
        $data['fun']  = $model->getRows(['type'=>1]);

        api_return(1,'获取成功',$data);

    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 资质管理
     */
    public function skills()
    {

        $rows = (new  \app\common\model\SkillApply)->getMy(['u.user_id' => $this->user_id]);

        api_return(1,'获取成功',$rows);

    }


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 资质申请详情页
     */
    public function detail()
    {

        $id = input('post.id');

        if (!is_numeric($id)) api_return(0,'参数错误');

        $map['skill_id'] = $id;

        $data['skill'] = Db::name('skill')
            ->where($map)
            ->field('skill_id,request,skill_name,explain,header_exp,skill_img,video,grade')
            ->cache(30)
            ->find();

        if (!$data['skill']) api_return(0,'非法参数');

        $data['skill']['grade'] = explode(',',$data['skill']['grade']);

        $map['user_id'] = $this->user_id;

        $data['my'] = Db::name('skill_apply')->where($map)->field('img,video,voice,explain,status')->find();

        $data['header_img'] = $this->userInfo('header_img');

        api_return(1,'获取成功',$data);

    }


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 资质申请
     */
    public function apply()
    {

        $this->ApiLimit(2,$this->user_id);

        $data = request()->only(['skill_id','img','video','voice','explain','is_use'],'post');

        $where['skill_id'] = $data['skill_id'];
        $where['status']   = 1;

        $skill = Db::name('skill')->where($where)->value('skill_id');

        if (!$skill) api_return(0,'禁止申请');

        $map['skill_id'] = $data['skill_id'];
        $map['user_id']  = $this->user_id;

        $model = new SkillApply();

        $apply = $model->where($map)->find();

        if ($apply){

            if ($apply['status'] == 0){

                api_return(0,'您的资质申请还在审核中,请勿重复操作');

            }else{

                $data['id'] = $apply['apply_id'];

            }

        }else{
            $skillInfo = Db::name('skill')->where('skill_id',$data['skill_id'])->cache(60)->find();
            $data['my_gift_id'] = $skillInfo['gift_id'];
            $data['my_form']    = $skillInfo['form_id'];

            $gift['gift_id'] = ['in',$skillInfo['gift_id']];
            $gift['status']  = 1;

            $data['mini_price'] = Db::name('gift')->where($gift)->order('price')->value('price');

        }

        $data['user_id'] = $this->user_id;

        $result = $model->saveChange($data);

        if ($result){

            api_return(1,'提交成功,请等待管理员审核');

        }

        api_return(0,$model->getError());
    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 资质上下架
     */
    public function shelf(int $id,int $is_use)
    {

        $map['skill_id'] = $id;
        $map['user_id']  = $this->user_id;
        $map['status']   = 1;

        $model = new SkillApply();

        $data  = $model->where($map)->find();

        if ($data){

            $result = $data->validate('base.skill_shelf')->save(['is_use'=>$is_use]);

            if ($result){

                api_return(1,'修改成功');

            }

            api_return(0,$data->getError());

        }else{

            api_return(0,'参数错误');

        }

    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 资质设置--显示页
     */
    public function setSkill()
    {
        $data['extend']   = $this->userExtra('invite,dispatch,filter');

        $map['a.user_id'] = $this->user_id;
        $map['a.status']  = 1;
        $map['a.is_use']  = 1;

        $model = new \app\common\model\SkillApply();

        $data['rows']  = $model->getRows($map);

        api_return(1,'获取成功',$data);

    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 技能编辑
     */
    public function editSkill()
    {

        $data = request()->only(['my_form','my_gift_id','apply_id'],'post');

        $map['apply_id'] = $data['apply_id'];
        $map['status']   = 1;
        $map['user_id']  = $this->user_id;

        $model = new \app\common\model\SkillApply();

        $row = $model->where($map)->find();

        if (!$row) api_return(0,'非法参数');

        $where['skill_id'] = $row['skill_id'];

        $skill = Db::name('skill')->where($where)->find();

        if (!strstr($skill['gift_id'],$data['my_gift_id'])) api_return(0,'礼物id错误');

        $result = $row->update($data);

        if ($result){

            api_return(1,'修改成功');

        }else{

            api_return(0,$model->getError());

        }

    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 技能详情
     */
    public function skillDetail()
    {
        $id = input('post.id');

        $map['a.status']   = 1;
        $map['a.apply_id'] = $id;

        $model = new \app\common\model\SkillApply();

        $data  = $model->detail($map);

        api_return(1,'获取成功',$data);

    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 根据申请id   获取用户技能申请详情
     */
    protected function getApply()
    {
        $id = input('post.id');

        $map['status']   = 1;
        $map['apply_id'] = $id;

        return Db::name('skill_apply')->where($map)->cache(15)->find();
    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 技能详情 -- 技能统计
     */
    public function skillCount()
    {

        $data = $this->getApply();

        $form['a.skill_id'] = $data['skill_id'];
        $form['a.user_id']  = $data['user_id'];
        $form['a.status']   = 1;
        $form['s.status']   = 1;

        $rows['forms'] = Db::name('skill_form_user')->alias('a')
            ->join('skill_form s','s.form_id = a.form_id','LEFT')
            ->where($form)
            ->order('a.num desc')
            ->field('a.num,s.form_name')
            ->cache(15)
            ->select();

        $tag['a.skill_id'] = $data['skill_id'];
        $tag['a.user_id']  = $data['user_id'];
        $tag['a.status']   = 1;
        $tag['t.status']   = 1;

        $rows['tags'] = Db::name('skill_tag_user')->alias('a')
            ->join('skill_tag t','t.tag = a.tag','LEFT')
            ->where($tag)
            ->order('a.num desc')
            ->field('a.num,t.tag_name')
            ->cache(15)
            ->select();

        $map['to_user']  = $data['user_id'];
        $map['skill_id'] = $data['skill_id'];
        $map['status']   = 5;

        $rows['count']['num'] = Db::name('order')->where($map)->count('order_id');

        $score = Db::name('order')->where($map)->avg('score');

        $rows['count']['score'] = numberDecimal($score);

        api_return(1,'获取成功',$rows);

    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 技能详情--技能评论
     */
    public function skillComment()
    {

        $data = $this->getApply();

        $map['a.to_user']  = $data['user_id'];
        $map['a.skill_id'] = $data['skill_id'];
        $map['a.status']   = 5;

        $model = new Order();

        $rows  = $model->getComment($map);

        api_return(1,'获取成功',$rows);

    }


    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 邀约界面
     */
    public function invite()
    {
        $id = dehashid(input('post.id'));

        if (!is_numeric($id)) api_return(0,'参数错误');

        $rows['userInfo'] = $this->userInfo('nick_name,header_img',$id);

        $map['a.user_id'] = $id;
        $map['a.is_use']  = 1;
        $map['a.status']  = 1;

        $model = new \app\common\model\SkillApply();

        $rows['rows'] = $model->getRows($map,true);

        api_return(1,'获取成功',$rows);

    }

    /**
     * Created by xiaosong
     * E-mail:4155433@gmail.com
     * 下单
     */
    public function order()
    {

        $this->ApiLimit(1,$this->user_id);

        $data = request()->only(['skill_id','to_user','form_id','gift_id','num','order_time','type','remark'],'post');

        $to_user = dehashid($data['to_user']);

        if (!is_numeric($to_user)) api_return(0,'参数错误');

        if ($to_user == $this->user_id){
            api_return(0,'您不能向自己下单');
        }

        $data['to_user'] = $to_user;

        $map['status']  = 1;
        $map['user_id'] = $to_user;
        $map['is_use']  = 1;

        $apply = Db::name('skill_apply')->where($map)->find();

        if (!$apply) api_return(0,'用户无此技能');

        if (!strstr($apply['my_form'],$data['form_id'])) api_return(0,'用户不接受此邀约形式');
        if (!strstr($apply['my_gift_id'],$data['gift_id'])) api_return(0,'用户不接受此礼物');
        if (!isInt($data['num'])) api_return(0,'数量错误');

        $where['gift_id'] = $data['gift_id'];
        $where['status']  = 1;
        $price = Db::name('gift')->where($where)->value('price');
        if (!$price) api_return(0,'礼物信息错误');

        $data['price'] = bcmul($price,$data['num'],2);

        $data['user_id'] = $this->user_id;

        Db::startTrans();
        try{
            //余额支付
            if ($data['type'] == 'integral'){

                $this->moneyDec($data['price']);

                $data['status'] = 1;
            }

            $model = new Logic();

            $result = $model->saveChange('order',$data,'order.add');

            if (!$result){

                api_return(0,$model->getError());

            }

            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            api_return(0,'服务器繁忙,请稍后重试',$e->getMessage());
        }

        api_return(1,'下单成功',$model->order_id);

    }










}