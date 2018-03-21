<?php
/**
 * Tests for beans_close_markup().
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
 * Class Tests_BeansCloseMarkup
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansCloseMarkup extends HTML_Test_Case {

	/**
	 * Test beans_close_markup() should return null when the tag is set to null.
	 */
	public function test_should_return_null_when_tag_set_to_null() {
		Monkey\Functions\expect( 'beans_apply_filters' )
			->once()
			->with( 'beans_archive_title_markup', null )
			->andReturnNull();

		$this->assertNull( beans_close_markup( 'beans_archive_title', null ) );
	}

	/**
	 * Test beans_close_markup() should fire "{$id}_append_markup" hooks and include the content in the returned HTML.
	 */
	public function test_should_fire_append_markup_hooks_and_include_content_in_returned_html() {
		Monkey\Functions\when( 'beans_apply_filters' )->returnArg( 2 );
		Monkey\Functions\expect( '_beans_render_action' )
			->once()
			->with( 'beans_archive_title_append_markup' )
			->andReturn( '<!-- _append_markup fired -->' );

		$actual = beans_close_markup( 'beans_archive_title', 'h1' );
		$this->assertStringStartsWith( '<!-- _append_markup fired -->', $actual );
	}

	/**
	 * Test beans_close_markup() should fire "{$id}_after_markup" hooks and include the content in the returned HTML.
	 */
	public function test_should_fire_after_markup_hooks_and_include_content_in_returned_html() {
		Monkey\Functions\when( 'beans_apply_filters' )->returnArg( 2 );
		Monkey\Functions\expect( '_beans_render_action' )
			->once()
			->with( 'beans_archive_title_after_markup' )
			->andReturn( '<!-- _after_markup fired -->' );

		$actual = beans_close_markup( 'beans_archive_title', 'h1' );
		$this->assertStringEndsWith( '<!-- _after_markup fired -->', $actual );
	}

	/**
	 * Test beans_close_markup() should return the built HTML element when append or after hooks are not registered.
	 */
	public function test_should_return_built_html_when_append_or_after_hooks_not_registered() {
		Monkey\Functions\when( 'beans_apply_filters' )->returnArg( 2 );
		Monkey\Functions\when( '_beans_render_action' )->justReturn( '' );

		$this->assertSame( '</h1>', beans_close_markup( 'beans_archive_title', 'h1' ) );
	}

	/**
	 * Test beans_open_markup() should return a built HTML when append or after hooks are registered.
	 */
	public function test_should_return_built_html_when_append_or_after_hooks() {
		Monkey\Functions\when( 'beans_apply_filters' )->returnArg( 2 );
		Monkey\Functions\expect( '_beans_render_action' )
			->once()
			->with( 'beans_archive_title_append_markup' )
			->ordered()
			->andReturn( '<!-- _append_markup fired -->' )
			->andAlsoExpectIt()
			->once()
			->with( 'beans_archive_title_after_markup' )
			->ordered()
			->andReturn( '<!-- _after_markup fired -->' );

		$actual   = beans_close_markup( 'beans_archive_title', 'h1' );
		$expected = <<<EOB
<!-- _append_markup fired --></h1><!-- _after_markup fired -->
EOB;
		$this->assertSame( $expected, $actual );
	}

	/**
	 * Test beans_close_markup() should return only the output from the hooked callbacks and not the HTML element when
	 * the tag is empty.
	 */
	public function test_should_return_only_hooked_callbacks_output_and_no_html_element_when_tag_is_empty() {
		Monkey\Functions\when( 'beans_apply_filters' )->returnArg( 2 );
		Monkey\Functions\expect( '_beans_render_action' )
			->twice()
			->with( 'beans_archive_title_append_markup' )
			->andReturn( '<!-- _append_markup fired -->' )
			->andAlsoExpectIt()
			->twice()
			->with( 'beans_archive_title_after_markup' )
			->andReturn( '<!-- _after_markup fired -->' );

		// Check with an empty string.
		$actual = beans_close_markup( 'beans_archive_title', '' );
		$this->assertSame( '<!-- _append_markup fired --><!-- _after_markup fired -->', $actual );

		// Check with false.
		$actual = beans_close_markup( 'beans_archive_title', false );
		$this->assertSame( '<!-- _append_markup fired --><!-- _after_markup fired -->', $actual );
	}
}
