<?php
$id = $_GET['id'] ?? '';

if (isset($_GET['id']) && !empty($_GET['id'])) {
	$m3u8 = parse($id);
//echo $m3u8;
$prefix = substr($m3u8,0,strrpos($m3u8,'/'));   #取得前缀 但不包含斜杆 /  需要补齐
$prefix = ''.$prefix.'/';
//echo $prefix;

$output = zxCurl($id,$m3u8);
//echo $output;
	if (strstr($output, "EXTM3U")) {
        $m3u8s = explode("\n", $output);
        $output = '';
        foreach ($m3u8s as $v) {
            $v = str_replace("\r", '', $v);
			
            if (strstr($v, ".ts") || strstr($v, ".m4s")) {   #切片行,以.ts结尾是标志
				if(strstr($v,'http') == false){
					$v = $prefix."".$v;   //切片没有前缀  需要手动补齐
				}
				
				
                $output .= scriptUrl() . "?ts=" . base64_encode($v) . "\n";
            } 
			elseif ($v !== '') {     #非切片行   如#EXT-X-PROGRAM-DATE-TIME:2023-10-11T06:21:56Z
                $output .= $v . "\n";
            }
        }
        echo $output;
    }
	
}


elseif (isset($_GET['ts']) && !empty($_GET['ts'])) {
    $ts = base64_decode($_GET['ts']);
	$parts = explode('/', $ts);
	$id = $parts[count($parts) - 2];

	

    $output = zxCurl($id,$ts);
    echo $output;
}



function parse($id){
	$url = 'https://hkdvb.com/'.$id.'';
	$headers = [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
	'Referer: https://hkdvb.com/'.$id.''
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_ENCODING, '');
    $data = curl_exec($ch);
    curl_close($ch);
	$pattern = '/file: "(.*?)"/';
	preg_match($pattern, $data, $matches);
	$m3u8 = $matches[1];
	header("Content-Type: text/plain");
	return $m3u8;
	
}

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

	

	
	if (strstr($m3u8, ".ts") ||strstr($m3u8, ".m4s") ){
		
		//echo '这是aac';
		//header('Content-type: audio/x-aac');
		header('Content-type: video/mp2t');
		//header('Content-type: application/octet-stream');
		return $data;
		
	}

	else{
		//$lines = explode("\n", $data);
    //$slicedLines = array_slice($lines, 0, 20);
    //$slicedData = implode("\n", $slicedLines);

    //return $slicedData;
		
		return $data;
		
	}
		
	
	
    
}

function scriptUrl()
{
    $httpType = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
        (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        ? 'https://'
        : 'http://';
    return $httpType . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
}

?>
