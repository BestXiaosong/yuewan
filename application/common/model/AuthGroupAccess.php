<?php
/**
 * 权限
 *
 * 权限列表管理
 * @author      LouisLivi<574747417@qq.com>
 * @version     1.0
 * @since       1.0
 */
namespace app\common\model;
use think\Db;

/**
 * 权限规则model
 */
class AuthGroupAccess extends Base{
    protected $table = '';

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->table = config('auth.auth_group_access');
    }
	/**
	 * 根据group_id获取全部用户id
	 * @param  int $group_id 用户组id
	 * @return array         用户数组
	 */
	public function getUidsByGroupId($group_id){
		$user_ids=$this
			->where(array('group_id'=>$group_id))
			->column('uid');
		return $user_ids;
	}

	/**
	 * 获取管理员权限列表
	 */
	public function getAllData(){
		$data = Db::name('auth_group_access')
			->field('u.user_id,u.user_name,aga.group_id,ag.title')
			->alias('aga')
			->join('admins u','aga.uid=u.user_id','RIGHT')
			->join('auth_group ag',' aga.group_id=ag.id','LEFT')
			->select();

		// 获取第一条数据
		$first=$data[0];
		$first['title']=array();
		$user_data[$first['user_id']]=$first;
		// 组合数组
		foreach ($data as $k => $v) {
			foreach ($user_data as $m => $n) {
				$uids=array_map(function($a){return $a['id'];}, $user_data);
				if (!in_array($v['user_id'], $uids)) {
					$v['title']=array();
					$user_data[$v['user_id']]=$v;
				}
			}
		}
		// 组合管理员title数组
		foreach ($user_data as $k => $v) {
			foreach ($data as $m => $n) {
				if ($n['user_id']==$k) {
					$user_data[$k]['title'][]=$n['title'];
				}
			}
			$user_data[$k]['title']=implode('、', $user_data[$k]['title']);
		}
		// 管理组title数组用顿号连接
		return $user_data;

	}


}
