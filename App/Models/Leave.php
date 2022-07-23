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

    public $dbIgnoreFields = array('id');

    public function init()
    {
    
        if($this->id>0)
        {

        }

        $this->addListener('beforeCreate', array($this,'doBeforeCreate'));
        $this->addListener('beforeUpdate', array($this,'doBeforeUpdate'));
    }


    protected function doBeforeCreate()
    {
        if($this->validate())
        {
            $time = time();

            $this->createdOn = $time;
            $this->userId = TinyPHP_Session::get('userId');
            $this->status = 0;

            return true;
        }
        else
        {
            return false;
        }
    }


    protected function doBeforeUpdate()
    {
           $this->actionBy = TinyPHP_Session::get('adminId');

           $data = $this->attendanceToUpdate();
           foreach($data as $row)
           {
               $attendance = new Models_Attendance($row['id']);
                if($this->status == 1)
                {
                    if($this->isHalf == 1)
                    {
                        $attendance->status = 'HPL';
                        $attendance->update(['status','updatedOn']);
                    }
                    else{
                        $attendance->status = 'PL';
                        $attendance->update(['status','updatedOn']);
                    }
                }
                elseif($this->status == 0 || $this->status == 2)
                {
                    $attendance->status = 'UL';
                    $attendance->update(['status','updatedOn']);
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

        $sql = "SELECT l.*,a.id AS attendanceId FROM users AS u INNER JOIN user_attendance AS a ON u.id=a.userId LEFT JOIN leaves as l ON l.userId=u.id WHERE U.isActive=1 AND a.date='$date' AND a.status='NA' AND DATE_FORMAT(FROM_UNIXTIME(l.startDate),\"%Y-%m-%d\") = '$date' AND l.status=1;";
        $result = $db->fetchAll($sql);
        return $result;

    }

    public function attendanceToUpdate()
    {
        global $db;

        $sql = "SELECT a.* FROM user_attendance as a LEFT JOIN leaves as l ON a.userId=l.userId WHERE  l.id=$this->id AND a.date BETWEEN DATE_FORMAT(FROM_UNIXTIME(l.startDate),\"%Y-%m-%d\") AND DATE_FORMAT(FROM_UNIXTIME(l.endDate),\"%Y-%m-%d\");";
        $result = $db->fetchAll($sql);
        return $result;
    }

}
?>