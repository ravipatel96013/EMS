<?php
class Plugins_AppSessionValidator {

	private $exemptedProperties = array();

	public function preDispatch(TinyPHP_Request $request)
	{
		$this->exemptedProperties['login'] = array('index', 'dologin');
		$this->exemptedProperties['resetpassword'] = array('index', 'sendlink', 'createpassword');
		$this->exemptedProperties['logout'] = array('index', 'success');
        $this->exemptedProperties['pagenotfound'] = array('index');

		

		if($request->getModuleName() == "app")
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
	    $id = getLoggedInUserId();

		if(empty($id) )
		{
	        $retUrl = $_SERVER['REQUEST_URI'];
	        header("Location: /app/login/?retURL=$retUrl");
	        exit();
		}
		else
		{
			$this->setVars();
		}
	}
	
	private function setVars()
	{
	    $loggedInUser = getLoggedInUser();
		
	    TinyPHP_Front::getInstance()->setPreDispatchVar('sessionUserId',$loggedInUser->id);
	    TinyPHP_Front::getInstance()->setPreDispatchVar('sessionUserName',$loggedInUser->firstName);
	}
}
?>