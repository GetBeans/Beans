<?php
/**
 * Tests for beans_field_image_assets()
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
 * @package Beans\Framework\Tests\Unit\API\Fields
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
	 * Test beans_field_image_assets() should render a single image field.
	 */
	public function test_should_render_single_image_field() {
		Monkey\Functions\expect( 'wp_get_attachment_image_src' )->with( 1, 'thumbnail' )->once()
			->andReturn( 'image.png' );

		$field = $this->merge_field_with_default( array(
			'id'    => 'beans_image_test',
			'type'  => 'image',
			'label' => 'Image Test',
			'value' => 1, // attachment ID.
		), false );

		ob_start();
		beans_field_image( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<a href="#" class="bs-add-image button button-small" style="display: none">Add Image</a>
<input type="hidden" name="beans_fields[beans_image_test]" value="">
<div class="bs-images-wrap" data-multiple="">
    <div class="bs-image-wrap">
        <input class="image-id" type="hidden" name="beans_fields[beans_image_test]" value="1" />
        <img src="image.png">
        <div class="bs-toolbar">
            <a href="#" class="dashicons dashicons-edit"></a>
            <a href="#" class="dashicons dashicons-post-trash"></a>
        </div>
    </div>
    <div class="bs-image-wrap bs-image-template">
        <input class="image-id" type="hidden" name="beans_fields[beans_image_test]" value="" disabled="disabled" />
        <img src="">
        <div class="bs-toolbar">
            <a href="#" class="dashicons dashicons-edit"></a>
            <a href="#" class="dashicons dashicons-post-trash"></a>
        </div>
    </div>
</div>
EOB;

		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}

	/**
	 * Test beans_field_image_assets() should render multiple images field.
	 */
	public function test_should_render_multiple_images_field() {
		Monkey\Functions\expect( 'wp_get_attachment_image_src' )
			->times( 3 )
			->andReturnUsing( function( $image_id ) {
				if ( 'placeholder' === $image_id ) {
					return '';
				}
				return "image-{$image_id}.png";
			} );

		$field = $this->merge_field_with_default( array(
			'id'       => 'beans_image_test',
			'type'     => 'image',
			'label'    => 'Image Test',
			'value'    => array( 1, 2 ), // attachment IDs.
			'multiple' => true,
		), false );

		ob_start();
		beans_field_image( $field );
		$html = ob_get_clean();

		$expected = <<<EOB
<a href="#" class="bs-add-image button button-small" style="display: none">Add Images</a>
<input type="hidden" name="beans_fields[beans_image_test]" value="">
<div class="bs-images-wrap" data-multiple="1">
    <div class="bs-image-wrap">
        <input class="image-id" type="hidden" name="beans_fields[beans_image_test][]" value="1" />
        <img src="image-1.png">
        <div class="bs-toolbar">
            <a href="#" class="dashicons dashicons-menu"></a>
            <a href="#" class="dashicons dashicons-edit"></a>
            <a href="#" class="dashicons dashicons-post-trash"></a>
        </div>
    </div>
    <div class="bs-image-wrap">
        <input class="image-id" type="hidden" name="beans_fields[beans_image_test][]" value="2" />
        <img src="image-2.png">
        <div class="bs-toolbar">
            <a href="#" class="dashicons dashicons-menu"></a>
            <a href="#" class="dashicons dashicons-edit"></a>
            <a href="#" class="dashicons dashicons-post-trash"></a>
        </div>
    </div>
    <div class="bs-image-wrap bs-image-template">
        <input class="image-id" type="hidden" name="beans_fields[beans_image_test][]" value="" disabled="disabled" />
        <img src="">
        <div class="bs-toolbar">
            <a href="#" class="dashicons dashicons-menu"></a>
            <a href="#" class="dashicons dashicons-edit"></a>
            <a href="#" class="dashicons dashicons-post-trash"></a>
        </div>
    </div>
</div>
EOB;

		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}
}
