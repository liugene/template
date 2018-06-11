<?php

namespace linkphp\template;

abstract class Storage
{

    abstract public function read($cacheFile, $vars = []);

    abstract public function write($cacheFile, $content);

    abstract public function check($cacheFile, $cacheTime);

}