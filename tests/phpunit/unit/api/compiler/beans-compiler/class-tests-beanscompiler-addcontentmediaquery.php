<?php
/**
 * Tests for the add_content_media_query() method of Beans_Compiler.
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
 * Class Tests_BeansCompiler_AddContentMediaQuery
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompiler_AddContentMediaQuery extends Compiler_Test_Case {

	/**
	 * Test Beans_Compiler::add_content_media_query() should return original content when current fragment is callable.
	 */
	public function test_should_return_content_when_fragment_is_callable() {
		Monkey\Functions\expect( 'wp_parse_args' )->never();
		Monkey\Functions\expect( 'beans_get' )->never();

		$compiler = $this->create_compiler();
		$css      = <<<EOB
.foo {
    margin: 0;
    padding: 0;
    width: 100%;
}
EOB;
		$this->set_current_fragment( $compiler, [ $this, __METHOD__ ] );
		$this->assertSame( $css, $compiler->add_content_media_query( $css ) );
	}

	/**
	 * Test Beans_Compiler::add_content_media_query() should return original content when there are no query args.
	 */
	public function test_should_return_content_when_no_query_args() {
		Monkey\Functions\expect( 'wp_parse_args' )->never();
		Monkey\Functions\expect( 'beans_get' )->never();

		$compiler = $this->create_compiler();
		$css      = <<<EOB
.foo {
    margin: 0;
    padding: 0;
    width: 100%;
}
EOB;
		$this->set_current_fragment( $compiler, 'http://foo.com/foo.css' );
		$this->assertSame( $css, $compiler->add_content_media_query( $css ) );

		$this->set_current_fragment( $compiler, 'http://foo.com/assets/less/foo.less' );
		$this->assertSame( $css, $compiler->add_content_media_query( $css ) );
	}

	/**
	 * Test Beans_Compiler::add_content_media_query() should return original content when the
	 * 'beans_compiler_media_query' query arg is not present in the current fragment.
	 */
	public function test_should_return_content_when_no_media_query() {
		Monkey\Functions\expect( 'wp_parse_args' )->never();
		Monkey\Functions\expect( 'beans_get' )->never();

		$compiler = $this->create_compiler( [] );
		$css      = <<<EOB
.foo {
    margin: 0;
    padding: 0;
    width: 100%;
}
EOB;
		$this->set_current_fragment( $compiler, 'http://foo.com/base.css?beans=rocks' );
		$this->assertSame( $css, $compiler->add_content_media_query( $css ) );

		$this->set_current_fragment( $compiler, 'http://foo.com/style.css?beans=rocks&beans_compiler=media_query' );
		$this->assertSame( $css, $compiler->add_content_media_query( $css ) );
	}

	/**
	 * Test Beans_Compiler::add_content_media_query() should wrap the content in the specified media query.
	 */
	public function test_should_wrap_content_in_media_query() {
		$compiler      = $this->create_compiler( [] );
		$css           = <<<EOB
.foo {
    margin: 0;
    padding: 0;
    width: 100%;
}
EOB;
		$media_queries = [
			'all',
			'print',
			'screen',
			'(min-width: 768px)',
		];

		foreach ( $media_queries as $media_query ) {
			$this->set_current_fragment( $compiler, 'http://foo.com/base.css?beans_compiler_media_query=' . $media_query );

			Monkey\Functions\expect( 'wp_parse_args' )
				->once()
				->with( 'beans_compiler_media_query=' . $media_query )
				->andReturnUsing(
					function( $query_args ) {
							parse_str( $query_args, $args );

							return $args;
					}
				);
			Monkey\Functions\expect( 'beans_get' )
				->once()
				->with( 'beans_compiler_media_query', [ 'beans_compiler_media_query' => $media_query ] )
				->andReturn( $media_query );

			$this->assertSame(
				"@media {$media_query} {\n{$css}\n}\n",
				$compiler->add_content_media_query( $css )
			);
		}
	}
}
