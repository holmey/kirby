<?php

namespace Kirby\Blueprint;

/**
 * Section
 *
 * @package   Kirby Blueprint
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      https://getkirby.com
 * @copyright Bastian Allgeier
 * @license   https://opensource.org/licenses/MIT
 */
class Section extends Node
{
	public Column $column;
	public string $type;

	public function __construct(
		Column $column,
		string $id
	) {
		parent::__construct(
			id: $id,
			model: $column->model
		);

		$this->column = $column;
	}
}