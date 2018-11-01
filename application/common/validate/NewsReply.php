<?php
namespace app\common\validate;

use think\Validate;

class NewsReply extends Validate
{
    protected $rule = [
        'news_id'  =>  'require|number',
        'user_id'  =>  'require|number',
        'type' =>  'require|checkType',
//        'content' =>  'require|max:40',
//        'status' =>  'require|checkStatus',
    ];

    protected $message = [
        'news_id.require'  =>  '咨询id必填',
        'news_id.number'  =>  '咨询id必须是数字',
        'user_id.require'  =>  '用户id必填',
        'user_id.number'  =>  '用户id必须是数字',
        'type.require'  =>  '评论类型必选',
//        'status.require' =>  '状态必须',
    ];

    protected $scene = [
        'add'   =>  ['news_id','type','user_id'],
        'edit'  =>['news_id','type','user_id','status']
    ];

    public function checkStatus($value){
        if($value==0||$value==1){
            return true;
        }
        return '不合法参数值';
    }

    public function checkType($value){
        if($value==2||$value==1){
            return true;
        }
        return '不合法参数值';
    }
}