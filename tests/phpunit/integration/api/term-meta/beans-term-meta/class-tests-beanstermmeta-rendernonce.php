<?php
/**
 * Tests for the render_nonce() method of Beans_Term_Meta.
 *
 * @package Beans\Framework\Tests\Integration\API\Term_Meta
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Term_Meta;

use Beans\Framework\Tests\Integration\API\Term_Meta\Includes\Term_Meta_Test_Case;
use Beans_Term_Meta;

require_once BEANS_THEME_DIR . '/lib/api/term-meta/class-beans-term-meta.php';
require_once dirname( __DIR__ ) . '/includes/class-term-meta-test-case.php';

/**
 * Class Tests_BeanTermMeta_RenderNonce
 *
 * @package Beans\Framework\Tests\Integration\API\Term_Meta
 * @group   api
 * @group   api-term-meta
 */
class Tests_BeansTermMeta_RenderNonce extends Term_Meta_Test_Case {

	/**
	 * Tests Beans_Term_Meta::render_nonce() should render nonce HTML.
	 */
	public function test_should_render_nonce_html() {
		$term_meta = new Beans_Term_Meta( 'tm-beans' );

		$expected_html_output = '<input type="hidden" name="beans_term_meta_nonce" value="%x" />';

		ob_start();
		$term_meta->render_nonce();
		$actual_output = ob_get_clean();

		$this->assertStringMatchesFormat( $expected_html_output, $actual_output );
	}
}
