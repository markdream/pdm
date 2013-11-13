<?php
class PasswordAction extends SBAction {
	private $privacy;
	private $type, $record;
	
	function __construct() {
		parent::__construct ();
		$this->privacy = D ( 'Privacy' );
		$this->type = M ( 'type' );
		$this->record = M ( 'record' );
	}
	
	function index() {
		$get = $this->_get ();
		
		if ($get ['typeid'] != null) {
			$map ['typeid'] = $get ['typeid'];
		}
		
		$map ['mark'] = 0;
		$map ['userid'] = $this->userid;
		$page = $this->pageInit ( $this->privacy->where ( $map )->count (), '个密码' );
		$_tmp_list = $this->privacy->field ( 'tid,typeid,title,account,password,optime' )->where ( $map )->limit ( $page->firstRow, $page->listRows )->order ( 'optime DESC' )->select ();
		//获取栏目
		$typelist = $this->get_type ();
		
		//启用加密  开始解密
		foreach ( $_tmp_list as $k => $v ) {
			$newlist [$k] ['tid'] = $v ['tid'];
			$newlist [$k] ['typename'] = $typelist [$v ['typeid']] ['typename'];
			$newlist [$k] ['title'] = $v ['title'];
			$newlist [$k] ['account'] = decode ( $v ['account'] );
			$newlist [$k] ['password'] = decode ( $v ['password'] );
			$newlist [$k] ['optime'] = $v ['optime'];
		}
		unset ( $_tmp_list );
		$this->assign ( 'typelist', $typelist );
		$this->assign ( 'list', $newlist );
		$this->display ();
	}
	
	//载入添加密码
	function add() {
		$get = $this->_get ();
		if ($get ['rcid'] != null) {
			$map_rec ['id'] = $get ['rcid'];
			$map_rec ['userid'] = $this->userid;
			$passwd = $this->record->field ( 'safeuname,password' )->where ( $map_rec )->find ();
			if ($passwd != null) {
				$this->assign ( 'passwd', $passwd );
			}
		}
		
		$map ['userid'] = $this->userid;
		$typelist = $this->type->where ( $map )->field ( 'type_id,typename' )->order ( 'rank' )->select ();
		$this->assign ( 'typelist', $typelist );
		$this->display ();
	}
	
	//执行新增密码
	function create() {
		if ($this->privacy->create ()) {
			if ($this->privacy->add () > 0) {
				//更新目录密码数
				$this->update_typenum ();
				$this->success ( '成功添加一个密码' );
			} else {
				$this->error ( '密码添加失败' );
			}
		} else {
			$this->error ($this->privacy->getError() );
		}
	}
	
	//显示一个密码
	function view() {
		$id = $this->_get ( 'spm' );
		$data = $this->_check_owner ( $id, true );
		$this->assign ( 'typelist', $this->get_type () );
		$this->assign ( 'priv', $data );
		$this->display ();
	}
	
	//系统不支持修改密码操作[为了安全起见]
	function update() {
		$id = $this->_post ( 'tid' );
		$this->_check_owner ( $id );
		$data ['title'] = h ( $this->_post ( 'title' ) );
		$data ['description'] = h ( $this->_post ( 'description' ) );
		$map ['tid'] = $id;
		if ($this->privacy->where ( $map )->save ( $data ) !== false) {
			$this->update_typenum ();
			$this->success ( '密码修改成功！' );
		} else {
			$this->error ( '密码修改失败！' );
		}
	}
	
	//将密码移到回收站
	function recycle() {
		$id = $this->_get ( 'spm' );
		$map ['tid'] = $id;
		$map ['userid'] = $this->userid;
		$data ['mark_time'] = time ();
		$data ['mark'] = 1;
		if ($this->privacy->where ( $map )->save ( $data ) !== false) {
			$this->update_typenum ();
			$this->success ( '密码已移到回收站！' );
		} else {
			$this->error ( '密码不存在！' );
		}
	}
	/**
	 * 提供搜索api
	 */
	function so_service() {
		$wd = trim($this->_get ( 'wd' ));
		if ($wd == null) {
			return null;
		}
		$map ['userid'] = $this->userid;
		$map ['title'] = array ('LIKE', '%' . $wd . '%' );
		$_list = $this->privacy->field ( 'title' )->where ( $map )->select ();
		foreach ( $_list as $k => $v ) {
			$tmp [$k] = $v ['title'];
		}
		unset ( $_list );
		$this->ajaxReturn ( $tmp );
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
	
	function dbhp() {
		$data = $this->privacy->field ( 'tid,account,password' )->select ();
		foreach ( $data as $k => $v ) {
			$map ['tid'] = $v ['tid'];
			$data ['id'] = shortGen ( $v ['tid'] );
			$this->privacy->where ( $map )->save ( $data );
		}
		
		echo $this->privacy->getLastSql ();
	
	}
	
	function dbfp() {
		$data = $this->privacy->field ( 'tid,account,password' )->select ();
		
		foreach ( $data as $k => $v ) {
			$tem [$k] ['safe_a'] = GM::decode ( $v ['account'] );
			$tem [$k] ['safe_p'] = GM::decode ( $v ['password'] );
		}
		dump ( $tem );
	}
	
	function xixi() {
		echo shortGen ( 'asdasasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdd' );
	}
}