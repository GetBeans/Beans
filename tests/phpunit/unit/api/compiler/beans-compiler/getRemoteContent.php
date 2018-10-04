<?php
/**
 * Tests for the get_remote_content() method of _Beans_Compiler.
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler;

use Beans\Framework\Tests\Unit\API\Compiler\Includes\Compiler_Test_Case;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-compiler-test-case.php';

/**
 * Class Tests_BeansCompiler_GetRemoteContent
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompiler_GetRemoteContent extends Compiler_Test_Case {

	/**
	 * Test _Beans_Compiler::get_remote_content() should return false when the fragment is empty.
	 */
	public function test_should_return_false_when_fragment_is_empty() {
		$compiler = $this->create_compiler( [] );

		// Run the tests.
		$this->assertfalse( $compiler->get_remote_content() );

		$this->set_current_fragment( $compiler, false );
		$this->assertfalse( $compiler->get_remote_content() );

		$this->set_current_fragment( $compiler, '' );
		$this->assertfalse( $compiler->get_remote_content() );
	}

	/**
	 * Test _Beans_Compiler::get_remote_content() should return an empty string when the remote site or file does not
	 * exist on http.
	 */
	public function test_should_return_empty_string_when_remote_does_not_exist_on_http() {
		// Set up the compiler.
		$fragment = 'http://beans.local/invalid-file.js';
		$compiler = $this->create_compiler( [] );
		$this->set_current_fragment( $compiler, $fragment );

		// Set up the mocks.
		Monkey\Functions\expect( 'wp_remote_get' )->once();
		Monkey\Functions\expect( 'is_wp_error' )->once()->andReturn( true );

		// Run the test.
		$this->assertSame( '', $compiler->get_remote_content() );
	}

	/**
	 * Test _Beans_Compiler::get_remote_content() should return an empty string when the remote site or file does not
	 * exist on https.
	 */
	public function test_should_return_empty_string_when_remote_does_not_exist_on_https() {
		// Set up the compiler.
		$fragment = 'http://beans.local/invalid-file.js';
		$compiler = $this->create_compiler( [] );
		$this->set_current_fragment( $compiler, $fragment );

		// Set up the mocks.
		Monkey\Functions\expect( 'wp_remote_get' )->twice();
		Monkey\Functions\expect( 'is_wp_error' )->twice()->andReturnValues( [ false, true ] );

		// Run the test.
		$this->assertSame( '', $compiler->get_remote_content() );
	}

	/**
	 * Test _Beans_Compiler::get_remote_content() should retry and then return false when the remote file does not
	 * exist.
	 */
	public function test_should_retry_and_return_false_when_remote_file_does_not_exist() {
		// Set up the compiler.
		$fragment = 'http://beans.local/invalid-file.js';
		$compiler = $this->create_compiler( [] );
		$this->set_current_fragment( $compiler, $fragment );

		// Set up the mocks.
		Monkey\Functions\expect( 'is_wp_error' )->twice()->andReturn( false );
		Monkey\Functions\expect( 'wp_remote_get' )
			->with( $fragment )
			->once()
			->ordered()
			// No "body" element is returned.
			->andReturn( [] )
			->andAlsoExpectIt()
			// During the retry, it should change to URL to https.
			->with( 'https://beans.local/invalid-file.js' )
			->once()
			->ordered()
			->andReturn(
				[
					'body'     => '',
					'response' => [
						'code' => 404, // HTTP code is 404 and not 200.
					],
				]
			);

		// Run the test.
		$this->assertFalse( $compiler->get_remote_content() );
	}

	/**
	 * Test _Beans_Compiler::get_remote_content() should return the content when the fragment has a relative url.
	 */
	public function test_should_return_content_when_relative_url() {
		// Set up the compiler.
		$fragment = '//fonts.googleapis.com/css?family=Lato';
		$compiler = $this->create_compiler( [] );
		$this->set_current_fragment( $compiler, $fragment );
		$request = [
			'body'     => $this->get_expected_content(),
			'response' => [
				'code' => 200,
			],
		];

		// Set up the mocks.
		Monkey\Functions\expect( 'is_wp_error' )->once()->andReturn( false );
		Monkey\Functions\expect( 'wp_remote_get' )
			// Check that it did add http: to the relative URL.
			->with( 'http:' . $fragment )
			->once()
			->andReturn( $request );
		Monkey\Functions\expect( 'wp_remote_retrieve_body' )
			->with( $request )
			->once()
			->andReturn( $this->get_expected_content() );

		// Run the tests.
		$content = $compiler->get_remote_content();

		$this->assertContains( "font-family: 'Lato';", $content );
		$this->assertContains( 'font-style: normal;', $content );
		$this->assertContains( 'font-weight: 400;', $content );
		$this->assertContains( "src: local('Lato Regular')", $content );
	}

	/**
	 * Test _Beans_Compiler::get_remote_content() should return the content when the fragment has an http URL.
	 */
	public function test_should_return_content_when_http() {
		// Set up the compiler.
		$fragment = 'http://fonts.googleapis.com/css?family=Lato';
		$compiler = $this->create_compiler( [] );
		$this->set_current_fragment( $compiler, $fragment );
		$request = [
			'body'     => $this->get_expected_content(),
			'response' => [
				'code' => 200,
			],
		];

		// Set up the mocks.
		Monkey\Functions\expect( 'is_wp_error' )->once()->andReturn( false );
		Monkey\Functions\expect( 'wp_remote_get' )
			// Check that it did add http: to the relative URL.
			->with( $fragment )
			->once()
			->andReturn( $request );
		Monkey\Functions\expect( 'wp_remote_retrieve_body' )
			->with( $request )
			->once()
			->andReturn( $this->get_expected_content() );

		// Run the tests.
		$content = $compiler->get_remote_content();

		$this->assertContains( "font-family: 'Lato';", $content );
		$this->assertContains( 'font-style: normal;', $content );
		$this->assertContains( 'font-weight: 400;', $content );
		$this->assertContains( "src: local('Lato Regular')", $content );
	}

	/**
	 * Test _Beans_Compiler::get_remote_content() should return the content when the fragment has an https URL.
	 */
	public function test_should_return_content_when_https() {
		// Set up the compiler.
		$fragment = 'https://fonts.googleapis.com/css?family=Lato';
		$compiler = $this->create_compiler( [] );
		$this->set_current_fragment( $compiler, $fragment );
		$request = [
			'body'     => $this->get_expected_content(),
			'response' => [
				'code' => 200,
			],
		];

		// Set up the mocks.
		Monkey\Functions\expect( 'is_wp_error' )->once()->andReturn( false );
		Monkey\Functions\expect( 'wp_remote_get' )
			// Check that it did add http: to the relative URL.
			->with( $fragment )
			->once()
			->andReturn( $request );
		Monkey\Functions\expect( 'wp_remote_retrieve_body' )
			->with( $request )
			->once()
			->andReturn( $this->get_expected_content() );

		// Run the tests.
		$content = $compiler->get_remote_content();

		$this->assertContains( "font-family: 'Lato';", $content );
		$this->assertContains( 'font-style: normal;', $content );
		$this->assertContains( 'font-weight: 400;', $content );
		$this->assertContains( "src: local('Lato Regular')", $content );
	}

	/**
	 * Get the expected content.
	 */
	private function get_expected_content() {
		return <<<EOC
/* latin-ext */
@font-face {
  font-family: 'Lato';
  font-style: normal;
  font-weight: 400;
  src: local('Lato Regular'), local('Lato-Regular'), url(https://fonts.gstatic.com/s/lato/v14/8qcEw_nrk_5HEcCpYdJu8BTbgVql8nDJpwnrE27mub0.woff2) format('woff2');
  unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+20A0-20AB, U+20AD-20CF, U+2C60-2C7F, U+A720-A7FF;
}
/* latin */
@font-face {
  font-family: 'Lato';
  font-style: normal;
  font-weight: 400;
  src: local('Lato Regular'), local('Lato-Regular'), url(https://fonts.gstatic.com/s/lato/v14/MDadn8DQ_3oT6kvnUq_2r_esZW2xOQ-xsNqO47m55DA.woff2) format('woff2');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2212, U+2215;
}
EOC;
	}
}
