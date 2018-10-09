<?php
/**
 * Tests for beans_get_widget_area()
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
 * Class Tests_BeansGetWidgetArea
 *
 * @package Beans\Framework\Tests\Unit\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansGetWidgetArea extends Beans_Widget_Test_Case {

	/**
	 * Test beans_get_widget_area() should return false when widget area data is not found.
	 */
	public function test_should_return_false_when_widget_area_data_not_found() {
		// Test for when needle is given.
		$this->assertFalse( beans_get_widget_area( 'bogus-needle' ) );

		// Test for when needle is not given.
		$this->assertFalse( beans_get_widget_area() );
	}

	/**
	 * Test beans_get_widget_area() should return all widget area data when needle is not specified.
	 */
	public function test_should_return_all_data_when_needle_not_specified() {
		global $_beans_widget_area;

		$_beans_widget_area = $this->get_sidebar_test_data();

		$this->assertSame( $this->get_sidebar_test_data(), beans_get_widget_area() );
	}

	/**
	 * Test beans_get_widget_area() should return specific widget data when a needle is specified.
	 */
	public function test_should_return_specific_widget_data_when_needle_specified() {
		global $_beans_widget_area;

		$_beans_widget_area = $this->get_sidebar_test_data();

		Monkey\Functions\expect( 'beans_get' )
			->once()
			->with( 'id', $_beans_widget_area, false )
			->andReturn( 'test_sidebar' );

		$this->assertEquals( 'test_sidebar', beans_get_widget_area( 'id' ) );
	}

	/**
	 * Return an array of expected sidebar data.
	 */
	protected function get_sidebar_test_data() {
		return [
			'name'           => 'Test Sidebar',
			'id'             => 'test_sidebar',
			'description'    => '',
			'class'          => '',
			'before_widget'  => '<!--widget-%1$s-->',
			'after_widget'   => '<!--widget-end-->',
			'before_title'   => '<!--title-start-->',
			'after_title'    => '<!--title-end-->',
			'widgets_count'  => 0,
			'current_widget' => 0,
		];
	}
}
