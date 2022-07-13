<?php
class Models_Attendance extends TinyPHP_ActiveRecord
{
    public $tableName = "user_attendance";
    public $id = "";
    public $userId = "";
    public $date = "";
    public $checkInDateTime = "";
    public $checkOutDateTime = "";
    public $status = "";
    public $createdOn = "";
    public $updatedOn = "";

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

            $this->checkInDateTime = NULL; 
            $this->checkOutDateTime = NULL;
            $this->status = "NA";
            $this->createdOn = $time;
            $this->updatedOn = $time;
            return true;
        }
        else
        {
            return false;
        }
    }


    protected function doBeforeUpdate()
    {
        if($this->validate())
        {   
            $this->updatedOn = time();
            return true;
        }
        else
        {
            return false;
        }
    }

    public function validate()
    {
        $this->validateAttendanceInfo();

        return !$this->hasErrors();
    }



    public function validateAttendanceInfo()
    {  
        // Validation Area


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

    public function checkEntries($month)
    {
        global $db;
        $sql = "SELECT * FROM `$this->tableName` WHERE MONTH(date) = $month AND YEAR(date) = 2022";
        $result = $db->fetchRow($sql);

        if(!$result == '')
        {
            return $result;
        }
    }


    public function getRow()
    {
        global $db;
        $today = date("Y-m-d");
        $loggedInUserId = getLoggedInUserId();

        $sql = "SELECT * FROM ". $this->tableName ." WHERE userId = $loggedInUserId AND date = '$today'";
        $result = $db->fetchRow($sql);
        return $result;
    }

}
?>