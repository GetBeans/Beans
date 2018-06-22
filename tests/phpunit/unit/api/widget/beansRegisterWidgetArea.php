<?php
/**
 * Tests for beans_register_widget_area()
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
 * Class Tests_BeansRegisterWidgetArea
 *
 * @package Beans\Framework\Tests\Unit\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansRegisterWidgetArea extends Beans_Widget_Test_Case {

	/**
	 * Test beans_register_widget_area() should return an empty string when the ID is not set.
	 */
	public function test_should_return_empty_string_when_ID_not_set() {
		Monkey\Functions\expect( 'beans_get' )
			->once()
			->with( 'id', [] )
			->andReturn( false );

		$this->assertSame( '', beans_register_widget_area( [] ) );
	}

	/**
	 * Test beans_register_widget_area() should return the widget area ID when the widget area is registered.
	 */
	public function test_should_return_widget_area_ID_when_widget_area_registered() {
		Monkey\Functions\expect( 'beans_get' )
			->once()->with(
				'id',
				[
					'id'         => 'new-widget-area',
					'beans_type' => 'grid',
				]
			)
			->andReturn( 'new-widget-area' );

		Monkey\Functions\expect( 'beans_apply_filters' )
			->once()
			->with( 'beans_widgets_area_args[_new-widget-area]', $this->get_merged_args() )
			->andReturn( $this->get_merged_args() );

		Monkey\Functions\expect( 'register_sidebar' )
			->once()
			->with( $this->get_merged_args() )
			->andReturn( 'new-widget-area' );

		$this->assertEquals(
			'new-widget-area',
			beans_register_widget_area( [
				'id'         => 'new-widget-area',
				'beans_type' => 'grid',
			] )
		);
	}

	/**
	 * Return the expected merged arguments array.
	 */
	protected function get_merged_args() {
		return [
			'beans_type'                 => 'grid',
			'beans_show_widget_title'    => true,
			'beans_show_widget_badge'    => false,
			'beans_widget_badge_content' => __( 'Hello', 'tm-beans' ),
			'id'                         => 'new-widget-area',
		];
	}
}
