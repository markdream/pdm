<?php
/**
 * [pdm] (C)2014 markdream Inc.
 *
 * $Id: PDMAction.class.php  2014-10-24 下午12:15:43 pony_chiang $
 */

defined('PDM_URL') or exit('Access Denied');
 
abstract class PDMAction extends Action {
	function __construct() {
		parent::__construct ();
		// waf监控
		if (PDM_WAF_MODE == 1) {
			import('@.Rover.Waf');
			Waf::load();
		}
	}
	
	// 分页
	protected function pageInit($count, $header = '条记录', $row = PDM_LIMIT, $url = '') {
		import ( "@.Rover.Page" );
		$page = new Page ( $count, $row );
		if ($url != '') {
			$page->url = $url;
		}
		$page->setConfig ( 'header', $header );
		$page->setConfig ( 'first', '首页' );
		$page->setConfig ( 'last', '尾页' );
		$page->setConfig ( 'theme', '<li class="previous">%upPage%</li><li class="next"> %downPage%</li>' );
		$this->assign ( 'page', $page->show () );
		
		//开始记录分页序号 S
		if ($page->nowPage == 1) {
			$current_page = 0;
			$row = 0;
		} else {
			$row = $page->listRows;
			$current_page = $page->nowPage;
		}
		$this->assign ( 'page_now', $current_page );
		$this->assign ( 'page_row', $row );
		//开始记录分页序号 E
		return $page;
	}
	
	/**
	 * 高级json返回
	 * @param mix $data 数据
	 * @param int $code 返回码
	 * @param string $msg 错误信息
	 */
	protected function jsonRender($data=null, $code = 0, $msg = '') {
		if ($code == 0) {
			if($data==null)
				$data=array('state'=>'successful');
			$status ['code'] = 0;
			$tmp = array_merge ( $status, $data );
		} else {
			$tmp ['code'] = $code;
			$tmp ['info'] = $msg;
		}
		$this->ajaxReturn ( $tmp );
	}
}