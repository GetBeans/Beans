<?php
/**
 * Tests for _beans_uikit_autoload_dependencies().
 *
 * @package Beans\Framework\Tests\Integration\API\UIkit
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\UIkit;

use Beans\Framework\Tests\Integration\API\UIkit\Includes\UIkit_Test_Case;

require_once __DIR__ . '/includes/class-uikit-test-case.php';

/**
 * Class Tests_BeansUikitAutoloadDependencies
 *
 * @package Beans\Framework\Tests\Integration\API\UIkit
 * @group   api
 * @group   api-uikit
 */
class Tests_BeansUikitAutoloadDependencies extends UIkit_Test_Case {

	/**
	 * Test _beans_uikit_autoload_dependencies() should add each core (autoload) dependency into the registry.
	 */
	public function test_should_add_each_core_dependency_into_registry() {
		global $_beans_uikit_enqueued_items;

		_beans_uikit_autoload_dependencies( [ 'alert', 'button', 'overlay', 'tab' ] );
		$this->assertSame( [ 'flex', 'switcher' ], $_beans_uikit_enqueued_items['components']['core'] );
	}

	/**
	 * Test _beans_uikit_autoload_dependencies() should add each add-ons (autoload) dependency into the registry.
	 */
	public function test_should_add_each_addons_dependency_into_registry() {
		global $_beans_uikit_enqueued_items;

		_beans_uikit_autoload_dependencies( [ 'accordion', 'autocomplete', 'slideset' ] );
		$this->assertSame( [ 'dotnav', 'slidenav' ], $_beans_uikit_enqueued_items['components']['add-ons'] );
	}

	/**
	 * Test _beans_uikit_autoload_dependencies() should add each (autoload) dependency into the registry.
	 */
	public function test_should_add_each_dependency_into_registry() {
		global $_beans_uikit_enqueued_items;

		_beans_uikit_autoload_dependencies( [ 'lightbox', 'slideshow' ] );
		$expected = [
			'core'    => [
				'animation',
				'flex',
				'close',
				'modal',
				'overlay',
			],
			'add-ons' => [
				'slidenav',
				'dotnav',
			],
		];
		$this->assertSame( $expected, $_beans_uikit_enqueued_items['components'] );
	}
}
