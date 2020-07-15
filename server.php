<?php
class Socket {

    public function __construct()
    {
//        $this->log(__FUNCTION__,'hello socket');
        echo 'socket server starting...' . PHP_EOL;
    }

    public function run() {
        //确保在连接客户端时不会超时
        set_time_limit(0);
        $ip = '127.0.0.1';
        $port = 1992;
        /*
         +-------------------------------
         *    @socket通信整个过程
         +-------------------------------
         *    @socket_create
         *    @socket_bind
         *    @socket_listen
         *    @socket_accept
         *    @socket_read
         *    @socket_write
         *    @socket_close
         +--------------------------------
         */

        /*----------------    以下操作都是手册上的    -------------------*/
        if(($sock = socket_create(AF_INET,SOCK_STREAM,SOL_TCP)) < 0) {
            echo "socket_create() 失败的原因是:".socket_strerror($sock)."\n";
            die();
        }

        if(($ret = socket_bind($sock,$ip,$port)) < 0) {
            echo "socket_bind() 失败的原因是:".socket_strerror($ret)."\n";
            die();
        }

        if(($ret = socket_listen($sock,4)) < 0) {
            echo "socket_listen() 失败的原因是:".socket_strerror($ret)."\n";
            die();
        }
        echo 'socket is started' . PHP_EOL;
        $count = 0;

        do {
            if (($msgsock = socket_accept($sock)) < 0) {
                echo "socket_accept() failed: reason: " . socket_strerror($msgsock) . "\n";
                break;
            } else {
                echo "有人链接成功！".$count." \n";

                //发到客户端
                $msg = '你的id ' . $count . PHP_EOL;
                socket_write($msgsock, $msg, strlen($msg));
                echo "主动发送成功\n";

                $buf = socket_read($msgsock,8192);
                echo "收到的信息:$buf\n";

                if($count >= 5){
                    break;
                };

                $count++;

            }
            //echo $buf;
            socket_close($msgsock);

        } while (true);

        socket_close($sock);
    }

    //Exception日志
    private function log($cmd,$str) {
        $file= './socket.log';
        $text='[Time ' . date('Y-m-d H:i:s') ."]\ncmd:" .$cmd. "\n" .$str. "\n---END---" . "\n";
        if(false !== fopen($file,'a+')){
            file_put_contents($file,$text,FILE_APPEND);
        }
    }

}

$s = new Socket();
$s->run();