<?php
class Models_Resetpassword extends TinyPHP_ActiveRecord
{
    public $tableName = "reset_password";
    public $id = "";
    public $userId = "";
    public $hashKey = "";
    public $isActive = 0;
    public $createdOn = "";

    public $dbIgnoreFields = array('id');

    public function init()
    {
        $this->addListener('beforeCreate', array($this,'doBeforeCreate'));
        $this->addListener('afterCreate', array($this,'doAfterCreate'));
    }

    protected function doBeforeCreate()
    {
        $this->start_transaction();

        global $db;
        $db->update('reset_password',['isActive'=>0],'userId='.$this->userId);

        $time = time();
        $this->createdOn = $time;
        return true;
    }

    protected function doAfterCreate()
    {
        $commit = 0;

        $user = new Models_User($this->userId);
        $mailer = new Helpers_Mailer();
        $from = 'sample@gmail.com';
        $to = $user->email;
        $subject = 'Reset Password';
        $body = "You Can Reset Your Password Using This Link : http://local.ems.com/app/resetpassword/createpassword?hashKey=".$this->hashKey;
        $isSent = $mailer->sendMail($from,$to,$subject,$body);
        if($isSent)
        {
            $commit = 1;
        }
        else
        {
        $this->addError($mailer->getErrors());
        }
        
        if($commit == 1)
        {
            $this->commit();
        }
        else{
            $this->rollback();
        }
    }

}
?>