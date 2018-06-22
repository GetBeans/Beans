<?php
/**
 * Tests for beans_has_widget_area()
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
 * Class Tests_BeansHasWidgetArea
 *
 * @package Beans\Framework\Tests\Unit\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansHasWidgetArea extends Beans_Widget_Test_Case {

	/**
	 * Test beans_has_widget_area() should return false when widget area is not registered.
	 */
	public function test_should_return_false_when_widget_area_not_registered() {
		$this->assertFalse( beans_has_widget_area( 'unregistered-area' ) );
	}

	/**
	 * Test beans_has_widget_area() should return true when widget area is registered.
	 */
	public function test_should_return_true_when_widget_area_registered() {
		global $wp_registered_sidebars;

		$sidebars = [
			'id'   => 'test_sidebar',
			'name' => 'Test Sidebar',
		];

		$wp_registered_sidebars['test_sidebar'] = $sidebars; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited -- Valid use case: setting up sidebars outside of WP.

		// Run test.
		$this->assertTrue( beans_has_widget_area( 'test_sidebar' ) );
	}
}
