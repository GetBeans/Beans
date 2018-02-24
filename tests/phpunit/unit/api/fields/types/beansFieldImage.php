<?php
/**
 * Tests for beans_field_image()
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
 * Class Tests_BeansFieldImage
 *
 * @package Beans\Framework\Tests\Unit\API\Fields\Types
 * @group   unit-tests
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
		Monkey\Functions\expect( 'wp_get_attachment_image_src' )
			->with( 1, 'thumbnail' )
			->once()
			->andReturn( 'image.png' );
		Monkey\Functions\expect( 'get_post_meta' )
			->once()
			->andReturn( 'This is the image alt value.' );

		$field = $this->merge_field_with_default( array(
			'id'    => 'beans_image_test',
			'type'  => 'image',
			'label' => 'Image Test',
			'value' => 1, // attachment ID.
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
        <img src="image.png" alt="This is the image alt value.">
        <div class="bs-toolbar">
        	<button aria-label="Edit Image" type="button" class="button bs-button-edit dashicons dashicons-edit"></button>
            <button aria-label="Delete Image" type="button" class="button bs-button-trash dashicons dashicons-trash"></button>
        </div>
    </div>
    <div class="bs-image-wrap bs-image-template">
        <input class="image-id" type="hidden" name="beans_fields[beans_image_test]" value="" disabled="disabled" />
        <img src="" alt="">
        <div class="bs-toolbar">
        	<button aria-label="Edit Image" type="button" class="button bs-button-edit dashicons dashicons-edit"></button>
            <button aria-label="Delete Image" type="button" class="button bs-button-trash dashicons dashicons-trash"></button>
        </div>
    </div>
</div>
EOB;

		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}

	/**
	 * Test beans_field_image() should render a multiple images field.
	 */
	public function test_should_render_multiple_images_field() {
		Monkey\Functions\expect( 'wp_get_attachment_image_src' )
			->times( 2 )
			->andReturnUsing( function( $image_id ) {
				if ( 'placeholder' === $image_id ) {
					return '';
				}
				return "image-{$image_id}.png";
			} );
		Monkey\Functions\expect( 'get_post_meta' )
			->times( 2 )
			->andReturn( 'This is the image alt value.' );

		$field = $this->merge_field_with_default( array(
			'id'       => 'beans_image_test',
			'type'     => 'image',
			'label'    => 'Image Test',
			'value'    => array( 1, 2 ), // attachment IDs.
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
        <img src="image-1.png" alt="This is the image alt value.">
        <div class="bs-toolbar">
            <button aria-label="Manage Images" type="button" class="button bs-button-menu dashicons dashicons-menu"></button>
        	<button aria-label="Edit Image" type="button" class="button bs-button-edit dashicons dashicons-edit"></button>
            <button aria-label="Delete Image" type="button" class="button bs-button-trash dashicons dashicons-trash"></button>
        </div>
    </div>
    <div class="bs-image-wrap">
        <input class="image-id" type="hidden" name="beans_fields[beans_image_test][]" value="2" />
        <img src="image-2.png" alt="This is the image alt value.">
        <div class="bs-toolbar">
            <button aria-label="Manage Images" type="button" class="button bs-button-menu dashicons dashicons-menu"></button>
        	<button aria-label="Edit Image" type="button" class="button bs-button-edit dashicons dashicons-edit"></button>
            <button aria-label="Delete Image" type="button" class="button bs-button-trash dashicons dashicons-trash"></button>
        </div>
    </div>
    <div class="bs-image-wrap bs-image-template">
        <input class="image-id" type="hidden" name="beans_fields[beans_image_test][]" value="" disabled="disabled" />
        <img src="" alt="">
        <div class="bs-toolbar">
            <button aria-label="Manage Images" type="button" class="button bs-button-menu dashicons dashicons-menu"></button>
        	<button aria-label="Edit Image" type="button" class="button bs-button-edit dashicons dashicons-edit"></button>
            <button aria-label="Delete Image" type="button" class="button bs-button-trash dashicons dashicons-trash"></button>
        </div>
    </div>
</div>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}

	/**
	 * Test beans_field_image() should render a single image field with the default alt when none exists.
	 */
	public function test_should_render_single_image_field_with_default_alt_when_none_exists() {
		Monkey\Functions\expect( 'wp_get_attachment_image_src' )
			->with( 1, 'thumbnail' )
			->once()
			->andReturn( 'image.png' );
		Monkey\Functions\expect( 'get_post_meta' )
			->once()
			->andReturn( '' );

		$field = $this->merge_field_with_default( array(
			'id'    => 'beans_image_test',
			'type'  => 'image',
			'label' => 'Image Test',
			'value' => 1, // attachment ID.
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
        <img src="image.png" alt="Sorry, no description was given for this image.">
        <div class="bs-toolbar">
        	<button aria-label="Edit Image" type="button" class="button bs-button-edit dashicons dashicons-edit"></button>
            <button aria-label="Delete Image" type="button" class="button bs-button-trash dashicons dashicons-trash"></button>
        </div>
    </div>
    <div class="bs-image-wrap bs-image-template">
        <input class="image-id" type="hidden" name="beans_fields[beans_image_test]" value="" disabled="disabled" />
        <img src="" alt="">
        <div class="bs-toolbar">
        	<button aria-label="Edit Image" type="button" class="button bs-button-edit dashicons dashicons-edit"></button>
            <button aria-label="Delete Image" type="button" class="button bs-button-trash dashicons dashicons-trash"></button>
        </div>
    </div>
</div>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}
}
