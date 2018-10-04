<?php
/**
 * Tests for beans_deregister_widget_area()
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Widget;

use Beans\Framework\Tests\Integration\API\Widget\Includes\Beans_Widget_Test_Case;

require_once dirname( __FILE__ ) . '/includes/class-beans-widget-test-case.php';

/**
 * Class Tests_BeansDeregisterWidgetArea
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansDeregisterWidgetArea extends Beans_Widget_Test_Case {

	/**
	 * Test beans_deregister_widget_area() should unregister a sidebar.
	 */
	public function test_should_unregister_sidebar() {
		global $wp_registered_sidebars;

		register_sidebar( [ 'id' => 'unwanted-sidebar' ] );

		$this->assertTrue( isset( $wp_registered_sidebars['unwanted-sidebar'] ) );

		beans_deregister_widget_area( 'unwanted-sidebar' );

		$this->assertFalse( isset( $wp_registered_sidebars['unwanted-sidebar'] ) );
	}
}
