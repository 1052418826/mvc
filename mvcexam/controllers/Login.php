<?php 
namespace controllers;
use system\Controller;
use models\Message;
use system\Pagination;
class Login extends Controller{
	public function index()
	{
		$model=new Message;
		$data = $model->select(); 
		$page=new Pagination(count($data),1);
		/*½øÐÐÊµ¼ÊµÄÊý¾ÝÌáÈ¡*/
		$data = $model->limit($page->length,$page->limit())->select();
		$pageStr = $page->show();
		$this->assign(['data'=>$data,'pagestr'=>$pageStr]);
		$this->display();
		
	}
	/*public function show()
	{
		 echo $_SERVER['PATH_INFO'];
	}*/
}






 ?>