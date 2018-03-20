<?php
/**
 * Tests for beans_output().
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
 * Class Tests_BeansOutput
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansOutput extends HTML_Test_Case {

	/**
	 * Test beans_output() should return null when the output is empty.
	 */
	public function test_should_return_null_when_output_is_empty() {
		$this->assertNull( beans_output( 'beans_post_meta_item_date', null ) );
		$this->assertNull( beans_output( 'beans_post_meta_item_author', '' ) );
		$this->assertNull( beans_output( 'beans_post_meta_item_comments', false ) );
	}

	/**
	 * Test beans_output() should fire the "{$id}_output" filter hook.
	 */
	public function test_should_fire_output_filter_hook() {
		add_option( 'beans_dev_mode', 0 );
		add_filter( 'beans_archive_title_text_output', 'return_fired_output' );
		Monkey\Functions\expect( 'return_fired_output' )
			->with( 'Beans rocks!' )
			->andReturn( 'WooHoo, I fired!' );

		// Run the test.
		$this->assertSame( 'WooHoo, I fired!', beans_output( 'beans_archive_title_text', 'Beans rocks!' ) );
	}

	/**
	 * Test beans_output() should return output when not in HTML dev mode.
	 */
	public function test_should_return_output_when_not_in_html_dev_mode() {
		add_option( 'beans_dev_mode', 0 );

		$this->assertSame( 'Beans rocks!', beans_output( 'beans_archive_title_text', 'Beans rocks!' ) );
	}

	/**
	 * Test beans_output() should return "comment wrapped" HTML when in HTML dev mode.
	 */
	public function test_should_return_comment_wrapped_html_when_in_html_dev_mode() {
		add_option( 'beans_dev_mode', 1 );

		$expected = <<<EOB
<!-- open output: beans_archive_title_text -->Beans rocks!<!-- close output: beans_archive_title_text -->
EOB;
		$this->assertSame( $expected, beans_output( 'beans_archive_title_text', 'Beans rocks!' ) );
	}

	/**
	 * Test beans_output() should pass additional arguments when firing the filter hook.
	 */
	public function test_should_pass_additional_args_when_firing_filter_hook() {
		add_filter( 'beans_breadcrumb_item_text_output', 'return_fired_output', 10, 3 );
		Monkey\Functions\expect( 'return_fired_output' )
			->twice()
			->with( 'Beans rocks!', 47, 'Hello' )
			->andReturnFirstArg();

		// Check with HTML dev mode disabled.
		add_option( 'beans_dev_mode', 0 );
		$this->assertSame(
			'Beans rocks!',
			beans_output( 'beans_breadcrumb_item_text', 'Beans rocks!', 47, 'Hello' )
		);

		// Check with HTML dev mode enabled.
		$expected = <<<EOB
<!-- open output: beans_breadcrumb_item_text -->Beans rocks!<!-- close output: beans_breadcrumb_item_text -->
EOB;
		update_option( 'beans_dev_mode', 1 );
		$this->assertSame(
			$expected,
			beans_output( 'beans_breadcrumb_item_text', 'Beans rocks!', 47, 'Hello' )
		);
	}
}
