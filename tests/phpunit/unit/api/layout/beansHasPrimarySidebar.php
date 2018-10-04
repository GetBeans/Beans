<?php
/**
 * Tests for beans_has_primary_sidebar()
 *
 * @package Beans\Framework\Tests\Unit\API\Layout
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Layout;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey;

/**
 * Class Tests_BeansHasPrimarySidebar
 *
 * @package Beans\Framework\Tests\Unit\API\Layout
 * @group   api
 * @group   api-layout
 */
class Tests_BeansHasPrimarySidebar extends Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->load_original_functions(
			array(
				'api/layout/functions.php',
			)
		);
	}

	/**
	 * Test beans_has_primary_sidebar() should return false when the layout is full-width.
	 */
	public function test_should_return_false_when_full_width_layout() {
		$this->assertFalse( beans_has_primary_sidebar( 'c' ) );
	}

	/**
	 * Test beans_has_primary_sidebar() should return false when the layout is content-secondary sidebar.
	 */
	public function test_should_return_false_when_content_secondary_sidebar_layout() {
		$this->assertFalse( beans_has_primary_sidebar( 'c_ss' ) );
	}

	/**
	 * Test beans_has_primary_sidebar() should return false when the layout is content-primary sidebar without an active widget.
	 */
	public function test_should_return_false_when_content_primary_sidebar_layout_without_active_widget() {
		Monkey\Functions\when( 'beans_is_active_widget_area' )->justReturn( false );

		$this->assertFalse( beans_has_primary_sidebar( 'c_sp' ) );
	}

	/**
	 * Test beans_has_primary_sidebar() should return true when the layout is content-primary sidebar with an active widget.
	 */
	public function test_should_return_true_when_content_primary_sidebar_layout_with_active_widget() {
		Monkey\Functions\when( 'beans_is_active_widget_area' )->justReturn( true );

		$this->assertTrue( beans_has_primary_sidebar( 'c_sp' ) );
	}
}
