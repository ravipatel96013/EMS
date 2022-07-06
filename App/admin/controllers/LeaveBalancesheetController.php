<?php
class Admin_LeaveBalancesheetController extends TinyPHP_Controller {
	
	public function indexAction()  
	{
		$this->disableFooter();
		$leaves = new Models_LeaveBalancesheet();
		$data = $leaves->showData();
		$this->setViewVar('data',$data);   
	}

}
?>