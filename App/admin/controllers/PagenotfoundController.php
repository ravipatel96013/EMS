<?php
class Admin_PagenotfoundController extends TinyPHP_Controller{
	
    public function init(){}

    public function indexAction()
    {
        $this->disableHeader();
        $this->disableFooter();
    }
	
}
?> 

