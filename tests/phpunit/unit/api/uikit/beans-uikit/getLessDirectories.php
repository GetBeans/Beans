<?php
/**
 * Tests for the get_less_directories() method of _Beans_Uikit.
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
 * Class Tests_BeansUikit_GetLessDirectories
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 * @group   api
 * @group   api-uikit
 */
class Tests_BeansUikit_GetLessDirectories extends UIkit_Test_Case {

	/**
	 * Test _Beans_Uikit::get_less_directories() should return only the UIkit type directory when no theme is
	 * registered.
	 */
	public function test_should_return_only_uikit_type_directory_when_no_theme_registered() {
		$beans_uikit = new _Beans_Uikit();

		global $_beans_uikit_enqueued_items;
		$this->assertEmpty( $_beans_uikit_enqueued_items['themes'] );
		$this->assertSame(
			[ BEANS_API_PATH . 'uikit/src/less/core' ],
			$beans_uikit->get_less_directories( 'core' )
		);
		$this->assertSame(
			[ BEANS_API_PATH . 'uikit/src/less/components' ],
			$beans_uikit->get_less_directories( 'components' )
		);
	}

	/**
	 * Test _Beans_Uikit::get_less_directories() should return the path to the 'components' directory when the type is
	 * 'add-ons'.
	 */
	public function test_should_return_path_to_components_directory_when_type_is_add_ons() {
		$beans_uikit = new _Beans_Uikit();

		$this->assertSame(
			[ BEANS_API_PATH . 'uikit/src/less/components' ],
			$beans_uikit->get_less_directories( 'add-ons' )
		);
	}

	/**
	 * Test _Beans_Uikit::get_less_directories() should return the UIkit type directory and each theme's directory.
	 */
	public function test_should_return_uikit_type_directory_and_each_theme_directory() {
		$beans_uikit = new _Beans_Uikit();

		global $_beans_uikit_enqueued_items;

		$themes                                = [
			BEANS_API_PATH . 'uikit/src/themes/almost-flat',
			BEANS_API_PATH . 'uikit/src/themes/default',
			BEANS_API_PATH . 'uikit/src/themes/gradient',
		];
		$_beans_uikit_enqueued_items['themes'] = $themes;
		$this->assertSame(
			array_merge( [ BEANS_API_PATH . 'uikit/src/less/core' ], $themes ),
			$beans_uikit->get_less_directories( 'core' )
		);
		$this->assertSame(
			array_merge( [ BEANS_API_PATH . 'uikit/src/less/components' ], $themes ),
			$beans_uikit->get_less_directories( 'components' )
		);

		$child_theme                             = vfsStream::url( 'themes/beans-child/assets/less/theme' );
		$themes[]                                = $child_theme;
		$_beans_uikit_enqueued_items['themes'][] = $child_theme;
		$this->assertSame(
			array_merge( [ BEANS_API_PATH . 'uikit/src/less/core' ], $themes ),
			$beans_uikit->get_less_directories( 'core' )
		);
		$this->assertSame(
			array_merge( [ BEANS_API_PATH . 'uikit/src/less/components' ], $themes ),
			$beans_uikit->get_less_directories( 'components' )
		);
	}
}
