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

        $status = 0;
        $errors = [];

        $email = $this->getRequest()->getPostVar('email');
        $pass = $this->getRequest()->getPostVar('password'); 
        
        $user = new Models_User();
        $user->fetchByProperty('email', $email);

        if( !$user->isEmpty )
        {
            if( $user->password == md5($pass) && $user->isActive == 1 )
            {
                if($user->role != 'user')
                {
                    $user->addError('You are not user');    
                }
                else{
                $status = 1;
                TinyPHP_Session::set('userName',$user->firstName);
                TinyPHP_Session::set('userId',$user->id);
                }
            }
            else
            {
                $user->addError('Invalid username or password');
            }
        }
        else
        {
            if($email == '' || $pass == '')
            {
                $user->addError('Fields are Empty');
            }
            else{
            $user->addError('Email does not exist');
            }
        }

        $response = ['status' => $status, 'errors' => $user->getErrors()];
        echo json_encode($response);
        die;
}
}
?>