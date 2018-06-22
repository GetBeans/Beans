<?php
/**
 * Tests for beans_have_widgets()
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Widget;

use Beans\Framework\Tests\Integration\API\Widget\Includes\Beans_Widget_Test_Case;

require_once dirname( __FILE__ ) . '/includes/class-beans-widget-test-case.php';

/**
 * Class Tests_BeansHaveWidgets
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansHaveWidgets extends Beans_Widget_Test_Case {

	/**
	 * Test beans_have_widgets() should return true when more widgets are available.
	 */
	public function test_should_return_true_when_widgets_available() {
		// Add a test sidebar with a test widget.
		beans_register_widget_area(
			[
				'id'   => 'test_sidebar',
				'name' => 'Test Sidebar',
			]
		);
		$this->add_test_widget_to_test_sidebar();

		// Run the Beans widget area setup.
		_beans_setup_widget_area( 'test_sidebar' );

		// Run the test.
		$this->assertTrue( beans_have_widgets() );
	}

	/**
	 * Test beans_have_widgets() should return false when no widgets are available.
	 */
	public function test_should_return_false_when_no_widgets_are_available() {
		// Add a test sidebar with no widgets.
		beans_register_widget_area(
			[
				'id'   => 'test_sidebar',
				'name' => 'Test Sidebar',
			]
		);

		// Run the Beans widget area setup.
		_beans_setup_widget_area( 'test_sidebar' );

		// Run the test.
		$this->assertFalse( beans_have_widgets() );
	}
}
