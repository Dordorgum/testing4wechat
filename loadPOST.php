<?php
  
include_once('simple_html_dom.php');


$wechatObj = new wechatCallbackapiTest();
$wechatObj->load();
 
class wechatCallbackapiTest
{

    public function load()
    {
		$cPage = $this->get_data('http://www.hkex.com.hk/eng/newsconsul/blog/blog.htm');
		echo $cPage;
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

}

?>