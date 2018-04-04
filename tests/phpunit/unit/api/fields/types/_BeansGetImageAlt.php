<?php
/**
 * Tests for _beans_get_image_alt()
 *
 * @package Beans\Framework\Tests\Unit\API\Fields\Types
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Fields\Types;

use Beans\Framework\Tests\Unit\API\Fields\Includes\Fields_Test_Case;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-fields-test-case.php';

/**
 * Class Tests_BeansGetImageAlt
 *
 * @package Beans\Framework\Tests\Unit\API\Fields\Types
 * @group   api
 * @group   api-fields
 */
class Tests_BeansGetImageAlt extends Fields_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		// Load the field type.
		require_once BEANS_THEME_DIR . '/lib/api/fields/types/image.php';
	}

	/**
	 * Test _beans_get_image_alt() should return null when an invalid image ID is given.
	 */
	public function test_should_return_null_when_invalid_image_id_given() {
		$this->assertNull( _beans_get_image_alt( 0 ) );
		$this->assertNull( _beans_get_image_alt( - 1 ) );
		$this->assertNull( _beans_get_image_alt( null ) );
		$this->assertNull( _beans_get_image_alt( false ) );
	}

	/**
	 * Test _beans_get_image_alt() should return the default when the image does not have an "alt" defined.
	 */
	public function test_should_return_default_alt_when_image_alt_not_defined() {
		Monkey\Functions\expect( 'get_post_meta' )
			->with( 1, '_wp_attachment_image_alt', true )
			->once()
			->andReturnNull();

		// Run the test.
		$this->assertSame( 'Sorry, no description was given for this image.', _beans_get_image_alt( 1 ) );
	}

	/**
	 * Test _beans_get_image_alt() should return the image's alt description.
	 */
	public function test_should_return_image_alt() {
		Monkey\Functions\expect( 'get_post_meta' )
			->with( 1, '_wp_attachment_image_alt', true )
			->once()
			->andReturn( 'This is the alt value.' );

		// Run the test.
		$this->assertSame( 'This is the alt value.', _beans_get_image_alt( 1 ) );
	}
}
