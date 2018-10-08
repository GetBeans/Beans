<?php
/**
 * Tests for the render_success_notice() method of Beans_Compiler_Options.
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Compiler;

use Beans_Compiler_Options;
use Beans\Framework\Tests\Integration\API\Compiler\Includes\Compiler_Options_Test_Case;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-compiler-options-test-case.php';

/**
 * Class Tests_BeansCompilerOptions_RenderSuccessNotice
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompilerOptions_RenderSuccessNotice extends Compiler_Options_Test_Case {

	/**
	 * Test Beans_Compiler_Options::render_success_notice() should not render when not flushing compiler cache.
	 */
	public function test_should_not_render_when_not_flushing_compiler_cache() {
		$this->go_to_settings_page();
		$this->assertArrayNotHasKey( 'beans_flush_compiler_cache', $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification -- No need for nonce in this test.

		ob_start();
		( new Beans_Compiler_Options() )->render_success_notice();

		$this->assertEmpty( ob_get_clean() );
	}

	/**
	 * Test Beans_Compiler_Options::render_success_notice() should render when flushing compiler cache.
	 */
	public function test_should_render_when_flushing_compiler_cache() {
		$this->go_to_settings_page();
		$_POST['beans_flush_compiler_cache'] = 1;

		ob_start();
		( new Beans_Compiler_Options() )->render_success_notice();
		$actual = ob_get_clean();

		$expected = <<<EOB
<div id="message" class="updated">
	<p>Cache flushed successfully!</p>
</div>
EOB;
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $actual ) );
	}
}
