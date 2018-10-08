<?php
/**
 * Tests for beans_uikit_register_theme().
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\UIkit;

use Beans\Framework\Tests\Unit\API\UIkit\Includes\UIkit_Test_Case;
use Brain\Monkey;
use org\bovigo\vfs\vfsStream;

require_once __DIR__ . '/includes/class-uikit-test-case.php';

/**
 * Class Tests_BeansUikitRegisterTheme
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 * @group   api
 * @group   api-uikit
 */
class Tests_BeansUikitRegisterTheme extends UIkit_Test_Case {

	/**
	 * Test beans_uikit_register_theme() should return true when theme is already registered.
	 */
	public function test_should_return_true_when_theme_is_already_registered() {
		Monkey\Functions\expect( 'beans_str_starts_with' )->never();
		Monkey\Functions\expect( 'beans_url_to_path' )->never();
		Monkey\Functions\expect( 'trailingslashit' )->never();

		// Check the built-in themes.
		foreach ( [ 'default', 'almost-flat', 'gradient', 'wordpress-admin' ] as $theme_id ) {
			$this->assertTrue( beans_uikit_register_theme( $theme_id, '' ) );
		}

		// Check the child theme.
		global $_beans_uikit_registered_items;
		$_beans_uikit_registered_items['themes']['beans-child'] = vfsStream::url( 'virtual-wp-content/themes/beans-child/assets/less/theme' );
		$this->assertTrue( beans_uikit_register_theme( 'beans-child', '' ) );
		$this->assertTrue( beans_uikit_register_theme( 'beans-child', vfsStream::url( 'virtual-wp-content/themes/beans-child/assets/less/theme' ) ) );
	}

	/**
	 * Test beans_uikit_register_theme() should return false when the theme is not registered and no path is given.
	 */
	public function test_should_return_false_when_theme_not_registered_and_no_path_given() {
		Monkey\Functions\expect( 'beans_str_starts_with' )->never();
		Monkey\Functions\expect( 'beans_url_to_path' )->never();
		Monkey\Functions\expect( 'trailingslashit' )->never();

		// Check the built-in themes.
		$this->assertFalse( beans_uikit_register_theme( 'foo', '' ) );
		$this->assertFalse( beans_uikit_register_theme( 'bar', '' ) );
		$this->assertFalse( beans_uikit_register_theme( 'beans-child', '' ) );
	}

	/**
	 * Test beans_uikit_register_theme() should register the theme when given the path.
	 */
	public function test_should_register_the_theme_when_given_path() {
		global $_beans_uikit_registered_items;
		$path = vfsStream::url( 'virtual-wp-content/themes/beans-child/assets/less/theme' );
		Monkey\Functions\expect( 'beans_str_starts_with' )
			->once()
			->with( $path, 'http' )
			->andReturn( false );
		Monkey\Functions\expect( 'beans_url_to_path' )->never();
		Monkey\Functions\when( 'trailingslashit' )->returnArg();

		$this->assertTrue( beans_uikit_register_theme( 'beans-child', $path ) );
		$this->assertArrayHasKey( 'beans-child', $_beans_uikit_registered_items['themes'] );
		$this->assertSame( $path, $_beans_uikit_registered_items['themes']['beans-child'] );
	}

	/**
	 * Test beans_uikit_register_theme() should register the theme when given the URL.
	 */
	public function test_should_register_the_theme_when_given_url() {
		global $_beans_uikit_registered_items;

		$path = vfsStream::url( 'virtual-wp-content/themes/beans-child/assets/less/theme' );
		$url  = 'http://example.com/virtual-wp-content/themes/beans-child/assets/less/theme';
		Monkey\Functions\expect( 'beans_str_starts_with' )
			->once()
			->with( $url, 'http' )
			->andReturn( true );
		Monkey\Functions\expect( 'beans_url_to_path' )
			->once()
			->with( $url )
			->andReturn( $path );
		Monkey\Functions\when( 'trailingslashit' )->returnArg();

		$this->assertTrue( beans_uikit_register_theme( 'beans-child', $url ) );
		$this->assertArrayHasKey( 'beans-child', $_beans_uikit_registered_items['themes'] );
		$this->assertSame( $path, $_beans_uikit_registered_items['themes']['beans-child'] );
	}
}
