<?php
class Models_LeaveItem extends TinyPHP_ActiveRecord
{
    public $tableName = "leave_items";
    public $id = "";
    public $leaveId = "";
    public $date = "";
    public $isLeaveBalanceDeducted = "";

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
            return true;
        
    }


    protected function doBeforeUpdate()
    {
             return true;
    }

    public function todayLeave($attendanceId)
    {
        global $db;

        $sql = "SELECT c.*,a.userId AS userId,c.id AS leaveItemId FROM user_attendance AS a LEFT JOIN leaves AS b ON b.userId=a.userId LEFT JOIN leave_items AS c ON c.leaveId=b.id AND c.date=a.date WHERE a.id=$attendanceId AND b.status='1'";
        $result = $db->fetchRow($sql);
        if(!$result == '')
        {
            return $result;
        }
    }

}
?>