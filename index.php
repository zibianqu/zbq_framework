<?php
// echo count(array(1,2,3,4));
// echo "<br>";
// echo count("abcde");
// echo "<br>";

define('DEBUG', true);

require __DIR__.'/vendor/autoload.php';
//初始化一些配置
require __DIR__.'/bootstrap/app.php';

//启动
start();
