<?php
/*
===============================
* ofiii.php
* author @餃子 20230607
* ofiii.php?type=txt	txt格式列表
* ofiii.php?type=m3u	m3u 文件
===============================
*/

$type = @$_GET['type'];
$content_id = @$_GET['content_id'];
$phpurl=is_https().$_SERVER['HTTP_HOST'].strtok($_SERVER["REQUEST_URI"],'?');
$ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.0.0 Safari/537.36';
if($type){	
	$url = 'https://www.ofiii.com/topic/2';
	$contents = download($url,array());
	eval(preg_replace('/.*?"map":(.*?)\],.*/s','$map = '."$1];",$contents));
	$case = 0;
	$m3u = "#EXTM3U\r\n";
	$text = "";
	foreach($map as $row){
		if($case >0){
			$url1 = 'https://www.ofiii.com/section/'.$row;
			$temp = download($url1,array());
			$temp = preg_replace('/.*?type="application\/json">(.*?)<\/script>.*/s',"$1",$temp);
			$d = json_decode($temp,true);
			$genre = '';
			foreach($d['props']['pageProps'] as $row2){
				$tempoutput = '';
					if(isset($row2['title'])){
						foreach($row2['meta'] as $row3){
							$title = $row3['title'];
							$content_id = $row3['content_id'];
							$logo = $row3['pics']['thumbnail'];
							if(!$logo){
								$logo = $row3['pics']['logo'];
							}
							if(!preg_match('/ofiii/',$content_id)){
								$genre = $row2['title'];
								$m3u .= "#EXTINF:-1 tvg-logo=".'"'.$logo.'"'."group-title=".'"'.$genre.'",'.$title."\r\n".$phpurl.'?content_id='.$content_id."\r\n";	
								$t[$genre][] = $title.",".$phpurl.'?content_id='.$content_id."\r\n";	
							}
						}
					}
				}
		}
		$case +=1;
	}
	$g = '';
	foreach($t as $key=>$value){
		$genre = $key;
		if($g != $genre){
			$g = $genre;
			$text .= $genre.",#genre\r\n";
		}
		foreach($value as $z){
			$text .= $z;
		}
	}
	if($type =='m3u'){
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header('Content-Type: application/vnd.apple.mpegurl'); 
		header("Pragma: no-cache");
		header('Content-Transfer-Encoding: chunked'); 
		header('Content-Length: ' . strlen($m3u)); 
		header("Content-Disposition: attachment; filename=index.m3u");
		echo $m3u;
	}elseif($type == 'txt'){
		header("Content-Type: text/json; charset=UTF-8");
		echo $text;
	}
}elseif($content_id){
	$pd = array(
	"jsonrpc"=> "2.0",
	"id"=> 123,
	"method" => "LoadService.GetURLs",
	"params"=> array(
		"media_type"=> "channel",
		"device_type"=> "pc",
		"asset_id"=> $content_id
		)
	);
	$out = trim(post('https://api.ofiii.com/cdi/v3/rpc',json_encode($pd)));
	$ud = json_decode($out,true);
	$playurl = $ud['result']['asset_urls'][0];
	if($playurl){
		$ex = explode("\n",download($playurl,array()));
		$newplayurl = dirname($playurl).'/'.array_slice($ex,-2)[0];
		//echo $newplayurl;
		
		$header = [
        "User-Agent: okhttp/3.12.11"
    ];
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $newplayurl);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_TIMEOUT, 50);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    //curl_setopt($curl, CURLOPT_PROXY, '192.168.10.171'); //http代理服务器地址
	//curl_setopt($curl, CURLOPT_PROXY, '127.0.0.1'); //http代理服务器地址
    //curl_setopt($curl, CURLOPT_PROXYPORT, '7890'); //http代理服务器端口
    //curl_setopt($curl, CURLOPT_PROXYPORT, '7890'); //http代理服务器端口
    //curl_setopt($curl, CURLOPT_PROXYPORT, '7890'); //http代理服务器端口
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_ENCODING, '');
    $data = curl_exec($curl);
	
	echo $data;
	
		
		
		
		
		
		
		//header("Location: ".$newplayurl);
	}
}
exit;

function download($url,$header){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_USERAGENT,$GLOBALS["ua"]);
	//curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1'); //http代理服务器地址
    //curl_setopt($ch, CURLOPT_PROXYPORT, '7890'); //http代理服务器端口
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($ch, CURLOPT_URL,$url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch,CURLOPT_TIMEOUT,3);
    $result= curl_exec ($ch);
	curl_close ($ch);
    return $result;
}

function post($url,$data){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
	//curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1'); //http代理服务器地址
    //curl_setopt($ch, CURLOPT_PROXYPORT, '7890'); //http代理服务器端口
	curl_setopt($ch,CURLOPT_HEADER,0);
    curl_setopt($ch, CURLOPT_POST,1 );
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json; charset=UTF-8',
		'Accept: application/json'
	));
	curl_setopt($ch, CURLOPT_USERAGENT,$GLOBALS["ua"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0);
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
    $response = curl_exec($ch);
	curl_close ($ch);
	return $response;
}

function is_https(){
	if(!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off'){
		return 'https://';
	}elseif(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'){
		return 'https://';
	}elseif(!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off'){
		return 'https://';
	}
	return 'http://';
}
?>