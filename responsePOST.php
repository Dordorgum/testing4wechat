<?php

$wechatObj = new wechatCallbackapiTest();
$wechatObj->responseMsg();
 
class wechatCallbackapiTest
{
    public function responseMsg()
    {
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		echo "test success - ".$postStr;
		
      	//extract post data
      	/*
		if (!empty($postStr))
		{      
			echo "test success - ".$postStr;
		}*/
	}
}

?>