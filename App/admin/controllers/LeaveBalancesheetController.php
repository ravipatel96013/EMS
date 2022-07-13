<?php
class Admin_LeaveBalancesheetController extends TinyPHP_Controller {
	
	public function indexAction()  
	{

	}

	public function addAction()
    {
        if($this->isPost())
        {
            $status = 0;
            $errors = [];

			$this->setNoRenderer(true);

        $leaves = new Models_LeaveBalancesheet();
	
        $leaves->getPostData();
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

        $dt->setJoins("INNER JOIN users AS b ON b.id = l.userId");

        $dt->addColumns(array(
            'id' => 'l.id',
            'userName' => 'b.firstName',
            'amount' => 'l.amount',
            'type' => 'l.type',
            'description' => 'l.description',
            'actionby' => 'l.actionTakenBy',
            'date' => 'DATE_FORMAT(FROM_UNIXTIME(l.createdOn), "%m-%d-%Y")'
        ));
        $dt->getData();
    }
}
?>

       