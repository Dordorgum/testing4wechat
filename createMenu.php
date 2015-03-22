<?php

$wechatObj = new wechatCallbackapiTest();
$wechatObj->createMenu();

class wechatCallbackapiTest
{
	private $weChatAppID = 'wx6a6eaf0c4456af06';
	private $weChatAppSecret = '9fb1078ca64247ffc09b74cd011077a1';
	private $weChatAccessToken;
	private $menuResult;
	
	function createMenu() 
	{
		$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->weChatAppID.'&secret='.$this->weChatAppSecret;
		$json= json_decode($this->get_data($url));
		$this->weChatAccessToken=$json->{'access_token'};
		
		echo "access token = ".$this->weChatAccessToken;
		
		$this->menuResult = $this->setMenu();
		
		echo "menu result = ". $this->menuResult;
   	}
	
	private function get_data($url, $method='GET') {
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
	private function strip_single($tag,$string)
	{
		$string=preg_replace('/<'.$tag.'[^>]*>/i', '', $string);
		$string=preg_replace('/<\/'.$tag.'>/i', '', $string);
		return $string;
	} 

	private function setMenu()
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

		$post_url = sprintf("https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s",$this->weChatAccessToken);
		
		$ch = curl_init($post_url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $stringMenu);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',                                      
			'Content-Length: '.strlen($stringMenu))
		);
		$resultJson = curl_exec($ch);
		
		$resultObj = json_decode($resultJson,true);
		
		echo $resultObj;
		
		return $resultObj['errmsg'];
	}
	
	

}

?>