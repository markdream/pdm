<?php
abstract class SBAction extends Action {
	
	protected $userid;
	function __construct() {
		parent::__construct ();
		//超时判断
		$this->checkTimeout ();
		if (session ( 'user' ) == null) {
			$this->redirect ( 'account/login' );
		}
		
		session ( 's_timeout', time () );
		$session_user = session ( 'user' );
		$this->userid = $session_user ['id'];
		$this->assign ( 'user', $session_user );
	}
	
	//分页封装
	protected function pageInit($count, $header = '条记录', $row = 10, $url = '') {
		import ( "@.Rover.Page" ); // 导入分页类
		$page = new Page ( $count, $row );
		if ($url != '') {
			$page->url = $url;
		}
		$page->setConfig ( 'header', $header );
		$page->setConfig ( 'next', '后页' );
		$page->setConfig ( 'prev', '前页' );
		$page->setConfig ( 'theme', '<li class="disabled"><a>Page</a></li> <li>%upPage%</li>  %linkPage% <li>%downPage%</li>' );
		$this->assign ( 'page', $page->show () );
		return $page;
	}
	
	//检查超时 3分钟不操作 强制退出
	private function checkTimeout() {
		$loginTime = session ( 's_timeout' );
		if (time () > ($loginTime + 60*3)) {
			$session_user = session ( 'user' );
			$_email = base64_encode ( $session_user ['email'] );
			session ( 'user', null );
			header ( 'Location:' . P_LINK . '/security/account/login?pvg=' . md5 ( time () ) . '&relogin=true&account=' . $_email . '&refer=' . urlencode ( P_HOST . $_SERVER ['REQUEST_URI'] ) );
			die ();
		}
	}
}