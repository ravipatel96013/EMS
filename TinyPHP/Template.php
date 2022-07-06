<?php
class TinyPHP_Template extends stdClass
{
    private static $instance;

    public $_helpers;

    private function __construct()
    {

        $this->_helpers = array();
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;

        }
        return self::$instance;
    }

    public function getHelper($params)
    {

        extract($params);
        $helperClassName = '';
        $helperClassName = "Helpers_Views_" . $name;
        $smarty = TinyPHP_View::getInstance()->getViewRenderer()->getRenderer();
        if (array_key_exists($name, $this->_helpers)) {
            $smarty->assign($var,$this->_helpers[$name]);
        } else {
            $c = new $helperClassName($params);
            $this->_helpers[$name] = $c;
            $smarty->assign($var,$this->_helpers[$name]);
        }
    }

}
