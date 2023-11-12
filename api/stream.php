<?php
if (isset($_GET['url'])) {
    $url = $_GET['url'];
    if (strpos($url, 'http') !== 0) {
        $url = base64_decode($url);
    }
}

 set_time_limit(0);



// 设置响应头，告诉客户端内容的类型
header('Content-Type: video/mp2t');

// 输出直播流内容给客户端
readfile($url);
?>