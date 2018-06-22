<?php
/**
 * Tests for beans_widget_shortcodes()
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Widget;

use Beans\Framework\Tests\Integration\API\Widget\Includes\Beans_Widget_Test_Case;

require_once dirname( __FILE__ ) . '/includes/class-beans-widget-test-case.php';

/**
 * Class Tests_BeansWidgetShortcodes
 *
 * @package Beans\Framework\Tests\Integration\API\Widget
 * @group   api
 * @group   api-widget
 */
class Tests_BeansWidgetShortcodes extends Beans_Widget_Test_Case {

	/**
	 * Test beans_widget_shortcodes() should return the content with shortcodes filtered out.
	 */
	public function test_should_return_content_with_shortcodes_filetered_out() {
		global $_beans_widget;

		$_beans_widget['key'] = 'shortcode value';

		// Run test for content as a string.
		$this->assertEquals(
			'Content with a shortcode value.',
			beans_widget_shortcodes( 'Content with a {key}.' )
		);

		// Run test for content as an array.
		$this->assertEquals(
			'someURLparemetername=URL content with a shortcode value.',
			beans_widget_shortcodes(
				[ 'someURLparemetername' => 'URL content with a {key}.' ]
			)
		);
	}
}
