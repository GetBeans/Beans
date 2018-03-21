<?php
/**
 * Tests for beans_open_markup_e().
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\HTML;

use Beans\Framework\Tests\Unit\API\HTML\Includes\HTML_Test_Case;
use Brain\Monkey;

require_once __DIR__ . '/includes/class-html-test-case.php';

/**
 * Class Tests_BeansOpenMarkupE
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansOpenMarkupE extends HTML_Test_Case {

	/**
	 * Test beans_open_markup_e() should echo empty when the tag is set to null.
	 */
	public function test_should_echo_empty_when_tag_set_to_null() {
		Monkey\Functions\expect( 'beans_open_markup' )
			->once()
			->with( 'beans_archive_title', null, array( 'class' => 'uk-article-title' ) )
			->andReturn( null );

		ob_start();
		beans_open_markup_e( 'beans_archive_title', null, array( 'class' => 'uk-article-title' ) );
		$this->assertEquals( '', ob_get_clean() );
	}

	/**
	 * Test beans_open_markup_e() should echo the HTML element only when before or prepend hooks are not registered.
	 */
	public function test_should_echo_html_element_when_hooks_not_registered() {
		Monkey\Functions\expect( 'beans_open_markup' )
			->once()
			->with( 'beans_archive_title', null, array( 'class' => 'uk-article-title' ) )
			->andReturn( '<h1 class="uk-article-title">' );

		// Run the tests.
		ob_start();
		beans_open_markup_e( 'beans_archive_title', null, array( 'class' => 'uk-article-title' ) );
		$this->assertEquals( '<h1 class="uk-article-title">', ob_get_clean() );
	}
}
