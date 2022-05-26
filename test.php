#!/usr/bin/env php
<?php
require __DIR__ . '/Worker.php';

\Worker\Worker::$logFile = __DIR__ . '/log.log';
\Worker\Worker::$pidFile = __DIR__ . '/worker.pid';
$worker = new \Worker\Worker();
// 2 processes
$worker->name = 'test';
$worker->count = 2; //进程数
$worker->alarm = 100; //失败预警值
$worker->onWorkerStart = function (\Worker\Worker $worker) {
    //todo 引用代码
    //增加定时器
/*    \Worker\Timer::add(6, function () use ($worker) {
        echo 'okT--------------------------------------------------' . $worker->id . ':' . time() . PHP_EOL;
    });*/
    // redis订阅示例 start
    $cfg = array(
        'redis' => array(
            'host' => '192.168.0.246',
            'port' => 6379,
            'password' => '123456',
            'select' => 7, //选择库
            'pconnect'=>true, //长连接
        ),
        'log_dir' => __DIR__ . '/log/', //日志记录主目录名称
        'log_size' => 4194304,// 日志文件大小限制
        'log_level' => 2,// 日志记录等级
    );
    function redis($name = 'redis')
    {
        lib_redis::$isExRedis = false;
        return lib_redis::getInstance(GetC($name));
    }
    require __DIR__ . '/../myphp/base.php';
    $result = redis()->subscribe('abc', 'ab');
    if(isset($result[0]) && $result[0]=='subscribe'){
        \Worker\Worker::$globalEvent->add(redis()->getSocket(), \Worker\Worker::$globalEvent::EV_READ, function($socket) use($worker){
            $result = redis()->parseResponse();
            if(isset($result[0]) && $result[0]=='message'){
                echo 'worker'.$worker->id.': '.json_encode($result).PHP_EOL;
            }
        });
    }
    //redis订阅示例 end
};
$worker->onRun = function (\Worker\Worker $worker) {
    $rand = mt_rand(0, 9); //模拟处理结果
    if ($rand == 0) {
        echo 'fail---' . $worker->id . ':' . time() . PHP_EOL;
        $result = false; //失败
    } elseif ($rand <= 5) {
        echo 'ok-----' . $worker->id . ':' . time() . PHP_EOL;
        $result = true; //成功
    } else {
        $result = null; //没有任何处理
    }
    return $result; //运行结果
};
$worker->onAlarm = function (\Worker\Worker $worker) {
    echo date("Y-m-d H:i:s") . '................. alarm ................. ' . PHP_EOL;
};
$worker->onWorkerStop = function (\Worker\Worker $worker) {
    echo 'end', PHP_EOL;
};
// Run worker
\Worker\Worker::runAll();