<?php
/**
 * 基础model
 *
 * 基础Model类
 * @author      LouisLivi<574747417@qq.com>
 * @version     1.0
 * @since       1.0
 */
namespace app\common\logic;


use think\Model;
/**
 * 基础model
 */
class Logic extends Model
{


    public function changeTable($table)
    {
        $this->setTable(config('database.prefix').$table);
        return $this;
    }

    public function saveChange($table = null,$data = [],$validate = true)
    {

        if (!$table){

            $this->error = '请确认要操作的表名';
            return false;

        }else{

            $this->setTable(config('database.prefix').$table);

        }

        if(is_numeric($data['id'])){
            return $this->validate($validate)->allowField(true)->save($data,[$this->getPk()=>$data['id']]);
        }else{
            return $this->validate($validate)->allowField(true)->save($data);
        }

    }





    public function saveAllChange($table = null,$data = [],$validate = true)
    {

    }








}
