<?php
/**
 * Tests for beans_output().
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
 * Class Tests_BeansOutput
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansOutput extends HTML_Test_Case {

	/**
	 * Test beans_output() should return null when the output is empty.
	 */
	public function test_should_return_null_when_output_is_empty() {
		Monkey\Functions\expect( 'beans_apply_filters' )->times( 3 )->andReturnUsing( function( $hook, $output ) {
			return $output;
		} );
		Monkey\Functions\expect( '_beans_is_html_dev_mode' )->never();

		$this->assertNull( beans_output( 'beans_post_meta_item_date', null ) );
		$this->assertNull( beans_output( 'beans_post_meta_item_author', '' ) );
		$this->assertNull( beans_output( 'beans_post_meta_item_comments', false ) );
	}

	/**
	 * Test beans_output() should return the filtered output after firing the "{$id}_output" filter hook.
	 */
	public function test_should_return_filtered_output_after_firing_output_filter_hook() {
		// Check the applied filter.
		Monkey\Filters\expectApplied( 'beans_archive_title_text_output' )
			->once()
			->with( 'Beans rocks!' )
			->andReturn( 'WooHoo, I fired!' );

		// Setup beans_apply_filters() mock to fire apply_filters().
		Monkey\Functions\expect( 'beans_apply_filters' )
			->once()
			->with( 'beans_archive_title_text_output', 'Beans rocks!' )
			->andReturnUsing( function( $hook, $output ) {
				return apply_filters( $hook, $output ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Prefix is in the value represented by the variable.
			} );

		// Check with HTML dev mode disabled.
		Monkey\Functions\expect( '_beans_is_html_dev_mode' )->once()->andReturn( false );

		// Run the tests.
		$this->assertSame( 'WooHoo, I fired!', beans_output( 'beans_archive_title_text', 'Beans rocks!' ) );
		$this->assertSame( 1, Monkey\Filters\applied( 'beans_archive_title_text_output' ) );
	}

	/**
	 * Test beans_output() should return the output when not in HTML dev mode.
	 */
	public function test_should_return_output_when_not_in_html_dev_mode() {
		Monkey\Functions\when( 'beans_apply_filters' )->returnArg( 2 );
		Monkey\Functions\expect( '_beans_is_html_dev_mode' )->once()->andReturn( false );

		$this->assertSame( 'Beans rocks!', beans_output( 'beans_archive_title_text', 'Beans rocks!' ) );
	}

	/**
	 * Test beans_output() should return the "comment wrapped" HTML when in HTML dev mode.
	 */
	public function test_should_return_comment_wrapped_html_when_in_html_dev_mode() {
		Monkey\Functions\when( 'beans_apply_filters' )->returnArg( 2 );
		Monkey\Functions\expect( '_beans_is_html_dev_mode' )->once()->andReturn( true );

		$expected = <<<EOB
<!-- open output: beans_archive_title_text -->Beans rocks!<!-- close output: beans_archive_title_text -->
EOB;
		$this->assertSame( $expected, beans_output( 'beans_archive_title_text', 'Beans rocks!' ) );
	}

	/**
	 * Test beans_output() should the pass additional arguments when firing the filter hook.
	 */
	public function test_should_pass_additional_args_when_firing_filter_hook() {
		// Setup beans_apply_filters() mock to fire apply_filters().
		Monkey\Functions\expect( 'beans_apply_filters' )
			->twice()
			->with( 'beans_breadcrumb_item_text_output', 'Beans rocks!', 47, 'Hello' )
			->andReturnUsing( function( $hook, $output ) {
				return $output;
			} );

		// Check with HTML dev mode disabled.
		Monkey\Functions\expect( '_beans_is_html_dev_mode' )->once()->andReturn( false );
		$this->assertSame(
			'Beans rocks!',
			beans_output( 'beans_breadcrumb_item_text', 'Beans rocks!', 47, 'Hello' )
		);

		// Check with HTML dev mode enabled.
		$expected = <<<EOB
<!-- open output: beans_breadcrumb_item_text -->Beans rocks!<!-- close output: beans_breadcrumb_item_text -->
EOB;
		Monkey\Functions\expect( '_beans_is_html_dev_mode' )->once()->andReturn( true );
		$this->assertSame(
			$expected,
			beans_output( 'beans_breadcrumb_item_text', 'Beans rocks!', 47, 'Hello' )
		);
	}
}
