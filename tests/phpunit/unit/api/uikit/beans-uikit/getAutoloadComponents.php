<?php
/**
 * Tests for the get_autoload_components() method of _Beans_Uikit.
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\UIkit;

use _Beans_Uikit;
use Beans\Framework\Tests\Unit\API\UIkit\Includes\UIkit_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-uikit-test-case.php';

/**
 * Class Tests_BeansUikit_GetAutoloadComponents
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 * @group   api
 * @group   api-uikit
 */
class Tests_BeansUikit_GetAutoloadComponents extends UIkit_Test_Case {

	/**
	 * Test _Beans_Uikit::get_autoload_components() should return empty arrays when the given components have no
	 * dependencies.
	 */
	public function test_should_return_empty_arrays_when_given_components_have_no_dependencies() {
		$beans_uikit = new _Beans_Uikit();
		$components  = [
			'alert',
			'badge',
			'article',
			'close',
			'dropdown',
		];

		$this->assertSame( [
			'core'    => [],
			'add-ons' => [],
		], $beans_uikit->get_autoload_components( $components ) );
	}

	/**
	 * Test _Beans_Uikit::get_autoload_components() should return dependencies for the panel.
	 */
	public function test_should_return_dependencies_for_panel() {
		$beans_uikit = new _Beans_Uikit();

		$expected = [
			'core'    => [ 'badge' ],
			'add-ons' => [],
		];
		$this->assertSame( $expected, $beans_uikit->get_autoload_components( [ 'panel' ] ) );
	}

	/**
	 * Test _Beans_Uikit::get_autoload_components() should return dependencies for the cover.
	 */
	public function test_should_return_dependencies_for_cover() {
		$beans_uikit = new _Beans_Uikit();

		$expected = [
			'core'    => [ 'flex' ],
			'add-ons' => [],
		];
		$this->assertSame( $expected, $beans_uikit->get_autoload_components( [ 'cover' ] ) );
	}

	/**
	 * Test _Beans_Uikit::get_autoload_components() should return dependencies for the overlay.
	 */
	public function test_should_return_dependencies_for_overlay() {
		$beans_uikit = new _Beans_Uikit();

		$expected = [
			'core'    => [ 'flex' ],
			'add-ons' => [],
		];
		$this->assertSame( $expected, $beans_uikit->get_autoload_components( [ 'overlay' ] ) );
	}

	/**
	 * Test _Beans_Uikit::get_autoload_components() should return dependencies for the tab.
	 */
	public function test_should_return_dependencies_for_tab() {
		$beans_uikit = new _Beans_Uikit();

		$expected = [
			'core'    => [ 'switcher' ],
			'add-ons' => [],
		];
		$this->assertSame( $expected, $beans_uikit->get_autoload_components( [ 'tab' ] ) );
	}

	/**
	 * Test _Beans_Uikit::get_autoload_components() should return dependencies for the modal.
	 */
	public function test_should_return_dependencies_for_modal() {
		$beans_uikit = new _Beans_Uikit();

		$expected = [
			'core'    => [ 'close' ],
			'add-ons' => [],
		];
		$this->assertSame( $expected, $beans_uikit->get_autoload_components( [ 'modal' ] ) );
	}

	/**
	 * Test _Beans_Uikit::get_autoload_components() should return dependencies for the scrollspy.
	 */
	public function test_should_return_dependencies_for_scrollspy() {
		$beans_uikit = new _Beans_Uikit();

		$expected = [
			'core'    => [ 'animation' ],
			'add-ons' => [],
		];
		$this->assertSame( $expected, $beans_uikit->get_autoload_components( [ 'scrollspy' ] ) );
	}

	/**
	 * Test _Beans_Uikit::get_autoload_components() should return dependencies for the lightbox.
	 */
	public function test_should_return_dependencies_for_lightbox() {
		$beans_uikit = new _Beans_Uikit();
		$expected    = [
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
		$this->assertSame( $expected, $beans_uikit->get_autoload_components( [ 'lightbox' ] ) );
	}

	/**
	 * Test _Beans_Uikit::get_autoload_components() should return dependencies for the slider.
	 */
	public function test_should_return_dependencies_for_slider() {
		$beans_uikit = new _Beans_Uikit();

		$expected = [
			'core'    => [],
			'add-ons' => [ 'slidenav' ],
		];
		$this->assertSame( $expected, $beans_uikit->get_autoload_components( [ 'slider' ] ) );
	}

	/**
	 * Test _Beans_Uikit::get_autoload_components() should return dependencies for the slideset.
	 */
	public function test_should_return_dependencies_for_slideset() {
		$beans_uikit = new _Beans_Uikit();

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
		$this->assertSame( $expected, $beans_uikit->get_autoload_components( [ 'slideset' ] ) );
	}

	/**
	 * Test _Beans_Uikit::get_autoload_components() should return dependencies for the slideshow.
	 */
	public function test_should_return_dependencies_for_slideshow() {
		$beans_uikit = new _Beans_Uikit();

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
		$this->assertSame( $expected, $beans_uikit->get_autoload_components( [ 'slideshow' ] ) );
	}

	/**
	 * Test _Beans_Uikit::get_autoload_components() should return dependencies for the parallax.
	 */
	public function test_should_return_dependencies_for_parallax() {
		$beans_uikit = new _Beans_Uikit();

		$expected = [
			'core'    => [ 'flex' ],
			'add-ons' => [],
		];
		$this->assertSame( $expected, $beans_uikit->get_autoload_components( [ 'parallax' ] ) );
	}

	/**
	 * Test _Beans_Uikit::get_autoload_components() should return dependencies for the notify.
	 */
	public function test_should_return_dependencies_for_notify() {
		$beans_uikit = new _Beans_Uikit();

		$expected = [
			'core'    => [ 'close' ],
			'add-ons' => [],
		];
		$this->assertSame( $expected, $beans_uikit->get_autoload_components( [ 'notify' ] ) );
	}

	/**
	 * Test _Beans_Uikit::get_autoload_components() should return all dependencies for the given components.
	 */
	public function test_should_return_all_dependencies_for_given_components() {
		$beans_uikit = new _Beans_Uikit();

		$expected = [
			'core'    => [
				'badge',
				'flex',
			],
			'add-ons' => [],
		];
		$this->assertSame( $expected, $beans_uikit->get_autoload_components( [
			'panel',
			'overlay',
		] ) );

		$expected = [
			'core'    => [ 'switcher' ],
			'add-ons' => [ 'slidenav' ],
		];
		$this->assertSame( $expected, $beans_uikit->get_autoload_components( [
			'tab',
			'slider',
		] ) );

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
		$this->assertSame( $expected, $beans_uikit->get_autoload_components( [
			'modal',
			'slideshow',
			'notify',
		] ) );

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
		$this->assertSame( $expected, $beans_uikit->get_autoload_components( [
			'lightbox',
			'notify',
			'panel',
			'slideshow',
			'tab',
		] ) );
	}
}
