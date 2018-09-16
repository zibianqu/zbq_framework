<?php 
/**
 * �����ǳ�ʼ��һЩ���ã����ݿ����ӳ�ʼ����·��ӳ��,��ͼ
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

//����ȫ�������ļ�
$config=require_once dirname(__DIR__).'/app/config/config.php';


/*--------------------------------------------------------------------
 | ����laravel�� Illuminate\Database ����ֱ��ʹ�ò�ѯ������
 |--------------------------------------------------------------------
 |
 */
if(!function_exists('DB')){
    function DB()
    {
        //���ݿ���������
        $database=require dirname(__DIR__)."/app/config/database.php";
        $capsule = new Capsule();
        // ��������
        $capsule->addConnection($database);
        //get the single;
        // ����ȫ�־�̬�ɷ���DB
        $capsule->setAsGlobal();
        // ����Eloquent
        $capsule->bootEloquent();
        return $capsule;
    }
}
try {
    //�����ȳ�ʼ��,�Ժ���Ե��ø÷�����ʹ�ò�ѯ������
    DB();
} catch (Exception $e){
    write($e->getMessage(), 'db', 'db_'.date('Ymd').'.log');
}


/*--------------------------------------------------------------------
 | ��ͼģ���ʼ��
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
            //case����û�м��뷵��ֵ������ִ��case�еĴ���
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
 | ·��ӳ�䵽controller��method��view
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
            if(strpos($url,"index.php")!==false){//û��ȥ��index.php��url
                $request_url=explode("index.php", $url);
                $request_url[1]=($request_url[1]!="")?$request_url[1]:'/';
                $parameters = $matcher->match($request_url[1]);
            } else{
                $request_url=$url;
                $parameters = $matcher->match($request_url);
            }
            //�����и������������ͼ������ʾ��ͼ�����û���ж���ʾ������
            //�ж��ǵ��õĿ�����������ͼ
            $param=isset($parameters['_controller'])?'_controller':'';
            $param=isset($parameters['_view'])?'_view':$param;
            switch ($param){
                case '_controller':
                    $class=$parameters['_controller'];
                    if(!class_exists($class,true)){//�ж����Ƿ����
                        throw new Exception("class is not exists class:".$class);
                    }
                    $controller=new $class;
                    $method=$parameters['_action'];
                    if(!method_exists($controller,$method)){//�ж����ֵķ����Ƿ����
                        throw new Exception("method is not exists method:".$method);
                    }
        
                    //����������ŵ�������һ��call_user_func_array����
                    $request_param=array();
                    $request_param=array_reduce(array_flip($parameters),function($v1,$v2)use($parameters){
                        if(strpos($v2, '_')===false){
                            $v1[$v2]=$parameters[$v2];
                        }
                        return $v1;
                    })??array();
                    //     $controller->$method();
                    call_user_func_array(array($controller,$method), $request_param);//����������е���ͨ��������Ҫ��ʵ����
                    return;
                case '_view':
                    $view=$parameters['_view'];
                    echo view()->make($view)->render();
                    return ;
            }
        
        }catch (Exception $e){
            //���ﲶ׽δƥ�䵽·�ɵ��쳣�����������쳣�������ڷ����쳣
            write($e->getMessage(), 'routes', 'routes_'.date('Ymd').'.log');
        } 
    }
    
}



