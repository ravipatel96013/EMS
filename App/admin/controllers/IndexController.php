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
	$selectedDate = $this->getRequest()->getVar('date','string', date("Y-m-d"));
	
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

	// Dashboard Datatable Data

	global $db;
	$sql = "SELECT a.id as attendanceId,c.firstName as Name,a.date as Date,DATE_FORMAT(a.checkInDateTime, '%r') as checkIn,DATE_FORMAT(a.checkOutDateTime, '%r') as checkOut,a.status as status,DATE_FORMAT(b.startTime, '%r') as breakStartTime,DATE_FORMAT(b.endTime, '%r') as breakEndTime,SUM(b.totalMinutes) as breakTime
	FROM `user_attendance` as a
	LEFT JOIN break_logs as b ON a.id=b.attendanceId AND b.startTime IS NOT NULL AND b.endTime IS NULL
	LEFT JOIN users as c ON c.id=a.userId
	WHERE a.date='$selectedDate' AND c.isActive = 1
	GROUP BY a.id";

	$sql2 = "SELECT a.id as attendanceId,SUM(b.totalMinutes) as breakTime
	FROM `user_attendance` as a
	LEFT JOIN break_logs as b ON a.id=b.attendanceId
	WHERE date='$selectedDate' 
	GROUP BY a.id";

	$result = $db->fetchAll($sql);
	
	$breakes = $db->fetchAll($sql2);

	$breakByAttendanceId = [];
    foreach($breakes as $break)
    {
        $breakByAttendanceId[$break['attendanceId']] = $break;
    }

	foreach($result as $key => $data)
	{
		if(isset($breakByAttendanceId[$data['attendanceId']]))
		{
			$break = $breakByAttendanceId[$data['attendanceId']];
			if($break['breakTime'] != NULL)
			{
				$result[$key]['breakTime'] = $break['breakTime'];
			}
		}
	}


	$this->setViewVar('attendanceData',$result);
	$this->setViewVar('breakData',$breakes);

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
	$this->setViewVar('selectedDate',$selectedDate);
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

	// public function getattendanceAction()
	// {
	// 	$this->setNoRenderer(true);
	// 	$date = $this->getRequest()->getVar("date");
	// 	global $db;

    //     $dt = new TinyPHP_DataTable();
	//     $dt->setDBAdapter($db);
    //     $dt->setTable('user_attendance AS a');
    //     $dt->setIdColumn('a.id');
		
	// 	$dt->setJoins('LEFT JOIN break_logs AS b ON a.id=b.attendanceId AND b.startTime IS NOT NULL AND b.endTime IS NULL
	// 				   LEFT JOIN users AS c ON c.id=a.userId');

    //     $dt->addColumns(array(
	// 		'name' => 'c.firstName',
    //         'date' => 'a.date',
    //         'checkInDateTime' => 'DATE_FORMAT(a.checkInDateTime, "%r")',
    //         'checkOutDateTime' => 'DATE_FORMAT(a.checkOutDateTime, "%r")',
    //         'totalMinutes' => 'SUM(b.totalMinutes)',
    //         'status' => 'a.status',
	// 		'breakStart' => 'DATE_FORMAT(b.startTime, "%r")',
	// 		'breakEnd' => 'DATE_FORMAT(b.endTime, "%r")'
    //     ));

	// 	$defaultFilters = array(
	// 		'a.date' => "'$date'",
	// 		'c.isActive' => 1,
	// 		'c.role' => "'user'"
	// 		);

	// 	$dt->setGroupBy('GROUP BY a.id');
	// 	$dt->setDefaultFilters($defaultFilters);
	// 	$dt->getData();
	// }
}
?>