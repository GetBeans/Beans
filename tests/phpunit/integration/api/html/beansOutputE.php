<?php
/**
 * Tests for beans_output_e().
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
 * Class Tests_BeansOutputE
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansOutputE extends HTML_Test_Case {

	/**
	 * Test beans_output() should echo an empty string when the output is empty.
	 */
	public function test_should_echo_null_when_output_is_empty() {
		$ids = array(
			'beans_post_meta_item_date'     => null,
			'beans_post_meta_item_author'   => '',
			'beans_post_meta_item_comments' => false,
		);

		foreach ( $ids as $id => $output ) {
			ob_start();
			beans_output_e( $id, $output );
			$this->assertEmpty( ob_get_clean() );
		}
	}

	/**
	 * Test beans_output_e() should echo the filtered output.
	 */
	public function test_should_fire_output_filter_hook() {
		add_option( 'beans_dev_mode', 0 );
		add_filter( 'beans_archive_title_text_output', 'return_fired_output' );
		Monkey\Functions\expect( 'return_fired_output' )
			->with( 'Beans rocks!' )
			->andReturn( 'WooHoo, I fired!' );

		// Run the test.
		ob_start();
		beans_output_e( 'beans_archive_title_text', 'Beans rocks!' );
		$this->assertSame( 'WooHoo, I fired!', ob_get_clean() );
	}

	/**
	 * Test beans_output_e() should echo the output when not in HTML dev mode.
	 */
	public function test_should_echo_output_when_not_in_html_dev_mode() {
		add_option( 'beans_dev_mode', 0 );

		ob_start();
		beans_output_e( 'beans_archive_title_text', 'Beans rocks!' );
		$this->assertSame( 'Beans rocks!', ob_get_clean() );
	}

	/**
	 * Test beans_output_e() should echo the "comment wrapped" HTML when in HTML dev mode.
	 */
	public function test_should_echo_comment_wrapped_html_when_in_html_dev_mode() {
		add_option( 'beans_dev_mode', 1 );

		ob_start();
		beans_output_e( 'beans_archive_title_text', 'Beans rocks!' );
		$actual = ob_get_clean();

		$expected = <<<EOB
<!-- open output: beans_archive_title_text -->Beans rocks!<!-- close output: beans_archive_title_text -->
EOB;
		$this->assertSame( $expected, $actual );
	}

	/**
	 * Test beans_output() should pass the additional arguments when firing the filter hook.
	 */
	public function test_should_pass_additional_args_when_firing_filter_hook() {
		add_filter( 'beans_breadcrumb_item_text_output', 'return_fired_output', 10, 3 );
		Monkey\Functions\expect( 'return_fired_output' )
			->twice()
			->with( 'Beans rocks!', 47, 'Hello' )
			->andReturnUsing( function( $output, $arg1, $arg2 ) {
				return $arg2;
			});

		// Check with HTML dev mode disabled.
		add_option( 'beans_dev_mode', 0 );
		ob_start();
		beans_output_e( 'beans_breadcrumb_item_text', 'Beans rocks!', 47, 'Hello' );
		$this->assertSame( 'Hello', ob_get_clean() );

		// Check with HTML dev mode enabled.
		update_option( 'beans_dev_mode', 1 );
		ob_start();
		beans_output_e( 'beans_breadcrumb_item_text', 'Beans rocks!', 47, 'Hello' );
		$actual = ob_get_clean();
		$expected = <<<EOB
<!-- open output: beans_breadcrumb_item_text -->Hello<!-- close output: beans_breadcrumb_item_text -->
EOB;
		$this->assertSame( $expected, $actual );
	}
}
