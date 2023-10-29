<?php


$playurl = $_GET['playurl'] ?? null;
$ts = $_GET['ts'] ?? null;

if ($playurl)
	$src=$_REQUEST['playurl'];
	$live = get_live_url($src);
	
	$php_url = get_php_url(); //获取php实时准确url
    $response = get_data($live,$src,$php_url);

if ($ts)
    get_ts($ts);


//echo $src;


function get_data($live,$src,$php_url){ 
	$prefix = str_replace("playlist.m3u8", "", $src);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $live);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (QtEmbedded; U; Linux; C) AppleWebKit/533.3 (KHTML, like Gecko) MAG200 stbapp ver: 2 rev: 250 Safari/533.3");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json"));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1'); //http代理服务器地址
    //curl_setopt($ch, CURLOPT_PROXYPORT, '7890'); //http代理服务器端口
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, false);
    $response = curl_exec($ch);
	curl_close($ch);
	$lines = explode("\n", $response);

for ($i = 0; $i < count($lines); $i++) {
	
    $line = trim($lines[$i]); // 去除行首和行尾空格

    if ($line !== "") { // 判断行是否为空
        if (substr($line, 0, 1) !== "#") {

			//$lines[$i] = base64_encode($lines[$i]);		
			$lines[$i] = $prefix."".$lines[$i];
			$lines[$i] = base64_encode($lines[$i]);	
			$lines[$i] = ''.$php_url.'?ts='.$lines[$i].'';
        }
    }
}
$modified_ts_data = implode("\n", $lines);
echo $modified_ts_data;

return $modified_ts_data;
}



function get_live_url($src){
	$sz1=array('1','2','3','4','5','6','7','8','9','0','A','B','C','D','E','F');
	$ipnum=count($sz1)-1;
	$sz1s=rand(0,$ipnum);
	$sz2=array('A','B','C','D','E','F');
	$ipnum=count($sz2)-1;
	$sz2s=rand(0,$ipnum);
	$sz3=rand(10000000, 99999999);
	$live=str_replace('//','|',$src);
	$ser=str_replace('|','//',strtok($live,'/'));
	$uri=str_replace($ser,'',$src);
	$tid='M'.$sz2[$sz2s].$sz1[$sz1s].$sz2[$sz2s].$sz3.$sz3;
	$day = floor((Time()*1000 + rand(1,999)) / 1000 / 3600 / 24 );
	$sum=md5('tvata nginx auth module'.$uri.$tid.$day);
	$live=$src.'?tid='.$tid.'&ct='.$day.'&tsum='.$sum;

	return $live;
	
}

function get_php_url(){  //获取php实时准确url,删除查询参数如?ts=
	$php_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$parsedUrl = parse_url($php_url);
	$query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';
	$urlWithoutQuery = $parsedUrl['scheme'].'://'.$parsedUrl['host'].$parsedUrl['path'];
	return $urlWithoutQuery;	  
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


//header("Location:$live");exit;
?>