<?php
/**
 * 回收站
 * @author jxcent@gmail.com
 *
 */
class RecycleAction extends SBAction {
	private $privacy;
	private $type, $record;
	
	function __construct() {
		parent::__construct ();
		$this->privacy = D ( 'Privacy' );
		$this->type = M ( 'type' );
		$this->record = M ( 'record' );
	}
	
	function password() {
		$get = $this->_get ();
		
		if ($get ['typeid'] != null) {
			$map ['typeid'] = $get ['typeid'];
		}
		
		$map ['mark'] = 1;
		$map ['userid'] = $this->userid;
		$page = $this->pageInit ( $this->privacy->where ( $map )->count (), '个密码' );
		$_tmp_list = $this->privacy->field ( 'tid,typeid,title,mark_time' )->where ( $map )->limit ( $page->firstRow, $page->listRows )->order ( 'optime DESC' )->select ();
		//获取栏目
		$typelist = $this->get_type ();
		
		//开始解密
		foreach ( $_tmp_list as $k => $v ) {
			$newlist [$k] ['tid'] = $v ['tid'];
			$newlist [$k] ['typename'] = $typelist [$v ['typeid']] ['typename'];
			$newlist [$k] ['title'] = $v ['title'];
			$newlist [$k] ['mark_time'] = $v ['mark_time'];
		}
		unset ( $_tmp_list );
		$this->assign ( 'typelist', $typelist );
		$this->assign ( 'list', $newlist );
		$this->display ();
	}
	
	//密码回滚
	function pwd_rollback() {
		$id = $this->_get ( 'spm' );
		$this->_check_owner ( $id );
		$map ['tid'] = $id;
		$data ['mark'] = 0;
		$data ['mark_time'] = 0;
		if ($this->privacy->where ( $map )->save ( $data ) !== false) {
			$this->success ( '密码还原成功！' );
		} else {
			$this->success ( '密码还原失败！' );
		}
	}
	
	//将密码删除
	function pwd_remove() {
		$id = $this->_get ( 'spm' );
		$retdata = $this->_check_owner ( $id, true );
		//判断密码失效
		$expire_time = $retdata ['mark_time'] + PDB_ALLOW_DELETE_DAY * 86400;
		if (time () <= $expire_time) {
			$this->error ( '这个密码放入回收站没有超过' . PDB_ALLOW_DELETE_DAY . '天，你不能删除它！' );
		}
		$map ['tid'] = $id;
		$map ['mark'] = 1;
		if ($this->privacy->where ( $map )->delete () > 0) {
			$this->success ( '密码已删除！' );
		} else {
			$this->error ( '密码不存在！' );
		}
	}
	
	//检查密码所有者
	private function _check_owner($id, $ret = false) {
		$map ['userid'] = $this->userid;
		$map ['tid'] = $id;
		$data = $this->privacy->field ( true )->where ( $map )->find ();
		if ($data == null) {
			$this->error ( '你没有这个密码' );
		}
		
		if ($ret == true) {
			return $data;
		}
	}
	
	//更新分类密码统计[正常 不包括回收站内的] 虽然目前性能没有考虑  对于几万数据绰绰有余
	private function update_typenum() {
		//获取该用户的分类
		$map_type ['userid'] = $this->userid;
		$owner_typelist = $this->type->field ( 'type_id' )->where ( $map_type )->select ();
		foreach ( $owner_typelist as $k => $v ) {
			$count = $this->privacy->where ( array ('typeid' => $v ['type_id'], 'mark' => 0 ) )->count ();
			$this->type->where ( array ('type_id' => $v ['type_id'] ) )->save ( array ('num' => $count ) );
		}
	}
	
	/**
	 * 获取栏目
	 */
	private function get_type() {
		$map ['userid'] = $this->userid;
		$_data = $this->type->field ( true )->where ( $map )->order ( 'rank' )->select ();
		
		foreach ( $_data as $k => $v ) {
			$data [$v ['type_id']] = $v;
		}
		unset ( $_data );
		return $data;
	}

}