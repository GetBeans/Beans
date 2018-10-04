<?php
/**
 * Tests for beans_get_default_layout()
 *
 * @package Beans\Framework\Tests\Integration\API\Layout
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Layout;

use Beans\Framework\Tests\Integration\Test_Case;

/**
 * Class Tests_BeansGetDefaultLayout
 *
 * @package Beans\Framework\Tests\Integration\API\Layout
 * @group   api
 * @group   api-layout
 */
class Tests_BeansGetDefaultLayout extends Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		// Reset the Beans' sidebars.
		beans_do_register_widget_areas();
	}

	/**
	 * Test beans_get_default_layout() should return "c" when there is no primary sidebar.
	 */
	public function test_should_return_c_when_no_primary_sidebar() {
		// Unregister the primary sidebar first.
		unregister_sidebar( 'sidebar_primary' );

		// Run the tests.
		$this->assertFalse( beans_has_widget_area( 'sidebar_primary' ) );
		$this->assertSame( 'c', beans_get_default_layout() );
	}

	/**
	 * Test beans_get_default_layout() should return "c_sp" when there is a primary sidebar.
	 */
	public function test_should_return_c_sp_when_has_primary_sidebar() {
		$this->assertTrue( beans_has_widget_area( 'sidebar_primary' ) );
		$this->assertSame( 'c_sp', beans_get_default_layout() );
	}
}
