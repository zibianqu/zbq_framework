<?php
namespace app\controller;


use app\model\Model;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function index()
    {

        try {
//             $result=DB()->table('test')->get();
//             var_dump($result);
            
            $m=new Model();
            $result=$m->all();

        } catch (\Exception $e){
          echo $e->getMessage();
        }
        echo "welcome to aproject!";
    }
    
    public function test($a)
    {
        echo "this is test".$a;
    } 
}