<?php
/**
 * Tests for the get_components_from_directory() method of _Beans_Uikit.
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\UIkit;

use _Beans_Uikit;
use Beans\Framework\Tests\Unit\API\UIkit\Includes\UIkit_Test_Case;
use org\bovigo\vfs\vfsStream;

require_once dirname( __DIR__ ) . '/includes/class-uikit-test-case.php';

/**
 * Class Tests_BeansUikit_GetComponentsFromDirectory
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 * @group   api
 * @group   api-uikit
 */
class Tests_BeansUikit_GetComponentsFromDirectory extends UIkit_Test_Case {

	/**
	 * Test _Beans_Uikit::get_components_from_directory() should return an empty array when the component files do not
	 * exist.
	 */
	public function test_should_return_empty_array_when_files_do_not_exist() {
		$beans_uikit = new _Beans_Uikit();

		// Check for styles.
		$actual = $beans_uikit->get_components_from_directory(
			[ 'foo', 'bar', 'baz' ],
			[ BEANS_API_PATH . 'uikit/src/less/core' ],
			'styles'
		);
		$this->assertEmpty( $actual );

		// Check for scripts.
		$actual = $beans_uikit->get_components_from_directory(
			[ 'foo', 'bar', 'baz' ],
			[ BEANS_API_PATH . 'uikit/src/js/core' ],
			'scripts'
		);
		$this->assertEmpty( $actual );
	}

	/**
	 * Test _Beans_Uikit::get_components_from_directory() should return an empty array when no components are requested.
	 */
	public function test_should_return_empty_array_when_no_components_requested() {
		$beans_uikit = new _Beans_Uikit();

		// Check for styles.
		$actual = $beans_uikit->get_components_from_directory(
			[],
			[ BEANS_API_PATH . 'uikit/src/less/core' ],
			'styles'
		);
		$this->assertEmpty( $actual );

		// Check for scripts.
		$actual = $beans_uikit->get_components_from_directory(
			[],
			[ BEANS_API_PATH . 'uikit/src/js/core' ],
			'scripts'
		);
		$this->assertEmpty( $actual );
	}

	/**
	 * Test _Beans_Uikit::get_components_from_directory() should return an array of LESS files when the component
	 * exists in core.
	 */
	public function test_should_return_array_of_less_files_when_component_exists_in_core() {
		$beans_uikit = new _Beans_Uikit();
		$actual      = $beans_uikit->get_components_from_directory(
			[ 'variables', 'badge', 'panel', 'doesnotexist', 'alert' ],
			[ BEANS_API_PATH . 'uikit/src/less/core' ],
			'styles'
		);
		$expected    = [
			BEANS_API_PATH . 'uikit/src/less/core/variables.less',
			BEANS_API_PATH . 'uikit/src/less/core/badge.less',
			BEANS_API_PATH . 'uikit/src/less/core/panel.less',
			BEANS_API_PATH . 'uikit/src/less/core/alert.less',
		];
		$this->assertSame( $expected, $actual );
	}

	/**
	 * Test _Beans_Uikit::get_components_from_directory() should return an array of LESS files when the component
	 * exists in the child theme.
	 */
	public function test_should_return_array_of_less_files_when_component_exists_in_child_theme() {
		$child_theme = vfsStream::url( 'themes/beans-child/assets/less/theme' );
		$beans_uikit = new _Beans_Uikit();
		$actual      = $beans_uikit->get_components_from_directory(
			[ 'variables', 'badge', 'panel', 'doesnotexist', 'alert' ],
			[ $child_theme ],
			'styles'
		);
		$expected    = [
			$child_theme . '/variables.less',
			$child_theme . '/panel.less',
			$child_theme . '/alert.less',
		];
		$this->assertSame( $expected, $actual );
	}

	/**
	 * Test _Beans_Uikit::get_components_from_directory() should return an array of LESS files when the component
	 * exists in the assets overwrite.
	 */
	public function test_should_return_array_of_less_files_when_component_exists_in_asset_overwrite() {
		$beans_uikit = new _Beans_Uikit();
		$actual      = $beans_uikit->get_components_from_directory(
			[ 'variables', 'badge', 'panel', 'doesnotexist', 'alert' ],
			[ BEANS_THEME_DIR . 'lib/assets/less/uikit-overwrite' ],
			'styles'
		);
		$expected    = [
			BEANS_THEME_DIR . 'lib/assets/less/uikit-overwrite/variables.less',
			BEANS_THEME_DIR . 'lib/assets/less/uikit-overwrite/panel.less',
		];
		$this->assertSame( $expected, $actual );
	}

	/**
	 * Test _Beans_Uikit::get_components_from_directory() should return an array of LESS files when the component
	 * exists in the given directories.
	 */
	public function test_should_return_array_of_less_files_when_components_exist_in_given_directories() {
		$child_theme = vfsStream::url( 'themes/beans-child/assets/less/theme' );
		$beans_uikit = new _Beans_Uikit();
		$actual      = $beans_uikit->get_components_from_directory(
			[ 'variables', 'badge', 'panel', 'doesnotexist', 'alert' ],
			[
				BEANS_API_PATH . 'uikit/src/less/core',
				BEANS_THEME_DIR . 'lib/assets/less/uikit-overwrite',
				$child_theme,
			],
			'styles'
		);
		$expected    = [
			BEANS_API_PATH . 'uikit/src/less/core/variables.less',
			BEANS_THEME_DIR . 'lib/assets/less/uikit-overwrite/variables.less',
			$child_theme . '/variables.less',
			BEANS_API_PATH . 'uikit/src/less/core/badge.less',
			BEANS_API_PATH . 'uikit/src/less/core/panel.less',
			BEANS_THEME_DIR . 'lib/assets/less/uikit-overwrite/panel.less',
			$child_theme . '/panel.less',
			BEANS_API_PATH . 'uikit/src/less/core/alert.less',
			$child_theme . '/alert.less',
		];
		$this->assertSame( $expected, $actual );
	}

	/**
	 * Test _Beans_Uikit::get_components_from_directory() should return an array of JavaScript files when the component
	 * exists in core.
	 */
	public function test_should_return_array_of_js_files_when_component_exists_in_core() {
		$beans_uikit = new _Beans_Uikit();
		$actual      = $beans_uikit->get_components_from_directory(
			[ 'core', 'offcanvas', 'alert', 'doesnotexist', 'button' ],
			[ BEANS_API_PATH . 'uikit/src/js/core' ],
			'scripts'
		);
		$expected    = [
			BEANS_API_PATH . 'uikit/src/js/core/core.min.js',
			BEANS_API_PATH . 'uikit/src/js/core/offcanvas.min.js',
			BEANS_API_PATH . 'uikit/src/js/core/alert.min.js',
			BEANS_API_PATH . 'uikit/src/js/core/button.min.js',
		];
		$this->assertSame( $expected, $actual );
	}

	/**
	 * Test _Beans_Uikit::get_components_from_directory() should return an array of JavaScript files when the component
	 * exists in the child theme.
	 */
	public function test_should_return_array_of_js_files_when_component_exists_in_child_theme() {
		$child_theme = vfsStream::url( 'themes/beans-child/assets/js' );
		$beans_uikit = new _Beans_Uikit();
		$actual      = $beans_uikit->get_components_from_directory(
			[ 'core', 'offcanvas', 'alert', 'doesnotexist', 'button' ],
			[ $child_theme ],
			'scripts'
		);
		$expected    = [
			$child_theme . '/alert.min.js',
		];
		$this->assertSame( $expected, $actual );
	}

	/**
	 * Test _Beans_Uikit::get_components_from_directory() should return an array of JavaScript files when the component
	 * exists in the given directories.
	 */
	public function test_should_return_array_of_js_files_when_components_exist_in_given_directories() {
		$child_theme = vfsStream::url( 'themes/beans-child/assets/js' );
		$beans_uikit = new _Beans_Uikit();
		$actual      = $beans_uikit->get_components_from_directory(
			[ 'core', 'offcanvas', 'alert', 'doesnotexist', 'button' ],
			[
				BEANS_API_PATH . 'uikit/src/js/core',
				$child_theme,
			],
			'scripts'
		);
		$expected    = [
			BEANS_API_PATH . 'uikit/src/js/core/core.min.js',
			BEANS_API_PATH . 'uikit/src/js/core/offcanvas.min.js',
			BEANS_API_PATH . 'uikit/src/js/core/alert.min.js',
			$child_theme . '/alert.min.js',
			BEANS_API_PATH . 'uikit/src/js/core/button.min.js',
		];
		$this->assertSame( $expected, $actual );
	}
}
