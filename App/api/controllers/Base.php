<?php
class Api_Controllers_Base extends TinyPHP_Controller 
{
    private $restRequest;

    public final function init() 
    {
        $this->setNoRenderer(true);
    }

    public function getRequestObj() 
    {
        return $this->restRequest;
    }
    
    public final function indexAction() 
    {
        $this->restRequest = TinyPHP_Libs_Rest_Utils::processRequest();
        switch ($this->restRequest->getMethod()) {
            
            case 'get':
                $this->getAction();
                break;
                
            case 'post':
                $this->addAction();
                break;
                
            case 'put':
                $this->updateAction();
                break;
                
            case 'delete':
               	$this->deleteAction();
               	break;
               	
            default:
                $body = json_encode(array('success'=> 0,'errors'=> array('The requested method is not implemented')));
                $this->sendresponse(501, $body, 'application/json');
        }
    }

    protected function getAction() 
    {
        $body = json_encode(array('success'=> 0,'errors'=> array('The requested method is not implemented')));
        $this->sendResponse(501, $body, 'application/json');
    }

    protected function addAction() 
    {
        $body = json_encode(array('success'=> 0,'errors'=> array('The requested method is not implemented')));
        $this->sendResponse(501, $body, 'application/json');
    }

    protected function updateAction() 
    {
        $body = json_encode(array('success'=> 0,'errors'=> array('The requested method is not implemented')));
        $this->sendResponse(501, $body, 'application/json');
    }
    
    protected function deleteAction() 
    {
        $body = json_encode(array('success'=> 0,'errors'=> array('The requested method is not implemented')));
    	$this->sendResponse(501, $body, 'application/json');
    }
    
    
    public function sendresponse($status, $body = '', $content_type = 'text/html') 
    {
    	ob_end_clean();	//clean any output before sending response.
    	
        TinyPHP_Libs_Rest_Utils::sendResponse($status, $body, $content_type);
    }
}

?>