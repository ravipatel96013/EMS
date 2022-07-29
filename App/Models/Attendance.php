<?php
class Models_Attendance extends TinyPHP_ActiveRecord
{
    public $tableName = "user_attendance";
    public $id = "";
    public $userId = "";
    public $date = "";
    public $checkInDateTime = NULL;
    public $checkOutDateTime = NULL;
    public $status = "";
    public $createdOn = "";
    public $updatedOn = "";

    public $dbIgnoreFields = array('id');

    public function init()
    {
    
        $this->addListener('beforeUpdate', array($this,'doBeforeUpdate'));
    }


    protected function doBeforeUpdate()
    {
        if(!$this->checkInDateTime == NULL)
        {
            
        }
            $this->updatedOn = time();
            return true;
    }


    public function getPresentMonthAttendance($id)
    {
        global $db;
        $currentYear = date("Y");
		$currentMonth = date("m");

        $sql = "SELECT COUNT(status) FROM ". $this->tableName. " WHERE userId = $id AND MONTH(date) = $currentMonth AND YEAR(date) = $currentYear AND status = 'P'";
        $result = $db->fetchRow($sql);
        if(!$result == '')
        {
            return $result;
        }
    }

    public function getActiveAttendance($id)
    {
        global $db;

        $sql = "SELECT * FROM ". $this->tableName ." WHERE userId = $id AND checkInDateTime IS NOT NULL AND checkOutDateTime IS NULL";
        $result = $db->fetchRow($sql);

        return $result;
    }

    public function getInfo()
    {
        global $db;

        $sql = "SELECT a.id,a.date,a.checkInDateTime,a.checkOutDateTime,SUM(b.totalMinutes) AS breakTime,a.status,TIMESTAMPDIFF(minute,checkInDateTime,checkOutDateTime) AS productiveTime FROM `user_attendance` AS a LEFT JOIN break_logs AS b ON a.id=b.attendanceId WHERE a.id=$this->id GROUP BY a.id";
        $result = $db->fetchRow($sql);
        return $result;
    }

}
?>