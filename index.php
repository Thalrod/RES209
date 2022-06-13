<?php

/*DEVELOPMENT ONLY*/
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/*END DEVELOPMENT ONLY*/

session_name("RAG_SESSION");
session_start();
session_regenerate_id(true);

define('WEBROOT', str_replace("index.php", "", $_SERVER['SCRIPT_NAME']));
define('ROOT', str_replace("index.php", "", $_SERVER['SCRIPT_FILENAME']));
define('BASE_URL', $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["SERVER_NAME"].WEBROOT);


require_once('core/Autoloader.php');


$router = new Router();
$router->routeReq();

?>