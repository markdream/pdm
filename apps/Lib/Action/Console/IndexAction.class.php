<?php
/**
 * [pdm] (C)2014 markdream Inc.
 *
 * $Id: IndexAction.class.php 2014-10-24 下午05:55:05 pony_chiang $
 */
defined ( 'PDM_URL' ) or exit ( 'Access Denied' );
class IndexAction extends CBaseAction {
	private $pwd;

	function __construct() {
		parent::__construct ();
		$this->pwd = M ( 'password' );
	}

	function index() {
		C ( 'TOKEN_ON', 0 );
		$wd = I ( 'wd' );
		if ($wd != '') {
			$map ['title|note'] = array ( 'LIKE','%' . $wd . '%' );
		}
		$map ['user_id'] = $this->uid;
		$hits_list = $this->pwd->where ( $map )->order ( 'hits DESC' )->limit ( 5 )->select ();
		$this->assign ( 'list', $hits_list );
		$this->display ();
	}
}