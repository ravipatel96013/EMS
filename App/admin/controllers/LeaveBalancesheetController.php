<?php
class Admin_LeaveBalancesheetController extends TinyPHP_Controller {
	
	public function indexAction()  
	{
        $user = new Models_User();
        $users = $user->getAll(['id','firstName','lastName']);
        // $this->setViewVar('data',$users);
	}

	public function addAction()
    {
        if($this->isPost())
        {
            $this->setNoRenderer(true);
            $status = 0;
            $errors = [];


        $leaves = new Models_LeaveBalancesheet();
	
        $leaves->getPostData();
        $leaves->actionTakenBy = TinyPHP_Session::get('adminName');
        $isCreated = $leaves->create();

        if( $isCreated == true )
        {
            $status = 1;
        }
        else
        {
            $errors = $leaves->getErrors();
        }
            
            $response = ["status" => $status, "errors" => $errors];
            echo json_encode($response);
            die; 

    	}
    }

    public function balancesheetlistAction()
    {
        $this->setNoRenderer(true);

        global $db;
        $dt = new TinyPHP_DataTable();
	    $dt->setDBAdapter($db);
        $dt->setTable('user_leave_balancesheet AS l');
        $dt->setIdColumn('l.id');

        $dt->setJoins("LEFT JOIN users AS b ON b.id = l.userId");

        $dt->addColumns(array(
            'id' => 'l.id',
            'userName' => 'b.firstName',
            'amount' => 'l.amount',
            'type' => 'l.type',
            'description' => 'l.description',
            'actionby' => 'l.actionTakenBy',
            'date' => 'DATE_FORMAT(FROM_UNIXTIME(l.createdOn), "%m-%d-%Y %h:%i")'
        ));
        $dt->getData();
    }
}
?>

       