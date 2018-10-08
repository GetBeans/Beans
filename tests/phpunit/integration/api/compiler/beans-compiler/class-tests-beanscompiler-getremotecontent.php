<?php
/**
 * Tests for the get_remote_content() method of Beans_Compiler.
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Compiler;

use Beans_Compiler;
use Beans\Framework\Tests\Integration\API\Compiler\Includes\Compiler_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-compiler-test-case.php';

/**
 * Class Tests_BeansCompiler_GetRemoteContent
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompiler_GetRemoteContent extends Compiler_Test_Case {

	/**
	 * Test Beans_Compiler::get_remote_content() should return false when fragment is empty.
	 */
	public function test_should_return_false_when_fragment_is_empty() {
		$compiler = new Beans_Compiler( [] );

		// Run the test.
		$this->assertfalse( $compiler->get_remote_content( '' ) );
	}

	/**
	 * Test Beans_Compiler::get_remote_content() should return empty string when the remote site or file does not
	 * exist.
	 */
	public function test_should_return_empty_string_when_remote_does_not_exist() {
		$fragment = 'http://beans.local/invalid-file.js';
		$compiler = new Beans_Compiler(
			[
				'fragments' => [ $fragment ],
			]
		);
		$this->set_current_fragment( $compiler, $fragment );

		// Run the test.
		$this->assertSame( '', $compiler->get_remote_content( $fragment ) );
	}

	/**
	 * Test Beans_Compiler::get_remote_content() should return the content when fragment is a relative url.
	 */
	public function test_should_return_content_when_fragment_is_relative_url() {
		$fragment = '//fonts.googleapis.com/css?family=Lato';
		$compiler = new Beans_Compiler(
			[
				'fragments' => [ $fragment ],
			]
		);
		$this->set_current_fragment( $compiler, $fragment );

		$content = $compiler->get_remote_content( $fragment );

		// Run the tests.
		$this->assertContains( "font-family: 'Lato';", $content );
		$this->assertContains( 'font-style: normal;', $content );
		$this->assertContains( 'font-weight: 400;', $content );
		$this->assertContains( "src: local('Lato Regular')", $content );
	}

	/**
	 * Test Beans_Compiler::get_remote_content() should return the content when fragment is an http URL.
	 */
	public function test_should_return_content_when_fragment_is_http() {
		$fragment = 'http://fonts.googleapis.com/css?family=Roboto';
		$compiler = new Beans_Compiler(
			[
				'fragments' => [ $fragment ],
			]
		);
		$this->set_current_fragment( $compiler, $fragment );

		$content = $compiler->get_remote_content( $fragment );

		// Run the tests.
		$this->assertContains( "font-family: 'Roboto';", $content );
		$this->assertContains( 'font-style: normal;', $content );
		$this->assertContains( 'font-weight: 400;', $content );
		$this->assertContains( "src: local('Roboto')", $content );
	}

	/**
	 * Test Beans_Compiler::get_remote_content() should return the content when fragment is an https URL.
	 */
	public function test_should_return_content_when_fragment_is_https() {
		$fragment = 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css';
		$compiler = new Beans_Compiler(
			[
				'fragments' => [ $fragment ],
			]
		);
		$this->set_current_fragment( $compiler, $fragment );

		$content = $compiler->get_remote_content( $fragment );

		// Run the tests.
		$this->assertContains( 'Font Awesome 4.7.0 by @davegandy - http://fontawesome.io - @fontawesome', $content );
		$this->assertContains( "@font-face{font-family:'FontAwesome';", $content );
		$this->assertContains( "src:url('../fonts/fontawesome-webfont.eot?v=4.7.0');", $content );
	}
}
