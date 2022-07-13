<?php
class App_LeavesController extends TinyPHP_Controller {
	
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

        $leaves = new Models_Leave();
	
        $leaves->type = $this->getRequest()->getPostVar('type');
        $startDate = $this->getRequest()->getPostVar('startDate');
        $endDate = $this->getRequest()->getPostVar('endDate');
        $stdt = new DateTime($startDate);
        $endt = new DateTime($endDate);
        $leaves->startDate =  $stdt->getTimestamp();
        $leaves->endDate = $endt->getTimestamp();
        $leaves->isHalf = $this->getRequest()->getPostVar('isHalf');
        $leaves->comment = $this->getRequest()->getPostVar('comment');
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

    public function leavelistAction()
    {
        $this->setNoRenderer(true);

        global $db;
        $dt = new TinyPHP_DataTable();
	    $dt->setDBAdapter($db);
        $dt->setTable('leaves AS l');
        $dt->setIdColumn('l.id');

        $dt->addColumns(array(
            'id' => 'l.id',
            'type' => 'l.type',
            'startDate' => 'DATE_FORMAT(FROM_UNIXTIME(l.startDate), "%m-%d-%Y")',
            'endDate' => 'DATE_FORMAT(FROM_UNIXTIME(l.endDate), "%m-%d-%Y")',
            'comment' => 'l.comment',
            'status' => 'l.status'
        ));

        $getLoggedInUserId = getLoggedInUserId();
     
        $defaultFilters = array(
            "l.userId" => $getLoggedInUserId
          );
        $dt->setDefaultFilters($defaultFilters);
        $dt->getData();
    }
}