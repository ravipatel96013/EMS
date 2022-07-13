<?php
class Scripts_UserAttendanceController extends TinyPHP_Controller
{
    public function defaultAction()
    {
        $this->setNoRenderer(true);
        $nextMonth = 0;
        $today = date('d');
        $currentMonth = date('m');    
        $currentYear = date('Y');
        $lastDay = cal_days_in_month(CAL_GREGORIAN,$currentMonth,$currentYear);
       
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
            $users = $attd->getUserId();
            $entries = $attd->checkEntries($nextMonth);
            if(empty($entries))
            {
                foreach($users as $user)
                {
                    for($i=1;$i<=$daysInMonth;$i++)
                    {
                        $attd->userId = $user['id'];
                        $attd->date = $currentYear."-".$nextMonth."-".$i;
                        $attd->create();
                    }
                }
                echo "Entries Created";
            }
            else {
                echo "Already Exist";
            }
        }
        else{
            echo "Its Not Last day of Month";
        }
    }
}