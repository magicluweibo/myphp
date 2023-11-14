<?php

if (isset($_GET['url'])) {
    $url = $_GET['url'];
    if (strpos($url, 'http') !== 0) {
        $url = base64_decode($url);
    }
}//


// 设置浏览器 User-Agent
ini_set('user_agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.93 Safari/537.36');

set_time_limit(0);

header('Content-Type: video/x-flv');
//header('Content-Type: video/mp2t');
//header('Content-Type: video/mp4');




// 输出直播流内容给客户端
readfile($url);
?>