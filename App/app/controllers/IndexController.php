<?php
class App_IndexController extends TinyPHP_Controller {
	
	public function indexAction()  
	{
		$userName = TinyPHP_Session::get("userName");
		$this->setViewVar('userName',$userName);
	}

	public function checkinAction()
    {
		$status = 0;
        $errors = [];

		$attd = new Models_Attendance();
		$row = $attd->getRow();
		if($row['checkInDateTime'] == NULL)
		{
			$attendance = new Models_Attendance($row['id']);
			$attendance->checkInDateTime = date('Y-m-d H:i:s');
			$attendance->status = 'P';
			$isUpdated = $attendance->update(array('checkInDateTime','updatedOn','status'));

			if($isUpdated)
			{
				$status = 1;
			}
			else
			{
				$attendance->getErrors();
			}
			$response = ["status" => $status, "errors" => $errors];
            echo json_encode($response);
            die; 
		}

    }

	public function checkoutAction()
    {
		$status = 0;
        $errors = [];

		$attd = new Models_Attendance();
		$row = $attd->getRow();
		if(!$row['checkInDateTime'] == NULL)
		{
			$attendance = new Models_Attendance($row['id']);
			$attendance->checkOutDateTime = date('Y-m-d H:i:s');
			$isUpdated = $attendance->update(array('checkOutDateTime','updatedOn',));

			if($isUpdated)
			{
				$status = 1;
			}
			else
			{
				$attendance->getErrors();
			}
			$response = ["status" => $status, "errors" => $errors];
            echo json_encode($response);
            die; 
		}

    }

	public function checkstatusAction()
	{
		$status = 0;
        $errors = [];

		$attd = new Models_Attendance();
		$break = new Models_BreakLog();
		$row = $attd->getRow();
		$breakRow = $break->getRow($row['id']);
	
		if(!$breakRow == NULL)
		{
			if(!$row['checkOutDateTime'] == NULL)
			{
				$status = 1;
			}
			else{
				if(!$row['checkInDateTime'] == NULL)
				{
					if($breakRow['endTime'] == NULL)
					{
						$status = 3;
					}
					else{
						$status = 4;
					}
				}
				else{
					$status = 2;
				}
			}
		}
		else {
			if(!$row['checkOutDateTime'] == NULL)
			{
				$status = 1;
			}
			else{
				if(!$row['checkInDateTime'] == NULL)
				{
					$status = 4;
				}
				else{
					$status = 2;
				}
			}
		}
		$response = ["status" => $status, "errors" => $errors];
		echo json_encode($response);
		die; 
	}

	public function startbreakAction()
    {
		$status = 0;
        $errors = [];

		$attd = new Models_Attendance();
		$row = $attd->getRow();
		if(!$row['checkInDateTime'] == NULL)
		{
			$bl = new Models_BreakLog;
			$bl->attendanceId = $row['id'];
			$bl->startTime = date('Y-m-d H:i:s');
			$isCreated = $bl->create();

			if($isCreated)
			{
				$status = 1;
			}
			else
			{
				$bl->getErrors();
			}
			$response = ["status" => $status, "errors" => $errors];
            echo json_encode($response);
            die; 
		}

    }

	public function endbreakAction()
    {
		$status = 0;
        $errors = [];

		$attd = new Models_Attendance();
		$row = $attd->getRow();
		$attendanceId = $row['id'];
		$break = new Models_BreakLog();
		$breakRow = $break->getRow($attendanceId);
		$updateId = $breakRow['id'];
		if($breakRow['endTime'] == NULL)
		{
			$bl = new Models_BreakLog($updateId);
			$bl->endTime = date('Y-m-d H:i:s');
			$to_time = strtotime($breakRow['startTime']);
			$from_time = strtotime(date('Y-m-d H:i:s'));
			$totalMinutes = round(abs($to_time - $from_time) / 60,2);
			$bl->totalMinutes = $totalMinutes;
			$isUpdated = $bl->update(array('endTime','totalMinutes'));

			if($isUpdated)
			{
				$status = 1;
			}
			else
			{
				$bl->getErrors();
			}
			$response = ["status" => $status, "errors" => $errors];
            echo json_encode($response);
            die; 
		}

    }
}
?>