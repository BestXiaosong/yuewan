<?php
namespace app\common\model;




class Helpers extends Base
{
    public function getList($map = []){
        return $this
            ->where($map)
            ->order('sort')
            ->paginate('',false,['query'=>request()->param()]);
    }

    public function getRows($map = [])
    {
        $rows = $this->field('h_id,img,title,brief,is_top,is_hot')->order('sort')->where($map)->cache(60)->select();

        $items['hot'] = [];
        $items['top'] = [];
        $items['rows'] = [];
        foreach ($rows as $k => $v){
            if ($v['is_hot'] == 1){
                $items['hot'][] = $v;
            }elseif ($v['is_top'] == 1){
                $items['top'][] = $v;
            }else{
                $items['rows'] = $v;
            }
        }
        return $items;
    }


}