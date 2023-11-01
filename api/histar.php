<?php
$url = base64_decode($_GET['url']);
$ts = $_GET['ts'];
if ($url)
	get_data($url);
if ($ts)
    get_ts($ts);
////主程序开始


function get_data($url){
	
	$php_url = get_php_url();
	$headers = [
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36',
	'Referer: histar.tv'
    ];

	$ch2 = curl_init();
	curl_setopt($ch2, CURLOPT_URL, $url);
	curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
	$ts = curl_exec($ch2);
	curl_close($ch2);
	$lines = explode("\n", $ts);
	for ($i = 0; $i < count($lines); $i++) {
		$line = trim($lines[$i]); // 去除行首和行尾空格

		if ($line !== "") { // 判断行是否为空
			if (substr($line, 0, 1) !== "#") {				
				$lines[$i] = base64_encode($lines[$i]);						
				$lines[$i] = ''.$php_url.'?ts='.$lines[$i].'';
			}
		}
	}
	$modified_ts_data = implode("\n", $lines);
	echo $modified_ts_data;
	return $modified_ts_data;
	//echo $ts;	
	//return $ts;
	
	
	
}


function get_ts($ts){
	if(isset($_GET['ts'])){
    $url = base64_decode($_GET['ts']);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
	$headers = [
    'User-Agent: (Windows NT 6.1; Win64; x64) PotPlayer/23.7.7'
    ];
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $content = curl_exec($ch);
    curl_close($ch);
	header('Content-type: video/mp2t');
    echo $content;
	}else{
    echo 'Invalid request';
}
}


function get_php_url(){  //获取php实时准确url,删除查询参数如?ts=
	$php_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$parsedUrl = parse_url($php_url);
	$query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';
	$urlWithoutQuery = $parsedUrl['scheme'].'://'.$parsedUrl['host'].$parsedUrl['path'];
	return $urlWithoutQuery;	  
}

?>