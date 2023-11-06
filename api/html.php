<?php
$url = $_GET['url'] ?? '';
if (strpos($url, 'http') !== 0) {
        $url = base64_decode($url);
    }
	
$html_code = zxCurl($url);
echo $html_code;
	
function zxCurl($url)
{
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $data = curl_exec($ch);
    curl_close($ch);
	return $data;
	
    
}	
	
	
?>