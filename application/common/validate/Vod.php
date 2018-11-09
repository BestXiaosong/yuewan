<?php
namespace app\common\validate;

use think\Validate;

class Vod extends Validate
{
    protected $rule = [
        'play_url|视频'  =>  'require',
    ];



}