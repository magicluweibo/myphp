<?php
$ts = $_GET['ts'] ?? '';
$ts = base64_decode($_GET['ts']);
	$parts = explode('/', $ts);
	$id = $parts[count($parts) - 2];
	$output = zxCurl($id,$ts);
	
	echo $output;
	
function zxCurl($id,$m3u8)
{
    $headers = [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
	'Referer: https://hkdvb.com/'.$id.''
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $m3u8);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $data = curl_exec($ch);
    curl_close($ch);	
	header('Content-type: video/mp2t');
	return $data;

	}
		
	
	
    
}	
	


?>