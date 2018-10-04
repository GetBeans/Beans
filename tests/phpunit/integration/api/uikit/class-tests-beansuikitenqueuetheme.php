<?php
/**
 * Tests for beans_uikit_enqueue_theme().
 *
 * @package Beans\Framework\Tests\Integration\API\UIkit
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\UIkit;

use Beans\Framework\Tests\Integration\API\UIkit\Includes\UIkit_Test_Case;
use org\bovigo\vfs\vfsStream;

require_once __DIR__ . '/includes/class-uikit-test-case.php';

/**
 * Class Tests_BeansUikitEnqueueTheme
 *
 * @package Beans\Framework\Tests\Integration\API\UIkit
 * @group   api
 * @group   api-uikit
 */
class Tests_BeansUikitEnqueueTheme extends UIkit_Test_Case {

	/**
	 * Test beans_uikit_enqueue_theme() should return false when no path is given and the theme is not registered.
	 */
	public function test_should_return_false_when_no_path_given_and_theme_not_registered() {
		global $_beans_uikit_registered_items, $_beans_uikit_enqueued_items;

		foreach ( [ 'foo', 'bar', 'beans-child' ] as $theme_id ) {
			$this->assertFalse( beans_uikit_enqueue_theme( $theme_id ) );
			$this->assertArrayNotHasKey( $theme_id, $_beans_uikit_registered_items['themes'] );
			$this->assertArrayNotHasKey( $theme_id, $_beans_uikit_enqueued_items['themes'] );
		}
	}

	/**
	 * Test beans_uikit_enqueue_theme() should enqueue when the theme is already registered.
	 */
	public function test_should_enqueue_when_theme_is_already_registered() {
		global $_beans_uikit_registered_items, $_beans_uikit_enqueued_items;

		// Check the built-in themes.
		foreach ( [ 'default', 'almost-flat', 'gradient', 'wordpress-admin' ] as $theme_id ) {
			$this->assertArrayHasKey( $theme_id, $_beans_uikit_registered_items['themes'] );
			$this->assertTrue( beans_uikit_enqueue_theme( $theme_id ) );
			$this->assertArrayHasKey( $theme_id, $_beans_uikit_registered_items['themes'] );
			$this->assertSame( $_beans_uikit_registered_items['themes'][ $theme_id ], $_beans_uikit_enqueued_items['themes'][ $theme_id ] );
		}

		// Check the child theme.
		$_beans_uikit_registered_items['themes']['beans-child'] = vfsStream::url( 'virtual-wp-content/themes/beans-child/assets/less/theme' );
		$this->assertTrue( beans_uikit_enqueue_theme( 'beans-child' ) );
		$this->assertArrayHasKey( 'beans-child', $_beans_uikit_registered_items['themes'] );
		$this->assertSame( vfsStream::url( 'virtual-wp-content/themes/beans-child/assets/less/theme' ), $_beans_uikit_enqueued_items['themes']['beans-child'] );
	}

	/**
	 * Test beans_uikit_enqueue_theme() should register and then enqueue the theme.
	 */
	public function test_should_register_and_then_enqueue_theme() {
		global $_beans_uikit_registered_items, $_beans_uikit_enqueued_items;
		$path = vfsStream::url( 'virtual-wp-content/themes/beans-child/assets/less/theme' );

		$this->assertArrayNotHasKey( 'beans-child', $_beans_uikit_registered_items['themes'] );
		$this->assertArrayNotHasKey( 'beans-child', $_beans_uikit_enqueued_items['themes'] );

		$this->assertTrue( beans_uikit_enqueue_theme( 'beans-child', $path ) );
		$this->assertArrayHasKey( 'beans-child', $_beans_uikit_registered_items['themes'] );
		$this->assertArrayHasKey( 'beans-child', $_beans_uikit_enqueued_items['themes'] );
		$this->assertSame( trailingslashit( $path ), $_beans_uikit_enqueued_items['themes']['beans-child'] );
	}
}
