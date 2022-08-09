<?php
class Admin_DailyattendanceController extends TinyPHP_Controller {
	
	public function indexAction()  
	{
        $selectedDate = $this->getRequest()->getVar('date','string', date("Y-m-d"));
        $this->setViewVar('selectedDate',$selectedDate);


        global $db;
        $sql = "SELECT a.id as attendanceId,c.firstName as firstName,c.lastName as lastName,DATE_FORMAT(a.date,'%d-%m-%Y') as Date,DATE_FORMAT(a.checkInDateTime, '%r') as checkIn,DATE_FORMAT(a.checkOutDateTime, '%r') as checkOut,a.status as status,DATE_FORMAT(b.startTime, '%r') as breakStartTime,DATE_FORMAT(b.endTime, '%r') as breakEndTime,SUM(b.totalMinutes) as breakTime
        FROM `user_attendance` as a
        LEFT JOIN break_logs as b ON a.id=b.attendanceId AND b.startTime IS NOT NULL AND b.endTime IS NULL
        LEFT JOIN users as c ON c.id=a.userId
        WHERE a.date='$selectedDate' AND c.isActive = 1
        GROUP BY a.id";
    
        $sql2 = "SELECT a.id as attendanceId,CONCAT(FLOOR(SUM(b.totalMinutes)/60),':',MOD(SUM(b.totalMinutes),60)) as breakTime
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
	}

}