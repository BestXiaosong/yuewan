<?php
/**
 * 基础model
 *
 * 基础Model类
 * @author      LouisLivi<574747417@qq.com>
 * @version     1.0
 * @since       1.0
 */
namespace app\common\model;


use think\Model;

class Job extends Model
{

    public function getList()
    {
        $map['pid'] = 0;

        $rows = $this->where($map)->select();

        foreach ($rows as $k => $v){
            $map['pid'] = $v['j_id'];
            $rows[$k]['son'] = $this->where($map)->select();
        }

        return $rows;

    }





}
