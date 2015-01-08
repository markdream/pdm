<?php
/**
 * [pdm] (C)2014 markdream Inc.
 *
 * $Id: IndexAction.class.php  2014-10-24 下午12:29:09 pony_chiang $
 */

defined('PDM_URL') or exit('Access Denied');
 
class IndexAction extends PDMAction{
	function __construct(){
		parent::__construct();
		
	}
	function index(){
		$this->display();
	}
}