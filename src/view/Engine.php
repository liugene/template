<?php

namespace linkphp\template\view;

abstract class Engine
{

    abstract public function display($template);

    abstract public function assign($name,$value=null);

}
