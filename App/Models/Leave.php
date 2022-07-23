<?php
class Models_Leave extends TinyPHP_ActiveRecord
{
    public $tableName = "leaves";
    public $id = "";
    public $userId = "";
    public $type = "";
    public $startDate = "";
    public $endDate = "";
    public $isHalf = "";
    public $comment = "";
    public $status = "";
    public $actionBy = "";
    public $createdOn = "";
    public $oldStatus = "";

    public $dbIgnoreFields = array('id','oldStatus');

    public function init()
    {
        $this->addListener('beforeCreate', array($this,'doBeforeCreate'));
        $this->addListener('afterCreate', array($this,'doAfterCreate'));
        $this->addListener('beforeUpdate', array($this,'doBeforeUpdate'));
        $this->addListener('afterUpdate', array($this,'doAfterUpdate'));
    }


    protected function doBeforeCreate()
    {
        if($this->validate())
        {
            $time = time();

            $this->createdOn = $time;
            $this->status = PENDING;

            return true;
        }
        else
        {
            return false;
        }
    }

    protected function doAfterCreate()
    {
            $dates = array();
            $current = strtotime($this->startDate);
            $date2 = strtotime($this->endDate);
            $stepVal = '+1 day';
            while( $current <= $date2 ) {
               $dates[] = date('Y-m-d', $current);
               $current = strtotime($stepVal, $current);
            }
         $dateRange = $dates;
         foreach($dateRange as $date)
         {
            $item = new Models_LeaveItem();
            $item->leaveId = $this->id;
            $item->date = $date;
            $item->isLeaveBalanceDeducted = 0;
            $item->create();
         }
         return true;
        // $name = TinyPHP_Session::get('usernName');
        // $mailer = new Helpers_Mailer();
        // $from = 'smitparmar.yrcoder@gmail.com';
        // $to = 'smitparmar21902@gmail.com';
        // $subject = 'Regarding leave application at YR CODER';
        // $body = 'Hy its '.$name.'Need a leave from'.$this->startDate.'To '.$this->endDate;
        // $isSent = $mailer->sendMail($from,$to,$subject,$body);
        // if(!$isSent)
        // {
        //     $this->addError($mailer->getErrors());
        // }
        // return true;
    }

    protected function doBeforeUpdate()
    {
        $this->actionBy = TinyPHP_Session::get('adminId');

        return true;

    }

    protected function doAfterUpdate()
    {
        $result = $this->attendanceToUpdate();
        foreach($result as $data)
        {
         $balanceSheet = new Models_LeaveBalancesheet();
         $leaveBalance = $balanceSheet->getLeaveBalance($this->userId);
         if($leaveBalance > 0)
         { 
             if($data['holidayId'] == NULL)
             {
                //  $timestamp = strtotime($data['leaveItemDate']);
                //  $leaveDay = date('D', $timestamp);
                //  if(!$leaveDay == 'Sun')
                   $userId = $data['userId'];
                   $actionTakenBy = TinyPHP_Session::get('adminName');
                   $leaveItem = new Models_LeaveItem($data['leaveItemId']);
                   if($this->status == APPROVED && ($this->oldStatus == PENDING || $this->oldStatus == DECLINED))
                   {
                        $description = 'Leave Approved';
                         if($this->isHalf == 1)
                         {
                            $service = new Service_LeaveBalancesheet();
                            $amount = 0.5;
                            $service->doDebit($userId,$amount,$description,$actionTakenBy);
                            $leaveItem->isLeaveBalanceDeducted = 1;
                            $leaveItem->update(['isLeaveBalanceDeducted']);
                         }
                         else
                         {
                            $service = new Service_LeaveBalancesheet();
                            $amount = 1;
                            $service->doDebit($userId,$amount,$description,$actionTakenBy);
                            $leaveItem->isLeaveBalanceDeducted = 1;
                            $leaveItem->update(['isLeaveBalanceDeducted']);
                         }
                     }
                     elseif($this->status == DECLINED && $this->oldStatus == APPROVED)
                     {
                        $description = 'Balance Revert';
                        if($data['isDeducted'] == 1)
                        {
                            if($this->isHalf == 1)
                            {
                               $service = new Service_LeaveBalancesheet();
                               $amount = 0.5;
                               $service->doCredit($userId,$amount,$description,$actionTakenBy);
                               $leaveItem->isLeaveBalanceDeducted = 0;
                               $leaveItem->update(['isLeaveBalanceDeducted']);
                            }
                            else
                            {
                               $service = new Service_LeaveBalancesheet();
                               $amount = 1;
                               $service->doCredit($userId,$amount,$description,$actionTakenBy);
                               $leaveItem->isLeaveBalanceDeducted = 0;
                               $leaveItem->update(['isLeaveBalanceDeducted']);
                            }
                        }
                     }
             }
         }
     }
           
           return true;
    }


    public function validate()
    {
        $this->validateLeaveInfo();

        return !$this->hasErrors();
    }



    public function validateLeaveInfo()
    {  
        
       if($this->comment == "")
       {
          $this->addError("Description is Empty");
       }

       if($this->type == "")
       {
        $this->addError("Type is Empty");
       }

        return !$this->hasErrors();
    }

    public function showData()
    {
        global $db;

        $sql = "SELECT * FROM ". $this->tableName;
        $result = $db->fetchAll($sql);
        if(!$result == '')
        {
            return $result;
        }
    }

    public function leaveApplications($where='')
    {
        global $db;

        $sql = "SELECT * FROM `users`,`leaves` WHERE users.id=leaves.userId $where;";
        $result = $db->fetchAll($sql);
        if(!$result == '')
        {
            return $result;
        }
    }

    public function fetchRow($id)
    {
        global $db; 

        $sql = "SELECT * FROM `leaves` WHERE id = $id;";
        $result = $db->fetchRow($sql);
        if(!$result == '')
        {
            return $result;
        }
    }
    
    public function getLeaves()
    {
        global $db;
        $date = date('Y-m-d');
        $sql = "SELECT a.id AS attendanceId,a.userId AS userId,a.status AS attendanceStatus,b.isHalf AS isHalf,b.status AS leaveStatus,c.id AS leaveItemId FROM `user_attendance` AS a LEFT JOIN leaves AS b ON b.userId=a.userId LEFT JOIN leave_items AS c ON c.leaveId=b.id AND c.date=a.date WHERE a.date='$date' AND a.status='NA'";
        $result = $db->fetchAll($sql);
        return $result;

    }

    public function attendanceToUpdate()
    {
       global $db;
       $sql = "SELECT b.isLeaveBalanceDeducted AS isDeducted, a.isHalf AS isHalf, c.userId AS userId,a.id AS leaveId, b.id AS leaveItemId, b.date AS leaveItemDate, c.id AS userAttendanceId, c.date AS attendanceDate, d.id AS holidayId FROM leaves AS a INNER JOIN leave_items AS b ON b.leaveId=a.id INNER JOIN user_attendance AS c ON c.date=b.date AND c.userId=a.userId LEFT JOIN holidays AS d ON d.date=c.date WHERE a.id=$this->id";
       $result = $db->fetchAll($sql);
       return $result;
}
}
?>