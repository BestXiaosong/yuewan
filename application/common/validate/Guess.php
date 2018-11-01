<?php
namespace app\common\validate;

use think\Validate;

class Guess extends Validate
{
    protected $rule = [
        'title|竞猜标题'  =>  'require',
        'answer_A|竞猜选项'  =>  'require',
        'answer_B|竞猜选项'  =>  'require',
    ];
}