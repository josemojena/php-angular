<?php

namespace Bazalt\Angular\Directive;


class NgBind extends \Bazalt\Angular\Directive
{
    protected function parseValue($matches)
    {
        $key = trim($matches['value']);
        $filters = isset($matches['filters']) ? explode('|', trim($matches['filters'], ' |')) : [];

        $value = $this->scope->getValue($key);
        if (!$value && $value != '') {
            return $matches[0];
        }
        
        foreach ( $filters as $filter ){
            $filter_split = explode( ':', $filter);
            $filter_function = array_slice($filter_split, 0, 1);
            $filter_function = trim($filter_function[0]);
            $filter_arguments = array_slice($filter_split, 1);
            array_unshift($filter_arguments, $value);

            if(array_key_exists($filter_function, $this->scope['filters'])){
                $value = call_user_func_array($this->scope['filters'][$filter_function] , $filter_arguments);
            }
        }

        return $value;
    }

    public function apply()
    {
        $this->element->nodeValue = preg_replace_callback('|{{\s*(?<value>[a-z0-9\.]*)\s*(\|\s*(?<filters>.*))?\s*}}|im', [$this, 'parseValue'], $this->element->wholeText);
    }
}

//function probando($arg1,$arg2,$arg3) { return 'you got it!' . '$arg1' .$arg1 .  '$arg2' .$arg2 . '$arg3' .$arg3; }
