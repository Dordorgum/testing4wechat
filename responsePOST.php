<?php

$wechatObj = new wechatCallbackapiTest();
$wechatObj->responseMsg();
 
class wechatCallbackapiTest
{
    public function responseMsg()
    {
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		echo "test success - ".$postStr;
		echo "_POST -".$_POST["data"];
		echo "_POST dump -".var_dump($_POST);
		
      	//extract post data
      	/*
		if (!empty($postStr))
		{      
			echo "test success - ".$postStr;
		}*/
	}
}

?>