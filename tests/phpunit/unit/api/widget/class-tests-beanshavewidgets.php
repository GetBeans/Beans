<?php
/**
 * Tests for beans_have_widgets()
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
 * Class Tests_BeansHaveWidgets
 *
 * @package Beans\Framework\Tests\Unit\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansHaveWidgets extends Beans_Widget_Test_Case {

	/**
	 * Test beans_have_widgets() should return false when no widgets are available.
	 */
	public function test_should_return_false_when_no_widgets_are_available() {
		global $_beans_widget_area;

		$_beans_widget_area = [];

		Monkey\Functions\expect( 'beans_get' )
			->once()
			->with( 'widgets', [] )
			->andReturn( false );

		$this->assertFalse( beans_have_widgets() );
	}

	/**
	 * Test beans_have_widgets() should return true when more widgets are available.
	 */
	public function test_should_return_true_when_widgets_available() {
		global $_beans_widget_area;

		$_beans_widget_area = [
			'widgets'        => [
				'text-1' => [ 'id' => 'text-1' ],
				'text-2' => [ 'id' => 'text-2' ],
			],
			'current_widget' => 1,
		];

		Monkey\Functions\expect( 'beans_get' )
			->once()
			->with( 'widgets', $_beans_widget_area )
			->andReturn( $_beans_widget_area['widgets'] );

		$this->assertTrue( beans_have_widgets() );
	}

	/**
	 * Test beans_have_widgets() should return call _beans_reset_widget() and return false when at the end of the widget loop.
	 */
	public function test_should_call_beans_reset_widget_and_return_false_when_end_of_widget_loop() {
		global $_beans_widget_area;

		$_beans_widget_area = [
			'widgets'        => [
				'text-1' => [ 'id' => 'text-1' ],
				'text-2' => [ 'id' => 'text-2' ],
			],
			'current_widget' => 2,
		];

		Monkey\Functions\expect( 'beans_get' )
			->once()
			->with( 'widgets', $_beans_widget_area )
			->andReturn( $_beans_widget_area['widgets'] );

		Monkey\Functions\expect( '_beans_reset_widget' )->once();

		$this->assertFalse( beans_have_widgets() );
	}

}
