<?php
// Config Constants
define('PHP_TIMEZONE_STRING', 'Asia/Kolkata');    //set the timezone string as per your app timezone requirements. Make sure it matches with DB time zone below.
define('DB_TIMEZONE_STRING', '+05:30');

define('APPROVED',1);
define('PENDING',0);
define('DECLINED',2);

define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT']);
define('SITE_URL', $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST']);

define('VIEW_COMPILE_DIR', APP_PATH . '/extras/templates_c');
define('VIEW_CACHE_DIR', APP_PATH . '/extras/templates_cache');
define('VIEW_CONFIG_DIR', APP_PATH . '/extras/templates_config');

define('SITE_NAME', 'Employee Managment System');
define('DOCUMENT_UPLOAD_PATH', SITE_ROOT . '/documents/');
define('DOCUMENT_FILE_PATH', SITE_URL . '/documents/');

define('ASSETS_DIR', TINY_PHP_PATH . '/Public/assets/');

define('SYSTEM_EMAIL', 'system@domain.com <Your name>');


if ($_SERVER['HTTP_HOST'] ==  'local.ems.com') {

  

    // DB Configuration
    define('DB_HOST', 'localhost');
    define('DB_UNAME', 'root');
    define('DB_PWD', 'root');
    define('DB_NAME', 'ems');

    // Notification Configuration
    define('DEBUG_EMAIL', 'debug@domain.com');


    // Errors / ENV Configuration
    define('ENV', "development");
    ini_set('display_errors', "On");
   //error_reporting(E_ERROR | E_WARNING | E_PARSE);
 
} else {
    /**
     * production/live env block!! 
     */

    define('DB_HOST', 'localhost');
    define('DB_UNAME', '');
    define('DB_PWD', '');
    define('DB_NAME', '');


    define('DEBUG_EMAIL', 'debug@domain.com');

    define('ENV', "production");
    ini_set('display_errors', "Off");
    error_reporting(0);
}
