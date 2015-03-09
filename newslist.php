<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" xml:lang="en-US" lang="en-US">
<head>
	<title>香港交易所新聞稿</title>
	
	<!-- Metadata -->	
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7, IE=9" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<!-- Stylesheets -->	
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.2/jquery.mobile-1.4.2.min.css" />
	
	<!-- Javascript -->	
	<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.4.2/jquery.mobile-1.4.2.min.js"></script>
</head>

<?php
include_once('simple_html_dom.php');
$postStr = $_POST["selectYear"];
if (empty($postStr)) $postStr="2014";
?>

<body>

<div data-role="page" id="newslist">

<form action="newslist.php" method="post">
<div class="ui-field-contain">
<select name="selectYear" id="selectYear" data-mini="true">
<option value="2014" <?if ($postStr==="2014") echo "selected";?>>2014</option>
<option value="2013" <?if ($postStr==="2013") echo "selected";?>>2013</option>
<option value="2012" <?if ($postStr==="2012") echo "selected";?>>2012</option>
<option value="2011" <?if ($postStr==="2011") echo "selected";?>>2011</option>
<option value="2010" <?if ($postStr==="2010") echo "selected";?>>2010</option></select>
</div></form>
<script>$("#selectYear").change(function() { this.form.submit(); });</script>

	<div role="main" class="ui-content">
	
		<ul data-role="listview" data-divider-theme="a">

<?
//echo "POST: ".$postStr;

$NewsPage = get_data('www.hkex.com.hk/chi/newsconsul/hkexnews/'.$postStr.'news_c.htm');
$Cpage = strip_single ("span", $NewsPage);
$Cpage = strip_single ("p", $Cpage);
$html = str_get_html($Cpage);

$newsDate = array();
$newsHref = array();
$newsTitle = array();

foreach($html->find('.ms-rteTableOddCol-1') as $td) 
{
	//array_push($newsDate, $td->innertext);
	
	//echo $td->next_sibling()->innertext."<br>\n\n\n";
	
	//echo $tr->next_sibling()->innertext."\n\n";
	//$inner = $tr->next_sibling();

	foreach($td->next_sibling()->find('a') as $ah)
	{
		//echo $td->href."\n\n";
		//echo $td->innertext."\n\n";
		$checkPDF = $ah->href;
		if (strpos($checkPDF ,'.pdf') === false)
		{
			if (strpos($checkPDF ,'media-server') === false)
			{
				$trimTitle = trim($ah->innertext);
				if ($trimTitle)
				{
					array_push($newsDate, $td->innertext);
					array_push($newsHref, $ah->href);
					array_push($newsTitle, $trimTitle);
					//$clean = trim(strip_single("p", $ah->innertext));
					//array_push($newsTitle, $clean);
				}
			}
		}
	}
}

$oldDate = "";

for($i=0;$i<count($newsDate);$i++)
{
	if ($oldDate !== $newsDate[$i])
	{
		echo '<li data-role="list-divider">'.$newsDate[$i].'</li>'."\n";
		echo '<li><a href="http://www.hkex.com.hk'.$newsHref[$i].'">'.$newsTitle[$i].'</a></li>'."\n";
		
		$oldDate = $newsDate[$i];
	}
	else
	{
		echo '<li><a href="http://www.hkex.com.hk'.$newsHref[$i].'">'.$newsTitle[$i].'</a></li>'."\n";
	}

	//echo $newsDate[$i]."<br>\n";
	//echo $newsHref[$i]."<br>\n";
	//echo $newsTitle[$i]."<br>\n\n";
}
?>

		</ul>
	</div><!-- /content -->	
	
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

