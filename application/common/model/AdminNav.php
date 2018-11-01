<?php
/**
 * 菜单
 *
 * 右侧菜单管理
 * @author      LouisLivi<574747417@qq.com>
 * @version     1.0
 * @since       1.0
 */
namespace app\common\model;
use think\Model;


/**
 * 菜单操作model
 */
class AdminNav extends Base {

	protected $table = 'cl_right';

	/**
	 * 删除数据
	 * @param	array	$map	where语句数组形式
	 * @return	boolean			操作是否成功
	 */
	public function deleteDataa($map){
		$count=$this
			->where(array('pid'=>$map['id']))
			->count();

		if($count!=0){
			return false;
		}

		$this->where($map)->delete();
		return true;
	}

	/**
	 * 获取全部菜单
	 * @param  string $type tree获取树形结构 level获取层级结构
	 * @return array       	结构数据
	 */
	public function getTreeDatas($type='tree',$order=''){
		// 判断是否需要排序
		if(empty($order)){
			$data=$this->select();

		}else{
			$data=$this->order('sort is null,'.$order)->select();

		}
		// 获取树形或者结构数据
		if($type=='tree'){
			$data=\Nx\Data::tree($data,'name','id','pid');
		}elseif($type="level"){
			$data=\Nx\Data::channelLevel($data,0,'&nbsp;','id');
			// 显示有权限的菜单
//			$auth=new \Auth\Auth();
//			foreach ($data as $k => $v) {
//				if ($auth->check('Admin'.$v['url'],$_SESSION['info']['id'])) {
//					foreach ($v['_data'] as $m => $n) {
//						if(!$auth->check('Admin'.$n['url'],$_SESSION['info']['id'])){
//							unset($data[$k]['_data'][$m]);
//						}
//					}
//				}else{
//					// 删除无权限的菜单
//					unset($data[$k]);
//				}
//			}
		}
		// p($data);die;

		return $data;

	}


}
