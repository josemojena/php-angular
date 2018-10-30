<?php

namespace Bazalt\Angular\Directive;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class NgIf extends \Bazalt\Angular\Directive {
	protected function parseValue( $matches ) {

		$key     = trim( $matches['value'] );
		$filters = isset( $matches['filters'] ) ? explode( '|', trim( $matches['filters'], ' |' ) ) : [];

		$language = new ExpressionLanguage();


		if ( $language->evaluate(
			$key,
			[ 'vm' => (object) [ 'variables' => $this->scope['variables'], 'data' => $this->scope['data'] ] ] ) ) {
			return '.' . $language->evaluate(
					$key,
					[ 'vm' => (object) [ 'variables' => $this->scope['variables'], 'data' => $this->scope['data'] ] ] );
		}

		return "";
	}

	public function apply() {
		$attrs     = $this->attributes();
		$attrValue = $attrs['ng-if'];

		$value = $this->scope->getValue( $attrValue );
		$this->element->removeAttribute( 'ng-if' );
		$scopeData = array(
			'vm' => (object) [
				'variables' => $this->scope['variables'],
				'data'      => $this->scope['data'],
			],
		);
		if ( $this->scope->offsetExists( 'itemNameForLoop' ) == true ) {
			$itemLoop = $this->scope->offsetGet( 'itemNameForLoop' );
			if ( $this->scope->offsetExists( $itemLoop ) != false ) {
				$scopeData[ $itemLoop ] = (object) $this->scope->offsetGet( $itemLoop );
			}
		}
		$language  = new ExpressionLanguage();
		$attrValue = preg_replace_callback( '|\[\s*(?<value>[a-z0-9\.]*)\s*(\|\s*(?<filters>.*))?\s*\]|im', [
			$this,
			'parseValue'
		], $attrValue );

		if ( ! $language->evaluate( $attrValue, $scopeData ) ) {
			$parent = $this->element->parentNode;
			$parent->removeChild( $this->element );
		}

	}
}
