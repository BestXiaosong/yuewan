<?php
namespace app\common\validate;

use think\Validate;

class Vod extends Validate
{
    protected $rule = [
        'title'  =>  'require|length:2,10',
        'img'  =>  'require',
//        'url|申请外链'  =>  'require|url',
//        'top'  =>  'require|in:0,1',
//        'money'  =>  'require|number',
//        'pass|通过率'  =>  'number|between:1,100',
//        'rate'  =>  'require|between:0.1,100',
//        'maid|代理返佣比例'  =>  'require|between:0.1,100',
//        'mini|最小期限'  =>  'require|number',
//        'max'  =>  'require|gt:mini',
    ];

    protected $message = [
        'title.length'  =>  '直播标题为2到10位字符',
        'title.require'  =>  '直播标题不能为空',
        'img.require'  =>  '封面图不能为空',
    ];



}