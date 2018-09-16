# zbq_framework
本框架是使用composer安装搭建自己的框架。

	引用扩展
1.	路由：Symfony/routing
2.	日志：Monolog/monolog
3.	数据库连接：Illuminate/database
4.	视图：Illuminate/view
更多扩展可以使用composer安装

	核心代码
1.	目录文件：./bootstrap/app.php
2.	数据库操作：可以使用DB()->table(‘test’);使用查询构建器
3.	视图操作：可以使用View()->make($name)->render();
4.	路由操作可以调用controller中的action方法

	路由配置

例子：
```
	//直接调用controller中的方法
$collection->add('index', new Route('/', array(
    '_controller' => AppC\IndexController::class, 
    '_action' => 'index',
)));
```
```
//直接调用视图
$collection->add('welcome', new Route('/welcome', array(
    '_view' =>'welcome',
)));
```

说明：
1.	index 和welcome是路由名称所以是不能重复的
AppC是命名空间，可能会有很多相同的控制器但在不同的命名空间所以加上命名空间
2.	加以区别
3.	_controller为控制器，_action为控制器中的方法,_view直接调用视图

