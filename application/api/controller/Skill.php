<?php
/**
 * Created by xiaosong
 * E-mail:306027376@qq.com
 * Date: 2018/11/14
 * Time: 16:14
 */

namespace app\api\controller;
use app\common\logic\SkillApply;
use app\common\model\Skill as model;
use think\Db;

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

        $data = request()->only(['my_form','my_gift_id'],'post');







    }












}