<?php
/**
 * Tests for the render_page() method of _Beans_Options.
 *
 * @package Beans\Framework\Tests\Unit\API\Options
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Options;

use _Beans_Options;
use Beans\Framework\Tests\Unit\API\Options\Includes\Options_Test_Case;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-options-test-case.php';

/**
 * Class Tests_BeansOptions_RenderPage
 *
 * @package Beans\Framework\Tests\Unit\API\Options
 * @group   api
 * @group   api-options
 */
class Tests_BeansOptions_RenderPage extends Options_Test_Case {

	/**
	 * Test _Beans_Options::render_page() should return null when the page does not have a metabox.
	 */
	public function test_should_return_null_when_page_does_not_have_metabox() {
		Monkey\Functions\expect( 'beans_get' )->with( 'beans_tests', [] )->once()->andReturn( null );

		$this->assertNull( ( new _Beans_Options() )->render_page( 'beans_tests' ) );
	}

	/**
	 * Test _Beans_Options::render_page() should render the form when "normal" context is configured.
	 */
	public function test_should_render_form_when_context_normal() {
		Monkey\Functions\expect( 'beans_get' )
			->with( 'beans_tests', [] )
			->once()
			->andReturn( [ 'column' => '' ] )
			->andAlsoExpectIt()
			->with( 'column', [ 'column' => '' ], [] )
			->once()
			->andReturn( [] )
			->andAlsoExpectIt()
			->with( 'page' )
			->once()
			->andReturn( 'beans-test' );
		Monkey\Functions\expect( 'do_meta_boxes' )->with( 'beans_tests', 'normal', null )->once()->andReturnNull();
		Monkey\Functions\expect( 'wp_nonce_field' )->twice()->andReturnNull();
		Monkey\Functions\expect( 'wp_create_nonce' )->once()->andReturn( 'foo' );

		// Run the method and grab the HTML out of the buffer.
		ob_start();
		( new _Beans_Options() )->render_page( 'beans_tests' );
		$html = ob_get_clean();

		$expected = <<<EOB
<form action="" method="post" class="bs-options" data-page="beans-test">
	<input type="hidden" name="beans_options_nonce" value="foo" />
	<div class="metabox-holder"></div>
	<p class="bs-options-form-actions">
		<input type="submit" name="beans_save_options" value="Save" class="button-primary">
		<input type="submit" name="beans_reset_options" value="Reset" class="button-secondary">
	</p>
</form>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}

	/**
	 * Test _Beans_Options::render_page() should render the form when "column" context is configured.
	 */
	public function test_should_render_form_when_column_context() {
		Monkey\Functions\expect( 'beans_get' )
			->with( 'beans_tests', [] )
			->once()
			->andReturn( [ 'column' => true ] )
			->andAlsoExpectIt()
			->with( 'column', [ 'column' => true ], [] )
			->once()
			->andReturn( true )
			->andAlsoExpectIt()
			->with( 'page' )
			->once()
			->andReturn( 'beans-test' );
		Monkey\Functions\expect( 'do_meta_boxes' )
			->with( 'beans_tests', 'normal', null )
			->once()
			->andReturnNull()
			->andAlsoExpectIt()
			->with( 'beans_tests', 'column', null )
			->once()
			->andReturnNull();
		Monkey\Functions\expect( 'wp_nonce_field' )->twice()->andReturnNull();
		Monkey\Functions\expect( 'wp_create_nonce' )->once()->andReturn( 'foo' );

		// Run the method and grab the HTML out of the buffer.
		ob_start();
		( new _Beans_Options() )->render_page( 'beans_tests' );
		$html = ob_get_clean();

		$expected = <<<EOB
<form action="" method="post" class="bs-options" data-page="beans-test">
	<input type="hidden" name="beans_options_nonce" value="foo" />
	<div class="metabox-holder column"></div>
	<p class="bs-options-form-actions">
		<input type="submit" name="beans_save_options" value="Save" class="button-primary">
		<input type="submit" name="beans_reset_options" value="Reset" class="button-secondary">
	</p>
</form>
EOB;
		// Run the test.
		$this->assertSame( $this->format_the_html( $expected ), $this->format_the_html( $html ) );
	}
}
