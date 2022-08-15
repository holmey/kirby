<?php

namespace Kirby\Panel;

use Kirby\Cms\File as CmsFile;
use Kirby\Toolkit\I18n;
use Throwable;

/**
 * Provides information about the file model for the Panel
 * @since 3.6.0
 *
 * @package   Kirby Panel
 * @author    Nico Hoffmann <nico@getkirby.com>
 * @link      https://getkirby.com
 * @copyright Bastian Allgeier
 * @license   https://getkirby.com/license
 */
class File extends Model
{
	public function __construct(
		protected CmsFile $model
	) {
	}

	/**
	 * Breadcrumb array
	 */
	public function breadcrumb(): array
	{
		$breadcrumb = [];
		$parent     = $this->model->parent();

		switch ($parent::CLASS_ALIAS) {
			case 'user':
				/** @var \Kirby\Cms\User $parent */
				// The breadcrumb is not necessary
				// on the account view
				if ($parent->isLoggedIn() === false) {
					$breadcrumb[] = [
						'label' => $parent->username(),
						'link'  => $parent->panel()->url(true)
					];
				}
				break;
			case 'page':
				/** @var \Kirby\Cms\Page $parent */
				$breadcrumb = $this->model->parents()->flip()->values(fn ($parent) => [
					'label' => $parent->title()->toString(),
					'link'  => $parent->panel()->url(true),
				]);
		}

		// add the file
		$breadcrumb[] = [
			'label' => $this->model->filename(),
			'link'  => $this->url(true),
		];

		return $breadcrumb;
	}

	/**
	 * Provides a kirbytag or markdown
	 * tag for the file, which will be
	 * used in the panel, when the file
	 * gets dragged onto a textarea
	 *
	 * @internal
	 * @param string|null $type (`auto`|`kirbytext`|`markdown`)
	 */
	public function dragText(string|null $type = null, bool $absolute = false): string
	{
		$type = $this->dragTextType($type);
		$url  = $absolute ? $this->model->id() : $this->model->filename();

		if ($dragTextFromCallback = $this->dragTextFromCallback($type, $url)) {
			return $dragTextFromCallback;
		}

		if ($type === 'markdown') {
			if ($this->model->type() === 'image') {
				return '![' . $this->model->alt() . '](' . $url . ')';
			}

			return '[' . $this->model->filename() . '](' . $url . ')';
		}

		if ($this->model->type() === 'image') {
			return '(image: ' . $url . ')';
		}
		if ($this->model->type() === 'video') {
			return '(video: ' . $url . ')';
		}

		return '(file: ' . $url . ')';
	}

	/**
	 * Provides options for the file dropdown
	 */
	public function dropdown(array $options = []): array
	{
		$file = $this->model;

		$defaults = $file->kirby()->request()->get(['view', 'update', 'delete']);
		$options  = array_merge($defaults, $options);

		$permissions = $this->options(['preview']);
		$view        = $options['view'] ?? 'view';
		$url         = $this->url(true);
		$result      = [];

		if ($view === 'list') {
			$result[] = [
				'link'   => $file->previewUrl(),
				'target' => '_blank',
				'icon'   => 'open',
				'text'   => I18n::translate('open')
			];
			$result[] = '-';
		}

		$result[] = [
			'dialog'   => $url . '/changeName',
			'icon'     => 'title',
			'text'     => I18n::translate('rename'),
			'disabled' => $this->isDisabledDropdownOption('changeName', $options, $permissions)
		];

		$result[] = [
			'click'    => 'replace',
			'icon'     => 'upload',
			'text'     => I18n::translate('replace'),
			'disabled' => $this->isDisabledDropdownOption('replace', $options, $permissions)
		];

		if ($view === 'list') {
			$result[] = '-';
			$result[] = [
				'dialog'   => $url . '/changeSort',
				'icon'     => 'sort',
				'text'     => I18n::translate('file.sort'),
				'disabled' => $this->isDisabledDropdownOption('update', $options, $permissions)
			];
		}

		$result[] = '-';
		$result[] = [
			'dialog'   => $url . '/delete',
			'icon'     => 'trash',
			'text'     => I18n::translate('delete'),
			'disabled' => $this->isDisabledDropdownOption('delete', $options, $permissions)
		];

		return $result;
	}

	/**
	 * Returns the setup for a dropdown option
	 * which is used in the changes dropdown
	 * for example
	 */
	public function dropdownOption(): array
	{
		return [
			'icon' => 'image',
			'text' => $this->model->filename(),
		] + parent::dropdownOption();
	}

	/**
	 * Returns an array of all actions
	 * that can be performed in the Panel
	 *
	 * @param array $unlock An array of options that will be force-unlocked
	 */
	public function options(array $unlock = []): array
	{
		$options = parent::options($unlock);

		try {
			// check if the file type is allowed at all,
			// otherwise it cannot be replaced
			$this->model->match($this->model->blueprint()->accept());
		} catch (Throwable) {
			$options['replace'] = false;
		}

		return $options;
	}

	/**
	 * Returns the full path without leading slash
	 */
	public function path(): string
	{
		return 'files/' . $this->model->filename();
	}

	/**
	 * Prepares the response data for file pickers
	 * and file fields
	 */
	public function pickerData(array $params = []): array
	{
		$id   = $this->model->id();
		$name = $this->model->filename();

		if (empty($params['model']) === false) {
			$parent   = $this->model->parent();
			$uuid     = $parent === $params['model'] ? $name : $id;
			$absolute = $parent !== $params['model'];
		}

		$params['text'] ??= '{{ file.filename }}';

		return array_merge(parent::pickerData($params), [
			'filename' => $name,
			'dragText' => $this->dragText('auto', $absolute ?? false),
			'type'     => $this->model->type(),
			'url'      => $this->model->url(),
			'uuid'     => $uuid ?? $id,
		]);
	}

	/**
	 * Returns the data array for the
	 * view's component props
	 * @internal
	 */
	public function props(): array
	{
		$file       = $this->model;
		$dimensions = $file->dimensions();
		$siblings   = $file->templateSiblings()->sortBy(
			'sort',
			'asc',
			'filename',
			'asc'
		);


		return array_merge(
			parent::props(),
			$this->prevNext(),
			[
				'blueprint' => $this->model->template() ?? 'default',
				'model' => [
					'content'    => $this->content(),
					'dimensions' => $dimensions->toArray(),
					'extension'  => $file->extension(),
					'filename'   => $file->filename(),
					'link'       => $this->url(true),
					'mime'       => $file->mime(),
					'niceSize'   => $file->niceSize(),
					'id'         => $id = $file->id(),
					'parent'     => $file->parent()->panel()->path(),
					'template'   => $file->template(),
					'type'       => $file->type(),
					'url'        => $file->url(),
				],
				'preview' => [
					'image'   => $this->image()?->render($file),
					'url'     => $url = $file->previewUrl(),
					'details' => [
						[
							'title' => I18n::translate('template'),
							'text'  => $file->template() ?? '—'
						],
						[
							'title' => I18n::translate('mime'),
							'text'  => $file->mime()
						],
						[
							'title' => I18n::translate('url'),
							'text'  => $id,
							'link'  => $url
						],
						[
							'title' => I18n::translate('size'),
							'text'  => $file->niceSize()
						],
						[
							'title' => I18n::translate('dimensions'),
							'text'  => $file->type() === 'image' ? $file->dimensions() . ' ' . I18n::translate('pixel') : '—'
						],
						[
							'title' => I18n::translate('orientation'),
							'text'  => $file->type() === 'image' ? I18n::translate('orientation.' . $dimensions->orientation()) : '—'
						],
					]
				]
			]
		);
	}

	/**
	 * Returns navigation array with
	 * previous and next file
	 * @internal
	 */
	public function prevNext(): array
	{
		$file     = $this->model;
		$siblings = $file->templateSiblings()->sortBy(
			'sort',
			'asc',
			'filename',
			'asc'
		);

		return [
			'next' => function () use ($file, $siblings): array|null {
				$next = $siblings->nth($siblings->indexOf($file) + 1);
				return $this->toPrevNextLink($next, 'filename');
			},
			'prev' => function () use ($file, $siblings): array|null {
				$prev = $siblings->nth($siblings->indexOf($file) - 1);
				return $this->toPrevNextLink($prev, 'filename');
			}
		];
	}
	/**
	 * Returns the url to the editing view
	 * in the panel
	 */
	public function url(bool $relative = false): string
	{
		$parent = $this->model->parent()->panel()->url($relative);
		return $parent . '/' . $this->path();
	}

	/**
	 * Returns the data array for
	 * this model's Panel view
	 * @internal
	 */
	public function view(): array
	{
		return [
			'breadcrumb' => fn (): array => $this->model->panel()->breadcrumb(),
			'component'  => 'k-file-view',
			'props'      => $this->props(),
			'search'     => 'files',
			'title'      => $this->model->filename(),
		];
	}
}
