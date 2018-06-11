<?php

namespace linkphp\template;

abstract class Engine
{

    protected $_compiler;

    protected $config = [];

    /**
     * @var Storage;
     */
    private $storage;

    public function __construct(Compiler $compiler)
    {
        $this->_compiler = $compiler;
    }

    public function config($name, $value = null)
    {
        if (is_array($name)) {
            $this->config = array_merge($this->config, $name);
        } else {
            $this->config[$name]   = $value;
        }
    }

    public function getConfig($name)
    {
        return $this->config[$name];
    }

    /**
     * @return  Storage;
     */
    public function storage()
    {
        if($this->storage){
            return $this->storage;
        }
        $class = 'linkphp\\template\\storage\\' . ucfirst($this->config['storage_drive']);
        $this->storage = new $class;
        return $this->storage;
    }

    abstract public function assign($name,$value=null);

    abstract public function display($template = null);

}