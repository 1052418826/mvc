<?php 
namespace system;
class Router
{
	public $controller='index';
	public $action='index';
	public function __construct()
	{
		//判断index.php后面的路径是否存在
	    if (isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])) {
	    	$server=$_SERVER['PATH_INFO'];
	    	$path=explode('/', $server);
	    	if (isset($path[1]) && !empty($path[1])) {
	    		$this->controller=$path[1];
	    	}
	    	if (isset($path[2]) && !empty($path[2])) {
	    		$this->action=$path[2];
	    	}
	    	for ($i=3; $i <count($path); $i+=2) { 
	    		$_GET[$path[$i]]=$path[$i+1];
	    	}

	    }
	    //判断是否有控制器和方法的参数传过来
	    if (isset($_GET['c']) && !empty($_GET['c'])) {
			$this->controller=$_GET['c'];
		}
		if (isset($_GET['f']) && !empty($_GET['f'])) {
			$this->action=$_GET['f'];
		}
		$_GET['controller']=ucwords($this->controller);
		$_GET['action']=$this->action;
		$con='controllers\\'.ucwords($this->controller);
		$controller=new $con();
		$action=$this->action;
		$controller->$action();
	}
}
 ?>