<?php 
include_once('simple_html_dom.php');

//$PricePage = get_data('http://www.hkex.com.hk/eng/invest/company/quote_page_e.asp?WidCoID=16');
//$IndexPage = get_data('http://www.hkex.com.hk/eng/index.htm');

//echo $returned_content; 
//$html = str_get_html($returned_content);
//echo $html->find('head title', 0)->innertext;

//echo getIndex($IndexPage, '/var hkex_hsi_index = "(.*)";/')."<br>\n";
//echo getIndex($IndexPage, '/var hkex_hsi_change = "(.*)";/')."<br>\n";
//echo getIndex($IndexPage, '/var hkex_hsi_change_percentage = "(.*)";/')."<br>\n";

//echo getNominalPrice($PricePage)."<br>\n";
//echo getPrice($PricePage,"Low")."<br>\n";
//echo getPrice($PricePage,"High")."<br>\n";


$CharlesPage = get_data('http://www.hkex.com.hk/eng/newsconsul/blog/blog.htm');
$Cpage = strip_single ("span", $CharlesPage);

$html = str_get_html($Cpage);
$divs = $html->find('div[id=ctl00_PlaceHolderMain_ctl05__ControlWrapper_RichHtmlField]',0);
//$html2 = str_get_html($divs->outertext);
//echo $divs;

//$counter=0;
$ahref = array();
$atitle = array();

foreach($divs->find('.ms-rteTableOddRow-5') as $tr) 
{
	//$counter++;
	//echo $counter."\n";
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

echo "Count ahref: ".count($ahref);

for($i=0;$i<count($ahref);$i++)
{
	echo $ahref[$i]."\n\n";
	echo $atitle[$i]."\n\n";
}

	
function strip_single($tag,$string){
    $string=preg_replace('/<'.$tag.'[^>]*>/i', '', $string);
    $string=preg_replace('/<\/'.$tag.'>/i', '', $string);
    return $string;
  } 

function get_data($url) {
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}



/*

function getIndex($url,$pattern)
{
	//$pattern = '/var hkex_hsi_index = "(.*)";/';
	preg_match($pattern, $url, $matches);
	$room_id = $matches[1];	

	return $room_id;
}

function getNominalPrice($url) 
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

function getPrice($url,$compareText) 
{
	$html = str_get_html($url);
	
	foreach($html->find('td') as $td) 
	{
		if ($td->plaintext == $compareText) 
		{
			$self_tr = $td->parent();
			$target_tr = $td->parent()->next_sibling();

			//echo "\n\n\n\n".$self_tr."\n\n\n\n";
			//echo "------------------------\n";

			//echo "\n\n\n\n".$target_tr."\n\n\n\n";
			//echo "------------------------\n";

			$counter = 0;
			foreach ($self_tr->find('td') as $td2)
			{
				//echo $td2."\n";		
				//echo $compareText;
				//echo $counter;
					
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
*/

/*
foreach ( $html->find('tr') as $tr)
{
	//echo $tr->innertext;
	
	$counter = 0;
	$match = 0;
	foreach ( $tr->find('td p') as $td)
	{
		$counter++;

		//echo $counter;
		
		if ($td->plaintext == 'Nominal Price') 
		{
			//echo $td;
			$match =1;
			$target_tr=$tr->next_sibling();
			//echo $target_tr;
			break;
		}
	}
	
	echo "Counter=".$counter."\n";
	echo $target_tr;
		
	echo "Match=".$match."\n";

	

}

foreach($html->find('td p') as $td) 
{
	if ($td->plaintext == 'Nominal Price') 
	{
		$table = $td->parent()->parent()->parent();
				
		foreach ($table->find('comment') as $element)
		{
   			$element->outertext = '';
		}
		
		echo "\n\n\n\n".$table."\n\n\n\n";
		echo "------------------------\n";
		
		foreach ($table->find('tr') as $tr)
		{	
			echo "\n\n\n\n".$tr."\n\n\n\n";
			echo "------------------------\n";
			
			$counter = 0;
			foreach ($tr->find('td p') as $td2)
			{
				echo $td2;
				
				$counter++;
				
				if ($td2->plaintext == 'Nominal Price') 
				{
					echo "Found Counter=".$counter."\n";					
					$target_tr=$tr->next_sibling();
					break;
				}
			}
			
			echo "Outside Counter=".$counter."\n";		
		
			if ($counter>0)
			{
				echo $target_tr;
				
				$result = $target_tr->find('td p', $counter)->plaintext;
				echo $result;
				

				$arr=array();
				
				foreach ($target_tr->find('td p') as $td)
				{
					array_push($arr, $td->plaintext);
				}
				
				for ($i=0; $i<count($arr); $i++)
				{
					echo $arr[$i]."\n";
				}
			}
		}


		
		break;
	}
}
*/


/*
$dom = new DOMDocument();
@$dom->loadHTMLFile('http://www.hkex.com.hk/eng/invest/company/quote_page_e.asp?WidCoID=16');
$xpath = new DOMXPath($dom);

foreach($xpath->query('//td[1]') as $td){
  echo $td->nodeValue;
}
*/




?>