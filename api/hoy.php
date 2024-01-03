<?php
// 获取请求的URI
$id = $_GET['id'];
$flv = $_GET['flv'];

// 构建目标URL
$targetUrl = "https://hoytv-live-stream.hoy.tv/".$id."/" . $flv;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $targetUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$output = curl_exec($ch);
curl_close($ch);
header("Content-Type: application/x-mpegurl");

$output = str_replace("index-fhd", "index.php?id=".$id."&flv=index-fhd", $output); 

echo $output;
?>