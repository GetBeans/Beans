<?php
/**
 * Tests for _beans_widget_area_subfilters()
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Widget;

use Beans\Framework\Tests\Integration\API\Widget\Includes\Beans_Widget_Test_Case;

require_once dirname( __FILE__ ) . '/includes/class-beans-widget-test-case.php';

/**
 * Class Tests_BeansWidgetAreaSubfilters
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansWidgetAreaSubfilters extends Beans_Widget_Test_Case {

	/**
	 * Test _beans_widget_area_subfilters() should return the widget area subfilters as a string.
	 */
	public function test_should_return_widget_area_subfilters_as_string() {
		beans_register_widget_area( 'test_sidebar' );

		_beans_setup_widget_area( 'test_sidebar' );

		$this->assertEquals( '[_test_sidebar]', _beans_widget_area_subfilters() );
	}
}
