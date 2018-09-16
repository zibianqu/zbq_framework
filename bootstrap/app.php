<?php 
/**
 * 这里是初始化一些配置，数据库链接初始化，路由映射,视图
 */
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\FileViewFinder;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\Engines\FileEngine;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;

//引入全局配置文件
$config=require_once dirname(__DIR__).'/app/config/config.php';


/*--------------------------------------------------------------------
 | 引用laravel中 Illuminate\Database 可以直接使用查询构建器
 |--------------------------------------------------------------------
 |
 */
if(!function_exists('DB')){
    function DB()
    {
        //数据库链接配置
        $database=require dirname(__DIR__)."/app/config/database.php";
        $capsule = new Capsule();
        // 创建链接
        $capsule->addConnection($database);
        //get the single;
        // 设置全局静态可访问DB
        $capsule->setAsGlobal();
        // 启动Eloquent
        $capsule->bootEloquent();
        return $capsule;
    }
}
try {
    //这里先初始化,以后可以调用该方法可使用查询构建器
    DB();
} catch (Exception $e){
    write($e->getMessage(), 'db', 'db_'.date('Ymd').'.log');
}


/*--------------------------------------------------------------------
 | 视图模板初始化
 |--------------------------------------------------------------------
 |
 */
if(!function_exists('view')){
    function view()
    {
        try {
            global $config;
            // dirname(__DIR__).'/resource/views/welcome.blad.php'
            // dirname(__DIR__).'/cache/compile'
            $resolver = new EngineResolver();
            $files=new Filesystem();
            $finder = new FileViewFinder($files, $config['view']['paths']);
            $events=new Dispatcher();
            $factory = new Factory($resolver, $finder, $events);
            //case下面没有加入返回值会依次执行case中的代码
            switch ('file'){
                case 'file':
                    $resolver->register('file', function () {
                        return new FileEngine();
                    });
                case 'php':
                    $resolver->register('php', function () {
                        return new PhpEngine();
                    });
                case 'blade':
                    $bladeCompiler=new BladeCompiler($files, $config['view']['compile']);
                    $resolver->register('blade', function()use($bladeCompiler) {
                        return new CompilerEngine($bladeCompiler);
                    });
            }    
            return $factory;
        } catch (Exception $e){
            write($e->getMessage(), 'views', 'views_'.date('Ymd').'.log');
        }
    }
}


/*--------------------------------------------------------------------
 | 路由映射到controller，method，view
 |--------------------------------------------------------------------
 |
 */
if(!function_exists('start')){
    function start()
    {
        require __DIR__.'/../routes/routes.php';
        try {
            $context = new RequestContext('/');
            $matcher = new UrlMatcher($collection, $context);
            $url=$_SERVER['REQUEST_URI'];
            if(strpos($url,"index.php")!==false){//没有去掉index.php的url
                $request_url=explode("index.php", $url);
                $request_url[1]=($request_url[1]!="")?$request_url[1]:'/';
                $parameters = $matcher->match($request_url[1]);
            } else{
                $request_url=$url;
                $parameters = $matcher->match($request_url);
            }
            //这里有个优先如果有视图，就显示视图，如果没有判断显示控制器
            //判断是调用的控制器还是视图
            $param=isset($parameters['_controller'])?'_controller':'';
            $param=isset($parameters['_view'])?'_view':$param;
            switch ($param){
                case '_controller':
                    $class=$parameters['_controller'];
                    if(!class_exists($class,true)){//判断类是否存在
                        throw new Exception("class is not exists class:".$class);
                    }
                    $controller=new $class;
                    $method=$parameters['_action'];
                    if(!method_exists($controller,$method)){//判断勒种的方法是否存在
                        throw new Exception("method is not exists method:".$method);
                    }
        
                    //将请求参数放到数组中一边call_user_func_array调用
                    $request_param=array();
                    $request_param=array_reduce(array_flip($parameters),function($v1,$v2)use($parameters){
                        if(strpos($v2, '_')===false){
                            $v1[$v2]=$parameters[$v2];
                        }
                        return $v1;
                    })??array();
                    //     $controller->$method();
                    call_user_func_array(array($controller,$method), $request_param);//这里调用类中的普通方法，需要类实例化
                    return;
                case '_view':
                    $view=$parameters['_view'];
                    echo view()->make($view)->render();
                    return ;
            }
        
        }catch (Exception $e){
            //这里捕捉未匹配到路由的异常，不存在类异常，不存在方法异常
            write($e->getMessage(), 'routes', 'routes_'.date('Ymd').'.log');
        } 
    }
    
}



