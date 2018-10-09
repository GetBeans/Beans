<?php
/**
 * Tests for _beans_reset_widget_area()
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
 * Class Tests_BeansResetWidgetArea
 *
 * @package Beans\Framework\Tests\Unit\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansResetWidgetArea extends Beans_Widget_Test_Case {

	/**
	 * Test _beans_reset_widget_area() should unset all widget area data.
	 */
	public function test_should_unset_all_widget_area_data() {
		global $_beans_widget_area;

		// We'll set the data via the function's view of the global since that's how it's ordinarily set in the API.
		$test_widget_area   = [ [ 'text-2' => [ 'id' => 'text-2' ] ] ];
		$_beans_widget_area = $test_widget_area;

		// Confirm that it's stored in the GLOBALS superglobal.
		$this->assertSame( $test_widget_area, $GLOBALS['_beans_widget_area'] );

		// Run the function.
		_beans_reset_widget_area();

		// Test that the superglobal is no longer set.
		$this->assertFalse( isset( $GLOBALS['_beans_widget_area'] ) );
	}
}
