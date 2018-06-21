<?php
/**
 * Tests for the register_less_components() method of _Beans_Uikit.
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
 * Class Tests_BeansUikit_RegisterLessComponents
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 * @group   api
 * @group   api-uikit
 */
class Tests_BeansUikit_RegisterLessComponents extends UIkit_Test_Case {

	/**
	 * Test _Beans_Uikit::register_less_components() should return an empty array when no theme or components are
	 * registered and no 'core' in the global.
	 */
	public function test_should_return_empty_array_when_no_theme_or_components_registered_and_no_core() {
		// Remove "core" from the components.
		global $_beans_uikit_enqueued_items;
		$_beans_uikit_enqueued_items['components'] = [ 'add-ons' => [] ];

		// Run the test.
		$this->assertEmpty( ( new _Beans_Uikit() )->register_less_components() );
	}

	/**
	 * Test _Beans_Uikit::register_less_components() should return variables and fixes when no theme or components are
	 * registered.
	 */
	public function test_should_return_variables_and_fixes_when_no_theme_or_components_registered() {
		// Check the global.
		global $_beans_uikit_enqueued_items;
		$this->assertSame( [
			'core'    => [],
			'add-ons' => [],
		], $_beans_uikit_enqueued_items['components'] );

		// Run the test.
		$this->assertSame(
			[
				BEANS_API_PATH . 'uikit/src/less/core/variables.less',
				BEANS_API_PATH . 'uikit/src/fixes.less',
			],
			( new _Beans_Uikit() )->register_less_components()
		);
	}

	/**
	 * Test _Beans_Uikit::register_less_components() should return an array of core paths when components are
	 * registered.
	 */
	public function test_should_return_array_of_core_paths_when_components_are_registered() {
		global $_beans_uikit_enqueued_items;
		$_beans_uikit_enqueued_items['components']['core'] = [ 'article', 'base', 'block', 'grid' ];

		$this->assertSame(
			[
				BEANS_API_PATH . 'uikit/src/less/core/variables.less',
				BEANS_API_PATH . 'uikit/src/less/core/article.less',
				BEANS_API_PATH . 'uikit/src/less/core/base.less',
				BEANS_API_PATH . 'uikit/src/less/core/block.less',
				BEANS_API_PATH . 'uikit/src/less/core/grid.less',
				BEANS_API_PATH . 'uikit/src/fixes.less',
			],
			( new _Beans_Uikit() )->register_less_components()
		);
	}

	/**
	 * Test _Beans_Uikit::register_less_components() should return an array of add-ons paths when components are
	 * registered.
	 */
	public function test_should_return_array_of_add_ons_paths_when_components_are_registered() {
		global $_beans_uikit_enqueued_items;
		$_beans_uikit_enqueued_items['components']['add-ons'] = [ 'slidenav' ];

		$this->assertSame(
			[
				BEANS_API_PATH . 'uikit/src/less/core/variables.less',
				BEANS_API_PATH . 'uikit/src/less/components/slidenav.less',
				BEANS_API_PATH . 'uikit/src/fixes.less',
			],
			( new _Beans_Uikit() )->register_less_components()
		);
	}

	/**
	 * Test _Beans_Uikit::register_less_components() should return an array of paths when components are registered.
	 */
	public function test_should_return_array_of_paths_when_components_are_registered() {
		global $_beans_uikit_enqueued_items;
		$_beans_uikit_enqueued_items['components']['core']    = [ 'badge', 'close', 'animation', 'flex' ];
		$_beans_uikit_enqueued_items['components']['add-ons'] = [ 'slideshow', 'dotnav', 'slidenav' ];

		$this->assertSame(
			[
				BEANS_API_PATH . 'uikit/src/less/core/variables.less',
				BEANS_API_PATH . 'uikit/src/less/core/badge.less',
				BEANS_API_PATH . 'uikit/src/less/core/close.less',
				BEANS_API_PATH . 'uikit/src/less/core/animation.less',
				BEANS_API_PATH . 'uikit/src/less/core/flex.less',
				BEANS_API_PATH . 'uikit/src/less/components/slideshow.less',
				BEANS_API_PATH . 'uikit/src/less/components/dotnav.less',
				BEANS_API_PATH . 'uikit/src/less/components/slidenav.less',
				BEANS_API_PATH . 'uikit/src/fixes.less',
			],
			( new _Beans_Uikit() )->register_less_components()
		);
	}

	/**
	 * Test _Beans_Uikit::register_less_components() should include theme components when a theme is registered.
	 */
	public function test_should_include_theme_components_when_theme_is_registered() {
		$theme = BEANS_API_PATH . 'uikit/src/themes/default';

		global $_beans_uikit_enqueued_items;
		$_beans_uikit_enqueued_items['themes']                = [ $theme ];
		$_beans_uikit_enqueued_items['components']['core']    = [ 'badge', 'close', 'animation', 'flex' ];
		$_beans_uikit_enqueued_items['components']['add-ons'] = [ 'slideshow', 'dotnav', 'slidenav' ];

		$components = ( new _Beans_Uikit() )->register_less_components();
		$this->assertContains( $theme . '/variables.less', $components );
		$this->assertContains( $theme . '/badge.less', $components );
		$this->assertContains( $theme . '/close.less', $components );
		$this->assertContains( $theme . '/dotnav.less', $components );
		$this->assertContains( $theme . '/slidenav.less', $components );
	}
}
