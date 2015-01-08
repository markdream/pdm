<?php
/**
 * [wpx] (C)2013 markdream Inc.
 * This is NOT a freeware, use is subject to license terms.
 *
 * $Id: ToolAction.class.php 2014-10-24 下午08:39:21 pony_chiang $
 */

/**
 * 用户密码生成辅助
 *
 * @package Console
 */
class ToolAction extends CBaseAction {
	private $history;

	function __construct() {
		parent::__construct ();
		$this->history = M ( 'history' );
	}

	function index() {
		C ( 'TOKEN_ON', 0 );
		$this->display ();
	}

	/**
	 * 生成密码
	 */
	function builder() {
		$request = I ( 'post.' );
		$alen = $request ['alen'];
		$plen = $request ['plen'];
		$char = $request ['char'];
		$type = $request ['type'];
		$chars = '';
		if ($char == 1) {
			$chars = '_+=@$()[]0Oo1Ll';
		}
		$data ['user'] = pdm_rand_code ( $alen, $type, $chars );
		$data ['pwd'] = pdm_rand_code ( $plen, $type, $chars );
		
		if ($data ['user'] != '' && $data ['pwd'] != '') {
			$data_history ['user_id'] = $this->uid;
			$data_history ['uname'] = pdm_encode ( $data ['user'], $this->auth_code );
			$data_history ['pwd'] = pdm_encode ( $data ['pwd'], $this->auth_code );
			$data_history ['create_time'] = NOW_TIME;
			$data_history ['create_ip'] = get_client_ip ( 1 );
			
			if ($this->history->add ( $data_history ) !== false) {
				$lastId = $this->history->getLastInsID ();
				$data ['signature'] = pdm_code ( $lastId );
				$this->jsonRender ( $data );
			}
		}
		$this->jsonRender ( null, - 1, 'error!' );
	}
}