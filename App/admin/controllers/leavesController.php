<?php
class Admin_LeavesController extends TinyPHP_Controller {
	
	public function indexAction()  
	{
	
	}

    public function updateAction()
    {
        if($this->isPost())
        {   
            // POST Request

            $this->setNoRenderer(true);

            $status = 0;
            $errors = [];

            $leaveId =  $this->getRequest()->getPostVar('id');
            $leave = new Models_Leave($leaveId);
            
            $leave->status = $this->getRequest()->getPostVar('status');
            $leave->oldStatus = $this->getRequest()->getPostVar('oldStatus');
        
                $isUpdated = $leave->update(['status','actionBy']);
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
        else
        {
            // GET Request
            $id = $this->getRequest()->getVar('id');
            $leave = new Models_Leave($id);
            if(!$leave->isEmpty)
            {
            $this->setViewVar('dataRow',$leave);
            }
            else{
                header("Location: /admin/leaves");
            }
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

        $dt->setJoins("INNER JOIN users AS b ON b.id = l.userId");

        $defaultFilters = array(
            "combination_filter" => "(l.status=".APPROVED." OR l.status=".DECLINED." OR l.status=".PENDING.")");
        
        $dt->setDefaultFilters($defaultFilters);

        $dt->addColumns(array(
            'id' => 'l.id',
            'userName' => 'b.firstName',
            'startDate' => 'l.startDate',
            'endDate' => 'l.endDate',
            'comment' => 'l.comment',
            'status' => 'l.status'
        ));
        $dt->getData();
    }


}