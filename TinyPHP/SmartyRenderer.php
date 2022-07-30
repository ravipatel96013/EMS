<?php
require('Libs/Smarty/Smarty.class.php');
class TinyPHP_SmartyRenderer extends ViewRenderer{

	private $_smarty;
	private static $instance;

	private function __construct(){}

	final public static function getInstance(){
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}


	public function init(){
	    
		$this->_smarty = new Smarty();
		$this->_smarty->debugging = false;
		$this->_smarty->caching = false;
		$this->_smarty->compile_check = false;
		
		$this->_smarty->force_compile = true;
		/*
		if( strtoupper(ENV) === "PRODUCTION" )
		{
		    $this->_smarty->force_compile = false;
		}
		*/
		
		
		if(defined('VIEW_COMPILE_DIR'))
		{
			$this->_smarty->compile_dir = VIEW_COMPILE_DIR;
		}
		else
		{
			trigger_error("View compile dir not specified in application config",E_USER_WARNING);
		}
		if(defined('VIEW_CACHE_DIR'))
		{
			$this->_smarty->cache_dir = VIEW_CACHE_DIR;
		}
		else
		{
			trigger_error("View cache dir not specified in application config",E_USER_WARNING);
		}
		if(defined('VIEW_CONFIG_DIR'))
		{
			$this->_smarty->config_dir = VIEW_CONFIG_DIR;
		}
		else
		{
			trigger_error("View config dir not specified in application config",E_USER_WARNING);
		}
		
        $templateObj = TinyPHP_Template::getInstance();


        $this->_smarty->register_function('load_helper', array($templateObj,'getHelper'));

	}

	public function setCaching($_mode)
	{
		$this->_smarty->force_compile = !$_mode;
		$this->_smarty->caching = $_mode;
	}

	public function clearCache()
	{
		$this->_smarty->clear_all_cache();
	}

	public function renderView(){

		$this->_smarty->template_dir = $this->getViewDir();
		$viewVars = $this->getViewVars();
		foreach($viewVars as $var=>$val){
			$this->_smarty->assign($var,$val);
		}
		return $this->_smarty->fetch($this->getViewFile());

	}
	
	public function renderViewFragment($_viewFragmentDir, $_viewFragmentFile, $_viewFragmentVars)
	{
		$this->_smarty->template_dir = $_viewFragmentDir;
		$viewVars = $_viewFragmentVars;
		foreach($viewVars as $var=>$val){
			$this->_smarty->assign($var,$val);
		}
		return $this->_smarty->fetch($_viewFragmentFile);
		
	}
	
	public function renderLayout($_viewContent)
	{
		$this->_smarty->template_dir = $this->getLayoutDir();
		$this->_smarty->assign('viewContent',$_viewContent);
		return $this->_smarty->fetch($this->getLayout());
	}


	public function getRenderer()
	{
		return $this->_smarty;
	}
	
	public function registerHelper($_helper)
	{
            if($_helper->getHelperName() !='')
            {
                $this->_smarty->register_function($_helper->getHelperName(), array($_helper,'helperFunc'));
            }
            
	}

   /* public function registerObject($objectName, $object)
    {
        $this->_smarty->register_object($objectName,$object,null,false);

    }*/

   

	

	
	
}
?>