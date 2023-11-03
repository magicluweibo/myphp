<?php
header('Content-Type: text/json;charset=utf-8');
//require_once 'ua.php';

/**
 * @author: 簡單
 * @link: https://log.37o.cc
 * @date: 2023年01月09日 23:43:46
 * @msg: ttv 代理
 */


$url = 'http://www.ubvip1688.com/ublive/v9/interface/';
$brand = 'OnePlus';
$model = 'HD1900';
$mac = '002748dee76a';
$header = ['brand:' . $brand, 'model:' . $model, 'mac:' . $mac];
$en_key = substr(md5(substr($mac, 0, 5) . $brand . $model . 'fuck0224'), 8, 16);
$de_key = substr(md5('wtf' . substr($mac, 0, 4) . $brand . $model . '0224'), 8, 16);
if (isset($_GET['list'])) {
    $area = openssl_encrypt('area=Taiwan', 'AES-128-CBC', $en_key, 0, $en_key);
    $channels_api = $url . 'getChannels?key=' . urlencode(urlencode($area)) . '%250A';
    //$str = zxCurl($channels_api, $header);
    $str = cache('Channel_list', 'zxCurl', [$channels_api, $header], 3600 * 4);
    $decrypt = openssl_decrypt(json_decode($str)->value, 'AES-128-CBC', $de_key, 0, $de_key);
    $decode = json_decode($decrypt, 1);
    $output = '';
    $previousCategory = '';
    foreach ($decode['channelList'] as $channel) {
        if ($channel['groupCategory'] != $previousCategory) {
            $output .= $channel['groupCategory'] . ',#genre#' . PHP_EOL;
            $previousCategory = $channel['groupCategory'];
        }
        $output .= $channel['channelName'] . ',' . script_url() . '?id=' . $channel['channelId'] . PHP_EOL;
    }
} elseif (isset($_GET['id']) && !empty($_GET['id'])) {
    //verifyUserAgent();
    $id = $_GET['id'];
    $channelId = openssl_encrypt('channelId=' . $id, 'AES-128-CBC', $en_key, 0, $en_key);
    $playUri_api = $url . 'playUri?key=' . urlencode(urlencode($channelId)) . '%250A';
    // $str = zxCurl($playUri_api, $header);
    $str = cache($id, 'zxCurl', [$playUri_api, $header], 3600 * 4);
    $output = openssl_decrypt(json_decode($str)->value, 'AES-128-CBC', $de_key, 0, $de_key);
    $output = json_decode($output)->playUriList[0]->playUri;
    // if (preg_match('/(huya|akamaihd|scdn|ifeng|douyu|bilivideo)/i', $output) != 1) {
    if (preg_match('/(google|ubvip)/i', $output) == 1) {
        if (strstr($output, 'google') != null) {
            preg_match_all('/(https:\/.*\.m3u8)/', zxCurl($output), $playURL, PREG_PATTERN_ORDER);
            $get_m3u8 = zxCurl($playURL[1][count($playURL[1]) - 1]);
            $output = preg_replace('/#EXT-X-DATERANGE:(.*?)\n#EXT-X-CUEPOINT:(.*?)\n/', '', $get_m3u8);
        } else {
            $output = zxCurl($output, $header);
        }
    } else {
        header('location:' . $output);
        exit;
    }
    // echo $output, "\n";
    if (strstr($output, "#EXTM3U") == null) {
        $output = json_decode($output)->data->streams[0]->url;
        header('location:' . $output);
        exit;
    }

    $m3u8s = explode("\n", $output);
    $output = '';
    foreach ($m3u8s as $v) {
        $v = str_replace("\r", '', $v);
        if (strpos($v, ".ts") > 0) {
            if (strstr($v, 'google') != null) {
                $output .= script_url() . "?ts=" . base64_encode($v) . "\n";
            } else {
                $output .= script_url() . "?ts=" . $v . "\n";
            }
        } elseif ($v != '') {
            $output .= $v . "\n";
        }
    }
    // header('Content-Type: application/vnd.apple.mpegurl');
    // header("Content-Disposition: attachment; filename=index.m3u8");
} elseif (isset($_GET['ts']) && !empty($_GET['ts'])) {
    //verifyUserAgent();
    if (strstr($_GET['ts'], 'http') == null) {
        $ts = base64_decode($_GET['ts']);
    } else {
        $ts = $_GET['ts'];
    }
    $output = zxCurl($ts);
}
echo $output;

function zxCurl($url, $headers = null)
{
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}

function script_url()
{
    $http_type = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
        (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
        ? 'https://'
        : 'http://';
    return $http_type . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
}

function strToHex($str)
{
    $hex = "";
    for ($i = 0; $i < strlen($str); $i++) $hex .= dechex(ord($str[$i]));
    $hex = strtoupper($hex);
    return $hex;
}

function hexToStr($hex)
{
    $str = "";
    for ($i = 0; $i < strlen($hex) - 1; $i += 2) $str .= chr(hexdec($hex[$i] . $hex[$i + 1]));
    return $str;
}

function cache($key, $callback, $arguments = [], $expiration = 1800)
{
    $cacheDir = './cache/ttv/';
    $cacheFile = $cacheDir . md5('jalkd##@378' . $key);
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0777, true);
    }
    if (file_exists($cacheFile) && time() < (filemtime($cacheFile) + $expiration)) {
        $data = unserialize(file_get_contents($cacheFile));
    } else {
        $data = call_user_func_array($callback, $arguments);
        if ($data && !empty($data)) {
            file_put_contents($cacheFile, serialize($data));
        }
    }
    return $data;
}