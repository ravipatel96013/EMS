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
    }

    protected function doBeforeCreate()
    {
        $time = time();
        $this->createdOn = $time;

        return true;
    }

}
?>