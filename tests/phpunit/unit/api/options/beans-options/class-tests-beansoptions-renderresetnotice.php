<?php
/**
 * Tests for the render_reset_notice() method of Beans_Options.
 *
 * @package Beans\Framework\Tests\Unit\API\Options
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Options;

use Beans_Options;
use Beans\Framework\Tests\Unit\API\Options\Includes\Options_Test_Case;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-options-test-case.php';

/**
 * Class Tests_BeansOptions_RenderResetNotice
 *
 * @package Beans\Framework\Tests\Unit\API\Options
 * @group   api
 * @group   api-options
 */
class Tests_BeansOptions_RenderResetNotice extends Options_Test_Case {

	/**
	 * Test Beans_Options::render_reset_notice() should render the error message when the "success" property is not set.
	 */
	public function test_should_render_error_message_when_success_not_set() {
		// Run the method and grab the HTML out of the buffer.
		ob_start();
		( new Beans_Options() )->render_reset_notice();
		$html = ob_get_clean();

		$expected = <<<EOB
<div id="message" class="error">
	<p>Settings could not be reset, please try again.</p>
</div>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}

	/**
	 * Test Beans_Options::render_reset_notice() should render the updated message when the "success" property is set.
	 */
	public function test_should_render_updated_message_when_success_is_set() {
		$property = $this->get_reflective_property( 'success', 'Beans_Options' );
		$instance = new Beans_Options();
		$property->setValue( $instance, true );

		// Run the method and grab the HTML out of the buffer.
		ob_start();
		$instance->render_reset_notice();
		$html = ob_get_clean();

		$expected = <<<EOB
<div id="message" class="updated">
	<p>Settings reset successfully!</p>
</div>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}
}
