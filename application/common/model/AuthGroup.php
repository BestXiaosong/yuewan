<?php
/**
 * 权限
 *
 * 权限组管理
 * @author      LouisLivi<574747417@qq.com>
 * @version     1.0
 * @since       1.0
 */
namespace app\common\model;
/**
 * 权限规则model
 */
class AuthGroup extends Base{
    protected $table = '';

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->table = config('auth.auth_group');
    }

	/**
	 * 传递主键id删除数据
	 * @param  array   $map  主键id
	 * @return boolean       操作是否成功
	 */
	public function deleteData($map){
		$result = $this->where($map)->delete();
		$group_map=array(
			'group_id'=>$map['id']
			);
		// 删除关联表中的组数据
		$AuthGroupAccess = new AuthGroupAccess();
		$AuthGroupAccess->deleteData($group_map);
		return $result;
	}



}
