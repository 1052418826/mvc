<?php 
namespace system;
use system\Router;
class Controller{
	public $data=array();
	private $T_P=array();
	private $T_R=array();
	public function tp(){
		$this->T_P[]="#\{\\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(\['.*'\])*)\}#"; 
		$this->T_P[]="#\{(loop|foreach) \\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\}#i"; 
		$this->T_P[]="#\{\/(loop|foreach|if)\}#i"; 
		//$this->T_P[]="#\{([k|v](\['.*'\])*)\}#"; // key 和 value
		$this->T_P[]="#\{([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(.*))\}#"; // 匹配常量
		$this->T_P[]="#\{if (.* ?)\}#i"; // if
		$this->T_P[]="#\{(else if|elseif) (.* ?)\}#i"; // elseif
		$this->T_P[]="#\{else\}#i"; // 匹配else
		$this->T_P[]="#\{(\#|\* )(.* ?)(\#|\* )\}#";  // 注释
		$this->T_R[]="<?php echo \$\\1;?>"; 
		$this->T_R[]="<?php foreach((array)\$\\2 as \$k=>\$v){ ?>"; 
		$this->T_R[]="<?php } ?>"; 
		/*$this->T_R[]="<?php echo \$\\1;?>"; */
		$this->T_R[]="<?php echo \\1;?>";
		$this->T_R[]="<?php if(\\1){ ?>"; 
		$this->T_R[]="<?php }else if(\\2){ ?>"; 
		$this->T_R[]="<?php }else{ ?>"; 
		$this->T_R[]=""; 

	}

	public function assign($key,$value=''){
		if(is_array($key)){
			$this->data = array_merge($this->data,$key);
		}else{
			$this->data[$key] = $value;
		}
	}
	public function display($path='')
	{	

		$data=$this->data;
		if (!empty($data)) {
			extract($data);
		}

		if (empty($path)) {
			$c=$_GET['controller'];
			$a=$_GET['action'];
			$pathname=VIEW.'/'.$c.'/'.$a.'.html';
		}else{
			$pathname=VIEW.'/'.$path.'.html';
		}
		$pathname=strtolower($pathname);
		$path=file_get_contents($pathname);
		$this->tp();
		$name=$this->tele($path,$pathname);
		include $name;
	}
	public function tele($path,$pathname)
	{
		$md5=md5($pathname);
		$name=RUNTIME.'/'.$md5.'.php';
		$path_name = preg_replace($this->T_P,$this->T_R,$path);
		file_put_contents($name,$path_name);
		return $name;
	}

	public function redirect($path)
	{
		return header('location:'.$path);
	}

}










 ?>