<?php
/**
 * 通用方法
 */
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

/**
 * 错误日志
 * @param unknown $msg 错误信息
 * @param unknown $logger
 * @param unknown $logName
 * @param unknown $level
 */
function write($msg,$logger,$logName,$level=Logger::DEBUG)
{
    //引入全局配置文件
    global $config;
    if(DEBUG)
        echo "<br> error: ".$msg."<br>";
    $logger=new Logger($logger);
    echo $config['log'].'/'.$logName;
    $logger->pushHandler(new StreamHandler($config['log'].'/'.$logName),$level);
    $logger->pushHandler(new FirePHPHandler());
    $logger->addInfo($msg);
}

function url($path)
{
    
}

function route($name)
{
    
}