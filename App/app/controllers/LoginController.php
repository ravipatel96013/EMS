<?php
class App_LoginController extends TinyPHP_Controller {
	
	public function indexAction()  
	{
        $this->disableHeader();
        $this->disableFooter();
	}
   
    public function dologinAction()
    {
        $this->setNoRenderer(true);
            $email = $this->getRequest()->getPostVar('email');
            $pass = md5($this->getRequest()->getPostVar('password'));
            $erroCode = 0;    
            $user = new Models_User();
            $response = $user->checkCred($email,$pass);
            if(!$response == '')
            {  
                if($response["role"] == 'user')
                {      
                TinyPHP_Session::set('userName',$response["firstName"]);
                TinyPHP_Session::set('userId',$response["id"]);
                $errorCode = 1; // ready to login.
                }
                else
                {
                    $errorCode = 2; // Not User error.
                }
            }else{
                $errorCode = 3; // Invalid Credentials error.
            }
            echo json_encode($errorCode);
}
}
?>