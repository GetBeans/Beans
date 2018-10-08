<?php
/**
 * Tests for the register() method of _Beans_Image_Options.
 *
 * @package Beans\Framework\Tests\Unit\API\Image
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Image;

use _Beans_Image_Options;
use Beans\Framework\Tests\Unit\API\Image\Includes\Options_Test_Case;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-options-test-case.php';

/**
 * Class Tests_BeansImageOptions_Register
 *
 * @package Beans\Framework\Tests\Unit\API\Image
 * @group   api
 * @group   api-image
 */
class Tests_BeansImageOptions_Register extends Options_Test_Case {

	/**
	 * Array of fields.
	 *
	 * @var array
	 */
	protected $fields = [];

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->fields = require BEANS_THEME_DIR . '/lib/api/image/config/fields.php';
	}

	/**
	 * Test _Beans_Image_Options::register() should register the options with column context when other metaboxes are
	 * registered.
	 */
	public function test_should_register_options_with_column_context_when_other_metaboxes_are_registered() {
		global $wp_meta_boxes;
		Monkey\Functions\expect( 'beans_get' )
			->once()
			->with( 'beans_settings', $wp_meta_boxes )
			->andReturn( [ 'foo' ] );
		Monkey\Functions\expect( 'beans_register_options' )
			->once()
			->with(
				$this->fields,
				'beans_settings',
				'images_options',
				[
					'title'   => 'Images options',
					'context' => 'column',
				]
			)
			->andReturn( true );

		$this->assertTrue( ( new _Beans_Image_Options() )->register() );
	}

	/**
	 * Test _Beans_Image_Options::register() should register the options with normal context when no metaboxes are
	 * registered.
	 */
	public function test_should_register_options_with_normal_context_when_no_metaboxes_are_registered() {
		global $wp_meta_boxes;
		Monkey\Functions\expect( 'beans_get' )
			->once()
			->with( 'beans_settings', $wp_meta_boxes )
			->andReturn( [] );
		Monkey\Functions\expect( 'beans_register_options' )
			->once()
			->with(
				$this->fields,
				'beans_settings',
				'images_options',
				[
					'title'   => 'Images options',
					'context' => 'normal',
				]
			)
			->andReturn( true );

		$this->assertTrue( ( new _Beans_Image_Options() )->register() );
	}
}
