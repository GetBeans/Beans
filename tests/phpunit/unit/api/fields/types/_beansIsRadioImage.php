<?php
/**
 * Tests for _beans_is_radio_image()
 *
 * @package Beans\Framework\Tests\Unit\API\Fields\Types
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Fields\Types;

use Beans\Framework\Tests\Unit\API\Fields\Includes\Fields_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-fields-test-case.php';

/**
 * Class Tests_BeansIsRadioImage
 *
 * @package Beans\Framework\Tests\Unit\API\Fields\Types
 * @group   api
 * @group   api-fields
 */
class Tests_BeansIsRadioImage extends Fields_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		// Load the field type.
		require_once BEANS_THEME_DIR . '/lib/api/fields/types/radio.php';
	}

	/**
	 * Test _beans_is_radio_image() should return true when an image source is given.
	 */
	public function test_should_return_true_when_image_src_given() {
		$test_data = array(
			'c'    => 'http://example.com/images/layouts/c.png',
			'c_sp' => 'http://example.com/images/layouts/c_sp.png',
			'sp_c' => 'http://example.com/images/layouts/sp_c.png',
		);

		foreach ( $test_data as $src ) {
			$this->assertTrue( _beans_is_radio_image( $src ) );
		}
	}

	/**
	 * Test _beans_is_radio_image() should false when a non-image source is given.
	 */
	public function test_should_return_false_when_non_image_src_given() {
		$test_data = array(
			__FILE__,
			'path/to/fields.js',
			BEANS_THEME_DIR . 'style.css',
			'http://example.com/path/to/some.pdf',
		);

		foreach ( $test_data as $src ) {
			$this->assertFalse( _beans_is_radio_image( $src ) );
		}
	}

	/**
	 * Test _beans_is_radio_image() should return true when an array is given, an array that configures the image.
	 */
	public function test_should_return_true_when_array_is_given() {
		$test_data = array(
			'c'    => array(
				'src'                => 'http://example.com/images/layouts/c.png',
				'screen_reader_text' => 'Content Only Layout',
			),
			'c_sp' => array(
				'src'                => 'http://example.com/images/layouts/c_sp.png',
				'alt'                => 'Content + Sidebar Primary Layout',
				'screen_reader_text' => 'Option for the Content + Sidebar Primary Layout',
			),
			'sp_c' => array(
				'src' => 'http://example.com/images/layouts/c_sp.png',
				'alt' => 'Sidebar Primary + Content Layout',
			),
		);

		foreach ( $test_data as $radio ) {
			$this->assertTrue( _beans_is_radio_image( $radio ) );
		}
	}
}
