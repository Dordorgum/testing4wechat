<?php
  
include_once('simple_html_dom.php');

//define your token
define("TOKEN", "weixin");
$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();


switch ($_SERVER['REQUEST_METHOD']) {
  case 'POST':
    $wechatObj->responseMsg(); 
    break;
  case 'GET':
    $wechatObj->valid(); 
    break;
  default:
    $wechatObj->valid();  
    break;
}


//$wechatObj->responseMsg();

class wechatCallbackapiTest
{
	private $weChatAppID = 'wxdef363a5d71f854f';
	private $weChatAppSecret = 'b5a9680c89589d34a1b162726a3cd0b6';
	private $weChatAccessToken;
	private $menuResult;
	private $stockConnect = array(1,2,3,4,5,6,8,11,12,13,14,16,17,19,20,23,27,38,41,54,66,67,69,81,83,101,107,116,119,123,135,142,144,148,151,152,165,168,175,177,178,179,187,189,200,210,215,220,242,257,267,270,272,291,293,297,300,302,303,308,315,316,317,322,323,330,336,338,341,358,363,371,384,386,388,390,392,410,425,440,460,489,493,494,506,511,522,525,548,551,552,553,564,566,586,588,590,604,606,631,636,639,656,659,669,670,683,688,691,700,728,737,751,753,754,762,813,817,829,836,845,846,848,857,861,867,868,874,880,881,883,902,914,916,917,934,939,941,960,966,981,991,992,995,998,1033,1038,1044,1053,1055,1065,1066,1068,1071,1072,1083,1088,1093,1099,1101,1108,1109,1112,1114,1117,1128,1138,1169,1171,1177,1186,1193,1199,1205,1208,1212,1230,1288,1293,1299,1313,1333,1336,1339,1359,1378,1382,1387,1398,1618,1680,1728,1766,1800,1813,1828,1833,1880,1882,1888,1898,1918,1919,1928,1929,1972,1988,2007,2008,2009,2018,2020,2038,2128,2168,2196,2238,2282,2313,2314,2318,2319,2328,2333,2356,2380,2386,2388,2600,2601,2607,2628,2688,2689,2727,2777,2866,2877,2880,2883,2899,3308,3311,3323,3328,3333,3360,3368,3377,3383,3389,3618,3800,3808,3888,3899,3900,3968,3988,3993,3998,6030,6808,6818,6837,6863);
	private $dualStock;
	
	/*
	function __construct() 
	{
		$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->weChatAppID.'&secret='.$this->weChatAppSecret;
		$json= json_decode($this->get_data($url));
		$this->weChatAccessToken=$json->{'access_token'};
		//$this->setDualStock();
		//$this->menuResult = $this->setMenu();
   	}*/
    
    public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr))
		{       
	        	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
	        $fromUsername = $postObj->FromUserName;
	        $toUsername = $postObj->ToUserName;
	        $messageType = trim($postObj->MsgType);
	        $eventAction = trim($postObj->Event);
	        $eventKey = trim($postObj->EventKey);

	        $keyword = trim($postObj->Content);
	        $time = time();
	        $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>"; 
							
            
			if(!empty( $keyword ))
            	{
	            	if (is_numeric( $keyword )) 
	            {
            		$CompanyPageURL = 'http://www.hkex.com.hk/eng/invest/company/profile_page_e.asp?WidCoID=';
            		//$CompanyPageURL = 'http://www.hkex.com.hk/chi/invest/company/profile_page_c.asp?WidCoID=';
            		$PricePageURL = 'http://www.hkex.com.hk/eng/invest/company/quote_page_e.asp?WidCoID=';
            	
            		$CompanyPage = $this->get_data($CompanyPageURL.$keyword);
					$CompanyName = $this->getCompany($CompanyPage);
					
					$PricePage = $this->get_data($PricePageURL.$keyword);
					$NominalPrice = $this->getNominalPrice($PricePage);
					$LowPrice = $this->getPrice($PricePage,"Low");
					$HighPrice = $this->getPrice($PricePage,"High");
            
 					$needle = array_search((int)$keyword, $this->stockConnect);
 					        
					if ($needle !== false)
					{
						$CompanyCode = "HKG:".sprintf('%05d', $keyword)."\n[港股通合资格股份]";
					}
					else
					{
						$CompanyCode = "HKG:".sprintf('%05d', $keyword);
					}
					   
              	$msgType = "text";
                	//$contentStr = "HKG:".sprintf('%05d', $keyword)."\n".$CompanyName."\n\n"."按盘价: $".$NominalPrice."\n"."最高价: $".$HighPrice."\n"."最低价: $".$LowPrice."\n价格资料最少延时15分钟\n";
                	$contentStr = $CompanyCode."\n".$CompanyName."\n\n"."按盘价: $".$NominalPrice."\n"."最高价: $".$HighPrice."\n"."最低价: $".$LowPrice."\n价格资料最少延时15分钟\n";
                	
                	if ($needle !== false)
                	{
                		$contentStr = $contentStr."\n"."(沪港通推出时合资格股份名单或会有变)\n";
                	}
                	
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
            	}
            	elseif (strtoupper($keyword) == "HS" || strtoupper($keyword) == "HSI" || strtoupper($keyword) == "HANG SENG" || strtoupper($keyword) == "HANGSENG" || strpos($keyword,'恒') !== false)
            	{
            		$IndexPage = $this->get_data('http://www.hkex.com.hk/eng/index.htm');
            		
	            	$IndexFigure = $this->getIndex($IndexPage, '/var hkex_hsi_index = "(.*)";/');
					$IndexChangeFigure = $this->getIndex($IndexPage, '/var hkex_hsi_change = "(.*)";/');
					$IndexChangePercentage = $this->getIndex($IndexPage, '/var hkex_hsi_change_percentage = "(.*)";/');

            	    $msgType = "text";
                	$contentStr = "恒生指数: ".$IndexFigure."\n升跌: ".$IndexChangeFigure."\n升跌%: ".$IndexChangePercentage;
      	
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
            	} 
            	else
            	{
              		$msgType = "text";
                	$contentStr = "感谢你的讯息!\n\n请输入股票代码(如:5 或 0005) 以查询其相关信息。\n";
                	
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
            	}
            }
            else
            {
            	// No Content tag from WeChat message
            	if ($messageType == "event")
            	{
            		if ($eventKey == "V1002_BLOG")
            		{
            			$CharlesPage = $this->get_data('http://www.hkex.com.hk/eng/newsconsul/blog/blog.htm');
						$Cpage = $this->strip_single("span", $CharlesPage);
						$html = str_get_html($Cpage);
						$divs = $html->find('div[id=ctl00_PlaceHolderMain_ctl05__ControlWrapper_RichHtmlField]',0);

            			$ahref = array();
						$atitle = array();
						
						foreach($divs->find('.ms-rteTableOddRow-5') as $tr) 
						{
							foreach($tr->next_sibling()->find('td a') as $td)
							{
								array_push($ahref, $td->href);
								array_push($atitle, $td->innertext);
							}
						}

            			$newsTplHeader = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<ArticleCount>4</ArticleCount>
							<Articles>";
						
						$itemOne="";
						for($i=0;$i<4;$i++)
						{
							$newsItem = "
							<item>
							<Title><![CDATA[%s]]></Title> 
							<Description><![CDATA[]]></Description>
							<PicUrl><![CDATA[http://www.hkex.com.hk/eng/newsconsul/blog/image/banner_2.jpg]]></PicUrl>
							<Url><![CDATA[%s]]></Url>
							</item>";
							
							//$itemOne = $itemOne.sprintf($newsItem, $atitle[$i], "http://www.hkex.com.hk".$ahref[$i]);
							$aURL = "http://phpsvn4wechat.duapp.com/blogdetail/?url=".$ahref[$i];
							$itemOne = $itemOne.sprintf($newsItem, $atitle[$i], $aURL);
						}
						
						$newsTplFooter = "
							</Articles>
							</xml>"; 
            	
            			$newsTpl = $newsTplHeader.$itemOne.$newsTplFooter;
            			
	              		$msgType = "news";
	                	//$contentStr = "感谢你的讯息!\n\n请输入股票代码(如:5 或 0005) 以查询其相关信息。\n"."Event Key: ".$eventKey;
	                	
	                	$resultStr = sprintf($newsTpl, $fromUsername, $toUsername, $time, $msgType);
	                	echo $resultStr;
            		}
            		elseif ($eventKey == "V3002_STOCK")
            		{
            			$msgType = "text";
                		$contentStr = "请发出股票代码信息(如:5 或 0005)以查询其相关信息。\n";
                	
                		$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                		echo $resultStr;
					}
					elseif ($eventKey == "V3001_QUOTA")
            		{
            			$msgType = "text";
                		$contentStr = "全年总额度: xxx 亿\n本日额度: xxx 亿\n本日余额: xxx 亿\n";
                	
                		$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                		echo $resultStr;

					}
            		else
            		{
            			if ($eventAction == "subscribe")
            			{       
	            			$this->menuResult = $this->setMenu();    
	            						
            			    $msgType = "text";
	                		$contentStr = "你好，欢迎关注香港交易所脉搏，我们将与您分享香港交易所有关活动的最新消息。";
	                	
	                		$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
	                		echo $resultStr;
		
            			}
            			else 
            			{
            				$msgType = "text";
	                		$contentStr = "Event Key: ".$eventKey;
	                	
	                		$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
	                		echo $resultStr;
            			}
            		}
            	}
            }
        } 
        else 
        {
        	echo "Error: No Post Data";
        	exit;
        }
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
	
	private function getCompany($url)
	{
		$html = str_get_html($url);
	
		foreach($html->find('td') as $td) 
		{
			if ($td->plaintext == 'Company/Securities Name:' || $td->plaintext == '公司/證券名稱:') 
			{
				$next_td = $td->next_sibling();
				return trim($next_td->plaintext);
				break;
			}
		}

	}
	
	private function getNominalPrice($url) 
	{
		$html = str_get_html($url);
		
		foreach($html->find('td p') as $td) 
		{
			if ($td->plaintext == 'Nominal Price') 
			{
				$self_tr = $td->parent()->parent();
				$target_tr = $td->parent()->parent()->next_sibling();
	
				$counter = 0;
				foreach ($self_tr->find('td p') as $td2)
				{
					//echo $td2."\n";			
					if ($td2->plaintext == 'Nominal Price') 
					{
						return $target_tr->find('td', $counter)->plaintext;
						break;
					}
					$counter++;
				}
		
				break;
			}
		}	
	}
	
	private function getPrice($url,$compareText) 
	{
		$html = str_get_html($url);
		
		foreach($html->find('td') as $td) 
		{
			if ($td->plaintext == $compareText) 
			{
				$self_tr = $td->parent();
				$target_tr = $td->parent()->next_sibling();
	
				$counter = 0;
				foreach ($self_tr->find('td') as $td2)
				{
					if ($td2->plaintext == $compareText) 
					{
						return $target_tr->find('td', $counter)->plaintext;
						break;
					}
					$counter++;
				}
		
				break;
			}
		}	
	}
	
	private function getIndex($url,$pattern)
	{
		preg_match($pattern, $url, $matches);
		$room_id = $matches[1];	
		return $room_id;
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
		return $resultObj['errmsg'];
	}
	
	private function setDualStock()
	{
		$arrayDual = array(
		"1288	"=>"601288",
		"753	"=>"601111",
		"2600	"=>"601600",
		"914	"=>"600585",
		"995	"=>"600012",
		"3988	"=>"601988",
		"3328	"=>"601328",
		"2009	"=>"601992",
		"588	"=>"601588",
		"187	"=>"600860",
		"998	"=>"601998",
		"1898	"=>"601898",
		"1800	"=>"601800",
		"939	"=>"601939",
		"1919	"=>"601919",
		"670	"=>"600115",
		"6818	"=>"601818",
		"2628	"=>"601628",
		"1988	"=>"600016",
		"3968	"=>"600036",
		"3993	"=>"603993",
		"2883	"=>"601808",
		"2601	"=>"601601",
		"386	"=>"600028",
		"1186	"=>"601186",
		"390	"=>"601390",
		"1088	"=>"601088",
		"2866	"=>"601866",
		"1138	"=>"600026",
		"1055	"=>"600029",
		"1053	"=>"601005",
		"6030	"=>"600030",
		"2880	"=>"601880",
		"991	"=>"601991",
		"1072	"=>"600875",
		"38		"=>"601038",
		"2333	"=>"601633",
		"2238	"=>"601238",
		"525	"=>"601333",
		"874	"=>"600332",
		"317	"=>"600685",
		"6837	"=>"600837",
		"1071	"=>"600027",
		"902	"=>"600011",
		"1398	"=>"601398",
		"177	"=>"600377",
		"358	"=>"600362",
		"300	"=>"600806",
		"1108	"=>"600876",
		"323	"=>"600808",
		"1618	"=>"601618",
		"553	"=>"600775",
		"1336	"=>"601336",
		"857	"=>"601857",
		"2318	"=>"601318",
		"2727	"=>"601727",
		"2196	"=>"600196",
		"2607	"=>"601607",
		"548	"=>"600548",
		"107	"=>"601107",
		"338	"=>"600688",
		"1033	"=>"600871",
		"1065	"=>"600874",
		"168	"=>"600600",
		"1171	"=>"600188",
		"564	"=>"601717",
		"2899	"=>"601899",
		);

		$this->dualStock = $arrayDual;
	}

}

?>