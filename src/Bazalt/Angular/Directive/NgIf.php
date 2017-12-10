<?php

namespace Bazalt\Angular\Directive;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class NgIf extends \Bazalt\Angular\Directive
{
    public function apply()
    {
        $attrs = $this->attributes();
        $attrValue = $attrs['ng-if'];

        $value = $this->scope->getValue($attrValue);
        $this->element->removeAttribute('ng-if');

        $language = new ExpressionLanguage();

        if (!$language->evaluate(
            $attrValue,
            [ 'vm' => (object)[ 'variables' => $this->scope['variables'], 'data' => $this->scope['data']]]
        )) {            $parent = $this->element->parentNode;
            $parent->removeChild($this->element);
        }
    }
}
