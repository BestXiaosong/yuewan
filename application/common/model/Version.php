<?php
namespace app\common\model;




class Version extends Base
{
    protected $autoWriteTimestamp = 'datetime';
    public function getList($where = []){
        return $this->where($where)->order('id DESC')->paginate(15,false,['query'=>request()->param()]);
    }

    public function saveChange($data){
        if(is_numeric($data['id'])){
            return $this->validate(true)->allowField(true)->save($data,['id'=>$data['id']]);
        }else{
            return $this->validate(true)->allowField(true)->save($data);
        }
    }
}