<?php
namespace app\controller;

class Controller
{
    static public function __callStatic($name,$arguments){
       echo $name;
       print_r($arguments);
    }
}
