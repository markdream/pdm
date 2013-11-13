<?php
class IndexAction extends Action{
	function index(){
		$this->ajaxReturn(array('code'=>-1,'ret'=>'Access error!'));
	}
}