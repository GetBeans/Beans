<?php
/**
 * Tests for the to_filename() method of _Beans_Uikit.
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
 * Class Tests_BeansUikit_ToFilename
 *
 * @package Beans\Framework\Tests\Unit\API\UIkit
 * @group   api
 * @group   api-uikit
 */
class Tests_BeansUikit_ToFilename extends UIkit_Test_Case {

	/**
	 * Test _Beans_Uikit::to_filename() should return null when given an ignored component.
	 */
	public function test_should_return_null_when_given_ignored_component() {
		$beans_uikit = new _Beans_Uikit();

		$this->assertNull( $beans_uikit->to_filename( 'uikit-customizer.less' ) );
		$this->assertNull( $beans_uikit->to_filename( BEANS_API_PATH . 'uikit/src/less/themes/default/uikit.less' ) );
	}

	/**
	 * Test _Beans_Uikit::to_filename() should remove the .min extension from the filename.
	 */
	public function test_should_remove_min_extension_from_filename() {
		$beans_uikit = new _Beans_Uikit();

		$this->assertSame(
			'parallax',
			$beans_uikit->to_filename( BEANS_API_PATH . 'uikit/src/js/component/parallax.min.js' )
		);
		$this->assertSame(
			'tooltip',
			$beans_uikit->to_filename( BEANS_API_PATH . 'uikit/src/js/component/tooltip.min.js' )
		);
	}

	/**
	 * Test _Beans_Uikit::to_filename() should return the component's filename when given absolute path.
	 */
	public function test_should_return_component_filename_when_given_absolute_path() {
		$beans_uikit = new _Beans_Uikit();

		$this->assertSame(
			'accordion',
			$beans_uikit->to_filename( BEANS_API_PATH . 'uikit/src/less/component/accordion.less' )
		);
		$this->assertSame(
			'accordion',
			$beans_uikit->to_filename( BEANS_API_PATH . 'uikit/src/js/component/accordion.min.js' )
		);
		$this->assertSame(
			'sortable',
			$beans_uikit->to_filename( BEANS_API_PATH . 'uikit/src/less/component/sortable.less' )
		);
		$this->assertSame(
			'sortable',
			$beans_uikit->to_filename( BEANS_API_PATH . 'uikit/src/js/component/sortable.min.js' )
		);
	}

	/**
	 * Test _Beans_Uikit::to_filename() should return the component's filename when given relative path.
	 */
	public function test_should_return_component_filename_when_given_relative_path() {
		$beans_uikit = new _Beans_Uikit();

		$this->assertSame(
			'accordion',
			$beans_uikit->to_filename( 'less/component/accordion.less' )
		);
		$this->assertSame(
			'accordion',
			$beans_uikit->to_filename( 'js/component/accordion.min.js' )
		);
		$this->assertSame(
			'sortable',
			$beans_uikit->to_filename( 'sortable.less' )
		);
		$this->assertSame(
			'sortable',
			$beans_uikit->to_filename( 'sortable.min.js' )
		);
	}
}
