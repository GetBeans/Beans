<?php
/**
 * Tests for beans_is_active_widget_area()
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
 * Class Tests_BeansIsActiveWidgetArea
 *
 * @package Beans\Framework\Tests\Unit\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansIsActiveWidgetArea extends Beans_Widget_Test_Case {

	/**
	 * Test beans_is_active_widget_area() should return false when the widget area is not active.
	 */
	public function test_should_return_false_when_widget_area_not_active() {
		Monkey\Functions\expect( 'is_active_sidebar' )
			->once()
			->with( 'inactive-widget-area' )
			->andReturn( false );

		$this->assertFalse( beans_is_active_widget_area( 'inactive-widget-area' ) );
	}

	/**
	 * Test beans_is_active_widget_area() should return true when the widget area is active.
	 */
	public function test_should_return_true_when_widget_area_is_active() {
		Monkey\Functions\expect( 'is_active_sidebar' )
			->once()
			->with( 'active-sidebar' )
			->andReturn( true );

		$this->assertTrue( beans_is_active_widget_area( 'active-sidebar' ) );
	}
}
