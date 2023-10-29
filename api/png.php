<?php
$url = $_GET['url'] ?? null;
//$url = 'https://4gtvimg2.4gtv.tv/4gtv-Image/Channel/mobile/logo_4gtv_4gtv-4gtv003_mobile.png';

$response = get_data($url);

function get_data($url){ 
    $ch = curl_init();
	$headers = [
	'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36'
];
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json"));
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);
	//header('Content-Type: application/octet-stream');
    //header("Content-Transfer-Encoding: Binary");
	header('Content-Type: image/png'); // 假设这是PNG图像，根据实际情况更改
	echo $response;
	return $response;
}

