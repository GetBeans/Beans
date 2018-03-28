<?php
/**
 * Tests the add_content_media_query method of _Beans_Compiler.
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler;

use _Beans_Compiler;
use Beans\Framework\Tests\Unit\API\Compiler\Includes\Compiler_Test_Case;
use Brain\Monkey;

require_once dirname( __DIR__ ) . '/includes/class-compiler-test-case.php';

/**
 * Class Tests_Beans_Compiler_Add_Content_Media_Query
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_Beans_Compiler_Add_Content_Media_Query extends Compiler_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		Monkey\Functions\when( 'beans_get_compiler_dir' )->justReturn( $this->compiled_dir );
		Monkey\Functions\when( 'beans_get_compiler_url' )->justReturn( $this->compiled_url );
	}

	/**
	 * Test add_content_media_query() should return original content when current fragment is callable.
	 */
	public function test_should_return_content_when_fragment_is_callable() {
		$compiler = new _Beans_Compiler( array() );
		$css      = <<<EOB
.foo {
    margin: 0;
    padding: 0;
    width: 100%;
}
EOB;
		$this->set_current_fragment( $compiler, array( $this, __METHOD__ ) );
		$this->assertSame( $css, $compiler->add_content_media_query( $css ) );
	}

	/**
	 * Test add_content_media_query() should return original content when there are no query args.
	 */
	public function test_should_return_content_when_no_query_args() {
		$compiler = new _Beans_Compiler( array() );
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
	 * Test add_content_media_query() should return original content when the 'beans_compiler_media_query'
	 * query arg is not present in the current fragment.
	 */
	public function test_should_return_content_when_no_media_query() {
		$compiler = new _Beans_Compiler( array() );
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
	 * Test add_content_media_query() should wrap the content in the specified media query.
	 */
	public function test_should_wrap_content_in_media_query() {
		$compiler      = new _Beans_Compiler( array() );
		$css           = <<<EOB
.foo {
    margin: 0;
    padding: 0;
    width: 100%;
}
EOB;
		$media_queries = array(
			'all',
			'print',
			'screen',
			'(min-width: 768px)',
		);

		Monkey\Functions\stubs( [
			'wp_parse_args' => function( $query_args ) {
				parse_str( $query_args, $args );
				return $args;
			},
		] );

		foreach ( $media_queries as $media_query ) {
			$this->set_current_fragment( $compiler, 'http://foo.com/base.css?beans_compiler_media_query=' . $media_query );
			$this->assertSame(
				"@media {$media_query} {\n{$css}\n}\n",
				$compiler->add_content_media_query( $css )
			);
		}
	}
}
