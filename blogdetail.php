<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" xml:lang="en-US" lang="en-US">
<head>
	<title>Charles Li Direct</title>
	
	<!-- Metadata -->	
	<meta charset="utf-8" />
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7, IE=9" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<!-- Stylesheets -->	
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.2/jquery.mobile-1.4.2.min.css" />
	<style type="text/css">
	.headimage img {width: 100%;}
	h2 {font-size: 18px; color:black}
	h4 {font-size: 10px; color:gray;}
	
	</style>
	
	<!-- Javascript -->	
	<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.4.2/jquery.mobile-1.4.2.min.js"></script>
</head>

<body>

<div data-role="page" id="newslist">

<div class="headimage"><img alt="Charles Li Direct" src="http://www.hkex.com.hk/eng/newsconsul/blog/image/banner_2.jpg"></div>
<?php 
include_once('simple_html_dom.php');

$getURL = $_GET["url"];
$getTitle = $_GET["title"];

$CharlesPage = get_data('http://www.hkex.com.hk'.$getURL);
$Cpage = strip_single ("span", $CharlesPage);

$html = str_get_html($Cpage);
$divs = $html->find('div[id=ctl00_PlaceHolderMain_ctl05__ControlWrapper_RichHtmlField]',0);

//echo $divs->outertext;
$paragraph = array();

foreach($divs->find('td p') as $p) 
{
	//echo trim($p->innertext)."\n\n<p>";
	
	$trimPara = trim($p->plaintext);
	
	if (!empty($trimPara))
	{
		array_push($paragraph, trim($p->innertext)); 
	}
}
?>

	<div role="main" class="ui-content">	

<?
echo "<H2>".$paragraph[0]."</H2>\n\n";

for($i=1;$i<count($paragraph)-1;$i++)
{
	if (isset($paragraph[$i]) && !empty($paragraph[$i]))
	{
		echo "<p>".$paragraph[$i]."\n\n";
	}
}

$lastCount = count($paragraph)-1;

echo "<H4>".$paragraph[$lastCount]."</H4>\n\n";

?>
	</div>
</div><!-- /Page -->
</body>
</html>


<?
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