<?php

namespace linkphp\template;

use linkphp\template\view\Link;

class Compiler
{

    /**
     * @var Tag
     */
    private $_tag;

    public function __construct(Tag $tag)
    {
        $this->_tag = $tag;
    }

    public function parser(Link $link)
    {

        $content = $this->_tag->parserTag($link->compiler_file);
        $pregRule_L = '#' . $link->getConfig('set_left_limiter') . '#';
        $pregRule_R = '#' . $link->getConfig('set_right_limiter') . '#';
        $content = preg_replace($pregRule_L,'<?php echo ',$content);
        $content = preg_replace($pregRule_R,'; ?>',$content);
        // 优化生成的php代码
        $content = preg_replace('/\?>\s*<\?php\s(?!echo\b)/s', '', $content);
        // 模板过滤输出
        $replace = $link->getConfig('tpl_replace_string');
        $content = str_replace(array_keys($replace), array_values($replace), $content);
        // 添加安全代码及模板引用记录
        $content = '<?php if (!defined(\'LINKPHP_VERSION\')) exit(); /*' . serialize($link) . '*/ ?>' . "\n" . $content;
        return $content;
    }

}