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
        $this->addListener('beforeCreate', array($this,'doBeforeCreate'));
        $this->addListener('afterCreate', array($this,'doAfterCreate'));
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

    protected function doAfterCreate()
    {
        $date = date('Y-m-d');
        if($this->date > $date)
        {
            $year = date('Y', strtotime($this->date));
            $month = date('m', strtotime($this->date));
            
            if($year == date('Y') && $month == date('m'))
            {
                global $db;   
                $where = "date='$this->date'";
                $db->update('user_attendance',['status'=>'HO'],$where);
            }
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

       if($this->date != "")
       {
        $date = date('Y-m-d');
        if($this->date < $date)
        {
            $this->addError("Invalid Date");
        }
       }
       else{
        $this->addError("Date is Empty");
       }


        return !$this->hasErrors();
    }

    public function fetchHoliday($id)
    {
        global $db;

        $sql = "SELECT * FROM ". $this->tableName ." WHERE id = '$id'";
        $result = $db->fetchRow($sql);
        return $result;
    }

    public function getUpComingHolidays()
    {
        global $db;
        $today = date('Y-m-d');
        $lastDay = date("Y-m-t",strtotime($today));

        $sql = "SELECT name,DATE_FORMAT(date, '%d-%m-%Y') as date FROM ". $this->tableName ." WHERE date BETWEEN'".$today."' AND '".$lastDay."' ORDER BY date ASC";
        $result = $db->fetchAll($sql);
        return $result;
    }

}
?>