<?php 
include_once('simple_html_dom.php');

$CharlesPage = get_data('http://www.hkex.com.hk/eng/newsconsul/blog/blog.htm');
$Cpage = strip_single ("span", $CharlesPage);


$html = str_get_html($Cpage);
$divs = $html->find('div[id=ctl00_PlaceHolderMain_ctl05__ControlWrapper_RichHtmlField]',0);
echo $divs;

$ahref = array();
$atitle = array();

foreach($divs->find('.ms-rteTableOddRow-5') as $tr) 
{
echo $tr;

	//echo $tr->next_sibling()->innertext."\n\n";
	//$inner = $tr->next_sibling();
	
	foreach($tr->next_sibling()->find('td a') as $td)
	{
		//echo $td->href."\n\n";
		//echo $td->innertext."\n\n";
		array_push($ahref, $td->href);
		array_push($atitle, $td->innertext);
	}
}

for($i=0;$i<count($ahref);$i++)
{
	echo $ahref[$i]."\n\n";
	echo $atitle[$i]."\n\n";
}
	
function strip_single($tag,$string)
{
	$string=preg_replace('/<'.$tag.'[^>]*>/i', '', $string);
	$string=preg_replace('/<\/'.$tag.'>/i', '', $string);
	return $string;
} 

function get_data($url) 
{
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

?>