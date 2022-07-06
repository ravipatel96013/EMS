<?php
class App_IndexController extends TinyPHP_Controller {
	
	public function indexAction()  
	{
		$userName = TinyPHP_Session::get("userName");
		$this->setViewVar('userName',$userName);
	}
}
?>