<?php
class Scripts_UserAttendanceController extends TinyPHP_Controller
{
    public function defaultAction()
    {
        $logs = [];
        $this->setNoRenderer(true);
        $today = date('d');
        $lastDay = date('t');
       
       if($today == $lastDay)
       { 
           
            $daysInNextMonth = date("t",strtotime("next month"));
            $nextMonth = date("m",strtotime("next month"));
            $yearOfNextMonth = date("Y",strtotime("next month"));

            $attd = new Models_Attendance();
            $attendace = $attd->getAll(['id'], "MONTH(date)={$nextMonth} AND YEAR(date)={$yearOfNextMonth}");
            if(count($attendace) == 0)
            {
                $entriesCreated = false;
                $user = new Models_User();
                $users = $user->getAll(['id'],'isActive=1');
                foreach($users as $user)
                {
                    $service = new Service_Attendance();
                    $isCreated = $service->addAttendance($yearOfNextMonth,$nextMonth,$user->id);
                    if($isCreated)
                    {
                        $entriesCreated = true;
                    }
                }
                if($entriesCreated)
                {
                    array_push($logs,"Entries Created");
                }
                else{
                    array_push($logs,"Something went Wrong");
                }
            }
            else {
                array_push($logs,"Already Exist");
            }
        }
        else{
            array_push($logs,"Its Not a Last day of month");
        }
    }

public function userattendancestatusAction()
{
    $this->setNoRenderer(true);
    $date = date('Y-m-d');
    $today = date('D');
    
    $holiday = new Models_Holiday();
    $holiday->fetchByProperty(['date'],[$date]);
    
    $where = "date='$date' AND status='NA'";
    $attendace = new Models_Attendance();
    $data = $attendace->getAll(['id','date','userId','status'],$where);
    
    $leave = new Models_Leave();
    $leaves = $leave->getLeaves();

    $leavesByUserId = [];
    foreach($leaves as $leave)
    {
        $leavesByUserId[$leave['userId']] = $leave;
    }


    foreach($data as $attendace)
    {
        $attd = new Models_Attendance($attendace->id);
        $leave = [];
        if( isset($leavesByUserId[$attendace->userId]) ) 
        {
            $leave = $leavesByUserId[$attendace->userId];
            if($leave['status'] == APPROVED)
               {
                   if($leave['isHalf'] == 1)
                   {
                        $attd->status = 'HPL';
                        $attd->update(['status','updatedOn']);
                    }
                    else
                    {
                        $attd->status = 'PL';
                        $attd->update(['status','updatedOn']);
                    }
                }
                elseif($leave['status'] == PENDING || $leave['status'] == DECLINED)
                {
                    $attd->status = 'UL';
                    $attd->update(['status','updatedOn']);
                }
        }
        else
        {
            if($today == 'Sun')
                {
                    $attd->status = 'WO';
                    $attd->update(['status','updatedOn']);   
                }
                elseif(!$holiday->isEmpty)
                {
                    $attd->status = 'HO';
                    $attd->update(['status','updatedOn']);
                }
                else
                {
                    $attd->status = 'UL';
                    $attd->update(['status','updatedOn']);
                }
        }
    }
    
}


 public function leaveAction()
 {
    $this->setNoRenderer(true);
    $logs = [];

    $today = date('d');
    $lastDay = date('t');

    if($today == $lastDay)
    {
        $user = new Models_User();
        $userIds = $user->getAll(['id']);
    
        foreach($userIds as $user)
        {
            $leave = new Service_LeaveBalancesheet();
            $userId = $user->id;
            $amount = 1;
            $description = 'Monthly Free Paid Leave';
            $actionTakenBy = 'SYSTEM';
            $leave->doCredit($userId,$amount,$description,$actionTakenBy);
        }
    }
    else {
        array_push($logs,"Its not a last day of Month");
    }   
 }
 }