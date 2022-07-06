<?php
define('APP_PATH', realpath(dirname(__FILE__) . '/../App/'));
define('TINY_PHP_PATH', realpath(dirname(__FILE__) . '/../'));

set_include_path(get_include_path() .PATH_SEPARATOR . TINY_PHP_PATH);
		
set_include_path(get_include_path() . PATH_SEPARATOR . APP_PATH);

if( file_exists(TINY_PHP_PATH.'/vendor/autoload.php') )
{
    require(TINY_PHP_PATH.'/vendor/autoload.php');
}

require(APP_PATH .'/Config.php');

require(APP_PATH .'/Functions.php');

require('TinyPHP/Front.php');

require(APP_PATH . "/Bootstrap.php");

?>