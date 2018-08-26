<?php

namespace linkphp\template\view;

use framework\Exception;
use linkphp\template\Engine;

class Link extends Engine
{
    /**
     * 模板编译文件
     * */
    private $temp_c;
    /**
     * 模板编译文件内容
     * */
    private $temp_content;
    /**
     * 模板复制变量
     * */
    private $tVar = [];

    public $compiler_file;

    /**
     * 模板编译
     * @return bool
     * */
    private function fetch($tempfile)
    {
        if($this->storage()->check($this->temp_c, $this->config['cache_time'])){
            return true;
        }
        $this->compiler_file = file_get_contents($tempfile);
        $this->temp_content = $this->_compiler->parser($this);
        $this->temp_c = RUNTIME_PATH . 'temp/temp_c/' . md5($tempfile) . '.c.php';
        $this->storage()->write($this->temp_c, $this->temp_content);
    }
    /**
     * 加载视图方法
     * @param $template
     * @throws Exception
     * */
    public function display($template = null)
    {
        $filename = CACHE_PATH . 'view/' . $template . $this->config['default_theme_suffix'];
        $this->fetch($filename);

        //加载视图文件
        $this->storage()->read($this->temp_c, $this->tVar);

    }
    /**
     * 模板赋值输出方法
     * @param string $name
     * @param string $value
     */
    public function assign($name,$value=null)
    {
        //模板赋值
        if(is_array($name)){
            $this->tVar = $name;
            return;
        }
        $this->tVar[$name] = $value;
    }
}