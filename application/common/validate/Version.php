<?php
namespace app\common\validate;

use think\Db;
use think\Validate;

class Version extends Validate
{
    protected $rule = [
        'versionCode|版本号'  =>  'require|number',
        'versionName|版本名'  =>  'require',
        'url|链接地址'  =>  'require|url',
    ];

}