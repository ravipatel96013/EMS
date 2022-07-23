<?php
class Models_User extends TinyPHP_ActiveRecord
{
    public $tableName = "users";
    public $id ="";
    public $email = "";
    public $password = "";
    public $firstName = "";
    public $lastName = "";
    public $phone = "";
    public $mobile= "";
    public $designation = "";
    public $joinDate = "";
    public $address = "";
    public $city = "";
    public $state = "";
    public $profileImage = "";
    public $isActive = 1;
    public $role = '';
    public $gender = '';
    public $dateOfBirth = '';
    public $emgContactNo = '';
    public $emgContactName = '';
    public $voterId = '';
    public $panId = '';
    public $aadharId = '';
    public $licenseNumber = '';
    public $bloodGroup = '';
    public $personalEmail = '';
    public $maritialStatus = '';
    public $bank = '';
    public $namePerBank = '';
    public $bankAcNumber = '';
    public $bankIFSC = '';
    public $confirmPassword = "";
    public $createdOn = "";
    public $updatedOn = "";


    public $dbIgnoreFields = array('id','confirmPassword','profileImage');

    public function init()
    {
    
        if($this->id>0)
        {
            $this->confirmPassword = $this->password;
        }

        $this->addListener('beforeCreate', array($this,'doBeforeCreate'));
        $this->addListener('beforeUpdate', array($this,'doBeforeUpdate'));
        $this->addListener('afterCreate', array($this,'doAfterCreate'));
    }


    protected function doBeforeCreate()
    {
        if($this->validate())
        {
            $this->createdOn = time();
            $this->updatedOn = time();

            if($this->password != "")
            {
                $this->password = md5($this->password);
            }
            return true;
        }
        else
        {
            return false;
        }
    }

    protected function doAfterCreate()
    {
        $attendance = new Models_Attendance();
        $where = 'userId='.$this->id;
        $data = $attendance->getAll(['id'],$where);
        if(empty($data))
        {
        $currentMonth = date('m');    
        $currentYear = date('Y');
        $nextMonth = 0;
        if($currentMonth == 12)
        {
            $nextMonth = 1;
        }
        else
        {
            $nextMonth = $currentMonth+1;
        }

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN,$nextMonth,$currentYear);
        for($i=1;$i<=$daysInMonth;$i++)
        {
            $attd = new Models_Attendance();
            $attd->userId = $this->id;
            $attd->date = $currentYear."-".$nextMonth."-".$i;
            $attd->create();
        }
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
        $this->validateUserInfo();
        $this->validatePassword();

        //$this->validateLoginInfo();

        return !$this->hasErrors();
    }

    private function validatePassword()
    {
        if($this->password=="")
        {
            $this->addError("Password is required");
        }
        else
        {
            if(strlen($this->password)<6)
            {
                $this->addError("Password must be minimum six characters long");
            }
            if($this->password != $this->confirmPassword)
            {
                $this->addError("Entered passwords do not match");
            }
        }


        return !$this->hasErrors();
    }

    public function validateUserInfo()
    {   
        if($this->firstName == "")
        {
            $this->addError("First name is required");
        }

        if($this->lastName == "")
        {
            $this->addError("Last name is required");
        }

        if($this->phone == "")
        {
            $this->addError("Phone number is blank");
        }
        else{
            if(!preg_match('/^[0-9]{10}+$/', $this->phone))
            {
                $this->addError("Enter Valid Phone Number");
            }

        }
        if($this->mobile == "")
        {
            $this->addError("Mobile number is blank");
        }
        else{
            if(!preg_match('/^[0-9]{10}+$/', $this->mobile))
            {
                $this->addError("Enter Valid Phone Number");
            }

        }

        if($this->emgContactNo == "")
        {
            $this->addError("Emergency Contact number is blank");
        }
        else{
            if(!preg_match('/^[0-9]{10}+$/', $this->emgContactNo))
            {
                $this->addError("Enter Valid Emergency Contact Number");
            }

        }

        if($this->address == "")
        {
            $this->addError("Address is Blank");
        }

        if($this->city == "")
        {
            $this->addError("City is Blank");
        }

        if($this->state == "")
        {
            $this->addError("State is Blank");
        }

        if($this->designation == "")
        {
            $this->addError("Designation is Blank");
        }

        if($this->email == "")
        {
            $this->addError("Email is Blank");
        }
        else{
            if(!filter_var($this->email, FILTER_VALIDATE_EMAIL))
            {
                $this->addError("Enter Valid Email");
            }
        }

        if($this->joinDate == "")
        {
            $this->addError("Join Date is Empty");
        }

        if($this->gender == "")
        {
            $this->addError("Gender is Empty");
        }

        if($this->dateOfBirth == "")
        {
            $this->addError("Date of Birth is Empty");
        }

        if($this->emgContactName == "")
        {
            $this->addError("Emergency Contact Name is Empty");
        }

        if($this->aadharId == "")
        {
            $this->addError("Aadhar Number is Empty ");
        }
        else{
            if(!preg_match('/^[0-9]{12}+$/', $this->aadharId))
            {
                $this->addError("Invalid Aadhar Number");
            }
        }
        
        if($this->bloodGroup == "")
        {
            $this->addError("Blood Group is Empty");
        }

        if($this->personalEmail == "")
        {
            $this->addError("Personal Email is Blank");
        }
        else{
            if(!filter_var($this->personalEmail, FILTER_VALIDATE_EMAIL))
            {
                $this->addError("Enter Valid Personal Email");
            }
        }

        return !$this->hasErrors();
    }


    public function isUniqueUsername($_username,$id)
    {
        global $db;

        $sql = "SELECT count(username) FROM ". $this->tableName ." WHERE username = '$_username'";
        if($id)
        {
            $sql .= " AND id <> ". $id;
        }

        $count = $db->fetchOne($sql);
        if($count==0)
        {
            return true;
        }
        else{
            return false;
        }
    }

    public function checkCred($email,$password)
    {
        global $db;

        $sql = "SELECT * FROM ". $this->tableName ." WHERE email = '$email' AND password = '$password'";
        $result = $db->fetchRow($sql);
        if(!$result == '')
        {
            return $result;
        }
    }
    
    public function fetchUsers()
    {
        global $db;

        $sql = "SELECT * FROM ". $this->tableName;
        $result = $db->fetchAll($sql);
        return $result;
    }

    public function fetchUser($id)
    {
        global $db;

        $sql = "SELECT * FROM ". $this->tableName ." WHERE id = '$id'";
        $result = $db->fetchRow($sql);
        return $result;
    }

}
?>