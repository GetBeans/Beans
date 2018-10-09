<?php
/**
 * Tests for the render_flush_button() method of Beans_Image_Options.
 *
 * @package Beans\Framework\Tests\Unit\API\Image
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Image;

use Beans_Image_Options;
use Beans\Framework\Tests\Unit\API\Image\Includes\Options_Test_Case;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-options-test-case.php';

/**
 * Class Tests_BeansImageOptions_RenderFlushButton
 *
 * @package Beans\Framework\Tests\Unit\API\Image
 * @group   api
 * @group   api-image
 */
class Tests_BeansImageOptions_RenderFlushButton extends Options_Test_Case {

	/**
	 * Test Beans_Image_Options::render_flush_button() should not render when the field is not for image options.
	 */
	public function test_should_not_render_when_field_is_not_image_options() {
		Monkey\Functions\expect( 'esc_html_e' )->never();

		ob_start();
		( new Beans_Image_Options() )->render_flush_button( [ 'id' => 'foo' ] );
		$this->assertEmpty( ob_get_clean() );
	}

	/**
	 * Test Beans_Image_Options::render_flush_button() should render when the field is for image options.
	 */
	public function test_should_render_when_field_is_image_options() {
		ob_start();
		( new Beans_Image_Options() )->render_flush_button( [ 'id' => 'beans_edited_images_directories' ] );
		$actual = ob_get_clean();

		$expected = <<<EOB
<input type="submit" name="beans_flush_edited_images" value="Flush images" class="button-secondary" />
EOB;
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $actual ) );
	}
}
