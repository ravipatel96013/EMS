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

            $leaves->userId = getLoggedInUserId();

            $leaves->startDate = $this->getRequest()->getPostVar('startDate');

            $leaves->endDate = $this->getRequest()->getPostVar('endDate');
            
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

    public function closeleaveAction()
    {
        $status = 0;
        $errors = [];

        $leaveId = $this->getRequest()->getPostVar('id');
        $leave = new Models_Leave($leaveId);
        $leave->status = CLOSED;
        $isUpdated = $leave->update(['status']);
        if($isUpdated)
        {
           $status = 1;
        }
        else
        {
            $errors = $leave->getErrors();
        }
        $response = ["status" => $status, "errors" => $errors];
        echo json_encode($response);
        die;
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
            'startDate' => 'DATE_FORMAT(l.startDate,"%d-%m-%Y")',
            'endDate' => 'DATE_FORMAT(l.endDate,"%d-%m-%Y")',
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