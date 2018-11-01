<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6 0006
 * Time: 10:37
 */

namespace app\common\logic;


use think\Model;

class Opinion extends Model
{
    /**
     * @param $data
     * @return array
     * 保存反馈信息
     */
    public function saveOpinion($data)
    {
        $res = $this->allowField(true)->validate(true)->save($data);
        if($res === false){
            return ['code'=>0,'msg'=>$this->getError()];
        }
        return ['code'=>1,'msg'=>'反馈成功'];

    }

}