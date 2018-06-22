<?php
/**
 * Tests for beans_get_widget_area()
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Widget;

use Beans\Framework\Tests\Integration\API\Widget\Includes\Beans_Widget_Test_Case;

require_once dirname( __FILE__ ) . '/includes/class-beans-widget-test-case.php';

/**
 * Class Tests_BeansGetWidgetArea
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansGetWidgetArea extends Beans_Widget_Test_Case {

	/**
	 * Test beans_get_widget_area() should return all widget area data when needle is not specified.
	 */
	public function test_should_return_all_data_when_needle_not_specified() {
		register_sidebar( [
			'id'   => 'test_sidebar',
			'name' => 'Test Sidebar',
		] );
		_beans_setup_widget_area( 'test_sidebar' );

		$this->assertSame( $this->get_expected_sidebar_data(), beans_get_widget_area() );
	}

	/**
	 * Test beans_get_widget_area() should return specific widget data when a needle is specified.
	 */
	public function test_should_return_specific_widget_data_when_needle_specified() {
		register_sidebar( [
			'id'   => 'test_sidebar',
			'name' => 'Test Sidebar',
		] );
		_beans_setup_widget_area( 'test_sidebar' );

		$this->assertEquals( 'test_sidebar', beans_get_widget_area( 'id' ) );
	}

	/**
	 * Return an array of expected sidebar data.
	 */
	protected function get_expected_sidebar_data() {
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
