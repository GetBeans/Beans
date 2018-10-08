<?php
/**
 * Tests for _beans_setup_widgets()
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
 * Class Tests_BeansSetupWidgets
 *
 * @package Beans\Framework\Tests\Unit\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansSetupWidgets extends Beans_Widget_Test_Case {

	/**
	 * Test _beans_setup_widgets() should ignore non-widgetized content.
	 */
	public function test_should_ignore_non_widgetized_content() {
		$this->assertEmpty( _beans_setup_widgets( 'random non-widget string' ) );
	}

	/**
	 * Test _beans_setup_widgets() should skip a widget if it has not been registered.
	 */
	public function test_should_ignore_widget_when_widget_not_registered() {
		global $wp_registered_widgets;

		$wp_registered_widgets = []; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited -- Valid use case: ensures no widgets are registered for this test.

		$this->assertEmpty( _beans_setup_widgets( '<!--widget-text-2-->widget output<!--widget-end-->' ) );
	}

	/**
	 * Test _beans_setup_widgets() should return the widget data when the widget is registered.
	 */
	public function test_should_return_widget_data_when_widget_is_registered() {
		global $wp_registered_widgets, $_beans_widget_area;

		$widget              = \Mockery::mock( 'WP_Widget' );
		$widget->id_base     = 'text';
		$widget->option_name = 'text';

		$sidebars = [
			'text-2' => [
				'id'        => 'text-2',
				'name'      => 'Test Widget',
				'classname' => 'widget_text',
				'callback'  => [ $widget, 'display_callback' ],
				'params'    => [ 0 => [ 'number' => 2 ] ],
			],
		];

		// Prime the $wp_registered widgets and $_beans_widget_area globals.
		$wp_registered_widgets                            = $sidebars; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited -- Valid use case: setting up sidebars outside of WP.
		$_beans_widget_area['widgets_count']              = 1;
		$_beans_widget_area['beans_show_widget_title']    = true;
		$_beans_widget_area['beans_show_widget_badge']    = false;
		$_beans_widget_area['beans_widget_badge_content'] = 'Hello';

		Monkey\Functions\expect( 'beans_get' )
			->once()
			->with( 'text-2', $wp_registered_widgets )
			->andReturn( $wp_registered_widgets['text-2'] );

		Monkey\Functions\expect( 'get_option' )
			->once()
			->with( 'text' )
			->andReturn( [ 2 => [ 'widget_options' ] ] );

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
				'options'       => [ 0 => 'widget_options' ],
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
