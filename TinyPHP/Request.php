<?php
class TinyPHP_Request
{

	private static $instance;
	//@@TODO: inject the values from Front;
	private $requestURI;
	private $uriParts;
	private $moduleName;
	private $controllerName;
	private $actionName;
	private $params;
	private $getVars;
	private $postVars;
	private $requestVars;
        private $headers;

	private function __construct() {
	}
	
	
	/**
	 * returns singleton TinyPHP_Request object
	 * 
	 * @return TinyPHP_Request
	 * 
	 */

	public static function getInstance() {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}

	public function getModuleName() {
		return $this->moduleName;
	}

	public function setModuleName($_moduleName) {
		$this->moduleName = $_moduleName;
	}

	public function getControllerName() {
		return $this->controllerName;
	}

	public function setControllerName($_controllerName) {
		$this->controllerName = $_controllerName;
	}

	public function getActionName() {
		return $this->actionName;
	}

	public function setActionName($_actionName) {
		$this->actionName = $_actionName;
	}

	public function getParams() {
		return $this->params;
	}

	public function setParams($_params = array()) {
		$this->params = $_params;
	}

	public function getURIParts() {
		return $this->uriParts;
	}

	public function getParam($_param, $_type = '', $_default = '') {

		if (isset($this->params[$_param])) {

			if (empty($_type)) {
				if(is_array($this->params[$_param]) || is_object($this->params[$_param]))
				{
					return $this->params[$_param];
				}
				else
				{
					if (trim($this->params[$_param]) == '')
						return $_default;
					else
						return $this->params[$_param];
				}
			}
			else {
				$typeMatches = false;
				if ('string' == $_type) {
					if (is_string($this->params[$_param]))
						$typeMatches = true;
				}
				elseif ('int' == $_type) {
					if (is_int($this->params[$_param]))
						$typeMatches = true;
				}
				elseif ('numeric' == $_type) {
					if (is_numeric($this->params[$_param]))
						$typeMatches = true;
				}

				if ($typeMatches)
					return $this->params[$_param];
				else
					return $_default;
			}
		}
		else
		{
			return $_default;
		}
	}

	public function getVar($var, $_type = '', $_default = '') {

		if (isset($_GET[$var])) {

			if (empty($_type)) {
				if (is_array($_GET[$var])) {
					return $_GET[$var];
				}

				if (trim($_GET[$var]) == '')
					return $_default;
				else
					return $_GET[$var];
			}
			else {
				$typeMatches = false;
				if ('string' == $_type) {
					if (is_string($_GET[$var]))
						$typeMatches = true;
				}
				elseif ('int' == $_type) {
					if (is_int($_GET[$var]))
						$typeMatches = true;
				}
				elseif ('numeric' == $_type) {
					if (is_numeric($_GET[$var]))
						$typeMatches = true;
				}

				if ($typeMatches)
					return $_GET[$var];
				else
					return $_default;
			}
		}
		else
		{
			return $_default;
		}
	}

	public function getPostVar($var, $_type = '', $_default = '') {
		if (isset($_POST[$var])) {

			if (empty($_type)) {
				if (is_array($_POST[$var])) {
					return $_POST[$var];
				}

				if (trim($_POST[$var]) == '')
					return $_default;
				else
					return $_POST[$var];
			}
			else {
				$typeMatches = false;
				if ('string' == $_type) {
					if (is_string($_POST[$var]))
						$typeMatches = true;
				}
				elseif ('int' == $_type) {
					if (is_int($_POST[$var]))
						$typeMatches = true;
				}
				elseif ('numeric' == $_type) {
					if (is_numeric($_POST[$var]))
						$typeMatches = true;
				}
				elseif ('array' == $_type) {
					if (is_array($_POST[$var]))
						$typeMatches = true;
				}

				if ($typeMatches)
					return $_POST[$var];
				else
					return $_default;
			}
		}
		else
		{
			return $_default;
		}
	}
        
        public function getHeader($_param, $_type = '', $_default = '') {
		if (isset($this->headers[$_param])) {

			if (empty($_type)) {
				if(is_array($this->headers[$_param]) || is_object($this->headers[$_param]))
				{
					return $this->headers[$_param];
				}
				else
				{
					if (trim($this->headers[$_param]) == '')
						return $_default;
					else
						return $this->headers[$_param];
				}
			}
			else {
				$typeMatches = false;
				if ('string' == $_type) {
					if (is_string($this->headers[$_param]))
						$typeMatches = true;
				}
				elseif ('int' == $_type) {
					if (is_int($this->headers[$_param]))
						$typeMatches = true;
				}
				elseif ('numeric' == $_type) {
					if (is_numeric($this->headers[$_param]))
						$typeMatches = true;
				}

				if ($typeMatches)
					return $this->headers[$_param];
				else
					return $_default;
			}
		}
		else
		{
			return $_default;
		}
	}
        
        public function getJsonParam($_param, $_type = '', $_default = '') {
            $jsonParam = array();
            if (strtolower($_SERVER['CONTENT_TYPE']) == "application/json" || strtolower($_SERVER['CONTENT_TYPE']) == "application/json; charset=utf-8"){
                $json = json_decode(file_get_contents("php://input"), true);
                if(is_array($json)){
                   $jsonParam = $json;
                }
            }

            if (isset($jsonParam[$_param])) {

                if (empty($_type)) {
                    if(is_array($jsonParam[$_param]))
                    {
                        return $jsonParam[$_param];
                    }
                    else
                    {
                        if (trim($jsonParam[$_param]) == '')
                            return $_default;
                        else
                            return trim($jsonParam[$_param]);
                    }
                }
                else {
                    $typeMatches = false;
                    if ('string' == $_type) {
                        if (is_string($jsonParam[$_param]))
                            $typeMatches = true;
                    }
                    elseif ('int' == $_type) {
                        if (is_int($jsonParam[$_param]))
                            $typeMatches = true;
                    }
                    elseif ('numeric' == $_type) {
                        if (is_numeric($jsonParam[$_param]))
                            $typeMatches = true;
                    }

                    if ($typeMatches)
                        return trim($jsonParam[$_param]);
                    else
                        return $_default;
                }
            }
            else
            {
                return $_default;
            }
	}

	public function getPost() {
		if ($_SERVER['REQUEST_METHOD'] == "POST")
			return $_POST;
	}

	public function getGet() {
		if ($_SERVER['REQUEST_METHOD'] == "GET")
			return $_GET;
	}
        
        public function getJson(){
            if ($_SERVER['CONTENT_TYPE'] == "application/json"){
                $json = json_decode(file_get_contents("php://input"), true);
                if(is_array($json)){
                    return $json;
                }
            }
            return array();
        }
        
        public function getHeaders(){
            if($this->headers){
                return $this->headers;
            }
            return array();
        }

        public function isPost() {
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			return true;
		} else {
			return false;
		}
	}
	
	public function isDelete()
	{
	    if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
	        return true;
	    } else {
	        return false;
	    }
	}

	public function setupRequest($modules, $_extraParams = array(), $_routing=false) {
		$this->sanitizeRequest();

		if (empty($this->requestURI))
			$this->requestURI = $_SERVER['REQUEST_URI'];

		if ($pos = strpos($this->requestURI, "?")) {
			$path = substr($this->requestURI, 0, $pos);
		} else {
			$path = $this->requestURI;
		}


		//remove leading and trailing slashes if any.
		$path = $this->removeEndSlashes($path);

		$pathParts = array();
		if ($path != "") {
			$pathParts = explode("/", $path);
			$this->uriParts = $pathParts;
		}
        
		
		$route = false;
		if( $this->uriParts )
		{
		    // Give first priority to overwritten urls
		    $router = TinyPHP_Router::getInstance();
		    $route = $router->matchRoute($this->uriParts);
		}
		
		
		if( $route !== FALSE || $_routing )
		{
		    // routing
		    if ( $route !== FALSE ) {
                $router->convertRoute($route, $this);
		    }
	        
	        $paramVars = $this->getParams();
	        if (!empty($paramVars)) {
	            array_walk_recursive($paramVars, array($this, 'doSanitization'));
	        }
            
	        $this->setParams($paramVars);
		}
		else
		{
		    //get module dir
		    $step = 0;
		    $this->moduleName = (isset($pathParts[$step])) ? $pathParts[$step] : "";
		    if (in_array($this->moduleName, $modules)) {
		        $this->moduleName = $pathParts[$step++];
		    } else {
		        $this->moduleName = "Default";
		    }
		    
		    //get controller dir
		    $this->controllerName = (isset($pathParts[$step])) ? $pathParts[$step++] : "index";
		    
		    //get action
		    $this->actionName = (isset($pathParts[$step])) ? $pathParts[$step++] : "index";
		    
		    //params
		    $params = array_slice($pathParts, $step);
		    $paramVars = array();
		    
		    if (!empty($params)) {
		        $i = 0;
		        while ($i < count($params)) {
		            if ($params[$i] != "") {
		                $paramVars[$params[$i]] = (isset($params[$i + 1])) ? $params[++$i] : "";
		            }
		            $i++;
		        }
		    }
		    
		    //extra parameters that may be supplied by forward action
		    if(is_array($_extraParams))
		    {
		        foreach($_extraParams as $key=>$val)
		        {
		            $paramVars[$key] = $val;
		        }
		    }
		    $this->setParams($paramVars);
		    
		}
		
        // headers
        $this->headers = getallheaders();

	}

	public function sanitizeRequest() {
		array_walk_recursive($_POST, array($this, "doSanitization"));
		array_walk_recursive($_GET, array($this, "doSanitization"));
		array_walk_recursive($_REQUEST, array($this, "doSanitization"));
	}

	private function doSanitization(& $item) {
		/*if (get_magic_quotes_gpc() == 0) {*/
			$item = addslashes($item);
		/*}*/

		$allowedTags = '<h1><h2><h3><h4><h5><h6><b><div><span><font><p><i><a><ul><ol><table><tr><td><li><pre><hr><blockquote><img><strong><br><small>';
		$item = strip_tags($item, $allowedTags);
	}

	private function removeEndSlashes($_path) {
		$path = $_path;
		if ($path != "") {
			if (substr($path, 0, 1) == "/") {
				$path = substr($path, 1, strlen($path));
			}
			if (substr($path, strlen($path) - 1, 1) == "/") {
				$path = substr($path, 0, strlen($path) - 1);
			}
		}
		return $path;
	}

}

?>