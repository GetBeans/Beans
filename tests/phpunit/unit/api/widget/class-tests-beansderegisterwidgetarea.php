<?php
/**
 * Tests for beans_deregister_widget_area()
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
 * Class Tests_BeansDeegisterWidgetArea
 *
 * @package Beans\Framework\Tests\Unit\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansDeregisterWidgetArea extends Beans_Widget_Test_Case {

	/**
	 * Test beans_deregister_widget_area() should call unregister_sidebar() and return null.
	 */
	public function test_should_call_unregister_sidebar_and_return_null() {
		Monkey\Functions\expect( 'unregister_sidebar' )
			->once()
			->with( 'unwanted_sidebar' )
			->andReturn( null );

		$this->assertNull( beans_deregister_widget_area( 'unwanted_sidebar' ) );
	}
}
