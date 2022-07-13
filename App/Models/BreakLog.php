<?php
class Models_BreakLog extends TinyPHP_ActiveRecord
{
    public $tableName = "break_log";
    public $id = "";
    public $attendanceId = "";
    public $startTime = "";
    public $endTime = "";
    public $totalMinutes = "";

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
         
            return true;
        }
        else
        {
            return false;
        }
    }

    public function validate()
    {
        $this->validateBreakInfo();

        return !$this->hasErrors();
    }



    public function validateBreakInfo()
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

    // public function getRow()
    // {
    //     global $db;
    //     $today = date("Y-m-d");
    //     $loggedInUserId = getLoggedInUserId();

    //     $sql = "SELECT * FROM ". $this->tableName ." WHERE userId = $loggedInUserId AND date = '$today'";
    //     $result = $db->fetchRow($sql);   
    //     return $result;
    // }
}
?>