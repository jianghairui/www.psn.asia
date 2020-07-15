<?php

error_reporting(E_ALL);
set_time_limit(0);
$port = 1992;
$ip = "127.0.0.1";

/*
 +-------------------------------
 *    @socket连接整个过程
 +-------------------------------
 *    @socket_create
 *    @socket_connect
 *    @socket_write
 *    @socket_read
 *    @socket_close
 +--------------------------------
 */

//创建socket
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket < 0) {
    echo "socket_create() failed: reason: " . socket_strerror($socket) . "\n";
} else {
    echo "socket created.\n";
}

//链接服务端
echo "正在连接 '$ip':'$port'...\n";
$result = socket_connect($socket, $ip, $port);
if ($result < 0) {
    echo "socket_connect() failed.\nReason: ($result) " . socket_strerror($result) . "\n";
}

$msg = "我是client,连接成功啦\r\n";
echo '已连接 正在给服务端发送消息' . PHP_EOL;
//给服务端发消息
$out = '';
if (!socket_write($socket, $msg, strlen($msg))) {
    echo "socket_write() failed: reason: " . socket_strerror($socket) . "\n";
}

while ($out = socket_read($socket, 8192)) {
    echo "接收服务器回传成功:" . $out . PHP_EOL;
}

socket_close($socket);
echo "断开连接...\n";
