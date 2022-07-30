<?php
class Plugins_AdminSessionValidator {

	private $exemptedProperties = array();

	public function preDispatch(TinyPHP_Request $request)
	{
		$this->exemptedProperties['login'] = array('index', 'dologin');
		//$this->exemptedProperties['resetpassword'] = array('index', 'sendresetlink', 'changepassword', 'updatepassword');
		$this->exemptedProperties['logout'] = array('index', 'success');
        //$this->exemptedProperties['unsubscribe'] = array('index');

		

		if($request->getModuleName() == "admin")
		{
			//$this->setVars();
				
			 $currentController = $request->getControllerName();
			 $currentAction = $request->getActionName();
			
			if(array_key_exists($currentController,$this->exemptedProperties))
			{
				if(in_array($currentAction,$this->exemptedProperties[$currentController]))
				{
					return;
				}
				else
				{					
					$this->validateSession();
				}

			}
			else
			{
				$this->validateSession();
			}
		}
	}


	private function validateSession()
	{
	    $id = getLoggedInAdminId();

		if(empty($id) )
		{
	        $retUrl = $_SERVER['REQUEST_URI'];
	        header("Location: /admin/login/?retURL=$retUrl");
	        exit();
		}
		else
		{
			$this->setVars();
		}
	}
	
	private function setVars()
	{
	    $loggedInAdmin = getLoggedInAdmin();
		
	    TinyPHP_Front::getInstance()->setPreDispatchVar('sessionAdminId',$loggedInAdmin->id);
	    TinyPHP_Front::getInstance()->setPreDispatchVar('sessionAdminName',$loggedInAdmin->firstName);
	}
}
?>