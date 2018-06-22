<?php
/**
 * Test Case for the Beans Widget API integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Widget\Includes
 *
 * @since 1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Widget\Includes;

use Beans\Framework\Tests\Integration\Test_Case;

/**
 * Abstract Class Beans_Widget_Test_Case
 *
 * @package Beans\Framework\Tests\Integration\API\Widget\Includes
 */
abstract class Beans_Widget_Test_Case extends Test_Case {

	/**
	 * Fixture to clean up the test environment after each test.
	 */
	public function tearDown() {
		unset( $GLOBALS['$_beans_widget_area'] );
		unset( $GLOBALS['current_screen'] );
		$this->clean_up_global_scope();

		parent::tearDown();
	}

	/**
	 * Register a test widget into a test sidebar.
	 */
	protected function add_test_widget_to_test_sidebar() {
		global $wp_registered_widgets;

		$widget_object                         = new \WP_Widget_Text();
		$widget_object->id                     = 'text-2';
		$widget_object->name                   = 'Test Widget';
		$widget_object->widget_options['text'] = 'Test Text Content';
		$widget_object->widget_options['name'] = 'Test Sidebar';
		$widget_object->widget_options['id']   = 'test_sidebar';

		$widget_registration_args = [
			'name'      => $widget_object->name,
			'id'        => $widget_object->id,
			'callback'  => [ $widget_object, 'widget' ],
			'params'    => [ $widget_object->widget_options ],
			'classname' => $widget_object->widget_options['classname'],
		];

		$wp_registered_widgets[ $widget_object->id ] = $widget_registration_args; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited -- Valid use case: we need to explicitly set the widget registration for widget API tests.

		add_filter( 'sidebars_widgets', [ $this, 'add_a_widget' ] );
	}

	/**
	 * Callback to add test widget via 'sidebars_widgets' filter.
	 *
	 * @param array $sidebars_widgets The WP sidebars_widgets array.
	 *
	 * @return array Modified sidebars_widgets.
	 */
	public function add_a_widget( $sidebars_widgets ) {
		$sidebars_widgets['test_sidebar'] = [ 0 => 'text-2' ];

		return $sidebars_widgets;
	}
}
