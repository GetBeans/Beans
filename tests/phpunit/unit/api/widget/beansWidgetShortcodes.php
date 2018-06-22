<?php
/**
 * Tests for beans_widget_shortcodes()
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
 * Class Tests_BeansWidgetShortcodes
 *
 * @package Beans\Framework\Tests\Unit\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansWidgetShortcodes extends Beans_Widget_Test_Case {

	/**
	 * Test beans_widget_shortcodes() should return the content when _beans_widget global is not set.
	 */
	public function test_should_return_content_when_beans_widget_area_global_not_set() {
		unset( $GLOBALS['_beans_widget'] );
		$content = 'Some widget content with a {key}.';

		$this->assertEquals( $content, beans_widget_area_shortcodes( $content ) );
	}

	/**
	 * Test beans_widget_shortcodes() should return the content with shortcodes filtered out when content is given as a string.
	 */
	public function test_should_return_content_with_shortcodes_filtered_out_when_content_given_as_string() {
		global $_beans_widget;

		$_beans_widget = [ 'Widget Data' ];

		Monkey\Functions\expect( 'build_query' )->never();

		Monkey\Functions\expect( 'beans_array_shortcodes' )
			->once()
			->with( 'Content with a {key}.', [ 'Widget Data' ] )
			->andReturn( 'Content with a shortcode value.' );

		// Run test for content as a string.
		$this->assertEquals(
			'Content with a shortcode value.',
			beans_widget_shortcodes( 'Content with a {key}.' )
		);
	}

	/**
	 * Test beans_widget_shortcodes() should return the content with shortcodes filtered out when content is given as an array.
	 */
	public function test_should_return_content_with_shortcodes_filtered_out_when_content_given_as_arrau() {
		global $_beans_widget;

		$_beans_widget = [ 'Widget Data' ];

		Monkey\Functions\expect( 'build_query' )
			->once()
			->with( [ 'someURLparemetername' => 'URL content with a {key}.' ] )
			->andReturn( 'someURLparemetername=URL content with a {key}.' );

		Monkey\Functions\expect( 'beans_array_shortcodes' )
			->once()
			->with( 'someURLparemetername=URL content with a {key}.', [ 'Widget Data' ] )
			->andReturn( 'someURLparemetername=URL content with a shortcode value.' );

		// Run test for content as a string.
		$this->assertEquals(
			'someURLparemetername=URL content with a shortcode value.',
			beans_widget_shortcodes( [ 'someURLparemetername' => 'URL content with a {key}.' ] )
		);
	}
}
