<?php
class App_HolidaysController extends TinyPHP_Controller {
	
	public function indexAction()  
	{
		  
	}

	public function holidaylistAction()
    {
        $this->setNoRenderer(true);

        global $db;
        $dt = new TinyPHP_DataTable();
	    $dt->setDBAdapter($db);
        $dt->setTable('holidays AS h');
        $dt->setIdColumn('h.id');

        $dt->addColumns(array(
            'name' => 'h.name',
            'description' => 'h.description',
            'date' => 'DATE_FORMAT(h.date, "%d-%m-%Y")'
        ));
        $dt->getData();
    }
}