<?php

namespace Bazalt\Angular\Directive;

use Bazalt\Angular\Scope;
use Analog\Analog;

class NgRepeat extends \Bazalt\Angular\Directive
{

	public function apply()
	{



		$attrs = $this->attributes();
		$attrValue = $attrs['ng-repeat'];
		if (!preg_match('|(?<item>.*)\s*in\s*(?<array>.*)|im', $attrValue, $matches)) {
			Analog::error(sprintf('Invalid value "%s" for ng-repeat directive', $attrValue));
			throw new \Exception(sprintf('Invalid value "%s" for ng-repeat directive', $attrValue));
			return;
		}
		$item = trim($matches['item']);
		$array = trim($matches['array']);

		$parent = $this->element->parentNode;
		$nodes = [];
		$arrays = $this->scope->getValue($array);
		foreach ($arrays as $value) {
			$node = $this->element->cloneNode(true);

			$parent->insertBefore($node, $this->element);

			$scope = $this->scope->newScope();
			$scope->offsetSet($item, $value);
			//Store the item element
			$scope->offsetSet('itemNameForLoop', $item);

			$node->removeAttribute('ng-repeat');
			$nodes []= $this->module->parser->parse($node, $scope);
		}
		$parent->removeChild($this->element);
		$this->node->nodes($nodes);
	}
}