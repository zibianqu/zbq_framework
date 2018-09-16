<?php 

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
//���ܻ��кܶ�ģ����ͬ���࣬����Ϊ�˼������ּ��������ռ�
use app\controller as AppC;//�����appģ��

/*------------------------------------------------------
 | ע������
 |-------------------------------------------------------
 | 1. Appc �����appģ��,���ܻ��кܶ�ģ����ͬ���࣬����Ϊ��
 |    �������ּ��������ռ�
 | 2. $collection->add($par1,$par2);ע��$par1�����ǲ�����
 |    ���ģ��ظ������Ḳ��ǰ�����úõ�·��
 | 3. _controller Ϊ��������_action Ϊ�������еķ�����_view
 |    ֱ�ӵ�����ͼ
 | 
 */

$collection = new RouteCollection();
$collection->add('blog_list', new Route('/blog', array(
    '_controller' => 'AppBundle:Blog:list',
)));
$collection->add('blog_show', new Route('/blog/{slug}', array(
    '_controller' => 'AppBundle:Blog:show',
)));

//����������::class ��Ȼ�ᱨ�� Uncaught Error: Undefined constant
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

//ֱ�ӵ�����ͼ
$collection->add('welcome', new Route('/welcome', array(
    '_view' =>'welcome',
)));


return $collection;

