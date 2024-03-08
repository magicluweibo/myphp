<?php
	$id = $_GET['id'] ?? null;
$ts = $_GET['ts'] ?? null;
	
    if ($id)
	$php_url = get_php_url();

	$url = 'http://127.0.0.1:35455/youtube/'.$id.'';
	$redirect_url = get_redirect_url($url);
	$response = get_data($redirect_url,$php_url);
if ($ts)
    get_ts($ts);











////////////////////////////////////////////////////////




function get_data($url,$php_url){ 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (QtEmbedded; U; Linux; C) AppleWebKit/533.3 (KHTML, like Gecko) MAG200 stbapp ver: 2 rev: 250 Safari/533.3");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json"));
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, false);
    $response = curl_exec($ch);
    curl_close($ch);
	$lines = explode("\n", $response);

	for ($i = 0; $i < count($lines); $i++) {
		$line = trim($lines[$i]); // 去除行首和行尾空格

		if ($line !== "") { // 判断行是否为空
			if (substr($line, 0, 1) !== "#") {

				$lines[$i] = base64_encode($lines[$i]);		
				//$lines[$i] = 'http://rollschen.hk3.345888.xyz.cdn.cloudflare.net/base64.php?ts='.$lines[$i].'';
				$lines[$i] = ''.$php_url.'?ts='.$lines[$i].'';
				//$lines[$i] = 'https://magicweber.x10.mx/youtube_base64.php?ts='.$lines[$i].'';
			}
		}
	}
$modified_ts_data = implode("\n", $lines);
echo $modified_ts_data;
return $modified_ts_data;
}

function get_redirect_url($url) {
  // 将 CURL 中的头部信息和主体一并返回
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
  $response = curl_exec($ch);
  $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
  $header = substr($response, 0, $header_size);
  curl_close($ch);

  // 解析头部信息中的 Location 字段，找到重定向后的地址
  preg_match_all('/Location:(.*?)\n/', $header, $matches);
  $redirect_url = array_pop($matches[1]);
  return trim($redirect_url);
}

function get_php_url(){  //获取php实时准确url
	$php_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$parsedUrl = parse_url($php_url);
	$query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';
	$urlWithoutQuery = $parsedUrl['scheme'].'://'.$parsedUrl['host'].$parsedUrl['path'];
	return $urlWithoutQuery;	
}

function get_ts($ts){
	if(isset($_GET['ts'])){
    $url = base64_decode($_GET['ts']);

    $redirect_url = get_redirect_url($url);
   
   
   
   
   
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $redirect_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_USERAGENT, 'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36 Edg/109.0.1518.78' );
    $content = curl_exec($ch);
    curl_close($ch);
    header('Content-type: video/mp2t');
    echo $content;
	}else{
    echo 'Invalid request';
}
	
	
	
}

?>