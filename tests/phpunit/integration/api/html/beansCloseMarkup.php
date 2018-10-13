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
			->andReturnUsing(
				function() {
					echo '<!-- _append_markup fired -->';
				}
			);
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
			->andReturnUsing(
				function() {
					echo '<!-- _after_markup fired -->';
				}
			);
		add_action( 'beans_archive_title_after_markup', '__beans_render_title_after_markup' );

		// Run the tests.
		$actual = beans_close_markup( 'beans_archive_title', 'h1' );
		$this->assertEquals( 1, did_action( 'beans_archive_title_after_markup' ) );
		$this->assertStringEndsWith( '<!-- _after_markup fired -->', $actual );
	}

	/**
	 * Test beans_close_markup() should return the closing tag only when a callback is not registered to either the
	 * "_append_markup" or "_after_markup" hook.
	 */
	public function test_should_return_closing_tag_when_callback_not_registered_to_either_hook() {
		$actual = beans_close_markup( 'beans_archive_title', 'h1' );
		$this->assertSame( '</h1>', $actual );
		$this->assertEquals( 0, did_action( 'beans_archive_title_append_markup' ) );
		$this->assertEquals( 0, did_action( 'beans_archive_title_after_markup' ) );
	}

	/**
	 * Test beans_close_markup() should return the append, tag, and after HTML when callbacks are registered
	 * to the "_append_markup" and "_after_markup" hooks.
	 */
	public function test_should_return_built_html_when_callback_registered_to_hooks() {
		add_action(
			'beans_archive_title_append_markup',
			function() {
				echo '<!-- _append_markup fired -->';
			}
		);
		add_action(
			'beans_archive_title_after_markup',
			function() {
				echo '<!-- _after_markup fired -->';
			}
		);

		// Run the tests.
		$actual   = beans_close_markup( 'beans_archive_title', 'h1' );
		$expected = <<<EOB
<!-- _append_markup fired --></h1><!-- _after_markup fired -->
EOB;

		$this->assertSame( $expected, $actual );
		$this->assertEquals( 1, did_action( 'beans_archive_title_append_markup' ) );
		$this->assertEquals( 1, did_action( 'beans_archive_title_after_markup' ) );
	}

	/**
	 * Test beans_close_markup() should return an empty string when (1) the tag is an empty string and (2) a callback
	 * is not registered to either the "_append_markup" or "_after_markup" hook.
	 */
	public function test_should_return_empty_string_when_empty_tag_and_callback_not_registered_to_hooks() {
		$this->assertSame( '', beans_close_markup( 'beans_archive_title', '' ) );
	}

	/**
	 * Test beans_close_markup() should return the hooked callbacks' output and not the closing tag when
	 * the tag is empty.
	 */
	public function test_should_return_hooked_callbacks_output_and_not_closing_tag_when_tag_is_empty() {
		add_action(
			'beans_archive_title_append_markup',
			function() {
				echo '<!-- _append_markup fired -->';
			}
		);
		add_action(
			'beans_archive_title_after_markup',
			function() {
				echo '<!-- _after_markup fired -->';
			}
		);

		// Check with an empty string.
		$actual = beans_close_markup( 'beans_archive_title', '' );
		$this->assertSame( '<!-- _append_markup fired --><!-- _after_markup fired -->', $actual );

		// Check with false.
		$actual = beans_close_markup( 'beans_archive_title', false );
		$this->assertSame( '<!-- _append_markup fired --><!-- _after_markup fired -->', $actual );

		// Check the hooks.
		$this->assertEquals( 2, did_action( 'beans_archive_title_append_markup' ) );
		$this->assertEquals( 2, did_action( 'beans_archive_title_after_markup' ) );
	}

	/**
	 * Test beans_close_markup() should escape the closing tag.
	 */
	public function test_should_escape_closing_tag() {
		$expected = <<<EOB
</&lt;script&gt;alert(&quot;Should escape me.&quot;)&lt;/script&gt;>
EOB;
		// Check when given as the tag.
		$this->assertSame( $expected, beans_close_markup( 'beans_post_title', '<script>alert("Should escape me.")</script>' ) );

		// Check when tag is filtered.
		add_filter(
			'beans_post_title_markup',
			function() {
				return '<script>alert("Should escape me.")</script>';
			}
		);
		$this->assertSame( $expected, beans_close_markup( 'beans_post_title', 'h1' ) );
	}
}
