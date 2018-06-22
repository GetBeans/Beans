<?php
/**
 * Tests for beans_get_default_layout()
 *
 * @package Beans\Framework\Tests\Unit\API\Layout
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Layout;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey;

/**
 * Class Tests_BeansGetDefaultLayout
 *
 * @package Beans\Framework\Tests\Unit\API\Layout
 * @group   api
 * @group   api-layout
 */
class Tests_BeansGetDefaultLayout extends Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->load_original_functions( [
			'api/layout/functions.php',
			'api/widget/functions.php',
		] );
	}

	/**
	 * Test beans_get_default_layout() should return "c" when there is no primary sidebar.
	 */
	public function test_should_return_c_when_no_primary_sidebar() {
		Monkey\Functions\expect( 'beans_has_widget_area' )
			->once()
			->with( 'sidebar_primary' )
			->andReturn( false );
		Monkey\Filters\expectApplied( 'beans_default_layout' )
			->once()
			->with( 'c' )
			->andReturn( 'c' );

		$this->assertSame( 'c', beans_get_default_layout() );
	}

	/**
	 * Test beans_get_default_layout() should return "c_sp" when there is a primary sidebar.
	 */
	public function test_should_return_c_sp_when_has_primary_sidebar() {
		Monkey\Functions\expect( 'beans_has_widget_area' )
			->once()
			->with( 'sidebar_primary' )
			->andReturn( true );
		Monkey\Filters\expectApplied( 'beans_default_layout' )
			->once()
			->with( 'c_sp' )
			->andReturn( 'c_sp' );

		$this->assertSame( 'c_sp', beans_get_default_layout() );
	}
}
