<?php
/**
 * Tests for _beans_is_html_dev_mode()
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\HTML;

use Beans\Framework\Tests\Integration\API\HTML\Includes\HTML_Test_Case;

require_once __DIR__ . '/includes/class-html-test-case.php';

/**
 * Class Tests_BeansIsHtmlDevMode
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansIsHtmlDevMode extends HTML_Test_Case {

	/**
	 * Test _beans_is_html_dev_mode() should return false when the option does not exist.
	 */
	public function test_should_false_when_option_does_not_exist() {
		// Let's make sure the option does not exist.
		delete_option( 'beans_dev_mode' );

		$this->assertFalse( defined( 'BEANS_HTML_DEV_MODE' ) );
		$this->assertFalse( _beans_is_html_dev_mode() );
	}

	/**
	 * Test _beans_is_html_dev_mode() should return the option's value.
	 */
	public function test_should_return_option_value() {
		add_option( 'beans_dev_mode', 1 );
		$this->assertTrue( _beans_is_html_dev_mode() );

		update_option( 'beans_dev_mode', 0 );
		$this->assertFalse( _beans_is_html_dev_mode() );

		// Clean up.
		delete_option( 'beans_dev_mode' );
	}
}
