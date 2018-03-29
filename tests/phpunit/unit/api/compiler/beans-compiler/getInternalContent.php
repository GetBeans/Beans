<?php
/**
 * Tests the get_internal_content method of _Beans_Compiler.
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler;

use Beans\Framework\Tests\Unit\API\Compiler\Includes\Compiler_Test_Case;
use Brain\Monkey;
use org\bovigo\vfs\vfsStream;

require_once dirname( __DIR__ ) . '/includes/class-compiler-test-case.php';

/**
 * Class Tests_Beans_Compiler_Get_Internal_Content
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_Beans_Compiler_Get_Internal_Content extends Compiler_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		Monkey\Functions\when( 'beans_url_to_path' )->returnArg();
	}

	/**
	 * Test get_internal_content() should return false when fragment is empty.
	 */
	public function test_should_return_false_when_fragment_is_empty() {
		$compiler = $this->create_compiler();

		// Run the test.
		$this->assertfalse( $compiler->get_internal_content() );
	}

	/**
	 * Test get_internal_content() should return false when the file does not exist.
	 */
	public function test_should_return_false_when_file_does_not_exist() {
		$compiler = $this->create_compiler();
		$this->set_reflective_property( vfsStream::url( 'compiled/fixtures/' ) . 'invalid-file.js', 'filename', $compiler );

		// Run the test.
		$this->assertfalse( $compiler->get_internal_content() );
	}

	/**
	 * Test get_internal_content() should return a fragment's contents.
	 */
	public function test_should_return_fragment_contents() {
		$fragment = vfsStream::url( 'compiled/fixtures/test.less' );
		$compiler = $this->create_compiler( array(
			'fragments' => array( $fragment ),
		) );

		// Setup the mocks.
		$this->set_reflective_property( $fragment, 'current_fragment', $compiler );
		$this->mock_filesystem_for_fragments( $compiler );

		// Run the tests.
		$expected = <<<EOB
@test-font-size: 18px;

body {
  background-color: #fff;
  color: @body-color;
  font-size: @test-font-size;
}

EOB;
		$this->assertSame( $expected, $compiler->get_internal_content() );
	}
}
