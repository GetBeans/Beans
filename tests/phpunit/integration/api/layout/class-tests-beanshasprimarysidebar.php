<?php
/**
 * Tests for beans_has_primary_sidebar()
 *
 * @package Beans\Framework\Tests\Integration\API\Layout
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Layout;

use Beans\Framework\Tests\Integration\Test_Case;
use Brain\Monkey;

/**
 * Class Tests_BeansHasPrimarySidebar
 *
 * @package Beans\Framework\Tests\Integration\API\Layout
 * @group   api
 * @group   api-layout
 */
class Tests_BeansHasPrimarySidebar extends Test_Case {

	/**
	 * Test beans_has_primary_sidebar() should return false when the layout is full-width.
	 */
	public function test_should_return_false_when_full_width_layout() {
		add_filter(
			'beans_default_layout',
			function( $default_layout ) {
				return 'c';
			}
		);

		$this->assertEquals( beans_get_layout(), 'c' );

		$this->assertFalse( beans_has_primary_sidebar( 'c' ) );
	}

	/**
	 * Test beans_has_primary_sidebar() should return false when the layout is content-secondary sidebar.
	 */
	public function test_should_return_false_when_content_sidebar_secondary_layout() {
		add_filter(
			'beans_default_layout',
			function( $default_layout ) {
				return 'c_ss';
			}
		);

		$this->assertEquals( beans_get_layout(), 'c_ss' );

		$this->assertFalse( beans_has_primary_sidebar( 'c_ss' ) );
	}

	/**
	 * Test beans_has_primary_sidebar() should return false when the layout is content-primary sidebar without an active widget.
	 */
	public function test_should_return_false_when_content_primary_sidebar_layout_without_active_widget() {
		Monkey\Functions\when( 'beans_is_active_widget_area' )->justReturn( false );

		add_filter(
			'beans_default_layout',
			function( $default_layout ) {
				return 'c_sp';
			}
		);

		$this->assertEquals( beans_get_layout(), 'c_sp' );

		$this->assertFalse( beans_has_primary_sidebar( 'c_sp' ) );
	}

	/**
	 * Test beans_has_primary_sidebar() should return true when the layout is content-primary sidebar with an active widget.
	 */
	public function test_should_return_true_when_content_primary_sidebar_layout_with_active_widget() {
		Monkey\Functions\when( 'beans_is_active_widget_area' )->justReturn( true );

		add_filter(
			'beans_default_layout',
			function( $default_layout ) {
				return 'c_sp';
			}
		);

		$this->assertEquals( beans_get_layout(), 'c_sp' );

		$this->assertTrue( beans_has_primary_sidebar( 'c_sp' ) );
	}
}
