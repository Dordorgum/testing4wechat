<?php

class HKExPageParser
{
	private static $CompanyPageURL = 'http://www.hkex.com.hk/eng/invest/company/profile_page_e.asp?WidCoID=';
	private static $PricePageURL = 'http://www.hkex.com.hk/eng/invest/company/quote_page_e.asp?WidCoID=';

       	
	private function get_data($url) {
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
	public function getCompany($url=$CompanyPageURL)
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
	
	public function getNominalPrice($url=$PricePageURL) 
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
	
	public function getPrice($compareText, $url=$PricePageURL) 
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

}


?>
