<?php
/**
 * Tests for beans_setup_widget_area()
 *
 * @package Beans\Framework\Tests\Unit\API\Widget
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Widget;

use Beans\Framework\Tests\Unit\API\Widget\Includes\Beans_Widget_Test_Case;
use Brain\Monkey;

require_once dirname( __FILE__ ) . '/includes/class-beans-widget-test-case.php';

/**
 * Class Tests_BeansSetupWidgetArea
 *
 * @package Beans\Framework\Tests\Unit\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansSetupWidgetArea extends Beans_Widget_Test_Case {

	/**
	 * Test _beans_setup_widget_area() should return false when the sidebar ID is not set.
	 */
	public function test_should_return_false_when_sidebar_id_not_set() {
		$this->assertFalse( _beans_setup_widget_area( 'missing_sidebar' ) );
	}

	/**
	 * Test _beans_setup_widget_area() should build the widget area data and return true when widget area exists.
	 */
	public function test_should_build_widget_area_data_and_return_true_when_widget_area_exists() {
		global $_beans_widget_area, $wp_registered_sidebars;

		$sidebars = [
			'sidebar_primary'   => [],
			'sidebar_secondary' => [],
			'test_sidebar'      => [],
		];

		$wp_registered_sidebars = $sidebars; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited -- Valid use case: setting up sidebars outside of WP.

		Monkey\Functions\expect( 'beans_render_function' )
			->once()
			->with( 'dynamic_sidebar', 'test_sidebar' )
			->andReturn( '<!--widget-text-2-->Some widget output<!--widget-end-->' );

		Monkey\Functions\expect( '_beans_setup_widgets' )
			->once()
			->with( '<!--widget-text-2-->Some widget output<!--widget-end-->' )
			->andReturnFirstArg();

		// Run test.
		$this->assertTrue( _beans_setup_widget_area( 'test_sidebar' ) );

		// Verify widget area data has been set up.
		$this->assertContains( 'test_sidebar', $_beans_widget_area );
		$this->assertArrayHasKey( 'widgets_count', $_beans_widget_area );
		$this->assertArrayHasKey( 'current_widget', $_beans_widget_area );
		$this->assertArrayHasKey( 'before_widgets', $_beans_widget_area );
		$this->assertArrayHasKey( 'widgets', $_beans_widget_area );
		$this->assertArrayHasKey( 'after_widgets', $_beans_widget_area );
	}
}
