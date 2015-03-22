<?php

$wechatObj = new wechatCallbackapiTest();
//$wechatObj->createMenu();

switch ( $_GET["action"]) {
  case 'create':
    $wechatObj->createMenu(); 
    break;
  case 'get':
   	$wechatObj->getCurrentMenu(); 
    break;
  default:
    $wechatObj->getCurrentMenu();  
    break;
}

class wechatCallbackapiTest
{
	//private $weChatAppID = 'wx6a6eaf0c4456af06';
	//private $weChatAppSecret = '9fb1078ca64247ffc09b74cd011077a1';
	
	//WeChat Dev Account
	private $weChatAppID = 'wxdef363a5d71f854f';
	private $weChatAppSecret = 'b5a9680c89589d34a1b162726a3cd0b6';
	
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
   	
   	function getCurrentMenu() 
	{	
		echo $this->getMenuWithToken($this->getAccessToken());
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
                "name": "沪港通", 
                "sub_button": [
                    {
                        "type": "click", 
                        "name": "沪港通成交", 
                        "key": "V1001_deal", 
                        "sub_button": [ ]
                    }, 
                    {
                        "type": "click", 
                        "name": "活跃股票", 
                        "key": "V1002_actstock", 
                        "sub_button": [ ]
                    }, 
                    {
                        "type": "click", 
                        "name": "恒生指数", 
                        "key": "V1003_index", 
                        "sub_button": [ ]
                    }, 
                    {
                        "type": "view", 
                        "name": "沪港通资料", 
                        "url": "https://sc.hkex.com.hk/gb/www.hkex.com.hk/chi/csm/chinaConnect.asp?LangCode=tc", 
                        "sub_button": [ ]
                    }, 
                    {
                        "type": "view", 
                        "name": "港股通大讲堂", 
                        "url": "http://220.246.12.161/wechat/forum.aspx", 
                        "sub_button": [ ]
                    }
                ]
            }, 
            {
                "name": "证券查询", 
                "sub_button": [
                    {
                        "type": "click", 
                        "name": "证券行情查询", 
                        "key": "V2001_stockinfo", 
                        "sub_button": [ ]
                    }, 
                    {
                        "type": "click", 
                        "name": "上市公司公告", 
                        "key": "V2002_companyinfo", 
                        "sub_button": [ ]
                    }, 
                    {
                        "type": "view", 
                        "name": "港股通证券名单", 
                        "url": "http://www.sse.com.cn/marketservices/hkexsc/disclo/eligible/", 
                        "sub_button": [ ]
                    }
                ]
            }, 
            {
                "name": "新闻资讯", 
                "sub_button": [
                    {
                        "type": "view", 
                        "name": "交易所公布 ", 
                        "url": "http://220.246.12.161/wechat/newslist.aspx", 
                        "sub_button": [ ]
                    }, 
                    {
                        "type": "click", 
                        "name": "小加网志", 
                        "key": "V1002_BLOG", 
                        "sub_button": [ ]
                    }, 
                    {
                        "type": "view", 
                        "name": "交易日曆", 
                        "url": "http://220.246.12.161/wechat/calendar.aspx", 
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