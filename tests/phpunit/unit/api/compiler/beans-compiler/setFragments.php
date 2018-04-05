<?php
/**
 * Tests the set_fragments method of _Beans_Compiler.
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
 * Class Tests_BeansCompiler_SetFragments
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompiler_SetFragments extends Compiler_Test_Case {

	/**
	 * Test set_fragments() should return unchanged fragments, meaning no fragments were added or removed.
	 */
	public function test_should_return_unchanged_fragments() {
		$config = array(
			'id'        => 'test',
			'type'      => 'style',
			'format'    => 'less',
			'fragments' => array(
				vfsStream::url( 'compiled/fixtures/variables.less' ),
				vfsStream::url( 'compiled/fixtures/test.less' ),
			),
		);

		$compiler = $this->create_compiler( $config );

		// Setup the mock.
		Monkey\Functions\expect( 'beans_get' )
			->once()
			->with( 'test', array() )
			->andReturn();

		// Check before we start.
		$this->assertSame( $config['fragments'], $compiler->config['fragments'] );

		// Run the test.
		$compiler->set_fragments();
		$this->assertSame( $config['fragments'], $compiler->config['fragments'] );
	}

	/**
	 * Test set_fragments() should return fragments merged with the fragments stored in the global variable.
	 */
	public function test_should_return_fragments_merged_with_global() {
		$config = array(
			'id'        => 'test',
			'type'      => 'style',
			'format'    => 'less',
			'fragments' => array(
				vfsStream::url( 'compiled/fixtures/variables.less' ),
				vfsStream::url( 'compiled/fixtures/test.less' ),
			),
		);

		$compiler = $this->create_compiler( $config );
		global $_beans_compiler_added_fragments;
		$_beans_compiler_added_fragments['less'] = array(
			'test' => array(
				'some-file.less',
				'another-file.less',
			),
		);

		// Setup the mock.
		Monkey\Functions\expect( 'beans_get' )
			->once()
			->with( 'test', $_beans_compiler_added_fragments['less'] )
			->andReturn( $_beans_compiler_added_fragments['less']['test'] );

		// Check before we start.
		$this->assertSame( $config['fragments'], $compiler->config['fragments'] );

		// Run the test.
		$expected = array_merge( $config['fragments'], $_beans_compiler_added_fragments['less']['test'] );
		$compiler->set_fragments();
		$this->assertSame( $expected, $compiler->config['fragments'] );
	}

	/**
	 * Test set_fragments() should fire the "beans_compiler_fragments_{id}" event.
	 */
	public function test_should_fire_event() {
		$config = array(
			'id'        => 'test',
			'type'      => 'style',
			'format'    => 'less',
			'fragments' => array(
				vfsStream::url( 'compiled/fixtures/variables.less' ),
				vfsStream::url( 'compiled/fixtures/test.less' ),
			),
		);

		$compiler = $this->create_compiler( $config );

		// Setup the mock.
		Monkey\Functions\expect( 'beans_get' )
			->once()
			->with( 'test', array() )
			->andReturn();
		Monkey\Filters\expectApplied( 'beans_compiler_fragments_test' )
			->once()
			->with( $config['fragments'] )
			->andReturn( $compiler->config['fragments'] );

		// Run the test.
		$compiler->set_fragments();
		$this->assertSame( $config['fragments'], $compiler->config['fragments'] );
	}
}
