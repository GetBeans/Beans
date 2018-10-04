<?php
/**
 * Tests for beans_widget_area_shortcodes()
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Widget;

use Beans\Framework\Tests\Integration\API\Widget\Includes\Beans_Widget_Test_Case;

require_once dirname( __FILE__ ) . '/includes/class-beans-widget-test-case.php';

/**
 * Class Tests_BeansWidgetAreaShortcodes
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansWidgetAreaShortcodes extends Beans_Widget_Test_Case {

	/**
	 * Test beans_widget_area_shortcodes() should return the content with shortcodes filtered out.
	 */
	public function test_should_return_content_with_shortcodes_filtered_out() {
		global $_beans_widget_area;

		$_beans_widget_area['key'] = 'shortcode value';

		// Run test for content as a string.
		$this->assertEquals(
			'Content with a shortcode value.',
			beans_widget_area_shortcodes( 'Content with a {key}.' )
		);

		// Run test for content as an array.
		$this->assertEquals(
			'someURLparemetername=URL content with a shortcode value.',
			beans_widget_area_shortcodes(
				[ 'someURLparemetername' => 'URL content with a {key}.' ]
			)
		);
	}
}
