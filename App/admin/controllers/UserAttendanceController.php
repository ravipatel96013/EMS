<?php
class Admin_UserAttendanceController extends TinyPHP_Controller {
    public function indexAction()
    {
        $currentYear = date('Y');
        $selectedYear = $this->getRequest()->getVar('year','numeric', date("Y"));
        $selectedMonth = $this->getRequest()->getVar('month','numeric', date("m"));
        $selectedUser = $this->getRequest()->getVar('user');

        $user = new Models_User();
        $users = $user->getAll(array('id','firstName','lastName'));

        $this->setViewVar('selectedYear',$selectedYear);
        $this->setViewVar('selectedMonth',$selectedMonth);
        $this->setViewVar('selectedUser',$selectedUser);
        $this->setViewVar('currentYear',$currentYear);
        $this->setViewVar('users',$users);
    }

    public function getattendanceAction()
	{
		$this->setNoRenderer(true);
		$month = $this->getRequest()->getVar("month");
		$year = $this->getRequest()->getVar("year");
		$userId = $this->getRequest()->getVar("user");
		global $db;

        $dt = new TinyPHP_DataTable();
	    $dt->setDBAdapter($db);
        $dt->setTable('user_attendance AS a');
        $dt->setIdColumn('a.id');
		
		$dt->setJoins('LEFT JOIN break_logs AS b ON a.id=b.attendanceId');

        $dt->addColumns(array(
			'id' => 'a.id',
            'date' => 'a.date',
            'checkInDateTime' => 'DATE_FORMAT(a.checkInDateTime, "%r")',
            'checkOutDateTime' => 'DATE_FORMAT(a.checkOutDateTime, "%r")',
            'totalMinutes' => 'SUM(b.totalMinutes)',
            'status' => 'a.status'
        ));

		$defaultFilters = array(
			"a.userId" => $userId,
			"MONTH(a.date)" => $month,
			"YEAR(a.date)" => $year,
			);

		$dt->setGroupBy('GROUP BY a.id');
		$dt->setDefaultFilters($defaultFilters);
		$dt->getData();
	}

    public function getinfoAction()
	{
		$this->setNoRenderer(true);
		$id = $this->getRequest()->getPostVar('id');
		$attendance = new Models_Attendance($id);
		$data = $attendance->getInfo();
		echo json_encode($data);
		die;
	}

    public function updateAction()
    {  
            $this->setNoRenderer(true);

            $status = 0;
            $errors = [];
            

            $id = $this->getRequest()->getPostVar('id');
			$checkInDateTime = $this->getRequest()->getPostVar('checkIn');
			$checkOutDateTime = $this->getRequest()->getPostVar('checkOut');
			$status = $this->getRequest()->getPostVar('status');

            $attendance = new Models_Attendance($id);

            if($attendance->isEmpty)
            {
                $errors[] = "Attendance Does not Exist";
            }
            else
            {
                $attendance->checkInDateTime = $checkInDateTime;
				$attendance->checkOutDateTime = $checkOutDateTime;
				$attendance->status = $status;
                $isUpdated = $attendance->update(array('checkInDateTime','checkOutDateTime','status'));

                if( $isUpdated == true )
                {
                    $status = 1;
                }
                else
                {
                    $errors = $attendance->getErrors();
                }
            }
            
            $response = ["status" => $status, "errors" => $errors];
            echo json_encode($response);
            die;   
    }

}