<?php

$postObj = new urlGETTest();
$postObj->load();
 
class urlGETTest
{

    public function load()
    {
    	$searchString = $_GET["text"];
    	$csv = iconv("UTF-8","GB2312",$searchString);
		echo "csv=".$csv."\n\n";
    	echo "text=".$searchString."\n\n";
		
    	$page_url = 'http://sc.hkex.com.hk/gb/www.hkex.com.hk/chi/invest/company/excompany_page_c.asp?QueryString='.$searchString;	
		echo "url=".$page_url."\n\n";
		
		$cPage = $this->get_data($page_url);
		echo $cPage;
    }

	private function get_data($url, $method = 'GET') {
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		
		$headerSent = curl_getinfo($ch, CURLINFO_HEADER_OUT ); 

		//$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		//$header = substr($data, 0, $header_size);
		//$body = substr($data, $header_size);
		
		curl_close($ch);
		return "Request Header:\n\n".$headerSent."\n\n\n\n\n\n\n".$data;
	}

}

?>