<?php
/**
 * [pdm] (C)2014 markdream Inc.
 *
 * $Id: RecycleAction.class.php 2014-12-27 下午07:44:24 pony_chiang $
 */
defined ( 'PDM_URL' ) or exit ( 'Access Denied' );
class RecycleAction extends CBaseAction {
	private $password;
	private $categories;

	function __construct() {
		parent::__construct ();
		$this->password = M ( 'password' );
		$this->categories = M ( 'categories' );
	}

	function index() {
		$map ['user_id'] = $this->uid;
		$map ['status'] = 1;
		$page = $this->pageInit ( $this->password->where ( $map )->count (), '条密码记录' );
		$list = $this->password->where ( $map )->field ( 'id,title,delete_time' )->order ( 'delete_time DESC' )->limit ( $page->firstRow, $page->listRows )->select ();
		$this->assign ( 'list', $list );
		$this->display ();
	}

	/**
	 * 撤销
	 */
	function rollback() {
		$id = I ( 'id' );
		$map ['user_id'] = $this->uid;
		$map ['id'] = pdm_code ( $id, 'DECODE' );
		
		$category_id = $this->password->where ( $map )->getField ( 'category_id' );
		
		$data_save ['status'] = 0;
		$data_save ['delete_time'] = 0;
		if ($this->password->where ( $map )->save ( $data_save ) !== false) {
			$this->_update_password ( $category_id );
			$this->success ( '密码撤销成功！' );
		} else {
			$this->error ( '操作失败！' );
		}
	}

	/**
	 * 删除密码
	 */
	function delete() {
		$id = I ( 'id' );
		$map ['user_id'] = $this->uid;
		$map ['id'] = pdm_code ( $id, 'DECODE' );
		$map ['status'] = 1;
		// 获取密码信息
		$data = $this->password->field ( 'category_id,delete_time' )->where ( $map )->find ();
		
		$expire_time = $data ['delete_time'] + PDM_ALLOW_DELETE_DAY * 86400;
		if (NOW_TIME <= $expire_time) {
			$this->error ( '这个密码放入回收站没有超过' . PDM_ALLOW_DELETE_DAY . '天，你不能删除它！' );
		}
		
		if ($this->password->where ( $map )->delete () > 0) {
			$this->_update_password ( $data ['category_id'] );
			$this->success ( '密码删除成功！' );
		} else {
			$this->error ( '密码删除失败！' );
		}
	}

	/**
	 * 更新密码栏目数目
	 * @param int $typeid  栏目id
	 */
	private function _update_password($typeid) {
		$map ['category_id'] = $typeid;
		$map ['user_id'] = $this->uid;
		$map ['status'] = 0;
		$count = $this->password->where ( $map )->count ();
		$map_category ['id'] = $typeid;
		$data_category ['sum'] = $count;
		$this->categories->where ( $map_category )->save ( $data_category );
	}
}