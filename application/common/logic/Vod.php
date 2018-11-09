<?php
namespace app\common\logic;

use app\admin\controller\Upload;
use think\Model;

class Vod extends Model
{


    public function saveChange($data){
        if(is_numeric($data['id'])){
            return $this->validate(true)->allowField(true)->save($data,['pid'=>$data['id']]);
        }else{
             return $this->validate(true)->allowField(true)->save($data);
        }
    }

}