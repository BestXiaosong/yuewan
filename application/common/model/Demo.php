<?php
namespace app\common\model;

use think\Model;


class Demo extends Model
{
    public function getList($where = []){
        return $this->where($where)->field('plate_number')->select();
    }

    public function cate($pid = 0,$rows = array(),$max = 0){
        $where['pid'] = $pid;
        $where['status'] = 1;
        $result = $this->where($where)->select();
        $max    = $max + 3;
        foreach($result as $k => $v){
            $v['cate_name'] = str_repeat('&nbsp',$max).'|--'.$v['cate_name'];
            $rows[] = $v;
            $rows   = $this->cate($v['fid'],$rows,$max);
        }
        return $rows;
    }

    public function getJoinList($filter = []){
        return $this->alias('que')
            ->where($filter)
            ->order('que.qid DESC')
            ->join('category cate','que.cid = cate.cid')
            ->field('que.*,cate.cate_name')
            ->paginate(15,false,['query'=>request()->param()]);
    }



}