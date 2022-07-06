<?php
class Admin_LoginController extends TinyPHP_Controller {
	
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
            $errorCode = 0;
            $user = new Models_User();
            $response = $user->checkCred($email,$pass);
            if(!$response == '')
            {  
                if($response["role"] == 'admin')
                {      
                TinyPHP_Session::set('adminName',$response["firstName"]);
                TinyPHP_Session::set('adminId',$response["id"]);
                $errorCode = 1; // ready to login.
                }
                else
                {
                    $errorCode = 2; // Not admin error.
                }
            }else{
                $errorCode = 3; // Invalid Credentials error.
            }
            echo json_encode($errorCode);
    
}
}
?>