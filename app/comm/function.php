<?php
/**
 * ͨ�÷���
 */
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

/**
 * ������־
 * @param unknown $msg ������Ϣ
 * @param unknown $logger
 * @param unknown $logName
 * @param unknown $level
 */
function write($msg,$logger,$logName,$level=Logger::DEBUG)
{
    //����ȫ�������ļ�
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