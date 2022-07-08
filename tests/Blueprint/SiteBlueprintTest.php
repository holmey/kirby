<?php

namespace Kirby\Blueprint;

/**
 * @covers \Kirby\Blueprint\SiteBlueprint
 */
class SiteBlueprintTest extends TestCase
{
	/**
	 * @covers ::__construct
	 */
	public function testConstruct()
	{
		$blueprint = new SiteBlueprint(
			model: $site = $this->site()
		);

		$this->assertSame($site, $blueprint->model);
		$this->assertSame('site', $blueprint->type);
		$this->assertSame('site', $blueprint->id);

		$this->assertInstanceOf(SiteOptions::class, $blueprint->options);
		$this->assertInstanceOf(Url::class, $blueprint->preview);
	}
}