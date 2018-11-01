<?php
namespace app\common\validate;

use think\Validate;

class NewsCollect extends Validate
{
    protected $rule = [
        'news_id' =>  'require|number',
        'user_id' =>  'require|number',
    ];

    protected $message = [
        'news_id.require' =>  '利好人数必须',
        'news_id.number' =>  '利好人数必须是数字',
        'user_id.require' =>  '利空人数必须',
        'user_id.number' =>  '利空人数必须是数字',
    ];


}