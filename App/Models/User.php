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
    public $designation = "";
    public $joinDate = "";
    public $address = "";
    public $city = "";
    public $state = "";
    public $profileImage = "";
    public $isActive = 1;
    public $role = '';
    public $confirmPassword = "";
    public $createdOn = "";
    public $updatedOn = "";
    public $updatePassword = true;

    private $oldPassword =  false;

    public $dbIgnoreFields = array('id','confirmPassword','oldPassword','createdOn','updatedOn','profileImage','updatePassword');

    public function init()
    {
    
        if($this->id>0)
        {
            $this->confirmPassword = $this->password;
            $this->oldPassword = $this->password;
        }

        $this->addListener('beforeCreate', array($this,'doBeforeCreate'));
        $this->addListener('beforeUpdate', array($this,'doBeforeUpdate'));
    }


    protected function doBeforeCreate()
    {
        if($this->validate())
        {
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


    protected function doBeforeUpdate()
    {
        if($this->validate())
        {
            if($this->updatePassword == true)
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

    public function validate()
    {
        $this->validateUserInfo();
        $this->validatePassword();

        //$this->validateLoginInfo();

        return !$this->hasErrors();
    }

    public function validateLoginInfo()
    {
        if($this->username == "")
        {
            $this->addError("Username is required");
        }
        else
        {
            if(!$this->isUniqueUsername($this->username,$this->id))
            {
                $this->addError("Username '". $this->username ."' is already in use");
            }
        }

        if( $this->role == "" ) 
        {
            $this->addError("User role is required");
        }

        if($this->_getCurrentAction() == "create")
        {
            $this->validatePassword();
        }
        elseif($this->_getCurrentAction() == "update")
        {
            if($this->password!="")
            {
                $this->validatePassword();
            }
        }
        
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