<?php 

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
//可能会有很多模块相同的类，所以为了加以区分加上命名空间
use app\controller as AppC;//这个是app模块

/*------------------------------------------------------
 | 注意事项
 |-------------------------------------------------------
 | 1. Appc 这个是app模块,可能会有很多模块相同的类，所以为了
 |    加以区分加上命名空间
 | 2. $collection->add($par1,$par2);注意$par1参数是不能重
 |    复的，重复后后面会覆盖前面设置好的路由
 | 3. _controller 为控制器，_action 为控制器中的方法，_view
 |    直接调用视图
 | 
 */

$collection = new RouteCollection();
$collection->add('blog_list', new Route('/blog', array(
    '_controller' => 'AppBundle:Blog:list',
)));
$collection->add('blog_show', new Route('/blog/{slug}', array(
    '_controller' => 'AppBundle:Blog:show',
)));

//这里必须加上::class 不然会报错 Uncaught Error: Undefined constant
//'app\controller\IndexController' in

$collection->add('index', new Route('/', array(
    '_controller' => AppC\IndexController::class, 
    '_action' => 'index',
)));


$collection->add('index_test', new Route('/test/{a}', array(
    '_controller' => AppC\IndexController::class,
    '_action' => 'test',
)));

$collection->add('test', new Route('/utest', array(
    '_controller' => AppC\UserController::class,
    '_action'=>'test',
)));

//直接调用视图
$collection->add('welcome', new Route('/welcome', array(
    '_view' =>'welcome',
)));


return $collection;

