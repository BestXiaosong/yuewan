<?php
namespace app\common\validate;

use think\Validate;

class SkillApply extends Validate
{

    protected $rule = [
        'img|技能封面照'  =>  'require',
        'voice|语音介绍'  =>  'require',
        'explain|技能说明'  =>  'require|max:200',
        'is_use|上下架'  =>  'between:0,1',
    ];






}