<?php

namespace Kirby\Field;

use Kirby\Cms\ModelWithContent;

/**
 * Headline field
 *
 * @package   Kirby Field
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      https://getkirby.com
 * @copyright Bastian Allgeier
 * @license   https://opensource.org/licenses/MIT
 */
class HeadlineField extends DisplayField
{
	public const TYPE = 'headline';

	public function __construct(
		public string $id,
		public bool $numbered = true,
		...$args
	) {
		parent::__construct($id, ...$args);
	}

	public function render(ModelWithContent $model): array
	{
		return parent::render($model) + [
			'numbered' => $this->numbered,
		];
	}
}
