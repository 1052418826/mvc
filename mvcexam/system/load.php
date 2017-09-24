<?php 
function autoload($className)
{
	$name=PATH.'/'.$className.'.php';
	$class=str_replace('\\','/',$name);
	if (file_exists($class)) {
		include ($class);
	}else{
		echo "该类文件不存在";die;
	}
	
}
function alert($php)
{
	echo <<<EOT
		<script>
		alert($php);
		</script>
EOT;
}


 ?>