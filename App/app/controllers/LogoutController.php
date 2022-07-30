<?php
class App_LogoutController extends TinyPHP_Controller {
	
	public function indexAction()
    {
        TinyPHP_Session::destroy('userId');
        TinyPHP_Session::destroy('userName');
        header('Location:/app/login/');
        exit();
    }
	}
?>