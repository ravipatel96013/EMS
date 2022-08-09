<?php
class Admin_IndexController extends TinyPHP_Controller {
	
	public function indexAction()  
	{

	$checkInButton = false;
	$checkOutButton = false;
	$startBreakButton = false;
	$endBreakButton = false;
	$completed = false;
	$attendanceNotFound = false;
	$pauseBreakWarning = false;
	$currentYear = date('Y');

		$selectedYear = $this->getRequest()->getVar('year', 'numeric', date("Y"));
		$selectedMonth = $this->getRequest()->getVar('month', 'numeric', date("m"));
		$monthOption = [1,2,3,4,5,6,7,8,9,10,11,12];
		$yearOption = [];

		for($i=2022;$i<=$currentYear+1;$i++)
		{
			array_push($yearOption,$i);
		}

		foreach($yearOption as $year)
		{
			if($year = $selectedYear)
			{
				$this->setViewVar('selectedYear',$selectedYear);
			}
		}

		foreach($monthOption as $month)
		{
			if($month = $selectedMonth)
			{
				$this->setViewVar('selectedMonth',$selectedMonth);
			}
		}
	
	$presentDay = 0;
	$loggedInAdminId = getLoggedInAdminId();
	$date = date('Y-m-d');

	$holidays = new Models_Holiday();
	$upComingHolidays = $holidays->getUpComingHolidays();

	$leave = new Models_LeaveBalancesheet();
	$leaveBalance = $leave->getLeaveBalance($loggedInAdminId);

	$break = new Models_BreakLog();
	$totalBreakTime = $break->getTotalBreakTime($loggedInAdminId);
	if($totalBreakTime != false)
	{
	$totalBreakMinutes = $totalBreakTime['SUM(b.totalMinutes)'];
	if(!$totalBreakMinutes == NULL)
	{
		if($totalBreakMinutes > 60)
		{
			$breakHours = (int)($totalBreakMinutes/60);
			$breakMinutes = round($totalBreakMinutes - 60*$breakHours,0);
		}
		else{
			$breakHours = 0;
			$breakMinutes = round($totalBreakMinutes,0);
		}
	}
	else
	{
		$breakHours = 0;
		$breakMinutes = 0;
	}
	$this->setViewVar('breakHours',$breakHours);
	$this->setViewVar('breakMinutes',$breakMinutes);
	}
	else{
		$attendanceNotFound = true;
	}
	$attendance = new Models_Attendance();
	$presentDay = $attendance->getPresentMonthAttendance($loggedInAdminId);
	$activeAttendance = $attendance->getActiveAttendance($loggedInAdminId);
	$break = new Models_BreakLog();
	$activeBreak = $break->getActiveBreak($loggedInAdminId);

	if(!empty($activeAttendance))
	{
		if(empty($activeBreak))
		{
			$startBreakButton = true;
			$checkOutButton = true;
		}
		else{
			$endBreakButton = true;
			$pauseBreakWarning = true;
		}
	}
	 else
	 {
		$attendance = new Models_Attendance();
		$attendance->fetchByProperty(array('userId','date'),array($loggedInAdminId,$date));

		if ( !$attendance->isEmpty && $attendance->checkInDateTime == NULL && $attendance->checkOutDateTime == NULL) 
		{
			$checkInButton = true;
		}
		else{
			$completed = true;
		}
	}

	global $db;
		$sql="SELECT COUNT(id) as workingDays
		FROM user_attendance
		WHERE userId=$loggedInAdminId AND status != 'HO' AND status != 'WO' AND MONTH(date)=$selectedMonth AND YEAR(date)=$selectedYear";

		$workingDays = $db->fetchRow($sql);
		$workingDays = $workingDays['workingDays'];
		$this->setViewVar('workingDays',$workingDays);

		$sql2 = "SELECT COUNT(status) as presentDays
		FROM `user_attendance`
		WHERE MONTH(date)=$selectedMonth AND YEAR(date)=$selectedYear AND status='P' AND userId=$loggedInAdminId";

		$presentDays = $db->fetchRow($sql2);
		$presentDays = $presentDays['presentDays'];
		$this->setViewVar('presentDays',$presentDays);

	$this->setViewVar('checkInButton',$checkInButton);
	$this->setViewVar('checkOutButton',$checkOutButton);
	$this->setViewVar('startBreakButton',$startBreakButton);
	$this->setViewVar('endBreakButton',$endBreakButton);
	$this->setViewVar('completed',$completed);
	$this->setViewVar('attendanceNotFound',$attendanceNotFound);
	$this->setViewVar('presentDays',$presentDay['COUNT(status)']);
	$this->setViewVar('leaveBalance',$leaveBalance['balance']);
	$this->setViewVar('upComingHolidays',$upComingHolidays);
	$this->setViewVar('pauseBreakWarning',$pauseBreakWarning);
	$this->setViewVar('monthOption',$monthOption);
	$this->setViewVar('yearOption',$yearOption);
	}

	public function checkinAction()
    {

		$this->setNoRenderer(true);
		$status = 0;
		$errors = [];
		$loggedInAdminId = getLoggedInAdminId();
		$date = date('Y-m-d');
		
		$attendance = new Models_Attendance();
		$activeAttendance = $attendance->getActiveAttendance($loggedInAdminId);
		
		if(!empty($activeAttendance))
		{
			array_push($errors,'you are already checkedin in system');
		}
		else
		{
			$attendance = new Models_Attendance();
			$attendance->fetchByProperty(array('userId','date'),array($loggedInAdminId,$date));
			
			if ( !$attendance->isEmpty && is_null($attendance->checkInDateTime) && is_null($attendance->checkOutDateTime) ) 
			{
				$checkInDateTime = date('Y-m-d H:i:s');
				$service = new Service_Attendance();
				$isCheckedIn = $service->checkin($attendance->id,$checkInDateTime);
				if($isCheckedIn)
				{
					$status = 1;
				}
				else{
					$errors = $service->getErrors;
				}	
			} 
			else 
			{
				array_push($errors,'Can not check-in');
			}
		}
		$response = ["status" => $status, "errors" => $errors];
		echo json_encode($response);
		die; 
    }

	public function checkoutAction()
    {
		$this->setNoRenderer(true);
		$status = 0;
        $errors = [];
		$loggedInAdminId = getLoggedInAdminId();
		

		$attendance = new Models_Attendance();
		$activeAttendance = $attendance->getActiveAttendance($loggedInAdminId);
		if(empty($attendance))
		{
			array_push($errors,'CheckIn First');
		}
		else
		{
			$service = new Service_Attendance();
			$checkOutDateTime = date('Y-m-d H:i:s');
			$isCheckedOut = $service->checkout($activeAttendance['id'],$checkOutDateTime);
			if($isCheckedOut)
			{
				$status = 1;
			}
			else{
				$errors = $service->getErrors;
			}
		}
		$response = ["status" => $status, "errors" => $errors];
		echo json_encode($response);
		die; 
	}


	public function startbreakAction()
    {
		$this->setNoRenderer(true);
		$status = 0;
        $errors = [];
		$loggedInAdminId = getLoggedInAdminId();

		$attendance = new Models_Attendance();
		$activeAttendance = $attendance->getActiveAttendance($loggedInAdminId);
		
		if(empty($activeAttendance))
		{
			array_push($errors,'You have not checkedIn yet');
		}
		else
		{	
			$break =new Models_BreakLog();
			$activeBreak = $break->getActiveBreak($loggedInAdminId);
			if(empty($activeBreak))
			{
				$break = new Models_BreakLog();
				$break->attendanceId = $activeAttendance['id'];
				$break->startTime = date('Y-m-d H:i:s');
				$isCreated = $break->create();
				if($isCreated)
				{
					$status = 1;
				}
				else
				{
					$errors = $break->getErrors();
				}
			}
			else
			{
				array_push($errors,"You have Active Break");
			}	
					
		}

		$response = ["status" => $status, "errors" => $errors];
		echo json_encode($response);
		die; 
	}


	public function endbreakAction()
    {
		$this->setNoRenderer(true);
		$status = 0;
        $errors = [];
		$loggedInAdminId = getLoggedInAdminId();

		$attendance = new Models_Attendance();
		$activeAttendance = $attendance->getActiveAttendance($loggedInAdminId);

		if(empty($activeAttendance))
		{
			array_push($errors,'You have not checkedIn yet');
		}
		else
		{	
			$break =new Models_BreakLog();
			$activeBreak = $break->getActiveBreak($loggedInAdminId);
			if(!empty($activeBreak))
			{
				$break = new Models_BreakLog($activeBreak['id']);
				$break->endTime = date('Y-m-d H:i:s');
				$to_time = strtotime($break->endTime);
				$from_time = strtotime($activeBreak['startTime']);
				
				$totalMinutes = round(abs($from_time - $to_time) / 60,2);
				$break->totalMinutes = $totalMinutes;
				
				$isUpdated = $break->update(array('endTime','totalMinutes'));
				if($isUpdated)
				{
					$status = 1;
				}
				else
				{
					$errors = $break->getErrors();
				}
			}
			else
			{
				array_push($errors,"You do not have Active Break");
			}						
		}
		$response = ["status" => $status, "errors" => $errors];
		echo json_encode($response);
		die; 
    }

	public function getattendanceAction()
	{
		$this->setNoRenderer(true);
		
		$month = $this->getRequest()->getVar("month");
		$year = $this->getRequest()->getVar("year");
		
		global $db;
		
		$loggedInUserId = getLoggedInAdminId();
        
		$dt = new TinyPHP_DataTable();
	    $dt->setDBAdapter($db);
        $dt->setTable('user_attendance AS a');
        $dt->setIdColumn('a.id');
		
		$dt->setJoins('LEFT JOIN break_logs AS b ON a.id=b.attendanceId');

        $dt->addColumns(array(
            'date' => 'DATE_FORMAT(a.date,"%d-%m-%Y")',
            'checkInDateTime' => 'DATE_FORMAT(a.checkInDateTime, "%r")',
            'checkOutDateTime' => 'DATE_FORMAT(a.checkOutDateTime, "%r")',
            'totalMinutes' => 'CONCAT(FLOOR(SUM(b.totalMinutes)/60),":",MOD(SUM(b.totalMinutes),60))',
            'status' => 'a.status'
        ));

		$defaultFilters = array(
			"a.userId" => $loggedInUserId,
			"MONTH(a.date)" => $month,
			"YEAR(a.date)" => $year,
			);

		$dt->setGroupBy('GROUP BY a.id');
		$dt->setDefaultFilters($defaultFilters);

		$dt->getData();
	}
}
?>