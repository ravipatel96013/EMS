<?php
class Service_Attendance extends Service_Base {

public function checkin($id,$checkInDateTime)
{

		$attendance = new Models_Attendance($id);

		if($attendance->isEmpty)
		{
			$this->addError('Attendance Does not exist');
		}
		else
		{
            $attendance->checkInDateTime = $checkInDateTime;
			$isUpdated = $attendance->update(array('checkInDateTime','updatedOn'));
			if($isUpdated)
			{
				return true;
			}
			else
			{
				$this->addError($attendance->getErrors());
			}
		
		}

        return false;
}

public function checkout($id,$checkOutDateTime)
{
    $isUpdated = false;
	$attendance = new Models_Attendance($id);
    
    if($attendance->isEmpty)
    {
        $this->addError('Attendance Does not exist');
    }
    else
    {   
        $leaveItem = new Models_LeaveItem();
        $leave = $leaveItem->todayLeave($id);
        $attendance = new Models_Attendance($id);
        $from_time = strtotime($attendance->checkInDateTime); 
        $to_time = strtotime($checkOutDateTime); 
        $workingHours = round(abs($from_time - $to_time) / 60,2);
        $breakTime = $attendance->getInfo();
        $breakTime = $breakTime['breakTime'];
        $productiveHours = ($workingHours - $breakTime)/60;
        if($productiveHours < 6)
        {
            if($leave['leaveId'] == NULL)
            {   
                $attendance->status = 'HUL';
                $attendance->checkOutDateTime = $checkOutDateTime;
                $attendance->update(array('checkOutDateTime','status','updatedOn'));
                $isUpdated = true;
            }
            else{
                if($leave['isHalf'] == 1 && $leave['isLeaveBalanceDeducted'] == 1)
                {
                    $attendance->status = 'HPL';
                    $attendance->checkOutDateTime = $checkOutDateTime;
                    $attendance->update(array('checkOutDateTime','status','updatedOn'));
                    $isUpdated = true;
                }
                elseif($leave['isHalf'] == 0 && $leave['isLeaveBalanceDeducted'] == 1)
                {
                    echo 'called';
                    die;
                    $attendance->status = 'UL';
                    $attendance->checkOutDateTime = $checkOutDateTime;
                    $attendance->update(array('checkOutDateTime','status','updatedOn'));
                    $isUpdated = true;
                    $balanceSheet = new Service_Leavebalancesheet();
                    $balanceSheet->doCredit($leave['userId'],1,'Leave Revert','SYSTEM');

                    $leaveItem = new Models_LeaveItem($leave['id']);
                    $leaveItem->isLeaveBalanceDeducted = 0;
                    $leaveItem->update(['isLeaveBalanceDeducted']);
                }
                else{
                    $attendance->status = 'HUL';
                    $attendance->checkOutDateTime = $checkOutDateTime;
                    $attendance->update(array('checkOutDateTime','status','updatedOn'));
                    $isUpdated = true;
                }
            }
            if($isUpdated)
            {
                return true;
            }
        }
        else {
            if(!$leave['leaveId'] == NULL)
            {
                $attendance->status = 'P';
                $attendance->checkOutDateTime = $checkOutDateTime;
                $attendance->update(array('checkOutDateTime','status','updatedOn'));
                $isUpdated = true;
                $amount = 0;
                if($leave['isHalf'] == 0 && $leave['isLeaveBalanceDeducted'] == 1)
                {
                    $amount = 1;
                }
                elseif($leave['isHalf'] == 1 && $leave['isLeaveBalanceDeducted'] == 1)
                {
                    $amount = 0.5;
                }  
                $balanceSheet = new Service_Leavebalancesheet();
                $balanceSheet->doCredit($leave['userId'],$amount,'Leave Revert','SYSTEM');
                $leaveItem = new Models_LeaveItem($leave['id']);
                $leaveItem->isLeaveBalanceDeducted = 0;
                $leaveItem->update(['isLeaveBalanceDeducted']);
            }
            else
            {
                $attendance->status = 'P';
                $attendance->checkOutDateTime = $checkOutDateTime;
                $attendance->update(array('checkOutDateTime','status','updatedOn'));
                $isUpdated = true;
            }
            }
            if($isUpdated)
            {
                return true;
            }
        }
    
}

public function addAttendance($year,$month,$userId)
{
    $fields = ['userId','date','checkInDateTime','checkOutDateTime','status','createdOn','updatedOn'];
    $insertData = [];
    $createdOn = time();
    $updatedOn = time();
    $daysInMonth = date('t',strtotime($month));
    $holiday = new Models_Holiday();
    $holidayList = $holiday->getAll(['date']);
    for($i=1;$i<=$daysInMonth;$i++)
    {
        $status = 'NA';
        $date = $year."-".$month."-".$i;
        $day = date('D',strtotime($date));
        if($day == 'Sun')
        {
            $status = 'WO';
        }
           foreach($holidayList as $holiday)
           {
                if($holiday->date == $date)
                {
                    $status = 'HO';
                }
            }
            array_push($insertData,[$userId,$date,NULL,NULL,$status,$createdOn,$updatedOn]);
    }
    $attendance = new Models_Attendance();
    $isInserted = $attendance::insertMultiple('user_attendance',$fields,$insertData);
    if($isInserted)
    {
        return true;
    }
    return false;
}

}