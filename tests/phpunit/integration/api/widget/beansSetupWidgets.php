<?php
/**
 * Tests for _beans_setup_widgets()
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Widget;

use Beans\Framework\Tests\Integration\API\Widget\Includes\Beans_Widget_Test_Case;

require_once dirname( __FILE__ ) . '/includes/class-beans-widget-test-case.php';

/**
 * Class Tests_BeansSetupWidgets
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansSetupWidgets extends Beans_Widget_Test_Case {

	/**
	 * Test _beans_setup_widgets() should return the widget data when the widget is registered.
	 */
	public function test_should_return_widget_data_when_widget_is_registered() {
		beans_register_widget_area( 'test_sidebar' );
		$this->add_test_widget_to_test_sidebar();

		$this->assertSame(
			$this->get_expected_widget_setup_data(),
			_beans_setup_widgets( '<!--widget-text-2-->widget output<!--widget-end-->' )
		);
	}

	/**
	 * Get an array of expected widget setup data.
	 *
	 * @return array Expected widget setup data
	 */
	protected function get_expected_widget_setup_data() {
		return [
			'text-2' => [
				'options'       => [],
				'type'          => 'text',
				'title'         => '',
				'count'         => 1,
				'id'            => 'text-2',
				'name'          => 'Test Widget',
				'classname'     => 'widget_text',
				'description'   => null,
				'content'       => 'widget output',
				'show_title'    => true,
				'badge'         => false,
				'badge_content' => 'Hello',
			],
		];
	}
}
