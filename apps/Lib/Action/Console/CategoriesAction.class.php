<?php
/**
 * [pdm] (C)2014 markdream Inc.
 *
 * $Id: CategoriesAction.class.php 2014-11-25 下午03:21:31 pony_chiang $
 */
defined ( 'PDM_URL' ) or exit ( 'Access Denied' );
class CategoriesAction extends CBaseAction {
	private $categories;

	function __construct() {
		parent::__construct ();
		$this->categories = M ( 'categories' );
	}

	function index() {
		import ( '@.Rover.Tree' );
		$tree = new Tree ();
		$map ['user_id'] = $this->uid;
		$list = $this->categories->where ( $map )->order ( 'listorder' )->select ();
		$tree->init ( $list );
		
		$_linkAddType = pdm_ux ( 'add?parent=' );
		$_linkEditType = pdm_ux ( 'edit?id=' );
		$_linkDeleteType = pdm_ux ( 'delete?id=' );
		
		$lanmu = "<span><i class='glyphicon glyphicon-leaf'></i> \$title (\$sum)</span> <a href='{$_linkAddType}\$id'>添加子类</a> | <a href='{$_linkEditType}\$id'>修改</a> | <a href='{$_linkDeleteType}\$id'>删除</a>";
		$moji = "<span><i class='glyphicon glyphicon-minus-sign'></i> \$title (\$sum)</span> <a href='{$_linkAddType}\$id'>添加子类</a> | <a href='{$_linkEditType}\$id'>修改</a> | <a href='{$_linkDeleteType}\$id'>删除</a>";
		$html = $tree->get_treeview ( 0, 'myTree', $lanmu, $moji );
		$this->assign ( 'html', $html );
		$this->display ();
	}

	function add() {
		if (IS_POST) {
			$post = I ( 'post.' );
			$data ['parent'] = $post ['parent'];
			$data ['title'] = $post ['title'];
			$data ['listorder'] = $post ['listorder'];
			$data ['add_time'] = NOW_TIME;
			$data ['user_id'] = $this->uid;
			
			if ($this->categories->add ( $data ) !== false) {
				$this->success ( '类别添加成功！' );
			} else {
				$this->error ( '类别添加失败！' );
			}
		} else {
			$parent = I ( 'parent' );
			$map ['user_id'] = $this->uid;
			$list = $this->categories->where ( $map )->order ( 'listorder' )->select ();
			import ( '@.Rover.Tree' );
			$tree = new Tree ();
			$tree->init ( $list );
			$str = "<option \$css value=\$id \$selected>\$spacer\$title</option>";
			$selected = $tree->get_tree ( 0, $str, $parent );
			$this->assign ( 'selected', $selected );
			$this->display ( 'mod' );
		}
	}

	function edit() {
		if (IS_POST) {
			$post = I ( 'post.' );
			$data ['parent'] = $post ['parent'];
			$data ['title'] = $post ['title'];
			$data ['listorder'] = $post ['listorder'];
			$map ['user_id'] = $this->uid;
			$map ['id'] = $post ['id'];
			if ($this->categories->where ( $map )->save ( $data ) !== false) {
				$this->success ( '类别修改成功！' );
			} else {
				$this->error ( '类别修改失败！' );
			}
		} else {
			$id = I ( 'id' );
			$map_categories ['id'] = $id;
			$map_categories ['user_id'] = $this->uid;
			
			$data = $this->categories->where ( $map_categories )->field ( true )->find ();
			if ($data == null) {
				$this->error ( '没有这个类别！' );
			}
			
			$map ['user_id'] = $this->uid;
			$list = $this->categories->where ( $map )->order ( 'listorder' )->select ();
			import ( '@.Rover.Tree' );
			$tree = new Tree ();
			$tree->init ( $list );
			$str = "<option \$css value=\$id \$selected>\$spacer\$title</option>";
			$selected = $tree->get_tree ( 0, $str, $data ['parent'] );
			$this->assign ( 'selected', $selected );
			$this->assign ( 'data', $data );
			$this->display ( 'mod' );
		}
	}

	function delete() {
		$id = I ( 'id' );
		$map ['id'] = $id;
		$map ['user_id'] = $this->uid;
		// 检查类别下是否存在密码
		$sum = $this->categories->where ( $map )->getField ( 'sum' );
		if ($sum > 0) {
			$this->error ( '你不能删除它，因为这个类别下面存在密码！' );
		}
		
		if ($this->categories->where ( $map )->delete () > 0) {
			$this->success ( '类别删除成功！' );
		} else {
			$this->error ( '类别删除失败！' );
		}
	}
}