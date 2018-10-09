<?php
/**
 * Tests for _beans_uikit_get_registered_theme().
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\UIkit;

use Beans\Framework\Tests\Unit\API\UIkit\Includes\UIkit_Test_Case;
use org\bovigo\vfs\vfsStream;

require_once __DIR__ . '/includes/class-uikit-test-case.php';

/**
 * Class Tests_BeansUikitGetRegisteredTheme
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 * @group   api
 * @group   api-uikit
 */
class Tests_BeansUikitGetRegisteredTheme extends UIkit_Test_Case {

	/**
	 * Test _beans_uikit_get_registered_theme() should return false when the theme is not registered.
	 */
	public function test_should_return_false_when_no_path_given_and_theme_not_registered() {
		global $_beans_uikit_registered_items;

		foreach ( [ 'foo', 'bar', 'beans-child' ] as $theme_id ) {
			$this->assertFalse( _beans_uikit_get_registered_theme( $theme_id ) );
			$this->assertArrayNotHasKey( $theme_id, $_beans_uikit_registered_items['themes'] );
		}
	}

	/**
	 * Test _beans_uikit_get_registered_theme() should return theme's path when theme is registered.
	 */
	public function test_should_theme_path_when_theme_is_registered() {
		global $_beans_uikit_registered_items;

		// Check the built-in themes.
		foreach ( [ 'default', 'almost-flat', 'gradient', 'wordpress-admin' ] as $theme_id ) {
			$this->assertArrayHasKey( $theme_id, $_beans_uikit_registered_items['themes'] );
			$this->assertSame( $this->themes[ $theme_id ], _beans_uikit_get_registered_theme( $theme_id ) );
		}

		// Check the child theme.
		$_beans_uikit_registered_items['themes']['beans-child'] = vfsStream::url( 'virtual-wp-content/themes/beans-child/assets/less/theme' );
		$this->assertArrayHasKey( 'beans-child', $_beans_uikit_registered_items['themes'] );
		$this->assertSame( vfsStream::url( 'virtual-wp-content/themes/beans-child/assets/less/theme' ), _beans_uikit_get_registered_theme( 'beans-child' ) );
	}
}
