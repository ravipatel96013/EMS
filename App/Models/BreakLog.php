<?php
class Models_BreakLog extends TinyPHP_ActiveRecord
{
    public $tableName = "break_logs";
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
            $this->endTime = NULL;
			$this->totalMinutes = NULL;
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


    public function getActiveBreak($id)
    {
        global $db;

        $sql = "SELECT b.* FROM `break_logs` AS b LEFT JOIN user_attendance AS a ON b.attendanceId = a.id WHERE a.userId=$id AND b.startTime IS NOT NULL AND b.endTime IS NULL";
        $result = $db->fetchRow($sql);

        return $result;
    }

    public function getTotalBreakTime($id)
    {
        global $db;
        $date = date('Y-m-d');

        $sql = "SELECT SUM(b.totalMinutes) FROM user_attendance AS a LEFT JOIN break_logs AS b ON a.id=b.attendanceId WHERE a.date='" .$date."' AND a.userId=$id GROUP BY a.id;";
        $result = $db->fetchRow($sql);

        return $result;
    }

}
?>