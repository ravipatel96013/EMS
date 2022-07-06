<?php
class Admin_HolidaysController extends TinyPHP_Controller {
	
	public function indexAction()  
	{
		$this->disableFooter();
		$holidays = new Models_Holiday();
		$data = $holidays->showData();
		$this->setViewVar('data',$data);   
	}

	public function deleteAction()
    {
		if($this->isPost())
        {
            $status = 0;
            $errors = [];

			$this->setNoRenderer(true);

        $id = $this->getRequest()->getPostVar('id');
        $holiday = new Models_Holiday($id);
		if($holiday->isEmpty)
            {
                $errors[] = "Invalid holiday, it does not exist";
            }
            else
            {
                $holiday->delete();
				$deletedRows = $holiday->getDeletedRows();

                if($deletedRows > 0)
                {
                    $status = 1;
                }
                else
                {
                    $errors = $holiday->getErrors();
                }
            }
            
            $response = ["status" => $status, "errors" => $errors];
            echo json_encode($response);
            die; 

    	}
	}

    public function addAction()
    {
        if($this->isPost())
        {
            $status = 0;
            $errors = [];

			$this->setNoRenderer(true);

        $holiday = new Models_Holiday();
	
        $holiday->getPostData();
        $isCreated = $holiday->create();

        if( $isCreated == true )
        {
            $status = 1;
        }
        else
        {
            $errors = $holiday->getErrors();
        }
            
            $response = ["status" => $status, "errors" => $errors];
            echo json_encode($response);
            die; 

    	}
    }

    public function updateAction()
    {
        if($this->isPost())
        {   
            // POST Request

            $this->setNoRenderer(true);

            $status = 0;
            $errors = [];
            

            $holidayId = $this->getRequest()->getPostVar('id');
            $holiday = new Models_Holiday($holidayId);

            if($holiday->isEmpty)
            {
                $errors[] = "Invalid Holiday, it does not exist";
            }
            else
            {
                $holiday->getPostData();
                $isUpdated = $holiday->update();

                if( $isUpdated == true )
                {
                    $status = 1;
                }
                else
                {
                    $errors = $holiday->getErrors();
                }
            }
            
            $response = ["status" => $status, "errors" => $errors];
            echo json_encode($response);
            die;
        }
        else
        {
            // GET Request

            $id = $this->getRequest()->getVar('id');

            $holiday = new Models_Holiday();        
            $holidayData = $holiday->fetchHoliday($id);
            $this->setViewVar('dataRow',$holidayData);
        }
           
    }


}
?>