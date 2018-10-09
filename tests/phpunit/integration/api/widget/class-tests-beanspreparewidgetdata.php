<?php
/**
 * Tests for _beans_prepare_widget_data()
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Widget;

use Beans\Framework\Tests\Integration\API\Widget\Includes\Beans_Widget_Test_Case;

require_once dirname( __FILE__ ) . '/includes/class-beans-widget-test-case.php';

/**
 * Class Tests__BeansPrepareWidgetData
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansPrepareWidgetData extends Beans_Widget_Test_Case {

	/**
	 * Test _beans_prepare_widget_data() should prepare the widget data.
	 */
	public function test_should_prepare_widget_data() {
		global $_beans_widget;

		beans_register_widget_area( [ 'id' => 'test_sidebar' ] );
		$this->add_test_widget_to_test_sidebar();
		_beans_setup_widget_area( 'test_sidebar' );

		// Verify that no widget data exists before test.
		$this->assertEmpty( $_beans_widget );

		// Call the function.
		_beans_prepare_widget_data( 'text-2' );

		// Verify that widget data is now prepared.
		$this->assertContains( 'Test Widget', $_beans_widget );
	}
}
