<?php 
header("content-type:text/html;charset=utf8");
define("TOKEN","ymm");
define("APPID","wx90f28fe2084d8c7e");
define("APPSECRET","a768499cf2823e468d211c74218f5587");
define('PATH',realpath(dirname(__FILE__)));
define('SYSTEM',PATH.'/system');
define('VIEW',PATH.'/views');
define('RUNTIME',PATH.'/runtime');
define('__ROOT__',str_replace($_SERVER['DOCUMENT_ROOT'],'',PATH).'/');
/*define('__ROOT__','http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']);*/
define('STYLE',__ROOT__.'style'.'/');
//引入自动加载文件
include (SYSTEM.'/load.php');
spl_autoload_register("autoload");
$router=new \system\Router();
 ?>