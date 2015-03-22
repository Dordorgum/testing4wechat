<?php

$wechatObj = new wechatCallbackapiTest();
$wechatObj->createMenu();

class wechatCallbackapiTest
{
	private $weChatAppID = 'wx6a6eaf0c4456af06';
	private $weChatAppSecret = '9fb1078ca64247ffc09b74cd011077a1';
	
	function createMenu() 
	{
		$weChatAccessToken= $this->getAccessToken();		
		$menuResult = $this->setMenuWithToken($weChatAccessToken);
		
		if (strcmp($menuResult, "ok") == 0) {
			//echo "equal";
			$result = $this->getMenuWithToken($weChatAccessToken);
			echo $result;
			//echo json_encode($result, JSON_PRETTY_PRINT);
		} else {
			echo "error = ". $menuResult;
		}
   	}

	private function getAccessToken() {
	
		$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->weChatAppID.'&secret='.$this->weChatAppSecret;

		$ch = curl_init();
		$timeout = 3;
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		$json= json_decode($data,true);
		
		curl_close($ch);
	
		return $json['access_token'];
	}
	
	private function getMenuWithToken($accessToken)
	{
		$url = 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token='.$accessToken;
				
		$ch = curl_init($url);
		$timeout = 3;
		
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$data = curl_exec($ch);
		//$json= json_decode($data,true);
		
		return $data;
	}

	private function setMenuWithToken($accessToken)
	{
	
$stringMenu='
{
    "button": [
        {
            "name": "新闻资料", 
            "sub_button": [ 
            	{
                    "type": "view", 
                    "name": "新闻稿", 
                    "url": "http://phpsvn4wechat.duapp.com/press/",
                    "sub_button": [ ]
                }, 
                {
                    "type": "click", 
                    "name": "小加网志", 
                    "key": "V1002_BLOG", 
                    "sub_button": [ ]
                }
            ]
        }, 
        {
            "type": "view", 
            "name": "沪港通", 
            "url": "http://www.hkex.com.hk/chi/csm/homepage.asp?LangCode=tc", 
            "sub_button": [ ]
        }, 
        {
            "name": "额度查询", 
            "sub_button": [
                {
                    "type": "click", 
                    "name": "沪港通额度", 
                    "key": "V3001_QUOTA", 
                    "sub_button": [ ]
                }, 
                {
                    "type": "click", 
                    "name": "股价及指数", 
                    "key": "V3002_STOCK", 
                    "sub_button": [ ]
                }
            ]
        }
    ]
}
';

		$post_url = sprintf("https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s",$accessToken);
		
		$ch = curl_init($post_url);
		$timeout = 3;
		
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $stringMenu);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',                                      
			'Content-Length: '.strlen($stringMenu))
		);
		$data = curl_exec($ch);
		$resultObj = json_decode($data,true);

		return $resultObj['errmsg'];
	}

}

?>