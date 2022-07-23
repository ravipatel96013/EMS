<?php
class Models_LeaveBalancesheet extends TinyPHP_ActiveRecord
{
    public $tableName = "user_leave_balancesheet";
    public $id = "";
    public $userId = "";
    public $amount = "";
    public $type = "";
    public $description = "";
    public $actionTakenBy = "";
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
        $this->validateLeaveInfo();

        return !$this->hasErrors();
    }



    public function validateLeaveInfo()
    {  
       if($this->userId == "")
       {
          $this->addError("UserId is Empty");
       }
        
       if($this->description == "")
       {
          $this->addError("Description is Empty");
       }

       if($this->amount == "")
       {
        $this->addError("Amount is Empty");
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

    public function leaveTransaction($where='')
    {
        global $db;

        $sql = "SELECT * FROM `users`,`user_leave_balancesheet` WHERE users.id=user_leave_balancesheet.userId $where;";
        $result = $db->fetchAll($sql);
        if(!$result == '')
        {
            return $result;
        }
    }

}
?>