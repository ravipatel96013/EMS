<?php
class App_LeaveBalancesheetController extends TinyPHP_Controller {
	
	public function indexAction()  
	{

	}

	public function balancesheetlistAction()
    {
        $this->setNoRenderer(true);

        global $db;
        $dt = new TinyPHP_DataTable();
	    $dt->setDBAdapter($db);
        $dt->setTable('user_leave_balancesheet AS l');
        $dt->setIdColumn('l.id');

        $dt->addColumns(array(
            'id' => 'l.id',
            'amount' => 'l.amount',
            'type' => 'l.type',
            'description' => 'l.description',
            'actionby' => 'l.actionTakenBy',
            'date' => 'DATE_FORMAT(FROM_UNIXTIME(l.createdOn), "%m-%d-%Y %h:%i")'
        ));

		$loggedInUserId = getLoggedInUserId();
		$defaultFilters = array(
			"l.userId" => $loggedInUserId, 
		);	
		$dt->setDefaultFilters($defaultFilters);
        $dt->getData();
		
    }
}