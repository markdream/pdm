<?php
/**
 * [pdm] (C)2014 markdream Inc.
 *
 * $Id: PasswordAction.class.php 2014-11-25 下午04:33:08 pony_chiang $
 */
defined ( 'PDM_URL' ) or exit ( 'Access Denied' );
class PasswordAction extends CBaseAction {
	private $password;
	private $categories;
	private $history;

	function __construct() {
		parent::__construct ();
		$this->password = M ( 'password' );
		$this->categories = M ( 'categories' );
		$this->history = M ( 'history' );
	}

	function index() {
		import ( '@.Rover.Tree' );
		$tree = new Tree ();
		$map ['user_id'] = $this->uid;
		$list = $this->categories->where ( $map )->order ( 'listorder' )->select ();
		$tree->init ( $list );
		
		$_linkAddType = pdm_ux ( 'add?typeid=' );
		$_linkViewType = pdm_ux ( 'collect?typeid=' );
		
		$leaf = "<span><i class='glyphicon glyphicon-leaf'></i> \$title (\$sum)</span> <a href='{$_linkAddType}\$id'>添加密码</a> | <a href='{$_linkViewType}\$id'>查看密码</a>";
		$folder = "<span><i class='glyphicon glyphicon-minus-sign'></i> \$title (\$sum)</span> <a href='{$_linkAddType}\$id'>添加密码</a> | <a href='{$_linkViewType}\$id'>查看密码</a>";
		$html = $tree->get_treeview ( 0, 'myTree', $leaf, $folder );
		$this->assign ( 'html', $html );
		$this->display ();
	}
	
	/* 添加密码 */
	function add() {
		if (IS_POST) {
			$post = I ( 'post.' );
			$this->_check_form ( $post );
			
			$data ['uname'] = pdm_encode ( $post ['uname'], $this->auth_code );
			$data ['pwd'] = pdm_encode ( $post ['pwd'], $this->auth_code );
			$data ['category_id'] = $post ['category_id'];
			$data ['user_id'] = $this->uid;
			$data ['add_time'] = NOW_TIME;
			$data ['add_ip'] = get_client_ip ( 1 );
			$data ['note'] = $post ['note'];
			$data ['title'] = $post ['title'];
			
			if ($this->password->add ( $data ) !== false) {
				
				$map_categories ['id'] = $post ['category_id'];
				$map_categories ['user_id'] = $this->uid;
				$data_categories ['sum'] = array ( 'exp','sum+1' );
				$this->categories->where ( $map_categories )->save ( $data_categories );
				
				$this->success ( '密码添加成功！', pdm_ux ( 'index' ) );
			} else {
				$this->error ( '密码添加失败！' );
			}
		} else {
			$get = I ( 'get.' );
			if ($get ['sign'] != '') {
				$map_history ['id'] = pdm_code ( $get ['sign'], 'DECODE' );
				$map_history ['user_id'] = $this->uid;
				$data_history = $this->history->where ( $map_history )->field ( 'uname,pwd' )->find ();
				if ($data_history == null) {
					$this->error ( '参数不正确！' );
				}
				
				$data ['uname'] = pdm_decode ( $data_history ['uname'], $this->auth_code );
				$data ['pwd'] = pdm_decode ( $data_history ['pwd'], $this->auth_code );
			}
			
			$map ['user_id'] = $this->uid;
			$list = $this->categories->where ( $map )->order ( 'listorder' )->select ();
			import ( '@.Rover.Tree' );
			$tree = new Tree ();
			$tree->init ( $list );
			$str = "<option \$css value=\$id \$selected>\$spacer\$title</option>";
			$selected = $tree->get_tree ( 0, $str, $get ['typeid'] );
			$this->assign ( 'selected', $selected );
			$this->assign ( 'data', $data );
			$this->display ( 'mod' );
		}
	}
	
	/* 列出密码 */
	function collect() {
		$typeid = I ( 'typeid' );
		$map ['category_id'] = $typeid;
		$map ['user_id'] = $this->uid;
		$map ['status'] = 0;
		$page = $this->pageInit ( $this->password->where ( $map )->count (), '条密码记录' );
		$list = $this->password->where ( $map )->field ( 'id,title,add_time' )->order ( 'add_time DESC' )->limit ( $page->firstRow, $page->listRows )->select ();
		$this->assign ( 'list', $list );
		$this->display ();
	}
	
	/* 查看密码 */
	function view() {
		$id = I ( 'id' );
		$map ['user_id'] = $this->uid;
		$map ['id'] = pdm_code ( $id, 'DECODE' );
		
		$data = $this->password->where ( $map )->field ( true )->find ();
		if ($data == null) {
			$this->error ( '参数错误！' );
		}
		
		$data ['uname'] = pdm_decode ( $data ['uname'], $this->auth_code );
		$data ['pwd'] = pdm_decode ( $data ['pwd'], $this->auth_code );
		
		// 获取类别
		$map_categories ['id'] = $data ['category_id'];
		$data ['category_name'] = $this->categories->where ( $map_categories )->getField ( 'title' );
		
		// 计算热点
		$data_password ['hits'] = array ( 'exp','hits+1' );
		$this->password->where ( $map )->save ( $data_password );
		
		$this->assign ( 'data', $data );
		$this->display ();
	}
	
	/* 删除密码 */
	function delete() {
		$id = I ( 'id' );
		$map ['user_id'] = $this->uid;
		$map ['id'] = pdm_code ( $id, 'DECODE' );
		$map ['status'] = 0;
		$data = $this->password->where ( $map )->field ( true )->find ();
		if ($data == null) {
			$this->error ( '参数错误！' );
		}
		$data_save ['status'] = 1;
		$data_save ['delete_time'] = NOW_TIME;
		if ($this->password->where ( $map )->save ( $data_save ) !== false) {
			$this->_update_password ( $data ['category_id'] );
			$this->success ( '密码放入回收站成功！' );
		} else {
			$this->error ( '操作失败！' );
		}
	}
	
	/* 密码搜索提示 */
	function search() {
		$wd = trim ( I ( 'query' ) );
		if ($wd == null) {
			return null;
		}
		$map ['userid'] = $this->uid;
		$map ['title'] = array ( 'LIKE','%' . $wd . '%' );
		$_list = $this->password->field ( 'id,title' )->where ( $map )->select ();
		foreach ( $_list as $k => $v ) {
			$tmp [$k] ['id'] = pdm_code ( $v ['id'] );
			$tmp [$k] ['name'] = $v ['title'];
		}
		unset ( $_list );
		$this->ajaxReturn ( $tmp );
	}

	private function _update_password($typeid) {
		$map ['category_id'] = $typeid;
		$map ['status'] = 0;
		$count = $this->password->where ( $map )->count ();
		$map_category ['id'] = $typeid;
		$data_category ['sum'] = $count;
		$this->categories->where ( $map_category )->save ( $data_category );
	}
	
	/* 检查表单 */
	private function _check_form($post) {
		$map ['id'] = $post ['category_id'];
		$map ['user_id'] = $this->uid;
		$id = $this->categories->where ( $map )->getField ( 'id' );
		if ($id == null) {
			$this->error ( '请选择密码类别！' );
		}
		if ($post ['title'] == '') {
			$this->error ( '密码标题不能为空！' );
		}
		
		if ($post ['uname'] == '') {
			$this->error ( '用户名不能为空！' );
		}
		
		if ($post ['pwd'] == '') {
			$this->error ( '密码不能为空！' );
		}
		
		if ($post ['note'] == '') {
			$this->error ( '备注不能为空！' );
		}
	}
}