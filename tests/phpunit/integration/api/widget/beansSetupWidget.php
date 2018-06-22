<?php
/**
 * Tests for beans_setup_widget()
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Widget;

use Beans\Framework\Tests\Integration\API\Widget\Includes\Beans_Widget_Test_Case;

require_once dirname( __FILE__ ) . '/includes/class-beans-widget-test-case.php';

/**
 * Class Tests_BeansSetupWidget
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansSetupWidget extends Beans_Widget_Test_Case {

	/**
	 * Test beans_setup_widget() should return false when the widget ID can't be found.
	 */
	public function test_should_return_false_when_widget_id_not_found() {
		global $_beans_widget_area;

		beans_register_widget_area( [ 'id' => 'test_sidebar' ] );
		$this->add_test_widget_to_test_sidebar();

		_beans_setup_widget_area( 'test_sidebar' );

		// Advance the widget pointer to a non-existent widget.
		$_beans_widget_area['current_widget'] = 1;
		$this->assertFalse( beans_setup_widget() );
	}

	/**
	 * Test beans_setup_widget() should advance widget pointer, prepare widget data, and return true when a widget ID is found.
	 */
	public function test_should_advance_widget_pointer_prepare_widget_data_and_return_true_when_widget_id_is_found() {
		global $_beans_widget_area, $_beans_widget;

		beans_register_widget_area( [ 'id' => 'test_sidebar' ] );
		$this->add_test_widget_to_test_sidebar();
		_beans_setup_widget_area( 'test_sidebar' );

		// Check widget pointer is at 0.
		$this->assertEquals( 0, $_beans_widget_area['current_widget'] );

		// Run test.
		$this->assertTrue( beans_setup_widget() );

		// Verify widget data has been prepared.
		$this->assertContains( 'Test Widget', $_beans_widget );

		// Verify widget pointer has advanced to 1.
		$this->assertEquals( 1, $_beans_widget_area['current_widget'] );
	}
}
