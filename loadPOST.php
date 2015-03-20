<?php
  
include_once('simple_html_dom.php');


$wechatObj = new wechatCallbackapiTest();
$wechatObj->load();
 
class wechatCallbackapiTest
{

    public function load()
    {
    	$stock_code = '5';
    	$stock_date = '20150321';
    	$hfStatus = 'AEM';
    	$viewstateencrypted = '';
    	$viewstate = '';
    
    	$fields = array(
						'txt_stock_code' => urlencode($stock_code),
						'txt_today' => urlencode($stock_date),
						'hfStatus' => urlencode($hfStatus),
						'__VIEWSTATEENCRYPTED' => urlencode($viewstateencrypted),
						'__VIEWSTATE' => urlencode($viewstate)
				);
				
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }

		rtrim($fields_string, '&');
				
		$cPage = $this->get_data('http://www.hkexnews.hk/listedco/listconews/advancedsearch/search_active_main.aspx','POST', $fields_string);
		echo $cPage;
    }

	private function get_data($url, $method, $string) {
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, count($fields));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $string);
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

}

?>