<?php
/**
 * Tests for _beans_widget_subfilters()
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Widget;

use Beans\Framework\Tests\Integration\API\Widget\Includes\Beans_Widget_Test_Case;

require_once dirname( __FILE__ ) . '/includes/class-beans-widget-test-case.php';

/**
 * Class Tests_BeansWidgetSubfilters
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansWidgetSubfilters extends Beans_Widget_Test_Case {

	/**
	 * Test _beans_widget_subfilters() should return the widget subfilters as a string.
	 */
	public function test_should_return_widget_subfilters_as_string() {
		beans_register_widget_area( 'test_sidebar' );
		$this->add_test_widget_to_test_sidebar();

		_beans_setup_widget_area( 'test_sidebar' );

		$this->assertEquals( '[_test_sidebar][_text][_text-2]', _beans_widget_subfilters() );
	}
}
