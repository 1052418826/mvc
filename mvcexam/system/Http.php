<?php
namespace system;
class Http {

	public static function getInfoByUrl($url,$config=[]){
		$_config = [
			CURLOPT_URL => $url,
		];
		$_config = $_config + $config;
		return self::customCurl($_config);
	}
	// 自定义通用的curl方法
	public static function customCurl($config){
		$_config = [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_AUTOREFERER => true,
			CURLOPT_HEADER => false,
			CURLOPT_SSL_VERIFYPEER=>false
		];

		$_config = $_config + $config;
		$ch = curl_init();
		curl_setopt_array($ch, $_config);
		$res = curl_exec($ch);
		curl_close($ch);
		return $res;
	}


}