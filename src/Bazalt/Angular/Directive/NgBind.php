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


        foreach ($filters as $filter) {

            $filter_split = explode(':', $filter);
            $filter_function = array_slice($filter_split, 0, 1);
            $filter_function = trim($filter_function[0]);
            $filter_arguments = array_slice($filter_split, 1);
            array_unshift($filter_arguments, $value);

            if (array_key_exists($filter_function, $this->scope['filters'])) {
                $value = call_user_func_array($this->scope['filters'][$filter_function], $filter_arguments);
            }
        }

        return $value;
    }

    public function apply()
    {
        $text = $this->element->wholeText;
        $specialCases = array('import-url' => false);

        if (preg_match('/\{\{.*?\'@import\s*url\s*\(\s*\'/', $text)) {
            $text = preg_replace_callback('/\{\{.*?\'@import\s*url\s*\(\s*\'\s*\+\s*(?<value>[a-z0-9A-Z\.]*)(\s*\+\s*\'\)\s*;\'\s*}}\s*)/', function ($matches) {
                return sprintf("{{%s}}", str_replace("\s", "", $matches['value']));
            }, $text);
            $specialCases['import-url'] = true;
        }

        $value = preg_replace_callback('|{{\s*(?<value>[a-z0-9\.]*)\s*(\|\s*(?<filters>.*))?\s*}}|im', [
            $this,
            'parseValue'
        ], $text);

        foreach ($specialCases as $key => $val) {
            if ($key == 'import-url' && $val == true) {
                $this->element->nodeValue = sprintf("@import url('%s');", trim(str_replace("\n", "", $value)));
            } else {
                $this->element->nodeValue = $value;
            }
        }
    }
}
