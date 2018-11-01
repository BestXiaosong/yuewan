<?php
namespace app\common\logic;

use think\Model;


class BannerCate extends Model
{
    public function saveChange($data){
        $data['update_time'] = time();
        if(is_numeric($data['id'])){
            return $this->validate(true)->allowField(true)->save($data,['cid'=>$data['id']]);
        }else{
            $data['create_time'] = time();
            return $this->validate(true)->allowField(true)->save($data);
        }
    }




}