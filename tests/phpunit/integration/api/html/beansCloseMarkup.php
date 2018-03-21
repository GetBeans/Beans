<?php
/**
 * Tests for beans_close_markup().
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\HTML;

use Beans\Framework\Tests\Integration\API\HTML\Includes\HTML_Test_Case;
use Brain\Monkey;

require_once __DIR__ . '/includes/class-html-test-case.php';

/**
 * Class Tests_BeansCloseMarkup
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansCloseMarkup extends HTML_Test_Case {

	/**
	 * Test beans_close_markup() should return null when the tag is set to null.
	 */
	public function test_should_return_null_when_tag_set_to_null() {
		$this->assertNull( beans_close_markup( 'beans_archive_title', null ) );
	}

	/**
	 * Test beans_close_markup() should fire "{$id}_append_markup" hooks and include the content in the returned HTML.
	 */
	public function test_should_fire_append_markup_hooks_and_include_content_in_returned_html() {
		Monkey\Functions\expect( '__beans_render_title_append_markup' )
			->once()
			->with( '' )
			->andReturnUsing( function() {
				echo '<!-- _append_markup fired -->';
			} );
		add_action( 'beans_archive_title_append_markup', '__beans_render_title_append_markup' );

		// Run the tests.
		$actual = beans_close_markup( 'beans_archive_title', 'h1' );
		$this->assertEquals( 1, did_action( 'beans_archive_title_append_markup' ) );
		$this->assertStringStartsWith( '<!-- _append_markup fired -->', $actual );
	}

	/**
	 * Test beans_close_markup() should fire "{$id}_after_markup" hooks and include the content in the returned HTML.
	 */
	public function test_should_fire_after_markup_hooks_and_include_content_in_returned_html() {
		Monkey\Functions\expect( '__beans_render_title_after_markup' )
			->once()
			->with( '' )
			->andReturnUsing( function() {
				echo '<!-- _after_markup fired -->';
			} );
		add_action( 'beans_archive_title_after_markup', '__beans_render_title_after_markup' );

		// Run the tests.
		$actual = beans_close_markup( 'beans_archive_title', 'h1' );
		$this->assertEquals( 1, did_action( 'beans_archive_title_after_markup' ) );
		$this->assertStringEndsWith( '<!-- _after_markup fired -->', $actual );
	}

	/**
	 * Test beans_close_markup() should return the built HTML element when append or after hooks are not registered.
	 */
	public function test_should_return_built_html_when_append_or_after_hooks_not_registered() {
		$actual = beans_close_markup( 'beans_archive_title', 'h1' );
		$this->assertSame( '</h1>', $actual );
		$this->assertEquals( 0, did_action( 'beans_archive_title_append_markup' ) );
		$this->assertEquals( 0, did_action( 'beans_archive_title_after_markup' ) );
	}

	/**
	 * Test beans_close_markup() should return a built HTML when append or after hooks are registered.
	 */
	public function test_should_return_built_html_when_append_or_after_hooks() {
		add_action( 'beans_archive_title_append_markup', function() {
			echo '<!-- _append_markup fired -->';
		} );
		add_action( 'beans_archive_title_after_markup', function() {
			echo '<!-- _after_markup fired -->';
		} );

		// Run the tests.
		$actual   = beans_close_markup( 'beans_archive_title', 'h1', array( 'class' => 'uk-article-title' ) );
		$expected = <<<EOB
<!-- _append_markup fired --></h1><!-- _after_markup fired -->
EOB;

		$this->assertSame( $expected, $actual );
		$this->assertEquals( 1, did_action( 'beans_archive_title_append_markup' ) );
		$this->assertEquals( 1, did_action( 'beans_archive_title_after_markup' ) );
	}

	/**
	 * Test beans_close_markup() should return only the output from the hooked callbacks and not the HTML element when
	 * the tag is empty.
	 */
	public function test_should_return_only_hooked_callbacks_output_and_no_html_element_when_tag_is_empty() {
		add_action( 'beans_archive_title_append_markup', function() {
			echo '<!-- _append_markup fired -->';
		} );
		add_action( 'beans_archive_title_after_markup', function() {
			echo '<!-- _after_markup fired -->';
		} );

		// Check with an empty string.
		$actual = beans_close_markup( 'beans_archive_title', '', array( 'class' => 'uk-article-title' ) );
		$this->assertSame( '<!-- _append_markup fired --><!-- _after_markup fired -->', $actual );

		// Check with false.
		$actual = beans_close_markup( 'beans_archive_title', false, array( 'class' => 'uk-article-title' ) );
		$this->assertSame( '<!-- _append_markup fired --><!-- _after_markup fired -->', $actual );

		// Check the hooks.
		$this->assertEquals( 2, did_action( 'beans_archive_title_append_markup' ) );
		$this->assertEquals( 2, did_action( 'beans_archive_title_after_markup' ) );
	}
}
