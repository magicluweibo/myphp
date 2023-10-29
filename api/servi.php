<?php
if (isset($_GET['url'])) {
    $url = $_GET['url'];
    if (strpos($url, 'http') !== 0) {
        $url = base64_decode($url);
    }
}



	
	// 设置Content-Type的header
header('Content-Type: video/mp4');  // 根据实际情况设置正确的Content-Type

// 打开组播直播源的URL作为输入流
$stream = fopen($url, 'r');

// 读取并输出数据，同时刷新输出缓冲区给用户
while (!feof($stream)) {
    $data = fread($stream, 8192);  // 每次读取8192字节的数据
    echo $data;
    flush();  // 刷新输出缓冲区
}

// 关闭输入流
fclose($stream);
	
	
	
?>