<?php
/**
 * Tests for the render_success_notice() method of Beans_Image_Options.
 *
 * @package Beans\Framework\Tests\Integration\API\Image
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Image;

use Beans_Image_Options;
use Beans\Framework\Tests\Integration\API\Image\Includes\Options_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-options-test-case.php';

/**
 * Class Tests_BeansImageOptions_RenderSuccessNotice
 *
 * @package Beans\Framework\Tests\Integration\API\Image
 * @group   api
 * @group   api-image
 */
class Tests_BeansImageOptions_RenderSuccessNotice extends Options_Test_Case {

	/**
	 * Test Beans_Image_Options::render_success_notice() should not render when not flushing edited images cache.
	 */
	public function test_should_not_render_when_not_flushing_edited_images_cache() {
		$this->go_to_settings_page();
		$this->assertArrayNotHasKey( 'beans_flush_edited_images', $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification -- No need for nonce in this test.

		ob_start();
		( new Beans_Image_Options() )->render_success_notice();
		$this->assertEmpty( ob_get_clean() );
	}

	/**
	 * Test Beans_Image_Options::render_success_notice() should render when flushing edited images cache.
	 */
	public function test_should_render_when_flushing_edited_images_cache() {
		$this->go_to_settings_page();
		$_POST['beans_flush_edited_images'] = 1;

		ob_start();
		( new Beans_Image_Options() )->render_success_notice();
		$actual = ob_get_clean();

		$expected = <<<EOB
<div id="message" class="updated">
	<p>Images flushed successfully!</p>
</div>
EOB;
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $actual ) );

		// Clean up.
		unset( $_POST['beans_flush_edited_images'] );
	}
}
