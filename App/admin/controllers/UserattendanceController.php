<?php
class Admin_UserattendanceController extends TinyPHP_Controller {
    public function indexAction()
    {
        $currentYear = date('Y');
        $selectedYear = $this->getRequest()->getVar('year','numeric', date("Y"));
        $selectedMonth = $this->getRequest()->getVar('month','numeric', date("m"));
        $selectedUser = $this->getRequest()->getVar('user','string',getLoggedInAdminId());
        $user = new Models_User($selectedUser);
        $userName = $user->firstName.' '.$user->lastName;
        $this->setViewVar('userName',$userName);
		$monthOption = [1,2,3,4,5,6,7,8,9,10,11,12];
		$yearOption = [];

        for($i=2022;$i<=$currentYear+1;$i++)
		{
			array_push($yearOption,$i);
		}

		foreach($yearOption as $year)
		{
			if($year == $selectedYear)
			{
				$this->setViewVar('selectedYear',$selectedYear);
			}
		}

		foreach($monthOption as $month)
		{
			if($month == $selectedMonth)
			{
				$this->setViewVar('selectedMonth',$selectedMonth);
			}
		}

        $leave = new Models_LeaveBalancesheet();
		$leaveBalance = $leave->getLeaveBalance($selectedUser);
        $leaveBalance = $leaveBalance['balance'];
        $this->setViewVar('leaveBalance',$leaveBalance);

        $user = new Models_User();
        $users = $user->getAll(array('id','firstName','lastName'));

        global $db;
		$sql="SELECT COUNT(id) as workingDays
		FROM user_attendance
		WHERE userId=$selectedUser AND status != 'HO' AND status != 'WO' AND MONTH(date)=$selectedMonth AND YEAR(date)=$selectedYear";

		$workingDays = $db->fetchRow($sql);
		$workingDays = $workingDays['workingDays'];
		$this->setViewVar('workingDays',$workingDays);

		$sql2 = "SELECT COUNT(status) as presentDays
		FROM `user_attendance`
		WHERE MONTH(date)=$selectedMonth AND YEAR(date)=$selectedYear AND status='P' AND userId=$selectedUser";
        $presentDays = $db->fetchRow($sql2);
        $presentDays = $presentDays['presentDays'];
        $this->setViewVar('presentDays',$presentDays);

        $sql3 = "SELECT status FROM `user_attendance` WHERE userId=$selectedUser";
        $attendanceStatus = $db->fetchAll($sql3);
        $PL = 0;
        $UL = 0;
        $WO = 0;
        $HUL = 0;
        $HPL = 0;
        $HO = 0;
        foreach($attendanceStatus as $data)
        {
            if($data['status'] == 'PL')
            {
                $PL++;
            } 
            elseif($data['status'] == 'UL')
            {
                $UL++;
            }
            elseif($data['status'] == 'WO')
            {
                $WO++;
            }
            elseif($data['status'] == 'HUL')
            {
                $HUL++;
            }
            elseif($data['status'] == 'HPL')
            {
                $HPL++;
            }
            elseif($data['status'] == 'HO')
            {
                $HO++;
            }
        }
        $this->setViewVar('PL',$PL);
        $this->setViewVar('UL',$UL);
        $this->setViewVar('WO',$WO);
        $this->setViewVar('HUL',$HUL);
        $this->setViewVar('HPL',$HPL);
        $this->setViewVar('HO',$HO);

        $this->setViewVar('selectedYear',$selectedYear);
        $this->setViewVar('selectedMonth',$selectedMonth);
        $this->setViewVar('selectedUser',$selectedUser);
        $this->setViewVar('currentYear',$currentYear);
        $this->setViewVar('monthOption',$monthOption);
        $this->setViewVar('yearOption',$yearOption);
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
            'date' => 'DATE_FORMAT(a.date,"%d-%m-%Y")',
            'checkInDateTime' => 'DATE_FORMAT(a.checkInDateTime, "%r")',
            'checkOutDateTime' => 'DATE_FORMAT(a.checkOutDateTime, "%r")',
            'totalMinutes' => 'CONCAT(FLOOR(SUM(b.totalMinutes)/60),":",MOD(SUM(b.totalMinutes),60))',
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
        if(!$attendance->isEmpty)
        {
		$data = $attendance->getInfo();
		echo json_encode($data);
		die;
        }
	}

    public function updateAction()
    {  
            $this->setNoRenderer(true);

            $status = 0;
            $errors = [];
            

            $id = $this->getRequest()->getPostVar('id');
			$checkInDateTime = $this->getRequest()->getPostVar('checkIn');
			$checkOutDateTime = $this->getRequest()->getPostVar('checkOut');
			$attendanceStatus = $this->getRequest()->getPostVar('status');

            function checkFormat($datetime)
            {
                if(preg_match("/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/", $datetime))
                {
                    return true;
                }
                else{
                    return false;
                }
            }

            if(checkFormat($checkInDateTime) && checkFormat($checkOutDateTime))
            {
            $attendance = new Models_Attendance($id);

            if($attendance->isEmpty)
            {
                $errors[] = "Attendance Does not Exist";
            }
            else
            {
                $attendance->checkInDateTime = $checkInDateTime;
				$attendance->checkOutDateTime = $checkOutDateTime;
				$attendance->status = $attendanceStatus;
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

            }
            else{
                array_push($errors,"Check-in or Check-out time is not valid");
            }
            
            $response = ["status" => $status, "errors" => $errors];
            echo json_encode($response);
            die;   
    }

}