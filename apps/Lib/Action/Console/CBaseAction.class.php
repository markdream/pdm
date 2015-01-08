<?php
/**
 * [pdm] (C)2014 markdream Inc.
 *
 * $Id: CBaseAction.class.php 2014-10-24 下午05:55:05 pony_chiang $
 */
defined ( 'PDM_URL' ) or exit ( 'Access Denied' );
abstract class CBaseAction extends PDMAction {
	protected $uid;
	protected $auth_code;
	protected $timeout;

	function __construct() {
		parent::__construct ();
		$_session = session ( 'user' );
		if ($_session == null) {
			redirect ( PDM_URL );
		}
		
		$this->_checkTimeout ();
		
		$this->uid = $_session ['id'];
		$this->auth_code = $_session ['auth_code'];
		$this->timeout = $_session ['timeout'];
		session ( 'pdm_timeout', NOW_TIME );
	}
	
	/* 超时检查 */
	private function _checkTimeout() {
		$optTime = session ( 'pdm_timeout' );
		$session_user = session ( 'user' );
		if (NOW_TIME > ($optTime + 60 * $session_user ['timeout'])) {
			$_email = base64_encode ( $session_user ['email'] );
			session ( 'user', null );
			redirect ( PDM_URL );
		}
	}
}