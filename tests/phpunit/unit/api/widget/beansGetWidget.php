<?php
/**
 * Tests for beans_get_widget()
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
 * Class Tests_BeansGetWidget
 *
 * @package Beans\Framework\Tests\Unit\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansGetWidget extends Beans_Widget_Test_Case {

	/**
	 * Test beans_get_widget() should return false when widget data is not found.
	 */
	public function test_should_return_false_when_widget_data_not_found() {
		// Test for when needle is given.
		$this->assertFalse( beans_get_widget( 'bogus-needle' ) );

		// Test for when needle is not given.
		$this->assertFalse( beans_get_widget() );
	}

	/**
	 * Test beans_get_widget() should return all widget data when needle is not specified.
	 */
	public function test_should_return_all_data_when_needle_not_specified() {
		global $_beans_widget;

		$_beans_widget = $this->get_widget_test_data();

		$this->assertSame( $this->get_widget_test_data(), beans_get_widget() );
	}

	/**
	 * Test beans_get_widget() should return specific widget data when a needle is specified.
	 */
	public function test_should_return_specific_widget_data_when_needle_specified() {
		global $_beans_widget;

		$_beans_widget = $this->get_widget_test_data();

		Monkey\Functions\expect( 'beans_get' )
			->once()
			->with( 'id', $_beans_widget, false )
			->andReturn( 'text-2' );

		$this->assertEquals( 'text-2', beans_get_widget( 'id' ) );
	}

	/**
	 * Return an array of expected sidebar data.
	 */
	protected function get_widget_test_data() {
		return [
			'name'        => 'Test Widget',
			'id'          => 'text-2',
			'description' => 'Some description',
			'class'       => 'widgettext',
			'title'       => 'Test Widget Title',
			'text'        => 'Arbitrary text content.',
		];
	}
}
