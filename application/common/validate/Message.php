<?php
namespace app\common\validate;

use think\Validate;

class Message extends Validate
{
    protected $rule = [
        'title' => 'require|max:30',
        'content' => 'require|max:255',
        'type'   => 'require|checkStatus'
    ];

    protected $message = [
        'title.require' => '标题必须',
        'title.max' => '标题最多30个字符',

        'content.require' => '内容不能为空',
        'content.max' => '内容最多255个字符',
        'type.require'=>'类型不能为空'
    ];

    public function checkStatus($value){
        if($value==0||$value==1){
            return true;
        }
        return '不合法参数值';
    }

}