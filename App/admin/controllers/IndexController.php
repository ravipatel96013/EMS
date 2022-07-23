<?php
class Admin_IndexController extends TinyPHP_Controller {
	
	public function indexAction()  
	{

	$checkInButton = false;
	$checkOutButton = false;
	$startBreakButton = false;
	$endBreakButton = false;
	$completed = false;
	$currentYear = date('Y');
	$selectedYear = $this->getRequest()->getVar('year');
	$selectedMonth = $this->getRequest()->getVar('month');
	$selectedUser = $this->getRequest()->getVar('user');
	
	$presentDay = 0;
	$loggedInAdminId = getLoggedInAdminId();
	$date = date('Y-m-d');

	$user = new Models_User();
	$users = $user->getAll(array('id','firstName','lastName'));

	$holidays = new Models_Holiday();
	$upComingHolidays = $holidays->getUpComingHolidays();

	$break = new Models_BreakLog();
	$totalBreakTime = $break->getTotalBreakTime($loggedInAdminId);
	$totalBreakMinutes = $totalBreakTime['SUM(b.totalMinutes)'];
	if(!$totalBreakMinutes == NULL)
	{
		if($totalBreakMinutes > 60)
		{
			$breakHours = $totalBreakMinutes/60;
			$breakMinutes = $totalBreakMinutes - 60*$breakHours;
		}
		else{
			$breakHours = 0;
			$breakMinutes = $totalBreakMinutes;
		}
	}
	else
	{
		$breakHours = 0;
		$breakMinutes = 0;
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

	$this->setViewVar('checkInButton',$checkInButton);
	$this->setViewVar('checkOutButton',$checkOutButton);
	$this->setViewVar('startBreakButton',$startBreakButton);
	$this->setViewVar('endBreakButton',$endBreakButton);
	$this->setViewVar('completed',$completed);
	$this->setViewVar('selectedYear',$selectedYear);
	$this->setViewVar('selectedMonth',$selectedMonth);
	$this->setViewVar('selectedUser',$selectedUser);
	$this->setViewVar('currentYear',$currentYear);
	$this->setViewVar('presentDays',$presentDay['COUNT(status)']);
	$this->setViewVar('breakHours',$breakHours);
	$this->setViewVar('breakMinutes',$breakMinutes);
	$this->setViewVar('upComingHolidays',$upComingHolidays);
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
				$to_time = strtotime($activeBreak['startTime']);
				$from_time = strtotime($break->endTime);
				$totalMinutes = round(abs($to_time - $from_time) / 60,2);
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
?>