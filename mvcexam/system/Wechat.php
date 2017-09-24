<?php
namespace system;
use system\Http;
use system\Wea;
class Wechat{
	public $textTpl = "<xml>
				 <ToUserName><![CDATA[%s]]></ToUserName>
				 <FromUserName><![CDATA[%s]]></FromUserName>
				 <CreateTime>%s</CreateTime>
				 <MsgType><![CDATA[text]]></MsgType>
				 <Content><![CDATA[%s]]></Content>
				 </xml>";
	public $header_doc = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[news]]></MsgType>
				<ArticleCount>%s</ArticleCount>
				<Articles>";

	public $item_doc = "<item>
				<Title><![CDATA[%s]]></Title> 
				<Description><![CDATA[%s]]></Description>
				<PicUrl><![CDATA[%s]]></PicUrl>
				<Url><![CDATA[%s]]></Url>
				</item>";
	public $end_doc = "</Articles></xml>";

	public function __construct(){
		$flag = $this->signal();
		if($flag && isset($_GET['echostr'])){
			echo $_GET['echostr'];
		}else{
			echo '';
		}
	}
	public function signal(){
		if(isset($_GET['echostr'])){
			$timestamp = $_GET['timestamp']; // 时间戳
			$nonce = $_GET['nonce']; // 随机数
			$signature = $_GET['signature'];
			// 第一步生成签名
			$arr = [TOKEN,$timestamp,$nonce];
			sort($arr,SORT_STRING);
			$str = sha1(implode('',$arr));
			if($str == $signature){
				return true;
			}else{
				return false;
			}
		}
		return true;
	}
	// 获取token值
	public function getAccessToken(){
		if(file_exists("./runtime/accesstoken") && time()-filemtime("./runtime/accesstoken")<7200){
			$accessToken =file_get_contents("./runtime/accesstoken");
		}else{
			$accessToken = Http::getInfoByUrl("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".APPID."&secret=".APPSECRET);
			file_put_contents("./runtime/accesstoken",$accessToken);
		}
		return  json_decode($accessToken);
	}

	public function getAll()
	{
		$accesstoken=$this->getAccessToken();
		
		if(file_exists('./userlist')){
			$data=file_get_contents('./userlist');
		}else{
			$data=Http::getInfoByUrl("https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$accesstoken->access_token."&next_openid=");
			file_put_contents("./userlist",$data);

		}
		return json_decode($data,true);

	}

	public function sendAll()
	{
		 $userlist=$this->getAll();
		 $accesstoken=$this->getAccessToken();
		 $users = implode(',',$userlist['data']['openid']);
		 var_dump($users);
		 $data = '{"touser":[';
		 foreach($userlist['data']['openid'] as $openid){
			$data.='"'.$openid.'",';
		 }
/*		 print_r($data);
		 echo "<br/>";die;*/
		 $data = rtrim($data,',');
		 $data.='],
	     "msgtype": "text",
	     "text": { "content": "群发测试哈哈！"}
	 }';

		$res = Http::getInfoByUrl("https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=".$accesstoken->access_token,[
			CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$data]);
		var_dump($res);
	}
	public function deleteMenu(){
		$accesstoken = $this->getAccessToken();
		$res = Http::getInfoByUrl("https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$accesstoken->access_token);
		var_dump($res);
	}
	public function openid($openid)
	{
		$token=$this->getAccessToken();
		$url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$token->access_token."&openid=".$openid."&lang=zh_CN ";
		$res=Http::getInfoByUrl($url);
		return json_decode($res);
	}

	public function examMenu()
	{
		$menu='{
     "button":[
     {	
          "type":"click",
          "name":"首页",
          "key":"V1001_TODAY_MUSIC"
      },
       {
          "type":"click",
          "name":"简介",
          "key":"pgone"
       },
 		{
	       "name":"菜单",
	       "sub_button":[
	       {	
	           "type":"view",
	           "name":"我的位置",
	           "url":"http://47.93.226.132/ymm/mvcexam/index.php/Weixin/map",
	           "key":"gai"
	        },
	        {	
	           "type":"view",
	           "name":"腾讯地图",
	           "url":"http://47.93.226.132/ymm/mvcexam/index.php/Weixin/txmap",
	           "key":"bridge"
	        },
	        {
	           "type":"click",
	           "name":"我的商城",
	           "url":"http://47.93.226.132/ymm/mvcexam/index.php/Weixin/shop",
	           "key":"tt"
	         },
	       ]
       }]
 }';
 	$accesstoken=$this->getAccessToken();
 	$res=Http::getInfoByUrl("https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$accesstoken->access_token,[CURLOPT_CUSTOMREQUEST=>"POST",CURLOPT_POSTFIELDS=>$menu,CURLOPT_SSL_VERIFYPEER=>false]);
 		var_dump($res);

	}


	public function getApi()
	{
		if (file_exists('./jsapi') && time()-filemtime("./jsapi")<7200) {
			$jsapi=file_get_contents('./jsapi');
		}else{
			$accesstoken=$this->getAccessToken();
			$jsapi=Http::getInfoByUrl("https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$accesstoken->access_token."&type=jsapi");
			file_put_contents("./jsapi",$jsapi);
		}
		return json_decode($jsapi);
	}

	// 消息解释
	public function parseMessage(){
		// 类型 MsgType 进行不同的解释
		// 接收信息
		// 第一种方式  $GLOBAL['HTTP_RAW_POST_DATA'];
		// 第二种方式 流式读取   php://input 得到原始的数据
		$data = file_get_contents("php://input");
		//echo $data;
		if($data){
			// 如何解析XML
			// 1. 使用SimpleXml
				//new SimpleXMLElement
			// 2. DOMDocument
			// 3. XMLReader  当内存小的情况，它是首选
			// 4. simplexml_load_string simplexml_load_file
			$postObj = simplexml_load_string($data);
			// 得到消息
            $type = trim($postObj->MsgType);
            $response = '';
            // 根据消息类型进行解析和响应
            switch($type){
            	case 'event':
            		$response=$this->handleEvent($postObj);
            		break;
            	case 'text':
            		$response = $this->handleText($postObj->ToUserName,$postObj->FromUserName,$postObj->Content);
            		break;
            	case 'image':
            		$response = $this->handleImage($postObj);
            		break;
            	case 'voice':
            		$response = $this->handleVoice($postObj);
            		break;
            	case 'location':
            		$response = $this->handleLocation($postObj);
            		break;
            	case 'link':
            		$response = $this->handleLink($postObj);
            		break;
            	
            	default:
            }
            var_dump($response);
		}
	}

	public function handleEvent($postObj)
	{
		switch($postObj->Event){
			case "subscribe":
			$res=$this->openid($postObj->FromUserName);
			$msg = [
					[
					'title'=>'欢迎你,亲爱的'.$res->nickname,
					'picurl'=>'http://wx.qlogo.cn/mmopen/KslfYqyZIAHgqfYjIIVCqZeKic2hAmq1Z8CpCabmmCTGONTUeoBK6W4dDkJLVnLPEp1qoog2wmzEklHogicmRibeeUBic3aF5nfj/0',
					'url'=>'http://www.baidu.com'
				]	];
			$response=$this->reponseText($postObj->ToUserName,$postObj->FromUserName,$msg);
			break;
			case 'CLICK':
			if($postObj->EventKey=='pgone'){
				$msg=[
				[
					'title'=>'北京八维研修学院',
					'description'=>"北京八维研修学院欢迎你",
					'picurl'=>"http://wx.qlogo.cn/mmopen/KslfYqyZIAHgqfYjIIVCqZeKic2hAmq1Z8CpCabmmCTGONTUeoBK6W4dDkJLVnLPEp1qoog2wmzEklHogicmRibeeUBic3aF5nfj/0",
					'url'=>'http://www.bawei.net'
				]
			];
			$response=$this->reponseText($postObj->ToUserName,$postObj->FromUserName,$msg);
			}
			break;
			case 'VIEW':
			break;
		}
		return $response;
	}
	// 发送文本信息
	public function reponseText($fromUser,$toUser,$message){
		if(is_array($message)){
			$str = sprintf($this->header_doc,$toUser,$fromUser,time(),count($message));
			foreach($message as $row){
				$str.= sprintf($this->item_doc,$row['title'],$row['description'],$row['picurl'],$row['url']);
			}
			$str.=$this->end_doc;
		}else{
			$str = sprintf($this->textTpl,$toUser,$fromUser,time(),$message);
		}

		return  $str;
	} 

	public function handleText($fromUser,$toUser,$message){

		switch($message){
			case '周国强':
				$msg = "全八维最帅气的讲师!";
				break;
			case '新闻':
				// 
				$msg = [
					[
						'title'=>'全八维最有气质,长得最英俊的老湿',
						'description'=>'想都不用想，就是1505c的讲师',
						'picurl'=>'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1502937497&di=7aacb7a183dfeb4935272f2084730ffc&imgtype=jpg&er=1&src=http%3A%2F%2Fs4.sinaimg.cn%2Forignal%2F4a86f72a59d6bf452dcb3',
						'url'=>'http://pcedu.pconline.com.cn/964/9643082.html'
					],
				];
			default:
					if (preg_match("/^(.*)天气$/",$message,$wea)) {
						$url="http://v.juhe.cn/weather/index?format=2&cityname=".$wea[1]."&key=66445daa79f761ec7aed86505a5b3579";
						$str=file_get_contents($url);
						$data=json_decode($str,true);
						$msg='';
						foreach ($data['result']['future'] as $key => $v) {
							$msg.="温度:".$v['temperature'];
							$msg.="天气:".$v['weather'];
							$msg.="风向:".$v['wind'];
							$msg.="日期:".$v['week'];
						}
					}
				
		}
		return $this->reponseText($fromUser,$toUser,$msg);
	}
	// 发送图片信息
	public function handleImage($dataObj){
		$msg = $dataObj->PicUrl;
		return $this->reponseText($dataObj->ToUserName,$dataObj->FromUserName,$msg);
	}

	public function handleVoice($dataObj){
		$msg = $dataObj->MediaId;
		return $this->reponseText($dataObj->ToUserName,$dataObj->FromUserName,$msg);
	}

	public function handleLocation($dataObj){
		$msg = "你的地址为: 经度为".$dataObj->Location_Y.",纬度为".$dataObj->Location_X;
		return $this->reponseText($dataObj->ToUserName,$dataObj->FromUserName,$msg);
	}
	public function handleLink($dataObj){
	$msg = "发送的链接为: ".$dataObj->Url;
	return $this->reponseText($dataObj->ToUserName,$dataObj->FromUserName,$msg);
	}

}