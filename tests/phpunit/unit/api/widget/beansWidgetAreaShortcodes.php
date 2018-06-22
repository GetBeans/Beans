<?php
/**
 * Tests for beans_widget_area_shortcodes()
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
 * Class Tests_BeansWidgetAreaShortcodes
 *
 * @package Beans\Framework\Tests\Unit\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansWidgetAreaShortcodes extends Beans_Widget_Test_Case {

	/**
	 * Test beans_widget_area_shortcodes() should return the content when _beans_widget_area global is not set.
	 */
	public function test_should_return_content_when_beans_widget_area_global_not_set() {
		unset( $GLOBALS['_beans_widget_area'] );
		$content = 'Some widget area content with a {key}.';

		$this->assertEquals( $content, beans_widget_area_shortcodes( $content ) );
	}

	/**
	 * Test beans_widget_area_shortcodes() should return the content with shortcodes filtered out when content is given as a string.
	 */
	public function test_should_return_content_with_shortcodes_filtered_out_when_content_given_as_string() {
		global $_beans_widget_area;

		$_beans_widget_area = [ 'Widget Area Data' ];

		Monkey\Functions\expect( 'build_query' )->never();

		Monkey\Functions\expect( 'beans_array_shortcodes' )
			->once()
			->with( 'Content with a {key}.', [ 'Widget Area Data' ] )
			->andReturn( 'Content with a shortcode value.' );

		// Run test for content as a string.
		$this->assertEquals(
			'Content with a shortcode value.',
			beans_widget_area_shortcodes( 'Content with a {key}.' )
		);
	}

	/**
	 * Test beans_widget_area_shortcodes() should return the content with shortcodes filtered out when content is given as an array.
	 */
	public function test_should_return_content_with_shortcodes_filtered_out_when_content_given_as_array() {
		global $_beans_widget_area;

		$_beans_widget_area = [ 'Widget Area Data' ];

		Monkey\Functions\expect( 'build_query' )
			->once()
			->with( [ 'someURLparemetername' => 'URL content with a {key}.' ] )
			->andReturn( 'someURLparemetername=URL content with a {key}.' );

		Monkey\Functions\expect( 'beans_array_shortcodes' )
			->once()
			->with( 'someURLparemetername=URL content with a {key}.', [ 'Widget Area Data' ] )
			->andReturn( 'someURLparemetername=URL content with a shortcode value.' );

		// Run test for content as a string.
		$this->assertEquals(
			'someURLparemetername=URL content with a shortcode value.',
			beans_widget_area_shortcodes( [ 'someURLparemetername' => 'URL content with a {key}.' ] )
		);
	}
}
