<?php
class Scripts_UserAttendanceController extends TinyPHP_Controller
{
    public function defaultAction()
    {
        $logs = [];

        $this->setNoRenderer(true);
        $nextMonth = 0;
        $today = date('d');
        $currentMonth = date('m');    
        $currentYear = date('Y');
        $lastDay = date('t');
       
       if($today == $lastDay)
       { 
            if($currentMonth == 12)
            {
                $nextMonth = 1;
            }
            else
            {
                $nextMonth = $currentMonth+1;
            }
            
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN,$nextMonth,$currentYear);

            $attd = new Models_Attendance();
            $attendace = $attd->getAll(['id'], "MONTH(date)={$nextMonth} AND YEAR(date)={$currentYear}");
            if(count($attendace) == 0)
            {
                $user = new Models_User();
                $users = $user->getAll(['id']);
                foreach($users as $user)
                {
                    for($i=1;$i<=$daysInMonth;$i++)
                    {
                        $attd = new Models_Attendance();
                        $attd->userId = $user->id;
                        $attd->date = $currentYear."-".$nextMonth."-".$i;
                        $attd->create();
                    }
                }
                array_push($logs,"Entries Created");
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

    $leave = new Models_Leave();
    $data = $leave->getLeaves();
    if(!empty($data))
    {
         foreach($data as $leave)
        {
            $attendace = new Models_Attendance($leave['attendanceId']);
            if($leave['status'] == 1)
            {
                if($leave['isHalf'] == 1)
                {
                $attendace->status = 'HPL';
                $attendace->update(['status','updatedOn']); 
                }
                else{
                $attendace->status = 'PL';
                $attendace->update(['status','updatedOn']);
                }
            }
            elseif($leave['status'] == 0 || $leave['status'] == 2)
            {
                $attendace->status = 'UL';
                $attendace->update(['status','updatedOn']);
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
            $leave = new Models_LeaveBalancesheet();
            $leave->userId = $user->id;
            $leave->amount = 1;
            $leave->type = 'credit';
            $leave->description = 'Monthly Free Paid Leave';
            $leave->actionTakenBy = 'SYSTEM';
            $leave->create();
        }
    }
    else {
        array_push($logs,"Its not a last day of Month");
    }   
 }
 }