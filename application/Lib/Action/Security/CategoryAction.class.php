<?php
class CategoryAction extends SBAction {
	private $type;
	
	function __construct() {
		parent::__construct ();
		$this->type = M ( 'type' );
	}
	
	function index() {
		$map ['userid'] = $this->userid;
		
		$page = $this->pageInit ( $this->type->where ( $map )->count (), '个分类' );
		
		$list = $this->type->field ( true )->where ( $map )->limit ( $page->firstRow, $page->listRows )->order ( 'rank' )->select ();
		$this->assign ( 'list', $list );
		$this->display ();
	}
	
	function create() {
		if ($this->type->create ()) {
			$this->type->userid = $this->userid;
			
			if ($this->type->add () > 0) {
				$this->success ( '成功添加一个分类',UX('index') );
			} else {
				$this->error ( '分类添加失败' );
			}
		} else {
			$this->error ( '内部错误' );
		}
	}
	
	function edit() {
		$id = $this->_get ( 'id' );
		$data = $this->check_owner ( $id, true );
		$this->assign ( 'type', $data );
		$this->display ();
	}
	
	function update(){
		$id=$this->_post('type_id');
		$this->check_owner($id);
		
		if($this->type->create()){
			if($this->type->save()>0){
				$this->success('分类修改成功！');
			}else{
				$this->error('分类修改失败！');
			}
		}else{
			$this->error('内部错误');
		}
	}
	
	
	function delete(){
		$id = $this->_get ( 'id' );
		$map['type_id']=$id;
		$map['userid']=$this->userid;
		if($this->type->where($map)->delete()>0){
			$this->success('分类删除成功！');
		}else{
			$this->success('分类不存在！');
		}
	}
	
	private function check_owner($typeId, $ret = false) {
		$map ['userid'] = $this->userid;
		$map ['type_id'] = $typeId;
		$data = $this->type->field ( true )->where ( $map )->find ();
		if ($data == null) {
			$this->error ( '你没有这个分类' );
		}
		
		if ($ret == true) {
			return $data;
		}
	}
}