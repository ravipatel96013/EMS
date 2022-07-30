<?php
abstract class TinyPHP_ViewHelper {

    protected $helperName = '';

    //put your code here
    final public function __construct() {        
        $this->init();
    }

    

    final public function getHelperName() {
        return $this->helperName;
    }
    
    abstract public function init();

    abstract public function helperFunc($params);
}

?>
