<?php
/**
 * Tests for the register_js_components() method of Beans_Uikit.
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\UIkit;

use Beans_Uikit;
use Beans\Framework\Tests\Unit\API\UIkit\Includes\UIkit_Test_Case;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-uikit-test-case.php';

/**
 * Class Tests_BeansUikit_RegisterJsComponents
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 * @group   api
 * @group   api-uikit
 */
class Tests_BeansUikit_RegisterJsComponents extends UIkit_Test_Case {

	/**
	 * Test Beans_Uikit::register_js_components() should return an empty array when no components are registered and
	 * no 'core' in the global.
	 */
	public function test_should_return_empty_array_when_no_components_registered_and_no_core() {
		// Remove "core" from the components.
		global $_beans_uikit_enqueued_items;
		$_beans_uikit_enqueued_items['components'] = [ 'add-ons' => [] ];

		// Run the test.
		$this->assertEmpty( ( new Beans_Uikit() )->register_js_components() );
	}

	/**
	 * Test Beans_Uikit::register_js_components() should return the base core components when no theme or components
	 * are registered.
	 */
	public function test_should_return_base_core_components_when_no_components_registered() {
		// Check the global.
		global $_beans_uikit_enqueued_items;
		$this->assertSame(
			[
				'core'    => [],
				'add-ons' => [],
			],
			$_beans_uikit_enqueued_items['components']
		);

		// Run the test.
		$this->assertSame(
			[
				BEANS_API_PATH . 'uikit/src/js/core/core.min.js',
				BEANS_API_PATH . 'uikit/src/js/core/utility.min.js',
				BEANS_API_PATH . 'uikit/src/js/core/touch.min.js',
			],
			( new Beans_Uikit() )->register_js_components()
		);
	}

	/**
	 * Test Beans_Uikit::register_js_components() should return an array of core paths when components are
	 * registered.
	 */
	public function test_should_return_array_of_core_paths_when_components_are_registered() {
		global $_beans_uikit_enqueued_items;
		$_beans_uikit_enqueued_items['components']['core'] = [ 'alert', 'button', 'nav', 'smooth-scroll' ];

		$this->assertSame(
			[
				// Base the core items.
				BEANS_API_PATH . 'uikit/src/js/core/core.min.js',
				BEANS_API_PATH . 'uikit/src/js/core/utility.min.js',
				BEANS_API_PATH . 'uikit/src/js/core/touch.min.js',
				// Enqueued items.
				BEANS_API_PATH . 'uikit/src/js/core/alert.min.js',
				BEANS_API_PATH . 'uikit/src/js/core/button.min.js',
				BEANS_API_PATH . 'uikit/src/js/core/nav.min.js',
				BEANS_API_PATH . 'uikit/src/js/core/smooth-scroll.min.js',
			],
			( new Beans_Uikit() )->register_js_components()
		);
	}

	/**
	 * Test Beans_Uikit::register_less_components() should return an array of add-ons paths when components are
	 * registered.
	 */
	public function test_should_return_array_of_add_ons_paths_when_components_are_registered() {
		global $_beans_uikit_enqueued_items;
		$_beans_uikit_enqueued_items['components']['add-ons'] = [ 'accordion', 'notify' ];

		$this->assertSame(
			[
				// Base the core items.
				BEANS_API_PATH . 'uikit/src/js/core/core.min.js',
				BEANS_API_PATH . 'uikit/src/js/core/utility.min.js',
				BEANS_API_PATH . 'uikit/src/js/core/touch.min.js',
				// Enqueued items.
				BEANS_API_PATH . 'uikit/src/js/components/accordion.min.js',
				BEANS_API_PATH . 'uikit/src/js/components/notify.min.js',
			],
			( new Beans_Uikit() )->register_js_components()
		);
	}

	/**
	 * Test Beans_Uikit::register_less_components() should return an array of paths when components are registered.
	 */
	public function test_should_return_array_of_paths_when_components_are_registered() {
		global $_beans_uikit_enqueued_items;
		$_beans_uikit_enqueued_items['components']['core']    = [ 'alert', 'button', 'nav', 'smooth-scroll' ];
		$_beans_uikit_enqueued_items['components']['add-ons'] = [ 'accordion', 'notify' ];

		$this->assertSame(
			[
				// Base the core items.
				BEANS_API_PATH . 'uikit/src/js/core/core.min.js',
				BEANS_API_PATH . 'uikit/src/js/core/utility.min.js',
				BEANS_API_PATH . 'uikit/src/js/core/touch.min.js',
				// Enqueued core items.
				BEANS_API_PATH . 'uikit/src/js/core/alert.min.js',
				BEANS_API_PATH . 'uikit/src/js/core/button.min.js',
				BEANS_API_PATH . 'uikit/src/js/core/nav.min.js',
				BEANS_API_PATH . 'uikit/src/js/core/smooth-scroll.min.js',
				// Enqueued add-on items.
				BEANS_API_PATH . 'uikit/src/js/components/accordion.min.js',
				BEANS_API_PATH . 'uikit/src/js/components/notify.min.js',
			],
			( new Beans_Uikit() )->register_js_components()
		);
	}
}
