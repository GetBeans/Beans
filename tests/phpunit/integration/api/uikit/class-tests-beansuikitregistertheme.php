<?php
/**
 * Tests for beans_uikit_register_theme().
 *
 * @package Beans\Framework\Tests\Integration\API\UIkit
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\UIkit;

use Beans\Framework\Tests\Integration\API\UIkit\Includes\UIkit_Test_Case;

require_once __DIR__ . '/includes/class-uikit-test-case.php';

/**
 * Class Tests_BeansUikitRegisterTheme
 *
 * @package Beans\Framework\Tests\Integration\API\UIkit
 * @group   api
 * @group   api-uikit
 */
class Tests_BeansUikitRegisterTheme extends UIkit_Test_Case {

	/**
	 * Test beans_uikit_register_theme() should register the theme when given the URL.
	 */
	public function test_should_register_the_theme_when_given_url() {
		global $_beans_uikit_registered_items;

		$url  = BEANS_API_URL . 'uikit/src/themes/default';
		$path = trailingslashit( beans_url_to_path( $url ) );
		$this->assertTrue( beans_uikit_register_theme( 'sample-theme', $url ) );
		$this->assertArrayHasKey( 'sample-theme', $_beans_uikit_registered_items['themes'] );
		$this->assertSame( $path, $_beans_uikit_registered_items['themes']['sample-theme'] );

		$url  = BEANS_API_URL . 'uikit/src/themes/gradient';
		$path = trailingslashit( beans_url_to_path( $url ) );
		$this->assertTrue( beans_uikit_register_theme( 'sample-gradient', $url ) );
		$this->assertArrayHasKey( 'sample-gradient', $_beans_uikit_registered_items['themes'] );
		$this->assertSame( $path, $_beans_uikit_registered_items['themes']['sample-gradient'] );
	}
}
