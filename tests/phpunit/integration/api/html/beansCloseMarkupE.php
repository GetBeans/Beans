<?php
/**
 * Tests for beans_close_markup_e().
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
 * Class Tests_BeansCloseMarkupE
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansCloseMarkupE extends HTML_Test_Case {

	/**
	 * Test beans_close_markup_e() should echo an empty string when the tag is set to null.
	 */
	public function test_should_echo_empty_when_tag_set_to_null() {
		ob_start();
		beans_close_markup_e( 'beans_archive_title', null );
		$this->assertEquals( '', ob_get_clean() );
	}

	/**
	 * Test beans_close_markup_e() should echo the closing tag only when a callback is not registered to either the
	 * "_append_markup" or "_after_markup" hook.
	 */
	public function test_should_echo_closing_tag_when_callback_not_registered_to_either_hook() {
		ob_start();
		beans_close_markup_e( 'beans_archive_title', 'h1' );
		$actual = ob_get_clean();

		$this->assertSame( '</h1>', $actual );
		$this->assertEquals( 0, did_action( 'beans_archive_title_append_markup' ) );
		$this->assertEquals( 0, did_action( 'beans_archive_title_after_markup' ) );
	}

	/**
	 * Test beans_close_markup_e() should echo the append, tag, and after HTML when callbacks are registered
	 * to the "_append_markup" and "_after_markup" hooks.
	 */
	public function test_should_echo_built_html_when_append_or_after_hooks() {
		add_action( 'beans_archive_title_append_markup', function() {
			echo '<!-- _append_markup fired -->';
		} );
		add_action( 'beans_archive_title_after_markup', function() {
			echo '<!-- _after_markup fired -->';
		} );

		ob_start();
		beans_close_markup_e( 'beans_archive_title', 'h1' );
		$actual   = ob_get_clean();
		$expected = <<<EOB
<!-- _append_markup fired --></h1><!-- _after_markup fired -->
EOB;
		// Run the tests.
		$this->assertSame( $expected, $actual );
		$this->assertEquals( 1, did_action( 'beans_archive_title_append_markup' ) );
		$this->assertEquals( 1, did_action( 'beans_archive_title_after_markup' ) );
	}
}
