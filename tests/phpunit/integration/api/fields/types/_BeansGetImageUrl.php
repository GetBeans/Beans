<?php
/**
 * Tests for _beans_get_image_url()
 *
 * @package Beans\Framework\Tests\Integration\API\Fields\Types
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Fields\Types;

use Beans\Framework\Tests\Integration\API\Fields\Includes\Fields_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-fields-test-case.php';

/**
 * Class Tests_BeansGetImageUrl
 *
 * @package Beans\Framework\Tests\Integration\API\Fields\Types
 * @group   integration-tests
 * @group   api
 */
class Tests_BeansGetImageUrl extends Fields_Test_Case {

	/**
	 * Prepares the test environment before loading the tests.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		// Load the field type.
		require_once BEANS_THEME_DIR . '/lib/api/fields/types/image.php';
	}

	/**
	 * Test _beans_get_image_url() should return null when an invalid image ID is given.
	 */
	public function test_should_return_null_when_invalid_image_id_given() {
		$this->assertNull( _beans_get_image_url( 0 ) );
		$this->assertNull( _beans_get_image_url( -1 ) );
		$this->assertNull( _beans_get_image_url( null ) );
		$this->assertNull( _beans_get_image_url( false ) );
	}

	/**
	 * Test _beans_get_image_url() should return null when the image does not exist.
	 */
	public function test_should_return_null_when_image_does_not_exist() {
		$this->assertNull( _beans_get_image_url( 9999999 ) );
	}

	/**
	 * Test _beans_get_image_url() should return image's URL.
	 */
	public function test_should_return_image_url() {
		$post_id  = self::factory()->post->create();
		$image_id = self::factory()->attachment->create_object( 'image.jpeg', $post_id, array(
			'post_mime_type' => 'image/jpeg',
			'post_type'      => 'attachment',
		) );

		// Run the test.
		$this->assertSame( 'http://example.org/wp-content/uploads/image.jpeg', _beans_get_image_url( $image_id ) );
	}
}
