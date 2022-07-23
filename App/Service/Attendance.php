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
			$attendance->status = 'P';
			$isUpdated = $attendance->update(array('checkInDateTime','updatedOn','status'));
			if($isUpdated)
			{
				return true;
			}
			else
			{
				$this->addError($attendance->getErrors());
			}
		
		}
}

public function checkout($id,$checkOutDateTime)
{

	$attendance = new Models_Attendance($id);
    
    if($attendance->isEmpty)
    {
        $this->addError('Attendance Does not exist');
    }
    else
    {
        $attendance->checkOutDateTime = $checkOutDateTime;
        
        $leaveItem = new Models_LeaveItem();
        $isLeave = $leaveItem->todayLeave($id);
        // echo '<pre>';
        // print_r($isLeave);
        // die;
        if(!empty($isLeave))
        {
            $balanceSheet = new Service_LeaveBalancesheet();
            $balanceSheet->doCredit($isLeave['userId'],1,'Leave Revert','SYSTEM');
            $leaveItem = new Models_LeaveItem($isLeave['leaveItemId']);
            $leaveItem->isLeaveBalanceDeducted = 0;
            $leaveItem->update(['isLeaveBalanceDeducted']);

        }

        
        $isUpdated = $attendance->update(array('checkOutDateTime','updatedOn'));
        if($isUpdated)
        {
           return true;
        }
        else
        {
            $this->addError($attendance->getErrors());
        }
    }
}

}