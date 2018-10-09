<?php
/**
 * Tests for _beans_widget_area_subfilters()
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
 * Class Tests_BeansWidgetAreaSubfilters
 *
 * @package Beans\Framework\Tests\Unit\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansWidgetAreaSubfilters extends Beans_Widget_Test_Case {

	/**
	 * Test _beans_widget_area_subfilters() should return the widget area subfilters as a string.
	 */
	public function test_should_return_widget_area_subfilters_as_string() {
		global $_beans_widget_area;

		$_beans_widget_area = [ 'id' => 'test_sidebar' ];

		$this->assertEquals( '[_test_sidebar]', _beans_widget_area_subfilters() );
	}
}
