<?php
/**
 * Tests for _beans_reset_widget()
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
 * Class Tests_BeansResetWidget
 *
 * @package Beans\Framework\Tests\Unit\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansResetWidget extends Beans_Widget_Test_Case {

	/**
	 * Test beans_reset_widget should unset widget data.
	 */
	public function test_should_unset_widget_data() {
		global $_beans_widget;

		// We'll set the data via the function's view of the global since that's how it's ordinarily set in the API.
		$test_widget = [
			[
				'id'   => 'text-2',
				'name' => 'Test Widget',
			],
		];

		$_beans_widget = $test_widget;

		// Confirm that it's stored in the GLOBALS superglobal.
		$this->assertSame( $test_widget, $GLOBALS['_beans_widget'] );

		// Run the function.
		_beans_reset_widget();

		// Test that the superglobal is no longer set.
		$this->assertFalse( isset( $GLOBALS['_beans_widget'] ) );
	}
}
