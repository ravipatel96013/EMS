<?php
# Global Variables


// Set time zone
if( defined('PHP_TIMEZONE_STRING') && PHP_TIMEZONE_STRING != '' )
{
	date_default_timezone_set(PHP_TIMEZONE_STRING);
}

# Init Front Controller
$front = TinyPHP_Front::getInstance();

# Start Session
TinyPHP_Session::init();

# Set Controller Directories
$front->setApplicationDirectory(APP_PATH);
$front->setControllerDirectory(array(
	'api' => '/api/controllers/',
	'app' => '/app/controllers/',
	'admin' => '/admin/controllers',
	'scripts' => '/scripts/controllers/',
));

// pre dipatch variables
$front->setPreDispatchVar('showHeader', true);
$front->setPreDispatchVar('showFooter', true);
$front->setPreDispatchVar('bodyClasses', "");

# Register Plugins


$AppSessionValidator = new Plugins_AppSessionValidator();
$front->registerPlugin($AppSessionValidator);

$AdminSessionValidator = new Plugins_AdminSessionValidator();
$front->registerPlugin($AdminSessionValidator);



# Routing - Url Re-Write

/*
$router = TinyPHP_Router::getInstance();

$router->addRoute('validate_token',
	new TinyPHP_Route('/validate/token/',
		array('module'=>'api','controller'=>'validate','action'=>'validate')
	)
);
*/


#dispatch now!
$front->dispatch();

#destroy
unset($front);
?>