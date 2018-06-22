<?php
/**
 * Tests for beans_get_widget()
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Widget;

use Beans\Framework\Tests\Integration\API\Widget\Includes\Beans_Widget_Test_Case;

require_once dirname( __FILE__ ) . '/includes/class-beans-widget-test-case.php';

/**
 * Class Tests_BeansGetWidget
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansGetWidget extends Beans_Widget_Test_Case {

	/**
	 * Test beans_get_widget() should return specific widget data when a needle is specified.
	 */
	public function test_should_return_specific_widget_data_when_needle_specified() {
		global $_beans_widget;

		$_beans_widget = $this->get_widget_test_data();

		$this->assertEquals( 'text-2', beans_get_widget( 'id' ) );
	}

	/**
	 * Return an array of expected sidebar data.
	 */
	protected function get_widget_test_data() {
		return [
			'name'        => 'Test Widget',
			'id'          => 'text-2',
			'description' => 'Some description',
			'class'       => 'widgettext',
			'title'       => 'Test Widget Title',
			'text'        => 'Arbitrary text content.',
		];
	}
}
