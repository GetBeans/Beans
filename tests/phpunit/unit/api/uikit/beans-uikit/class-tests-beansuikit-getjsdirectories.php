<?php
/**
 * Tests for the get_js_directories() method of _Beans_Uikit.
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
 * Class Tests_BeansUikit_GetJsDirectories
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 * @group   api
 * @group   api-uikit
 */
class Tests_BeansUikit_GetJsDirectories extends UIkit_Test_Case {

	/**
	 * Test _Beans_Uikit::get_js_directories() should return the path to the type's directory.
	 */
	public function test_should_return_path_to_type_directory() {
		$beans_uikit = new _Beans_Uikit();

		$this->assertSame(
			[ BEANS_API_PATH . 'uikit/src/js/core' ],
			$beans_uikit->get_js_directories( 'core' )
		);
		$this->assertSame(
			[ BEANS_API_PATH . 'uikit/src/js/components' ],
			$beans_uikit->get_js_directories( 'components' )
		);
	}

	/**
	 * Test _Beans_Uikit::get_js_directories() should return the path to the 'components' directory when the type is
	 * 'add-ons'.
	 */
	public function test_should_return_path_to_components_directory_when_type_is_add_ons() {
		$beans_uikit = new _Beans_Uikit();

		$this->assertSame(
			[ BEANS_API_PATH . 'uikit/src/js/components' ],
			$beans_uikit->get_js_directories( 'add-ons' )
		);
	}
}
