<?php
/**
 * Tests for the render_nonce() method of Beans_Post_Meta.
 *
 * @package Beans\Framework\Tests\Unit\API\Post_Meta
 *
 * @since 1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Post_Meta;

use Beans\Framework\Tests\Unit\API\Post_Meta\Includes\Post_Meta_Test_Case;
use Beans_Post_Meta;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-post-meta-test-case.php';

/**
 * Class Tests_BeansPostMeta_RenderNonce
 *
 * @package Beans\Framework\Tests\Unit\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_BeansPostMeta_RenderNonce extends Post_Meta_Test_Case {

	/**
	 * Test Beans_Post_Meta::render_nonce() should echo correct nonce input HTML.
	 */
	public function test_should_echo_nonce_input_html() {
		Monkey\Functions\expect( 'wp_create_nonce' )->once()->with( 'beans_post_meta_nonce' )->andReturn( '123456' );
		$expected_html_output = '<input type="hidden" name="beans_post_meta_nonce" value="123456" />';

		$post_meta = new Beans_Post_Meta( 'tm-beans', [ 'title' => 'Post Options' ] );
		ob_start();
		$post_meta->render_nonce();
		$actual_output = ob_get_clean();

		$this->assertContains( $expected_html_output, $actual_output );
	}
}
