<?php
class App_HolidaysController extends TinyPHP_Controller {
	
	public function indexAction()  
	{
		$holidays = new Models_Holiday();
		$data = $holidays->showData();
		$this->setViewVar('data',$data);   
	}
}