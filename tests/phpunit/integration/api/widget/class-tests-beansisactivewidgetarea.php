<?php
/**
 * Tests for beans_is_active_widget_area()
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Widget;

use Beans\Framework\Tests\Integration\API\Widget\Includes\Beans_Widget_Test_Case;

require_once dirname( __FILE__ ) . '/includes/class-beans-widget-test-case.php';

/**
 * Class Tests_BeansIsActiveWidgetArea
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansIsActiveWidgetArea extends Beans_Widget_Test_Case {

	/**
	 * Test beans_is_active_widget_area() should return false when widget area is not active.
	 */
	public function test_should_return_false_when_widget_area_not_active() {
		$this->assertFalse( beans_is_active_widget_area( 'inactive-widget-area' ) );
	}

	/**
	 * Test beans_is_active_widget_area() should return true when widget area is active.
	 */
	public function test_should_return_true_when_widget_area_is_active() {
		global $_wp_sidebars_widgets;

		// Clear global widget areas registry so we can start clean.
		$_wp_sidebars_widgets = []; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited -- Valid use case: we need test to start with clean sidebars_widgets.

		// Prime the WP database with an active sidebar.
		update_option( 'sidebars_widgets', [ 'an-active-sidebar' => [ 'text-2' ] ] );

		$this->assertTrue( beans_is_active_widget_area( 'an-active-sidebar' ) );
	}
}
