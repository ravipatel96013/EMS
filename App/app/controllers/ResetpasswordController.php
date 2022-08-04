<?php
class App_ResetpasswordController extends TinyPHP_Controller {
	
	public function indexAction()  
	{
        $this->disableHeader();
    }

    public function sendlinkAction()
    {
        $this->setNoRenderer(true);

        $status = 0;
        $errors = [];

        $email = $this->getRequest()->getPostVar('email');
        $user = new Models_User();
        $user->fetchByProperty('email',$email);
        if($user->isEmpty || $user->isActive == 0 || $user->role == 'admin')
        {
            array_push($errors,'Email Does Not Exist');
        }
        else
        {   
            $byt = random_bytes(10);

            $resetPassword = new Models_Resetpassword();
            $resetPassword->userId = $user->id;
            $resetPassword->hashKey = bin2hex($byt);
            $resetPassword->isActive = 1;
            $isCreated = $resetPassword->create();
            if($isCreated)
            {
                $status = 1;
            }
            else
            {
                $errors = $resetPassword->getErrors();
            }
        }
        $response = ["status" => $status, "errors" => $errors];
        echo json_encode($response);
        die;
    }

    public function createpasswordAction()
    {
        $status = 0;
        $errors = [];

        $this->disableHeader();
        if($this->isPost())
        {
            $key = $this->getRequest()->getPostVar('key');
            $password =  $this->getRequest()->getPostVar('password');
            $confirmPassword = $this->getRequest()->getPostVar('confirmPassword');

            $resetPassword = new Models_Resetpassword();
            $resetPassword->fetchByProperty('hashKey',$key);
            if(!$resetPassword->isEmpty)
            {
                $user = new Models_User($resetPassword->userId);
                if(!$user->isEmpty)
                {
                    $user->password = $password;
                    $user->confirmPassword = $confirmPassword;
                    $isUpdated = $user->update();
                    if($isUpdated)
                    {
                        $status = 1;
                        $resetPassword->isActive = 0;
                        $resetPassword->update(['isActive']);
                    }
                    else{
                        $errors = $user->getErrors();
                    }
                }
            }   

            $response = ["status" => $status, "errors" => $errors];
            echo json_encode($response);
            die;
        }
        else{
            // GET METHOD
             $hashKey = $this->getRequest()->getVar('hashKey');
            $resetPassword = new Models_Resetpassword();
            $resetPassword->fetchByProperty('hashKey',$hashKey);
            if($resetPassword->isEmpty)
            {
                header("Location:/app/login");
            }
            else
            {   
                $isValid = time() - $resetPassword->createdOn;
                if($isValid > 3600 || $resetPassword->isActive == 0)
                {
                    $this->setNoRenderer(true);
                    echo 'Link is Expired';
                }
                else{
                    $this->setViewVar('key',$hashKey);
                }
            }
        }
    }
}