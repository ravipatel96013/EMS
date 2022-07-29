<?php
class Admin_LeaveBalancesheetController extends TinyPHP_Controller {
	
	public function indexAction()  
	{
        $user = new Models_User();
        $users = $user->getAll(['id','firstName','lastName'],'isActive=1');
        $this->setViewVar('data',$users);
	}

	public function addAction()
    {
        if($this->isPost())
        {
            $this->setNoRenderer(true);
            $status = 0;
            $errors = [];
	
        $userId = $this->getRequest()->getPostVar('userId');
        $amount = $this->getRequest()->getPostVar('amount');
        $type = $this->getRequest()->getPostVar('type');
        $description = $this->getRequest()->getPostVar('description');
        $actionTakenBy = TinyPHP_Session::get('adminName');
        $service = new Service_LeaveBalancesheet();
        if($type == 'credit')
        {
            $isCreated = $service->doCredit($userId,$amount,$description,$actionTakenBy);
        }
        elseif($type == 'debit'){
            $isCreated = $service->doDebit($userId,$amount,$description,$actionTakenBy);
        }    


        if( $isCreated == true )
        {
            $status = 1;
        }
        else
        {
            $errors = $service->getErrors();
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

       