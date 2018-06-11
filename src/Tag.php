<?php

namespace linkphp\template;

class Tag
{

    private $tag = [
        'if'  => ['tagIf'],
        'elseif'  => ['tagElseif'],
        'else'  => ['tagElse'],
        'switch'  => ['tagSwitch'],
        'case' => ['tagCase'],
        'default' => ['tagDefault']
    ];

    public function addTag($tag, $rule)
    {
        if(is_array($tag)){
            $this->tag = array_merge($this->tag, $tag);
        } else {
            $this->tag[$tag] = $rule;
        }
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function parserTag($content)
    {
        return $content;
    }

    /**
     * if标签解析
     * 格式：
     * {if condition=" $a eq 1"}
     * {elseif condition="$a eq 2" /}
     * {else /}
     * {/if}
     * 表达式支持 eq neq gt egt lt elt == > >= < <= or and || &&
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagIf($tag, $content)
    {
        $condition = !empty($tag['expression']) ? $tag['expression'] : $tag['condition'];
        $condition = $this->parseCondition($condition);
        $parseStr  = '<?php if(' . $condition . '): ?>' . $content . '<?php endif; ?>';
        return $parseStr;
    }

    /**
     * elseif标签解析
     * 格式：见if标签
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagElseif($tag, $content)
    {
        $condition = !empty($tag['expression']) ? $tag['expression'] : $tag['condition'];
        $condition = $this->parseCondition($condition);
        $parseStr  = '<?php elseif(' . $condition . '): ?>';
        return $parseStr;
    }

    /**
     * else标签解析
     * 格式：见if标签
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagElse($tag)
    {
        $parseStr = '<?php else: ?>';
        return $parseStr;
    }

    /**
     * switch标签解析
     * 格式：
     * {switch name="a.name"}
     * {case value="1" break="false"}1{/case}
     * {case value="2" }2{/case}
     * {default /}other
     * {/switch}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagSwitch($tag, $content)
    {
        $name     = !empty($tag['expression']) ? $tag['expression'] : $tag['name'];
        $name     = $this->autoBuildVar($name);
        $parseStr = '<?php switch(' . $name . '): ?>' . $content . '<?php endswitch; ?>';
        return $parseStr;
    }

    /**
     * case标签解析 需要配合switch才有效
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagCase($tag, $content)
    {
        $value = !empty($tag['expression']) ? $tag['expression'] : $tag['value'];
        $flag  = substr($value, 0, 1);
        if ('$' == $flag || ':' == $flag) {
            $value = $this->autoBuildVar($value);
            $value = 'case ' . $value . ':';
        } elseif (strpos($value, '|')) {
            $values = explode('|', $value);
            $value  = '';
            foreach ($values as $val) {
                $value .= 'case "' . addslashes($val) . '":';
            }
        } else {
            $value = 'case "' . $value . '":';
        }
        $parseStr = '<?php ' . $value . ' ?>' . $content;
        $isBreak  = isset($tag['break']) ? $tag['break'] : '';
        if ('' == $isBreak || $isBreak) {
            $parseStr .= '<?php break; ?>';
        }
        return $parseStr;
    }

    /**
     * default标签解析 需要配合switch才有效
     * 使用： {default /}ddfdf
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagDefault($tag)
    {
        $parseStr = '<?php default: ?>';
        return $parseStr;
    }

    /**
     * 解析条件表达式
     * @access public
     * @param  string $condition 表达式标签内容
     * @return string
     */
    public function parseCondition($condition)
    {
        if (strpos($condition, ':')) {
            $condition = ' ' . substr(strstr($condition, ':'), 1);
        }
        $condition = str_ireplace(array_keys($this->comparison), array_values($this->comparison), $condition);
        $this->tpl->parseVar($condition);
        // $this->tpl->parseVarFunction($condition); // XXX: 此句能解析表达式中用|分隔的函数，但表达式中如果有|、||这样的逻辑运算就产生了歧异
        return $condition;
    }

}