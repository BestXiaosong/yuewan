<?php
/**
 * 权限
 *
 * 权限列表
 * @author      LouisLivi<574747417@qq.com>
 * @version     1.0
 * @since       1.0
 */
namespace app\common\model;
/**
 * 权限规则model
 */
class AuthRule extends Base{
    protected $table = '';

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->table = config('auth.auth_rule');
    }

	/**
	 * 删除数据auth_rule
	 * @param	array	$map	where语句数组形式
	 * @return	boolean			操作是否成功
	 */
	public function deleteData($map){
		$count=$this
			->where(array('pid'=>$map['id']))
			->count();
		if($count!=0){
			return false;
		}

		$result=$this->where($map)->delete();
		return $result;
	}




}
