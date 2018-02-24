<?php
/**
 * Tests for beans_field_image()
 *
 * @package Beans\Framework\Tests\Integration\API\Fields\Types
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Fields\Types;

use Beans\Framework\Tests\Integration\API\Fields\Includes\Fields_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-fields-test-case.php';

/**
 * Class Tests_BeansFieldImage
 *
 * @package Beans\Framework\Tests\Integration\API\Fields\Types
 * @group   integration-tests
 * @group   api
 */
class Tests_BeansFieldImage extends Fields_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		// Load the field type.
		require_once BEANS_THEME_DIR . '/lib/api/fields/types/image.php';
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {
		parent::setUp();

		beans_remove_action( 'beans_field_enqueue_scripts_image', 'beans_field_enqueue_scripts_image' );
		beans_remove_action( 'beans_field_image_assets', 'beans_field_image_assets' );
	}

	/**
	 * Test beans_field_image() should render a single image field.
	 */
	public function test_should_render_single_image_field() {
		$post_id  = self::factory()->post->create();
		$image_id = self::factory()->attachment->create_object( 'image.png', $post_id, array(
			'post_mime_type' => 'image/jpeg',
			'post_type'      => 'attachment',
		) );
		update_post_meta( $image_id, '_wp_attachment_image_alt', 'This is the alt value.', true );

		$field = $this->merge_field_with_default( array(
			'id'    => 'beans_image_test',
			'type'  => 'image',
			'label' => 'Image Test',
			'value' => $image_id,
		), false );

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field_image( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<button class="bs-add-image button button-small" type="button" style="display: none">Add Image</button>
<input id="beans_image_test" type="hidden" name="beans_fields[beans_image_test]" value="">
<div class="bs-images-wrap" data-multiple="">
    <div class="bs-image-wrap">
        <input class="image-id" type="hidden" name="beans_fields[beans_image_test]" value="1" />
        <img src="http://example.org/wp-content/uploads/image.png" alt="This is the alt value.">
        <div class="bs-toolbar">
            <button aria-label="Edit Image" type="button" class="button bs-button-edit dashicons dashicons-edit"></button>
            <button aria-label="Delete Image" type="button" class="button bs-button-trash dashicons dashicons-post-trash"></button>
        </div>
    </div>
    <div class="bs-image-wrap bs-image-template">
        <input class="image-id" type="hidden" name="beans_fields[beans_image_test]" value="" disabled="disabled" />
        <img src="" alt="">
        <div class="bs-toolbar">
            <button aria-label="Edit Image" type="button" class="button bs-button-edit dashicons dashicons-edit"></button>
            <button aria-label="Delete Image" type="button" class="button bs-button-trash dashicons dashicons-post-trash"></button>
        </div>
    </div>
</div>
EOB;
		$expected = str_replace( 'value="1"', 'value="' . $image_id . '"', $expected );

		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}

	/**
	 * Test beans_field_image() should render multiple images field.
	 */
	public function test_should_render_multiple_images_field() {
		$images   = array();
		$post_id  = self::factory()->post->create();
		$images[] = self::factory()->attachment->create_object( 'image-1.png', $post_id, array(
			'post_mime_type' => 'image/jpeg',
			'post_type'      => 'attachment',
		) );
		update_post_meta( $images[0], '_wp_attachment_image_alt', 'Image 1 alt.', true );
		$images[] = self::factory()->attachment->create_object( 'image-2.png', $post_id, array(
			'post_mime_type' => 'image/jpeg',
			'post_type'      => 'attachment',
		) );
		update_post_meta( $images[1], '_wp_attachment_image_alt', 'Image 2 alt.', true );

		$field = $this->merge_field_with_default( array(
			'id'       => 'beans_image_test',
			'type'     => 'image',
			'label'    => 'Image Test',
			'value'    => $images,
			'multiple' => true,
		), false );

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field_image( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<button class="bs-add-image button button-small" type="button" style="display: none">Add Images</button>
<input id="beans_image_test" type="hidden" name="beans_fields[beans_image_test]" value="">
<div class="bs-images-wrap" data-multiple="1">
    <div class="bs-image-wrap">
        <input class="image-id" type="hidden" name="beans_fields[beans_image_test][]" value="1" />
        <img src="http://example.org/wp-content/uploads/image-1.png" alt="Image 1 alt.">
        <div class="bs-toolbar">
            <button aria-label="Manage Images" type="button" class="button bs-button-menu dashicons dashicons-menu"></button>
        	<button aria-label="Edit Image" type="button" class="button bs-button-edit dashicons dashicons-edit"></button>
            <button aria-label="Delete Image" type="button" class="button bs-button-trash dashicons dashicons-post-trash"></button>
        </div>
    </div>
    <div class="bs-image-wrap">
        <input class="image-id" type="hidden" name="beans_fields[beans_image_test][]" value="2" />
        <img src="http://example.org/wp-content/uploads/image-2.png" alt="Image 2 alt.">
        <div class="bs-toolbar">
            <button aria-label="Manage Images" type="button" class="button bs-button-menu dashicons dashicons-menu"></button>
        	<button aria-label="Edit Image" type="button" class="button bs-button-edit dashicons dashicons-edit"></button>
            <button aria-label="Delete Image" type="button" class="button bs-button-trash dashicons dashicons-post-trash"></button>
        </div>
    </div>
    <div class="bs-image-wrap bs-image-template">
        <input class="image-id" type="hidden" name="beans_fields[beans_image_test][]" value="" disabled="disabled" />
        <img src="" alt="">
        <div class="bs-toolbar">
            <button aria-label="Manage Images" type="button" class="button bs-button-menu dashicons dashicons-menu"></button>
        	<button aria-label="Edit Image" type="button" class="button bs-button-edit dashicons dashicons-edit"></button>
            <button aria-label="Delete Image" type="button" class="button bs-button-trash dashicons dashicons-post-trash"></button>
        </div>
    </div>
</div>
EOB;
		$expected = str_replace( 'value="1"', 'value="' . $images[0] . '"', $expected );
		$expected = str_replace( 'value="2"', 'value="' . $images[1] . '"', $expected );

		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}

	/**
	 * Test beans_field_image() should render a single image field with the default alt when none exists.
	 */
	public function test_should_render_single_image_field_with_default_alt_when_none_exists() {
		$post_id  = self::factory()->post->create();
		$image_id = self::factory()->attachment->create_object( 'image.png', $post_id, array(
			'post_mime_type' => 'image/jpeg',
			'post_type'      => 'attachment',
		) );

		$field = $this->merge_field_with_default( array(
			'id'    => 'beans_image_test',
			'type'  => 'image',
			'label' => 'Image Test',
			'value' => $image_id,
		), false );

		// Run the function and grab the HTML out of the buffer.
		ob_start();
		beans_field_image( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<button class="bs-add-image button button-small" type="button" style="display: none">Add Image</button>
<input id="beans_image_test" type="hidden" name="beans_fields[beans_image_test]" value="">
<div class="bs-images-wrap" data-multiple="">
    <div class="bs-image-wrap">
        <input class="image-id" type="hidden" name="beans_fields[beans_image_test]" value="1" />
        <img src="http://example.org/wp-content/uploads/image.png" alt="Sorry, no alt was given for this image.">
        <div class="bs-toolbar">
            <button aria-label="Edit Image" type="button" class="button bs-button-edit dashicons dashicons-edit"></button>
            <button aria-label="Delete Image" type="button" class="button bs-button-trash dashicons dashicons-post-trash"></button>
        </div>
    </div>
    <div class="bs-image-wrap bs-image-template">
        <input class="image-id" type="hidden" name="beans_fields[beans_image_test]" value="" disabled="disabled" />
        <img src="" alt="">
        <div class="bs-toolbar">
            <button aria-label="Edit Image" type="button" class="button bs-button-edit dashicons dashicons-edit"></button>
            <button aria-label="Delete Image" type="button" class="button bs-button-trash dashicons dashicons-post-trash"></button>
        </div>
    </div>
</div>
EOB;
		$expected = str_replace( 'value="1"', 'value="' . $image_id . '"', $expected );

		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}
}
