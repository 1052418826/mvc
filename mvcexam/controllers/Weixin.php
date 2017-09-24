<?php 
namespace controllers;
use system\Wechat;
use system\Controller;
use system\Http;
class Weixin extends Controller
{
	private $weixin;
	public function __construct()
	{
		$this->weixin=new Wechat;
	}
	public function index()
	{

		$this->weixin->parseMessage();
	}

	public function menu()
	{
		$this->weixin->examMenu();
	}

	public function deletemenu()
	{
		$this->weixin->deleteMenu();
	}

	public function map()
	{
		$jsapi=$this->weixin->getApi();
		$ticket=$jsapi->ticket;
		$noncestr=$this->Rander(16);//随机数
		$time=time();//时间戳
		$appid=APPID;
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$url="$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$sha1=sha1("jsapi_ticket=".$ticket."&noncestr=".$noncestr."&timestamp=".$time."&url=".$url);
		$this->assign(['appid'=>$appid,'time'=>$time,'noncestr'=>$noncestr,'signature'=>$sha1]);
		$this->display();
	}


	public function Rander($number=16)
	{
		$str="qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890";
		$num='';
		for ($i=0; $i < $number; $i++) { 
			$num.=substr($str,mt_rand(0,strlen($str)-1),1);
		}
		return $num;
	}

	public function txmap()
	{
		$url="http://apis.map.qq.com/tools/poimarker?type=0&marker=coord:39.96554,116.26719;title:成都;addr:北京市海淀区复兴路32号院|coord:39.87803,116.19025;title:成都园;addr:北京市丰台区射击场路15号北京园博园|coord:39.88129,116.27062;title:老成都;addr:北京市丰台区岳各庄梅市口路西府景园六号楼底商|coord:39.9982,116.19015;title:北京园博园成都园;addr:北京市丰台区园博园内&key=OB4BZ-D4W3U-B7VVO-4PJWW-6TKDJ-WPB77&referer=myapp";
	    $map=Http::getInfoByUrl($url);
	    $this->assign(['map'=>$map]);
	    $this->display();
	}
}






 ?>