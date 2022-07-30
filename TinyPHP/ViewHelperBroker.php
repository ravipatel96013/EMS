<?php

final class TinyPHP_ViewHelperBroker {

    private static $instance;
    private $_helpers;

    private function __construct() {
        
    }

    final public static function getInstance() {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
            self::$instance->_helpers = array();
        }
        return self::$instance;
    }

    public function registerHelper($_helper, $stack_index = null) {
        if (false !== array_search($_helper, $this->_helpers, true)) {
            trigger_error('Helper is already registered', E_USER_ERROR);
            return;
        }
        
        if (!empty($stack_index)) {
            if (isset($this->_helpers[$stack_index])) {
                trigger_error('There is a helper already registered at index ' . $stack_index, E_USER_ERROR);
            } else {
                $this->_helpers[$stack_index] = $_helper;
                return true;
            }
        } else {
            array_push($this->_helpers, $_helper);
            return true;
        }
    }

    public function getHelpers() {
        return $this->_helpers;
    }
    
    public function getHelper($helper_name)
    {
   		foreach ($this->_helpers as $_helper) {
   			
   			if($_helper->getHelperName() == $helper_name)
   			{
   				return $_helper;
   			}            
        }	
        
        return false;
    }

    public function registerToRenderer($_viewRenderer) {

        foreach ($this->_helpers as $_helper) {
            $_viewRenderer->registerHelper($_helper);
        }
    }

}

?>