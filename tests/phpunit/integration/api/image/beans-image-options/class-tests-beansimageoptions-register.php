<?php
/**
 * Tests for the register() method of _Beans_Image_Options.
 *
 * @package Beans\Framework\Test\Integration\API\Image
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Image;

use _Beans_Image_Options;
use Beans\Framework\Tests\Integration\API\Image\Includes\Options_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-options-test-case.php';

/**
 * Class Tests_BeansImageOptions_Register
 *
 * @package Beans\Framework\Tests\Integration\API\Image
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
	public function setUp() {
		parent::setUp();

		$this->fields = [
			[
				'id'          => 'beans_edited_images_directories',
				'type'        => 'flush_edited_images',
				'description' => 'Clear all edited images. New images will be created on page load.',
			],
		];

		require_once BEANS_THEME_DIR . '/lib/api/options/functions.php';
	}

	/**
	 * Test _Beans_Image_Options::register() should register the options with column context when other metaboxes are
	 * registered.
	 */
	public function test_should_register_options_with_column_context_when_other_metaboxes_are_registered() {
		$this->go_to_settings_page();

		global $wp_meta_boxes;
		$wp_meta_boxes = [ 'beans_settings' => [ 'foo' ] ]; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited -- Valid use case to set up the test.

		$this->assertTrue( ( new _Beans_Image_Options() )->register() );

		// Check that the right fields did get registered.
		$registered_fields = beans_get_fields( 'option', 'images_options' );
		$this->assertCount( 1, $registered_fields );
		$this->assertArraySubset(
			[
				'id'   => 'beans_edited_images_directories',
				'type' => 'flush_edited_images',
			],
			current( $registered_fields )
		);

		// Check that the metabox did get registered.
		global $wp_meta_boxes;
		$this->assertArrayHasKey( 'images_options', $wp_meta_boxes['beans_settings']['column']['default'] );
		$this->assertEquals( 'Images options', $wp_meta_boxes['beans_settings']['column']['default']['images_options']['title'] );

		// Clean up.
		unset( $wp_meta_boxes['beans_settings'] );
	}

	/**
	 * Test _Beans_Image_Options::register() should register the options with normal context when no metaboxes are
	 * registered.
	 */
	public function test_should_register_options_with_normal_context_when_no_metaboxes_are_registered() {
		$this->go_to_settings_page();

		$this->assertTrue( ( new _Beans_Image_Options() )->register() );

		// Check that the right fields did get registered.
		$registered_fields = beans_get_fields( 'option', 'images_options' );
		$this->assertCount( 1, $registered_fields );
		$this->assertArraySubset(
			[
				'id'   => 'beans_edited_images_directories',
				'type' => 'flush_edited_images',
			],
			current( $registered_fields )
		);

		// Check that the metabox did get registered.
		global $wp_meta_boxes;
		$this->assertArrayHasKey( 'images_options', $wp_meta_boxes['beans_settings']['normal']['default'] );
		$this->assertEquals( 'Images options', $wp_meta_boxes['beans_settings']['normal']['default']['images_options']['title'] );

		// Clean up.
		unset( $wp_meta_boxes['beans_settings'] );
	}
}
