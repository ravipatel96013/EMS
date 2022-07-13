<?php
class Models_Holiday extends TinyPHP_ActiveRecord
{
    public $tableName = "holidays";
    public $id = "";
    public $name = "";
    public $description = "";
    public $date = "";
    public $createdBy = "";
    public $updatedBy = "";
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

            $this->createdOn = $time;
            $this->updatedOn = $time;
            $this->createdBy = getLoggedInAdminId();
            $this->updatedBy = getLoggedInAdminId();

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
            $time = time();
            $this->updatedOn = $time;
            $this->updatedBy = getLoggedInAdminId();
            return true;
        }
        else
        {
            return false;
        }
    }

    public function validate()
    {
        $this->validateHolidayInfo();

        return !$this->hasErrors();
    }



    public function validateHolidayInfo()
    {  
       if($this->name == "")
       {
          $this->addError("Name is Empty");
       }
        
       if($this->description == "")
       {
          $this->addError("Description is Empty");
       }

       if($this->date == "")
       {
        $this->addError("Date is Empty");
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

    public function fetchHoliday($id)
    {
        global $db;

        $sql = "SELECT * FROM ". $this->tableName ." WHERE id = '$id'";
        $result = $db->fetchRow($sql);
        return $result;
    }

}
?>