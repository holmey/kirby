<?php

namespace Kirby\Blueprint;

use Kirby\Cms\Page;

/**
 * Page options
 *
 * @package   Kirby Blueprint
 * @author    Bastian Allgeier <bastian@getkirby.com>
 * @link      https://getkirby.com
 * @copyright Bastian Allgeier
 * @license   https://opensource.org/licenses/MIT
 */
class PageOptions extends ModelOptions
{
	public ModelOption $changeSlug;
	public ModelOption $changeStatus;
	public ModelOption $changeTemplate;
	public ModelOption $changeTitle;
	public ModelOption $create;
	public ModelOption $delete;
	public ModelOption $duplicate;
	public ModelOption $preview;
	public ModelOption $read;
	public ModelOption $update;

	public function __construct(
		bool|array|null $changeSlug = null,
		bool|array|null $changeStatus = null,
		bool|array|null $changeTemplate = null,
		bool|array|null $changeTitle = null,
		bool|array|null $create = null,
		bool|array|null $delete = null,
		bool|array|null $duplicate = null,
		bool|array|null $preview = null,
		bool|array|null $read = null,
		bool|array|null $update = null,
	) {
		$this->changeSlug     = ModelOption::factory($changeSlug);
		$this->changeStatus   = ModelOption::factory($changeStatus);
		$this->changeTemplate = ModelOption::factory($changeTemplate);
		$this->changeTitle    = ModelOption::factory($changeTitle);
		$this->create         = ModelOption::factory($create);
		$this->delete         = ModelOption::factory($delete);
		$this->duplicate      = ModelOption::factory($duplicate);
		$this->preview        = ModelOption::factory($preview);
		$this->read           = ModelOption::factory($read);
		$this->update         = ModelOption::factory($update);
	}
}