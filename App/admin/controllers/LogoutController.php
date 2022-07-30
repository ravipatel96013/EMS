<?php
class Admin_LogoutController extends TinyPHP_Controller {
	
	public function indexAction()
    {
        TinyPHP_Session::destroy('adminId');
        TinyPHP_Session::destroy('adminName');
        header('Location:/admin/login/');
        exit();
    }
    
}
?>