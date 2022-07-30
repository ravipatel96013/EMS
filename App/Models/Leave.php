<?php
class Models_Leave extends TinyPHP_ActiveRecord
{
    public $tableName = "leaves";
    public $id = "";
    public $userId = "";
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
            $this->start_transaction();
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
        $commit = false;
        $isCreated = false;

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
             $day =  date('D', strtotime($date));
             $holiday = new Models_Holiday();
             $holiday->fetchByProperty('date',$date);
             $attendance = new Models_Attendance();
             $attendance->fetchByProperty(['userId','date'],[$this->userId,$date]);
             if($day == 'Sun' || (!($holiday->isEmpty)))
             {
             }
             else
             {
                $item = new Models_LeaveItem();
                $item->leaveId = $this->id;
                $item->date = $date;
                $item->isLeaveBalanceDeducted = 0;
                $isCreated = $item->create();
             }
        }
        if($isCreated == true)
        {
            $commit = true;
        }

        if($commit == true)
        {
            $this->commit();
        }
        else{
            $this->addError("Can not create the Leave application");
            $this->rollback();
        }
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
        $date = date('Y-m-d');
        foreach($result as $data)
        {
            $attendance = new Models_Attendance();
            $balanceSheet = new Models_LeaveBalancesheet();
            $leaveBalance = $balanceSheet->getLeaveBalance($this->userId);
            if($leaveBalance > 0)
            {  
                  $userId = $data['userId'];
                  $actionTakenBy = TinyPHP_Session::get('adminName');
                  $leaveItem = new Models_LeaveItem($data['leaveItemId']);
                  if($this->status == APPROVED && ($this->oldStatus == PENDING || $this->oldStatus == DECLINED))
                  {
                          $description = 'Leave Approved';
                          if($this->isHalf == 1)
                          {
                              if($data['leaveItemDate'] < $date)  
                              {
                                $attendance->fetchByProperty(['userId','date'],[$data['userId'],$data['leaveItemDate']]);
                                $attendance->status = 'HPL';
                                $attendance->update(['status','updatedOn']);
                              }
                              $service = new Service_Leavebalancesheet();
                              $amount = 0.5;
                              $service->doDebit($userId,$amount,$description,$actionTakenBy);
                              $leaveItem->isLeaveBalanceDeducted = 1;
                              $leaveItem->update(['isLeaveBalanceDeducted']);
                          }
                          else
                          {
                            if($data['leaveItemDate'] < $date)  
                              {
                                $attendance->fetchByProperty(['userId','date'],[$data['userId'],$data['leaveItemDate']]);
                                $attendance->status = 'PL';
                                $attendance->update(['status','updatedOn']);
                              }
                              $service = new Service_Leavebalancesheet();
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
                              if($data['leaveItemDate'] < $date)  
                              {
                                $attendance->fetchByProperty(['userId','date'],[$data['userId'],$data['leaveItemDate']]);
                                $attendance->status = 'HUL';
                                $attendance->update(['status','updatedOn']);
                              }
                              $service = new Service_Leavebalancesheet();
                              $amount = 0.5;
                              $service->doCredit($userId,$amount,$description,$actionTakenBy);
                              $leaveItem->isLeaveBalanceDeducted = 0;
                              $leaveItem->update(['isLeaveBalanceDeducted']);
                              }
                              else
                              {
                              if($data['leaveItemDate'] < $date)  
                              {
                                $attendance->fetchByProperty(['userId','date'],[$data['userId'],$data['leaveItemDate']]);
                                $attendance->status = 'UL';
                                $attendance->update(['status','updatedOn']);
                              }
                              $service = new Service_Leavebalancesheet();
                              $amount = 1;
                              $service->doCredit($userId,$amount,$description,$actionTakenBy);
                              $leaveItem->isLeaveBalanceDeducted = 0;
                              $leaveItem->update(['isLeaveBalanceDeducted']);
                              }
                          }
                      }
         }
     }
           
           return true;
    }


    public function validate()
    {
        if($this->comment == "")
        {
           $this->addError("Comment is Empty");
        }

        if(!$this->startDate == '')
        {
            $this->startDate = date('Y-m-d',strtotime($this->startDate));
        }
        else
        {
            $this->addError("Start Date is Empty");
        }

        if(!$this->endDate == '')
        {
            $this->endDate = date('Y-m-d',strtotime($this->endDate));
            if($this->endDate < $this->startDate)
            {
                $this->addError("Start and End Date id not Valid");
            }
        }
        else
        {
            $this->addError("End Date is Empty");
        }

        return !$this->hasErrors();
    }
    
    public function getLeaves()
    {
        global $db;
        $date = date('Y-m-d');
        $sql = "SELECT l.id as leaveItemId,lv.userId as userId,l.date as leaveDate,lv.status as status,lv.isHalf AS isHalf FROM leave_items AS l LEFT JOIN leaves AS lv ON lv.id=l.leaveId WHERE l.date='$date'";
        $result = $db->fetchAll($sql);
        return $result;

    }

    public function attendanceToUpdate()
    {
       global $db;
       $sql = "SELECT a.userId as userId,b.isLeaveBalanceDeducted AS isDeducted, a.isHalf AS isHalf,a.id AS leaveId, b.id AS leaveItemId, b.date AS leaveItemDate,d.id AS holidayId FROM leaves AS a INNER JOIN leave_items AS b ON b.leaveId=a.id LEFT JOIN holidays AS d ON d.date=b.date WHERE a.id=$this->id;";
       $result = $db->fetchAll($sql);
       return $result;
}
}
?>