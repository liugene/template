<?php

namespace linkphp\template;

use linkphp\http\HttpRequest;
use linkphp\template\view\Link;

class View
{

    private static $_instance;

    /**
     * @var Link
     */
    private $_engine;

    /**
     * 模板替换标签
     * @var array $replace
     */
    private $replace = [];

    public function __construct(HttpRequest $httpRequest, $replace)
    {
        $root = $httpRequest->root();
        $base = $httpRequest->baseFile();
        $baseReplace = [
            '__ROOT__'   => $root,
            '__URL__'    => $base . '/' . '' . '/' . '',
            '__STATIC__' => $root . '/static',
            '__CSS__'    => $root . '/static/css',
            '__JS__'     => $root . '/static/js',
        ];
        $this->replace = array_merge($baseReplace, (array) $replace);
    }

    /**
     * 初始化视图
     * @access public
     * @param array $engine  模板引擎参数
     * @param array $replace  字符串替换参数
     * @return object
     */
    public static function instance($engine = [], $replace = [])
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self(app()->get(\linkphp\http\HttpRequest::class), $engine, $replace);
        }
        return self::$_instance;
    }

    /**
     * 设置当前模板解析的引擎
     * @access public
     * @param array|string $options 引擎参数
     * @return $this
     */
    public function engine($options = [])
    {
        if (is_string($options)) {
            $type    = $options;
            $options = [];
        } else {
            $type = !empty($options['type']) ? $options['type'] : 'Link';
        }

        $class = false !== strpos($type, '\\') ? $type : '\\linkphp\\template\\view\\' . ucfirst($type);
        if (isset($options['type'])) {
            unset($options['type']);
        }
        $this->_engine = app()->get($class);
        return $this;
    }

    /**
     * 加载显示模板视图方法
     * @param string $template
     * @return string
     */
    public function display($template)
    {
        // 页面缓存
        ob_start();
        ob_implicit_flush(0);
        // 渲染输出
        try {
            $this->_engine->display($template);
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }

        // 获取并清空缓存
        $content = ob_get_clean();
        return $content;
    }

    /**
     * 模板赋值输出方法
     * @param string $name
     * @param string $value
     */
    public function assign($name,$value=null)
    {
        $this->_engine->assign($name,$value);
    }

    public function fetch(){}

    /**
     * 配置模板引擎
     * @access private
     * @param string|array  $name 参数名
     * @param mixed         $value 参数值
     * @return $this
     */
    public function config($name, $value = null)
    {
        $this->_engine->config($name, $value);
        return $this;
    }

    /**
     * 视图内容替换
     * @access public
     * @param string|array  $content 被替换内容（支持批量替换）
     * @param string        $replace    替换内容
     * @return $this
     */
    public function replace($content, $replace = '')
    {
        if (is_array($content)) {
            $this->replace = array_merge($this->replace, $content);
        } else {
            $this->replace[$content] = $replace;
        }
        return $this;
    }

}
