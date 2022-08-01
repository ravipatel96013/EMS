<?php
class Models_User extends TinyPHP_ActiveRecord
{
    public $tableName = "users";
    public $id ="";
    public $email = "";
    public $password = "";
    public $firstName = "";
    public $lastName = "";
    public $mobile= "";
    public $designation = "";
    public $joinDate = "";
    public $address = "";
    public $city = "";
    public $state = "";
    public $profileImage = "";
    public $isActive = 0;
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
        $this->addListener('beforeCreate', array($this,'doBeforeCreate'));
        $this->addListener('beforeUpdate', array($this,'doBeforeUpdate'));
        $this->addListener('afterCreate', array($this,'doAfterCreate'));
        $this->addListener('afterUpdate', array($this,'doAfterUpdate'));
    }


    protected function doBeforeCreate()
    {
        if($this->validate())
        {
            if($this->validatePassword())
            {
                $this->password = md5($this->password);
                $this->createdOn = time();
                $this->updatedOn = time();
                return true;
            }
        }
        else
        {
            return false;
        }
    }

    protected function doAfterCreate()
    {
        if($this->isActive == 1)
        {
        $attendance = new Models_Attendance();
        $where = 'userId='.$this->id;
        $data = $attendance->getAll(['id'],$where);
        if(empty($data))
        {
            $year = date('Y');
            $month = date('m');
            $service = new Service_Attendance();
            $service->addAttendance($year,$month,$this->id);
        }
    }
    }


    protected function doBeforeUpdate()
    {
        if($this->validate())
        {
            if($this->password == "")
            {
                array_push($this->dbIgnoreFields,'password');
                return true;
            }
            else{
                if($this->validatePassword())
                {
                    $this->password = md5($this->password);
                    $this->updatedOn = time();
                    return true;
                }
            }
        }
        else
        {
            return false;
        }
    }

    protected function doAfterupdate()
    {
        if($this->isActive == 1)
        { 
           $currentMonth = date('m');
           $currentYear = date('Y'); 
           $attd = new Models_Attendance();
           $attendace = $attd->getAll(['id'], "userId={$this->id} AND MONTH(date)={$currentMonth} AND YEAR(date)={$currentYear}");
           if(empty($attendace))
           {
            $service = new Service_Attendance();
            $service->addAttendance($currentYear,$currentMonth,$this->id);
           }
        }
    }

    public function validate()
    {
        $this->validateUserInfo();

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

        if($this->mobile == $this->emgContactNo)
        {
            $this->addError("Mobile Number and Eergency Contact Number Can Not Be Same");
        }

        if($this->email == $this->personalEmail)
        {
            $this->addError("E-mail and Personal E-mail Can Not Be Same");          
        }

        return !$this->hasErrors();
    }
}
?>