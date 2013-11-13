<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends SBAction {
	
	function __construct() {
		parent::__construct ();
	}
	
	public function index() {
		$wd = trim($this->_get ( 'q' ));
		if ($wd != null) {
			G('run');
			$map ['userid'] = $this->userid;
			$map ['title|description'] = array ('LIKE', '%' . $wd . '%' );
			$_list = M('privacy')->field ( 'title,description,tid,optime,mark' )->where ( $map )->select ();
			$mt=keyword2red($wd,$_list,array('title','description'));
			$this->assign ('list', $mt );
			$this->assign('so',1);
			$this->assign('runtime',G('run','end'));
		}
		$this->display ();
	}

}