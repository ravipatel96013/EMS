<?php

abstract class TinyPHP_Session {

    private function __contruct() {
        
    }
    
    public static function regenerate($_deleteOldSession=false)
    {
    	return session_regenerate_id($_deleteOldSession);
    }

    public static function getSessionId() {
        return session_id();
    }

    public static function init() {
        session_start();
    }

    public static function destroy($key='') {
    	if(empty($key))
    	{
        	session_destroy();
    	}
    	else
    	{
    		if (isset($_SESSION[$key])) {
    			unset($_SESSION[$key]);
    		}
    	}
    }

    public static function set($name, $value) {

        $_SESSION[$name] = $value;
    }

    public static function get($name) {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
    }

}

?>