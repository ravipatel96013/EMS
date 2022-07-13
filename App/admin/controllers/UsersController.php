<?php
class Admin_UsersController extends TinyPHP_Controller {
	
	public function indexAction()
    {

    }


    public function deleteAction()
    {
        if($this->isPost())
        {
            $status = 0;
            $errors = [];

            $this->setNoRenderer(true);

            $id = $this->getRequest()->getPostVar('id');
            $user = new Models_User($id);

            if($user->isEmpty)
            {
                $errors[] = "Invalid user, it does not exist";
            }
            else
            {
                $isDeleted = $user->delete();

                if( $isDeleted == true )
                {
                    $status = 1;
                }
                else
                {
                    $errors = $user->getErrors();
                }
            }
            
            $response = ["status" => $status, "errors" => $errors];
            echo json_encode($response);
            die; 
        }      
    }

    public function addAction()
    {
        if($this->isPost())
        {       
            // Post Method
                $status = 0;
                $errors = [];

                $this->setNoRenderer(true);

                $user = new Models_User();
                $user->getPostData();
                $user->confirmPassword = $this->getRequest()->getPostVar('confirmPassword');
                $isCreated = $user->create();

                if( $isCreated == true )
                {
                    $status = 1;
                }
                else
                {
                    $errors = $user->getErrors();
                }
             $response = ["status" => $status, "errors" => $errors];
             echo json_encode($response);
             die;
        }
        else
        {
            // GET Request
        }
    }

    public function updateAction()
    {
        if($this->isPost())
        {   
            // POST Request

            $this->setNoRenderer(true);

            $status = 0;
            $errors = [];
            

            $userId = $this->getRequest()->getPostVar('id');
            $user = new Models_User($userId);

            if($user->isEmpty)
            {
                $errors[] = "Invalid user, it does not exist";
            }
            else
            {
                $user->getPostData();
                $user->confirmPassword = $this->getRequest()->getPostVar('confirmPassword');
                $isUpdated = $user->update();

                if( $isUpdated == true )
                {
                    $status = 1;
                }
                else
                {
                    $errors = $user->getErrors();
                }
            }
            
            $response = ["status" => $status, "errors" => $errors];
            echo json_encode($response);
            die;
        }
        else
        {
            // GET Request

            $id = $this->getRequest()->getVar('id');

            $user = new Models_User();        
            $userData = $user->fetchUser($id);
            $this->setViewVar('userRow',$userData);
        }
           
    }

    public function userlistAction()
    {
        $this->setNoRenderer(true);

        global $db;
        $dt = new TinyPHP_DataTable();
	    $dt->setDBAdapter($db);
        $dt->setTable('users AS u');
        $dt->setIdColumn('u.id');

        $dt->addColumns(array(
            'id' => 'u.id',
            'firstName' => 'u.firstName',
            'joinDate' => 'u.joinDate',
            'email' => 'u.email',
            'phone' => 'u.phone',
            'role' => 'u.role',
            'designation' => 'u.designation',
            'address' => 'u.address',
        ));
        $dt->getData();
    }

}
?>