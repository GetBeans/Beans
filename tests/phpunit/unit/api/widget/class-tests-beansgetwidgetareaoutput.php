<?php
/**
 * Tests for beans_get_widget_area_output()
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
 * Class Tests_BeansGetWidgetAreaOutput
 *
 * @package Beans\Framework\Tests\Unit\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansGetWidgetAreaOutput extends Beans_Widget_Test_Case {

	/**
	 * Test beans_get_widget_area_output() should return false when the widget area is not registered.
	 */
	public function test_should_return_false_when_widget_area_not_registered() {
		Monkey\Functions\expect( 'beans_has_widget_area' )
			->once()
			->with( 'unregistered-widget-area' )
			->andReturn( false );

		$this->assertFalse( beans_get_widget_area_output( 'unregistered-widget-area' ) );
	}

	/**
	 * Test beans_get_widget_area_output() should call required functions and do required actions when a widget area is registered.
	 */
	public function test_should_return_widget_output_when_widget_area_is_registered() {
		Monkey\Functions\expect( 'beans_has_widget_area' )
			->once()
			->with( 'primary_sidebar' )
			->andReturn( true );

		Monkey\Functions\expect( '_beans_setup_widget_area' )
			->once()
			->with( 'primary_sidebar' )
			->andReturn( true );

		Monkey\Functions\expect( '_beans_reset_widget_area' )
			->once()
			->andReturn();

		$output = beans_get_widget_area_output( 'primary_sidebar' );

		$this->assertEquals( 1, did_action( 'beans_widget_area_init' ) );
		$this->assertEquals( 1, did_action( 'beans_widget_area' ) );
		$this->assertEquals( 1, did_action( 'beans_widget_area_reset' ) );
	}
}
