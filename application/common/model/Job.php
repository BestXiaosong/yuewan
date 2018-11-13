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

        $rows = $this->where($map)->field('j_id,job')->order('sort')->select();

        foreach ($rows as $k => $v){
            $map['pid'] = $v['j_id'];
            $rows[$k]['items'] = $this->where($map)->field('j_id,job')->order('sort')->select();
        }

        return $rows;

    }


    public function cate($pid = 0,$rows = [],$max = 0){
        $where['pid'] = $pid;
        $result = $this->where($where)->order('sort')->select();
        $max    = $max + 3;
        foreach($result as $k => $v){
            if ($max == 3){
                $v['_name'] = $v['job'];
                $rows[] = $v;
                $rows   = $this->cate($v['j_id'],$rows,$max);
            }else{
                $v['_name'] = str_repeat('&nbsp',$max).'└─ &nbsp'.$v['job'];
                $rows[] = $v;
            }
        }
        return $rows;
    }





}
