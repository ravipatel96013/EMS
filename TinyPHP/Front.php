<?php
require('Loader.php');
class TinyPHP_Front {

	private $currentControllerDir;
	private $applicationDir;
	private $directories;
	private $dbConnectAttempt = 0;
	private $_plugins;
	private $_viewHelperBroker;
	private static $instance;
	private static $_controllerObj;
	private static $_viewObj;
	private static $_loaderObj;
	private static $_layoutObj;
	private static $_requestObj;
	private $_predispatchVars = array();
	private $noRenderer = false;
	private $layoutEnabled = true;
	private static $_exceptionObj = null;
	private $reroute = false;

	private function __construct() 
	{
		$pathToFront = dirname(__FILE__);
		set_include_path(get_include_path() . PATH_SEPARATOR . $pathToFront);

		$this->directories = array();
		self::$_loaderObj = new Loader();
		self::$_layoutObj	= TinyPHP_Layout::getInstance();
		self::$_requestObj = TinyPHP_Request::getInstance();
		$this->_plugins = new TinyPHP_PluginBroker();
	}

	
	public static function getInstance() 
	{
		if (!isset(self::$instance)) 
		{
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}

	
	public function __clone() 
	{
		trigger_error('Clone is not allowed.', E_USER_ERROR);
	}

	public function setApplicationDirectory($_appDir) 
	{
		$this->applicationDir = str_replace('\\', '/', $_appDir);
		$this->applicationDir = $this->removeTrailingSlashes($this->applicationDir);
	}

	public function getApplicationDirectory() 
	{
		return $this->applicationDir;
	}

	public function setControllerDirectory($_directories) 
	{
		$this->directories = $_directories;
	}

	public function setNoRenderer($_noRenderer) 
	{
		$this->noRenderer = $_noRenderer;
	}

	public function disableLayout($_disable) 
	{
		$this->layoutEnabled = (true == $_disable) ? false : true;
	}

	public function isLayoutEnabled() 
	{
		return $this->layoutEnabled;
	}

	public static function setLayout($module_name, $layout_dir, $layout_file = null) 
	{
		TinyPHP_Layout::getInstance()->setLayout($module_name, $layout_dir, $layout_file);
	}

	public static function getLayoutObject() 
	{
		return TinyPHP_Layout::getInstance();
	}

	public static function getException() 
	{
		return self::$_exceptionObj;
	}

	public static function getControllerObject() 
	{
		return self::$_controllerObj;
	}

	public static function getViewObject() 
	{
		return self::$_viewObj;
	}

	public function registerPlugin($_plugin, $stack_index = null) 
	{
		$this->_plugins->registerPlugin($_plugin, $stack_index);
	}

	public function registerViewHelper($_helper, $stack_index = null) 
	{
		if (!is_object($this->_viewHelperBroker))
		{
		    $this->_viewHelperBroker = TinyPHP_ViewHelperBroker::getInstance();
		}
			
		$this->_viewHelperBroker->registerHelper($_helper, $stack_index);
	}

	public function setPreDispatchVar($_varName, $_value) 
	{
		$this->_predispatchVars[$_varName] = $_value;
	}

	public function getPreDispatchVars($_varname = "") 
	{
		if ($_varname == "") 
		{
			return $this->_predispatchVars;
		}
		else 
		{
			if (isset($this->_predispatchVars[$_varname])) 
			{
				return $this->_predispatchVars[$_varname];
			}
		}
	}
	
	public function resetReRouting()
	{
		$this->reroute = false;
	}

	public function dispatch($_moduleName = "", $_controllerName = "", $_actionName = "", $_extraParams = array()) 
	{
	    
	    global $db, $dataCache, $siteConfig;
		
		self::$_requestObj->setupRequest(array_keys($this->directories), $_extraParams, $this->reroute);

		
		/* module name */
		$moduleName = (empty($_moduleName)) ? self::$_requestObj->getModuleName() : $_moduleName;
		

		/* controller name */
		$controllerName = (empty($_controllerName)) ? self::$_requestObj->getControllerName() : $_controllerName;

		
		/* action name */
		$actionName = (empty($_actionName)) ? self::$_requestObj->getActionName() : $_actionName;

		
		/* requset parameters */
		$params = self::$_requestObj->getParams();


		/* Initialize View Object */
		$viewObj = TinyPHP_View::getInstance();
		self::$_viewObj = $viewObj;


		/* Default controller directory */ 
		$this->currentControllerDir = "/controllers";
		if (array_key_exists($moduleName, $this->directories)) 
		{
			$this->currentControllerDir = "/" . $this->removeEndSlashes($this->directories[$moduleName]);
		}
		
		/* prepend root application dir */
		$this->currentControllerDir = $this->applicationDir . $this->currentControllerDir; 

		/* Path info(Module, Controller, Action) */
		$pathInfo = array('module' => $moduleName, 'controller' => $controllerName, 'controllerDir' => $this->currentControllerDir, 'action' => $actionName, 'params' => $params);
		
		
		/* Controller Class Name */
		$controllerClassName = "";
		$bareControllerClassName = ucfirst($controllerName) . "Controller";
		if ($moduleName != "Default") 
		{
			$controllerClassName = ucfirst($moduleName) . "_";
		}
		$controllerClassName .= $bareControllerClassName;

		
		/* include the controller file */
		$controllerFile = $this->currentControllerDir;
		$controllerFile .= "/" . ucfirst($controllerName) . "Controller.php"; #controller file

		try {
			
			if($this->dbConnectAttempt === 0 && DB_HOST !='' && DB_UNAME != '' && DB_PWD != '')
            {		
            
                $this->dbConnectAttempt =  1;					
					
				$db = TinyPHP_DB::factory(DB_HOST, DB_UNAME,DB_PWD,DB_NAME,'Zend_Db','Pdo_Mysql');

					
                # set msyql time zone (to sync with php)					
				if(defined('DB_TIMEZONE_STRING') && DB_TIMEZONE_STRING !='')
				{
				    $db->query('SET @@session.time_zone ="'. DB_TIMEZONE_STRING .'";');					
				}
					
				$dataCache = Models_SQLCache::getInstance();
					
				#configs
				$siteConfig = new Models_SiteConfig();	
            }
			
            
			if (file_exists($controllerFile)) 
			{
				require_once($controllerFile);

				if (class_exists($controllerClassName, FALSE)) 
				{
					$controllerObj = new $controllerClassName;
					
					$_actionName = $actionName . "Action";

					if (!is_subclass_of($controllerObj, 'TinyPHP_Controller')) 
					{
						trigger_error('Controller must be of type TinyPHP_Controller', E_USER_ERROR);
					}
					
					if (method_exists($controllerObj, $_actionName) && is_callable(array($controllerObj, $_actionName))) 
					{
						self::$_controllerObj = $controllerObj;

						$this->resetReRouting();
						
						/* run the registered plguins */
						$this->_plugins->preDispatch(self::$_requestObj);

						if (method_exists($controllerObj, 'init')) 
						{
							call_user_func(array($controllerObj, 'init')); #call action
						}
						
						/* register any view helpers */
						if (is_object($this->_viewHelperBroker) && count($this->_viewHelperBroker->getHelpers())>0) 
						{
							$this->_viewHelperBroker->registerToRenderer($viewObj->getViewRenderer());
						}

						$layoutObj = TinyPHP_Layout::getInstance();

						//registered early so that view fragments can also acccess these view vars if needed.
						$viewObj->setViewVar('pathInfo', $pathInfo);
						$viewObj->setViewVars($this->getPreDispatchVars());  #set the var values from the pre-dispatch plugins							

						call_user_func(array($controllerObj, $_actionName)); #call action

						if (false == $this->noRenderer) 
						{
							$layoutDir = $this->applicationDir . $layoutObj->getLayoutDir($moduleName);
							$layout = $layoutObj->getLayoutFile($moduleName);

							if ($viewObj->getViewDir() == "") #detect view directory based on current module,controller and action
							{ 
								$viewDir = $this->applicationDir;
								if ($moduleName != "Default") 
								{
									$viewDir .= "/" . $moduleName;
								}

								/* setup view directories and files */
								$viewDir .= "/views/scripts/" . $controllerName;
							}
							else
							{
								$viewDir = $viewObj->getViewDir();
							}

							if ($viewObj->getViewFile() == "") 
							{
								$viewFile = $actionName . ".html";
							}
							else
							{
								$viewFile = $viewObj->getViewFile() . ".html";
							}

							if (false == $this->layoutEnabled || file_exists($layoutDir . "/" . $layout))
							{
								if ($this->isLayoutEnabled()) 
								{
									$viewObj->setLayoutDir($layoutDir);
									$viewObj->setLayout($layout);
								}
								
								if (file_exists($viewDir . "/" . $viewFile))
								{
									$viewObj->setViewDir($viewDir);
									$viewObj->setViewFile($viewFile);
									$viewObj->render();
								}
								else
								{
									trigger_error("Required View file ($viewFile) not found at $viewDir", E_USER_ERROR);
								}
							}
							else
							{
								trigger_error("Required Layout file ($layout) not found at $layoutDir", E_USER_ERROR);
							}
						}
					}
					else
					{
						if($this->reroute == false)
						{
							$this->reroute = true;
							$this->dispatch();
						}
						else
						{
							$this->reroute = true;
                            $this->dispatch("{$moduleName}", "pagenotfound", "index", array());
						}
					}
					
				}
				else
				{
					trigger_error("Unable to load class: $controllerClassName", E_USER_ERROR);
				}
			}
			else
			{

				if($this->reroute == false)
				{
					$this->reroute = true;
					$this->dispatch();
				}
				else
				{
				    $this->reroute = true;
				    $this->dispatch("{$moduleName}", "pagenotfound", "index", array());
				}

			}
		}
		catch (TinyPHP_Exception $e)
		{
			$this->handleException($e);
		}
		catch (Exception $e)
		{
		 
		  $this->handleException($e);
		}
	}
	
	
	private function handleException($e)
	{
		$err_info = "";
		$err_info.= "An exception has occured:\n";
		$err_info.= "File: " . $e->getFile() . "\n";
		$err_info.= "Line: " . $e->getLine() . "\n";
		$err_info.= "Message: \n" . $e->getMessage() . "\n\n";
		$err_info.= "Back Trace: \n" . $e->getTraceAsString() . "\n";
		$err_info.= "Module: " . TinyPHP_Request::getInstance()->getModuleName() . "\n\n";
		$err_info.= "Controller: " . TinyPHP_Request::getInstance()->getControllerName() . "\n";
		$err_info.= "Action: " . TinyPHP_Request::getInstance()->getActionName() . "\n";
		$err_info.= "Params: " . print_r(TinyPHP_Request::getInstance()->getParams(), true) . "\n";
		$err_info.= "GET: " . print_r(TinyPHP_Request::getInstance()->getGet(), true) . "\n";
		$err_info.= "POST: " . print_r(TinyPHP_Request::getInstance()->getPost(), true) . "\n";

		if (!empty($_SESSION))
			$err_info.= "Session: " . print_r($_SESSION, true) . "\n";
		
		$e->dump = $err_info;
		self::$_exceptionObj = $e;

		$this->setNoRenderer(false);

		//check for controllers.
		if (file_exists($this->currentControllerDir . "/ErrorController.php"))
		{
			$this->dispatch(null, 'error', 'error');
			return;
		}
		elseif (file_exists($this->applicationDir . "/controllers/ErrorController.php"))
		{
			$this->dispatch('Default', 'error', 'error');
			return;
		}
		else
		{
			echo "<pre>";
			echo $err_info;
			echo "</pre>";
		}
	}
	
	private function removeEndSlashes($_path)
	{
		$path = $_path;
		if ($path != "")
		{
			if (substr($path, 0, 1) == "/")
			{
				$path = substr($path, 1, strlen($path));
			}
			
			if (substr($path, strlen($path) - 1, 1) == "/")
			{
				$path = substr($path, 0, strlen($path) - 1);
			}
		}
		
		return $path;
	}

	private function removeTrailingSlashes($_path)
	{
		$path = $_path;
		if ($path != "")
		{
			if (substr($path, strlen($path) - 1, 1) == "/")
			{
				$path = substr($path, 0, strlen($path) - 1);
			}
		}
		
		return $path;
	}
}
?>