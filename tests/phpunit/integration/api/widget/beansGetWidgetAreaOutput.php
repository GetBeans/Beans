<?php
/**
 * Tests for beans_get_widget_area_output()
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Widget;

use Beans\Framework\Tests\Integration\API\Widget\Includes\Beans_Widget_Test_Case;

require_once dirname( __FILE__ ) . '/includes/class-beans-widget-test-case.php';

/**
 * Class Tests_BeansGetWidgetAreaOutput
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansGetWidgetAreaOutput extends Beans_Widget_Test_Case {

	/**
	 * Test beans_get_widget_area_output() should return false when the widget area is not registered.
	 */
	public function test_should_return_false_when_widget_area_not_registered() {
		$this->assertFalse( beans_get_widget_area_output( 'unregistered-widget-area' ) );
	}

	/**
	 * Test beans_get_widget_area_output() should return the widget output when a widget area is registered.
	 */
	public function test_should_return_widget_output_when_widget_area_is_registered() {
		global $wp_registered_sidebars;

		beans_register_widget_area( [
			'id'   => 'test_sidebar',
			'name' => 'Test Sidebar',
		] );
		$this->add_test_widget_to_test_sidebar();
		$this->assertSame(
			$this->format_the_html( $this->get_expected_output() ),
			$this->format_the_html( beans_get_widget_area_output( 'test_sidebar' ) )
		);
	}

	/**
	 * Get the expected output (html) of the Beans default primary sidebar.
	 */
	protected function get_expected_output() {
		return '<div class="tm-widget uk-panel widget_text text-2"></div>';
	}
}
