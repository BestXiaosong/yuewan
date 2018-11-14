<?php
/**
 * Created by xiaosong
 * E-mail:306027376@qq.com
 * Date: 2018/11/14
 * Time: 10:16
 */

namespace app\admin\controller;
use app\common\model\Skill as model;
use Qiniu\Auth;

class Skill extends Base
{

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 技能列表
     */
    public function index()
    {

        $map = [];

        if (input('skill_name')) $map['skill_name'] = ['like','%'.trim(input('skill_name')).'%'];

        if(!isEmpty($_GET['type'])) $map['type'] = trim(input('type'));

        $model = new model();
        $rows  = $model->getList($map);
        $this->assign([
            'title' => '技能列表',
            'rows' => $rows,
            'pageHTML' => $rows->render(),
        ]);

        return view();

    }

    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 技能编辑
     */
    public function edit()
    {
        $auth  = new Auth(config('qiniu')['ACCESSKEY'], config('qiniu')['SECRETKEY']);
        $token = $auth->uploadToken(config('qiniu')['bucket']);
        $domain = config('qiniu')['domain'];
        $this->assign([
            'token' => $token,
            'domain'=> $domain,
        ]);

        $table = 'skill';
        $this->_edit($table,'技能编辑',url('index'),'skill');
        return view();
    }


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 更改状态
     */
    public function change()
    {
        $this->_change('skill');
    }


    /**
     * Created by xiaosong
     * E-mail:306027376@qq.com
     * 技能删除
     */
    public function del()
    {
        $this->_del('skill');
    }


}