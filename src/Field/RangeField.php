<?php

namespace Kirby\Field;

use Kirby\Cms\ModelWithContent;

/**
 * Range field
 *
 * @package   Kirby Field
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      https://getkirby.com
 * @copyright Bastian Allgeier
 * @license   https://opensource.org/licenses/MIT
 */
class RangeField extends NumberField
{
	public const TYPE = 'range';

	public function __construct(
		public string $id,
		public bool $tooltip = true,
		...$args
	) {
		parent::__construct($id, ...$args);
	}

	public function render(ModelWithContent $model): array
	{
		return parent::render($model) + [
			'tooltip' => $this->tooltip
		];
	}
}