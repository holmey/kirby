<?php

namespace Kirby\Blueprint;

/**
 * Property
 *
 * @package   Kirby Blueprint
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      https://getkirby.com
 * @copyright Bastian Allgeier
 * @license   https://opensource.org/licenses/MIT
 */
abstract class Property
{
	public function __toString(): string
	{
		return $this->toString();
	}

	public function toString(): string
	{
		return (string)$this->value() ?? '';
	}

	public function value()
	{
		return $this->value;
	}
}