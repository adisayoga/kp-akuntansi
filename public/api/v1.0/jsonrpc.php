<?php
//Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../../application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath('../../../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap();

// Instantiate server, etc.
$server = new Zend_Json_Server();
$server->setClass('Model_JsonRpc');

if ('GET' == $_SERVER['REQUEST_METHOD']) {
	// Hang if we're asked to
	if(isset($_REQUEST['hang']) && $_REQUEST['hang']) sleep((int)$_REQUEST['hang']);
	
    // Indicate the URL endpoint, and the JSON-RPC version used:
    $server->setTarget('/api/v1.0/jsonrpc.php')
           ->setEnvelope(Zend_Json_Server_Smd::ENV_JSONRPC_2);

    // Grab the SMD
    $smd = $server->getServiceMap();

    // Return the SMD to the client
    header('Content-Type: application/json');
    echo $smd;
    return;
}

$server->handle();

