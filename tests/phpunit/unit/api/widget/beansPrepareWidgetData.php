<?php
/**
 * Tests for _beans_prepare_widget_data()
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
 * Class Tests_BeansPrepareWidgetData
 *
 * @package Beans\Framework\Tests\Unit\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansPrepareWidgetData extends Beans_Widget_Test_Case {

	/**
	 * Test _beans_prepare_widget_data() should prepare the widget data.
	 */
	public function test_should_prepare_widget_data() {
		global $_beans_widget;

		Monkey\Functions\expect( 'beans_get_widget_area' )
			->once()
			->with( 'widgets' )
			->andReturn(
				[
					'text-2' => [
						'id'   => 'text-2',
						'name' => 'Test Widget',
					],
				]
			);

		// Call the function.
		_beans_prepare_widget_data( 'text-2' );

		// Verify that widget data is now prepared.
		$this->assertContains( 'Test Widget', $_beans_widget );
	}
}
