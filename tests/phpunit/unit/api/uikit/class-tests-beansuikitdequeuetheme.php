<?php
/**
 * Tests for beans_uikit_dequeue_theme().
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
 * Class Tests_BeansUikitDequeueTheme
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 * @group   api
 * @group   api-uikit
 */
class Tests_BeansUikitDequeueTheme extends UIkit_Test_Case {

	/**
	 * Test beans_uikit_dequeue_theme() should do nothing when the theme is not enqueued.
	 */
	public function test_should_do_nothing_when_theme_is_not_enqueued() {
		global $_beans_uikit_enqueued_items;

		foreach ( [ 'foo', 'bar', 'beans-child' ] as $theme_id ) {
			$this->assertArrayNotHasKey( $theme_id, $_beans_uikit_enqueued_items['themes'] );
			$this->assertNull( beans_uikit_dequeue_theme( $theme_id ) );
			$this->assertArrayNotHasKey( $theme_id, $_beans_uikit_enqueued_items['themes'] );
		}
	}

	/**
	 * Test beans_uikit_dequeue_theme() should remove the theme from the enqueue registry.
	 */
	public function test_should_remove_theme_from_enqueue_registry() {
		global $_beans_uikit_enqueued_items;

		// Check the built-in themes.
		foreach ( [ 'default', 'almost-flat', 'gradient', 'wordpress-admin' ] as $theme_id ) {
			$_beans_uikit_enqueued_items['themes'][ $theme_id ] = $this->themes[ $theme_id ];
			$this->assertArrayHasKey( $theme_id, $_beans_uikit_enqueued_items['themes'] );
			$this->assertNull( beans_uikit_dequeue_theme( $theme_id ) );
			$this->assertArrayNotHasKey( $theme_id, $_beans_uikit_enqueued_items['themes'] );
		}

		// Check the child theme.
		$_beans_uikit_enqueued_items['themes']['beans-child'] = vfsStream::url( 'virtual-wp-content/themes/beans-child/assets/less/theme' );
		$this->assertArrayHasKey( 'beans-child', $_beans_uikit_enqueued_items['themes'] );
		$this->assertNull( beans_uikit_dequeue_theme( 'beans-child' ) );
		$this->assertArrayNotHasKey( 'beans-child', $_beans_uikit_enqueued_items['themes'] );
	}
}
