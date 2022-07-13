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
		$row = $attd->getRow();
		$CheckedIn = $row['checkInDateTime'];
		$CheckedOut = $row['checkOutDateTime'];
		if($CheckedIn && $CheckedOut)
		{
			$status = 1; // Disable both button
		}
		elseif($CheckedIn == NULL)
		{
			$status = 2; // Show check-in button 
		}
		else
		{
			$status = 3; // Show check-out button
		}
		$response = ["status" => $status, "errors" => $errors];
		echo json_encode($response);
		die; 
	}

	public function startbreakAction()
    {
		$status = 0;
        $errors = [];

		$attd = new Models_Attendance()
		$rowId = $attd->getRowId();
		if(!$isCheckedIn)
		{
			$attendance = new Models_Attendance($rowId['id']);
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
}
?>