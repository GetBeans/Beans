<?php
/**
 * Tests for beans_register_widget_area()
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Widget;

use Beans\Framework\Tests\Integration\API\Widget\Includes\Beans_Widget_Test_Case;

require_once dirname( __FILE__ ) . '/includes/class-beans-widget-test-case.php';

/**
 * Class Tests_BeansRegisterWidgetArea
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansRegisterWidgetArea extends Beans_Widget_Test_Case {

	/**
	 * Test beans_register_widget_area() should return an empty string when the ID is not set.
	 */
	public function test_should_return_empty_string_when_ID_not_set() {
		$this->assertSame( '', beans_register_widget_area( [] ) );
	}

	/**
	 * Test beans_register_widget_area() should register a sidebar and return the widget area ID when the widget area is registered.
	 */
	public function test_should_register_sidebar_and_return_widget_area_ID_when_widget_area_registered() {
		global $wp_registered_sidebars;

		// Verify new widget area is not yet added.
		$this->assertFalse( isset( $wp_registered_sidebars['new-widget-area'] ) );

		$this->assertEquals(
			'new-widget-area',
			beans_register_widget_area( [
				'id'         => 'new-widget-area',
				'beans_type' => 'grid',
			] )
		);

		// Confirm that the sidebar is now in the WP global sidebar array.
		$this->assertTrue( isset( $wp_registered_sidebars['new-widget-area'] ) );
		$this->assertContains( 'grid', $wp_registered_sidebars['new-widget-area'] );
	}
}
