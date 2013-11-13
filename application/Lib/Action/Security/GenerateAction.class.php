<?php
class GenerateAction extends SBAction {
	
	private $record;
	
	function __construct() {
		parent::__construct ();
		$this->record = M ( 'record' );
	}
	
	function index() {
		$this->display ();
	}

	//自定义生成
	function create_mine() {
		//重新生成
		if($_GET['refer']=='rebuild'){
			$get=$this->_get();
			$len_account = $get ['la'];
			$len_pwd = $get ['lp'];
			$enable_char = $get ['ec'];
		}else{
			$post = $this->_post ();
			$len_account = $post ['len_account'];
			$len_pwd = $post ['len_pwd'];
			$enable_char = $post ['enable_char'];
		}
		
		$chars = '';
		if ($enable_char == 1) {
			$chars = '_+=@$()[]0Oo1Ll';
		}
		$username = randCode ( $len_account, 4, $chars );
		$password = randCode ( $len_pwd, 4, $chars );
		$record_id=$this->_save_history($username, $password);
		
		$this->assign('rcid',$record_id);
		$this->assign ( 'username', $username );
		$this->assign ( 'pwd', $password );
		$this->assign('refer',UX('Generate/create_mine').'?refer=rebuild&la='.$len_account.'&ec='.$enable_char.'&lp='.$len_pwd);
		$this->display ('create');
	}
	
	//显示历史生成的记录[虽然没有用但是可以参考借鉴的]
	function history() {
		if (session ( 'user' ) == null) {
			$this->redirect ( 'account/login' );
		}
		$map ['userid'] = $this->userid;
		$page = $this->pageInit ( $this->record->where ( $map )->count () );
		$ls = $this->record->field ( true )->order ( 'gendate DESC' )->where ( $map )->limit ( $page->firstRow, $page->listRows )->select ();
		$this->assign ( 'list', $ls );
		$this->display ();
	}

	//保存历史生成的密码帐号
	private function _save_history($username,$password){
		$record_id=shortGen(time().$this->userid.mt_rand(1000, 9999));
		$data['id']=$record_id;
		$data ['userid'] = $this->userid;
		$data ['safeuname'] = encode($username);
		$data ['password'] = encode($password);
		$data ['gendate'] = time();
		$data ['genip'] = get_client_ip ();
		$this->record->add ( $data );
		return $record_id;
	}
}