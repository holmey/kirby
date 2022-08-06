<?php

namespace Kirby\Field;

use Kirby\Cms\ModelWithContent;
use Kirby\Value\HtmlValue;

/**
 * Writer field
 *
 * @package   Kirby Field
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      https://getkirby.com
 * @copyright Bastian Allgeier
 * @license   https://opensource.org/licenses/MIT
 */
class WriterField extends InputField
{
	public const TYPE = 'writer';
	public HtmlValue $value;

	public function __construct(
		public string $id,
		public bool $inline = false,
		public FieldIcon|null $icon = null,
		public WriterFieldMarks|null $marks = null,
		public WriterFieldNodes|null $nodes = null,
		public FieldPlaceholder|null $placeholder = null,
		...$args
	) {
		parent::__construct($id, ...$args);

		$this->value = new HtmlValue(
			required: $this->required
		);
	}

	public function defaults(): void
	{
		$this->marks ??= new Marks();
		$this->nodes ??= new Nodes();
	}

	public function render(ModelWithContent $model): array
	{
		return parent::render($model) + [
			'icon'        => $this->icon?->render($model),
			'inline'      => $this->inline,
			'marks'       => $this->marks?->render($model),
			'nodes'       => $this->nodes?->render($model),
			'placeholder' => $this->placeholder?->render($model),
		];
	}
}
