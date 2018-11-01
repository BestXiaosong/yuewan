<?php
namespace app\common\validate;

use think\Validate;

class News extends Validate
{
    protected $rule = [
        'title'  =>  'require|max:255',
        'lihao' =>  'require|number',
        'likong' =>  'require|number',
        'is_top' =>  'require|checkIsTop',
        'img' =>  'require|max:255',
        'detail' =>  'require',
        'status'=>'checkIsTop'
    ];

    protected $message = [
        'title.require'  =>  '标题必须',
        'title.max'  =>  '用户名长度最多255位',
        'lihao.require' =>  '利好人数必须',
        'lihao.number' =>  '利好人数必须是数字',
        'likong.require' =>  '利空人数必须',
        'likong.number' =>  '利空人数必须是数字',
        'is_top.require' =>  '推荐必选',
        'img.require' =>  '封面图片必须',
        'img.max' =>  '封面图片长度最多255个',
        'detail.require' =>  '详情必须',
    ];

    protected $scene = [
        'edit'   =>  ['title','lihao','likong','is_top','img','detail','status'],
        'add'   =>  ['title','lihao','likong','is_top','img','detail','status'],

    ];

    public function checkIsTop($value){
        if($value==0||$value==1){
            return true;
        }
        return '不合法参数值';

    }

}