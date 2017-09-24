<?php 
header("content-type:text/html;charset=utf8");
define("TOKEN","ymm");
define("APPID","wx90f28fe2084d8c7e");
define("APPSECRET","a768499cf2823e468d211c74218f5587");
define('PATH',realpath(dirname(__FILE__)));
define('SYSTEM',PATH.'/system');
define('VIEW',PATH.'/views');
define('RUNTIME',PATH.'/runtime');
include (SYSTEM.'/load.php');
spl_autoload_register("autoload");
$router=new \system\Router();

 ?>