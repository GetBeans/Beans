<?php
/**
 * Tests for beans_uikit_get_all_dependencies().
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\UIkit;

use Beans\Framework\Tests\Unit\API\UIkit\Includes\UIkit_Test_Case;

require_once __DIR__ . '/includes/class-uikit-test-case.php';

/**
 * Class Tests_BeansUikitGetAllDependencies
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 * @group   api
 * @group   api-uikit
 */
class Tests_BeansUikitGetAllDependencies extends UIkit_Test_Case {

	/**
	 * Test beans_uikit_get_all_dependencies() should return empty arrays when the given components have no
	 * dependencies.
	 */
	public function test_should_return_empty_arrays_when_given_components_have_no_dependencies() {
		$components = [
			'alert',
			'badge',
			'article',
			'close',
			'dropdown',
		];

		$this->assertSame(
			[
				'core'    => [],
				'add-ons' => [],
			],
			beans_uikit_get_all_dependencies( $components )
		);
	}

	/**
	 * Test beans_uikit_get_all_dependencies() should return dependencies for the panel.
	 */
	public function test_should_return_dependencies_for_panel() {
		$expected = [
			'core'    => [ 'badge' ],
			'add-ons' => [],
		];
		$this->assertSame( $expected, beans_uikit_get_all_dependencies( [ 'panel' ] ) );
	}

	/**
	 * Test beans_uikit_get_all_dependencies() should return dependencies for the cover.
	 */
	public function test_should_return_dependencies_for_cover() {
		$expected = [
			'core'    => [ 'flex' ],
			'add-ons' => [],
		];
		$this->assertSame( $expected, beans_uikit_get_all_dependencies( [ 'cover' ] ) );
	}

	/**
	 * Test beans_uikit_get_all_dependencies() should return dependencies for the overlay.
	 */
	public function test_should_return_dependencies_for_overlay() {
		$expected = [
			'core'    => [ 'flex' ],
			'add-ons' => [],
		];
		$this->assertSame( $expected, beans_uikit_get_all_dependencies( [ 'overlay' ] ) );
	}

	/**
	 * Test beans_uikit_get_all_dependencies() should return dependencies for the tab.
	 */
	public function test_should_return_dependencies_for_tab() {
		$expected = [
			'core'    => [ 'switcher' ],
			'add-ons' => [],
		];
		$this->assertSame( $expected, beans_uikit_get_all_dependencies( [ 'tab' ] ) );
	}

	/**
	 * Test beans_uikit_get_all_dependencies() should return dependencies for the modal.
	 */
	public function test_should_return_dependencies_for_modal() {
		$expected = [
			'core'    => [ 'close' ],
			'add-ons' => [],
		];
		$this->assertSame( $expected, beans_uikit_get_all_dependencies( [ 'modal' ] ) );
	}

	/**
	 * Test beans_uikit_get_all_dependencies() should return dependencies for the scrollspy.
	 */
	public function test_should_return_dependencies_for_scrollspy() {
		$expected = [
			'core'    => [ 'animation' ],
			'add-ons' => [],
		];
		$this->assertSame( $expected, beans_uikit_get_all_dependencies( [ 'scrollspy' ] ) );
	}

	/**
	 * Test beans_uikit_get_all_dependencies() should return dependencies for the lightbox.
	 */
	public function test_should_return_dependencies_for_lightbox() {
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
			],
		];
		$this->assertSame( $expected, beans_uikit_get_all_dependencies( [ 'lightbox' ] ) );
	}

	/**
	 * Test beans_uikit_get_all_dependencies() should return dependencies for the slider.
	 */
	public function test_should_return_dependencies_for_slider() {
		$expected = [
			'core'    => [],
			'add-ons' => [ 'slidenav' ],
		];
		$this->assertSame( $expected, beans_uikit_get_all_dependencies( [ 'slider' ] ) );
	}

	/**
	 * Test beans_uikit_get_all_dependencies() should return dependencies for the slideset.
	 */
	public function test_should_return_dependencies_for_slideset() {
		$expected = [
			'core'    => [
				'animation',
				'flex',
			],
			'add-ons' => [
				'dotnav',
				'slidenav',
			],
		];
		$this->assertSame( $expected, beans_uikit_get_all_dependencies( [ 'slideset' ] ) );
	}

	/**
	 * Test beans_uikit_get_all_dependencies() should return dependencies for the slideshow.
	 */
	public function test_should_return_dependencies_for_slideshow() {
		$expected = [
			'core'    => [
				'animation',
				'flex',
			],
			'add-ons' => [
				'dotnav',
				'slidenav',
			],
		];
		$this->assertSame( $expected, beans_uikit_get_all_dependencies( [ 'slideshow' ] ) );
	}

	/**
	 * Test beans_uikit_get_all_dependencies() should return dependencies for the parallax.
	 */
	public function test_should_return_dependencies_for_parallax() {
		$expected = [
			'core'    => [ 'flex' ],
			'add-ons' => [],
		];
		$this->assertSame( $expected, beans_uikit_get_all_dependencies( [ 'parallax' ] ) );
	}

	/**
	 * Test beans_uikit_get_all_dependencies() should return dependencies for the notify.
	 */
	public function test_should_return_dependencies_for_notify() {
		$expected = [
			'core'    => [ 'close' ],
			'add-ons' => [],
		];
		$this->assertSame( $expected, beans_uikit_get_all_dependencies( [ 'notify' ] ) );
	}

	/**
	 * Test beans_uikit_get_all_dependencies() should return all dependencies for the given components.
	 */
	public function test_should_return_all_dependencies_for_given_components() {
		$expected = [
			'core'    => [
				'badge',
				'flex',
			],
			'add-ons' => [],
		];
		$this->assertSame(
			$expected,
			beans_uikit_get_all_dependencies(
				[
					'panel',
					'overlay',
				]
			)
		);

		$expected = [
			'core'    => [ 'switcher' ],
			'add-ons' => [ 'slidenav' ],
		];
		$this->assertSame(
			$expected,
			beans_uikit_get_all_dependencies(
				[
					'tab',
					'slider',
				]
			)
		);

		$expected = [
			'core'    => [
				'close',
				'animation',
				'flex',
			],
			'add-ons' => [
				'dotnav',
				'slidenav',
			],
		];
		$this->assertSame(
			$expected,
			beans_uikit_get_all_dependencies(
				[
					'modal',
					'slideshow',
					'notify',
				]
			)
		);

		$expected = [
			'core'    => [
				'animation',
				'flex',
				'close',
				'modal',
				'overlay',
				'badge',
				'switcher',
			],
			'add-ons' => [
				'slidenav',
				'dotnav',
			],
		];
		$this->assertSame(
			$expected,
			beans_uikit_get_all_dependencies(
				[
					'lightbox',
					'notify',
					'panel',
					'slideshow',
					'tab',
				]
			)
		);
	}
}
