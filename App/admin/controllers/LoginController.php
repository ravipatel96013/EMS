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
                if($user->role != 'admin')
                {
                    $user->addError('You are not Admin');    
                }
                else{
                $status = 1;
                TinyPHP_Session::set('adminName',$user->firstName);
                TinyPHP_Session::set('adminId',$user->id);
                }
            }
            else
            {
                $user->addError('Invalid Credentials');
            }
        }
        else
        {
            if(empty($user->email) || ($user->password))
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