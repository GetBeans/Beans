<?php
/**
 * Tests for the render_nonce() method of _Beans_Term_Meta.
 *
 * @package Beans\Framework\Tests\Unit\API\Term_Meta
 *
 * @since 1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Term_Meta;

use Beans\Framework\Tests\Unit\API\Term_Meta\Includes\Term_Meta_Test_Case;
use _Beans_Term_Meta;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-term-meta-test-case.php';

/**
 * Class Tests_BeansTermMeta_RenderNonce
 *
 * @package Beans\Framework\Tests\Unit\API\Term_Meta
 * @group   api
 * @group   api-term-meta
 */
class Tests_BeansTermMeta_RenderNonce extends Term_Meta_Test_Case {

	/**
	 * Test _Beans_Term_Meta::render_nonce() should render the nonce HTML.
	 */
	public function test_should_render_nonce_html() {
		Monkey\Functions\expect( 'wp_create_nonce' )
			->once()
			->with( 'beans_term_meta_nonce' )
			->andReturn( '123456' );
		$expected_html_output = '<input type="hidden" name="beans_term_meta_nonce" value="123456" />';

		$term_meta = new _Beans_Term_Meta( 'tm-beans' );
		ob_start();
		$term_meta->render_nonce();
		$actual_output = ob_get_clean();

		$this->assertContains( $expected_html_output, $actual_output );
	}
}
