<?php
/**
 * Tests for _beans_get_image_alt()
 *
 * @package Beans\Framework\Tests\Integration\API\Fields\Types
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Fields\Types;

use Beans\Framework\Tests\Integration\API\Fields\Includes\Fields_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-fields-test-case.php';

/**
 * Class Tests_BeansGetImageAlt
 *
 * @package Beans\Framework\Tests\Integration\API\Fields\Types
 * @group   api
 * @group   api-fields
 */
class Tests_BeansGetImageAlt extends Fields_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
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
		$post_id  = self::factory()->post->create();
		$image_id = self::factory()->attachment->create_object(
			'image.png',
			$post_id,
			[
				'post_mime_type' => 'image/png',
				'post_type'      => 'attachment',
			]
		);

		// Run the test.
		$this->assertSame( 'Sorry, no description was given for this image.', _beans_get_image_alt( $image_id ) );
	}

	/**
	 * Test _beans_get_image_alt() should return the image's alt description.
	 */
	public function test_should_return_image_alt() {
		$post_id  = self::factory()->post->create();
		$image_id = self::factory()->attachment->create_object(
			'image.jpeg',
			$post_id,
			[
				'post_mime_type' => 'image/jpeg',
				'post_type'      => 'attachment',
			]
		);
		$alt      = 'This is the alt value.';
		update_post_meta( $image_id, '_wp_attachment_image_alt', $alt, true );

		// Run the test.
		$this->assertSame( $alt, _beans_get_image_alt( $image_id ) );
	}
}
