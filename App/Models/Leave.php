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
    public $approvedBy = "";
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
        if($this->validate())
        {
           $this->approvedBy = TinyPHP_Session::get('adminId');
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

}
?>