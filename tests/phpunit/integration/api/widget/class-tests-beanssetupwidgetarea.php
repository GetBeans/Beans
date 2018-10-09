<?php
/**
 * Tests for _beans_setup_widget_area()
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Widget;

use Beans\Framework\Tests\Integration\API\Widget\Includes\Beans_Widget_Test_Case;

require_once dirname( __FILE__ ) . '/includes/class-beans-widget-test-case.php';

/**
 * Class Tests_BeansSetupWidgetArea
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansSetupWidgetArea extends Beans_Widget_Test_Case {

	/**
	 * Test _beans_setup_widget_area() should build the widget area data and return true when widget area exists.
	 */
	public function test_should_build_widget_area_data_and_return_true_when_widget_area_exists() {
		global $_beans_widget_area;

		beans_register_widget_area( 'test_sidebar' );
		$this->add_test_widget_to_test_sidebar();

		// Clear test widget area data global before running test.
		$_beans_widget_area = [];

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
