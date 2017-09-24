<?php 
namespace system;
class Wea{
	public static function index($message)
	{
	  $url="http://v.juhe.cn/weather/index?format=2&cityname=".$message."&key=66445daa79f761ec7aed86505a5b3579";
		$res=file_get_contents($url);
		$msg=json_decode($res,true);
		$flag="没有查询到这个这个城市的天气信息";
		if ($msg['error_code']==0) {
			$flag=$msg['result']['today']['weather'];
		}
		return $flag;
	}
}







 ?>