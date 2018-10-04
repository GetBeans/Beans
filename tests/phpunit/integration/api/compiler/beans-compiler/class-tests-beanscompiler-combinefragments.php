<?php
/**
 * Tests for the combine_fragments() method of _Beans_Compiler.
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Compiler;

use _Beans_Compiler;
use Beans\Framework\Tests\Integration\API\Compiler\Includes\Compiler_Test_Case;
use org\bovigo\vfs\vfsStream;

require_once dirname( __DIR__ ) . '/includes/class-compiler-test-case.php';

/**
 * Class Tests_BeansCompiler_CombineFragments
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompiler_CombineFragments extends Compiler_Test_Case {

	/**
	 * The CSS content.
	 *
	 * @var string
	 */
	protected $css;

	/**
	 * The Less content.
	 *
	 * @var string
	 */
	protected $less;

	/**
	 * The jQuery content.
	 *
	 * @var string
	 */
	protected $jquery;

	/**
	 * The JavaScript content.
	 *
	 * @var string
	 */
	protected $js;

	/**
	 * Set up the tests.
	 */
	public function setUp() {
		parent::setUp();

		$fixtures     = $this->mock_filesystem->getChild( 'fixtures' );
		$this->css    = $fixtures->getChild( 'style.css' )->getContent();
		$this->less   = $fixtures->getChild( 'variables.less' )->getContent() . $fixtures->getChild( 'test.less' )->getContent();
		$this->jquery = $fixtures->getChild( 'jquery.test.js' )->getContent();
		$this->js     = $fixtures->getChild( 'my-game-clock.js' )->getContent();
	}

	/**
	 * Test _Beans_Compiler::combine_fragments() should return an empty string when there are no fragments to combine.
	 */
	public function test_should_return_empty_string_when_no_fragments() {
		$compiler = new _Beans_Compiler( [] );

		// Run the test.
		$compiler->combine_fragments();
		$this->assertSame( '', $compiler->compiled_content );
	}

	/**
	 * Test _Beans_Compiler::combine_fragments() should return an empty string when the fragment does not exist.
	 */
	public function test_should_return_empty_string_when_fragment_does_not_exist() {
		$fragment = vfsStream::url( 'compiled/fixtures/' ) . 'invalid-file.js';
		$compiler = new _Beans_Compiler( [] );
		$this->set_current_fragment( $compiler, $fragment );

		// Run the test.
		$compiler->combine_fragments();
		$this->assertSame( '', $compiler->compiled_content );
	}

	/**
	 * Test _Beans_Compiler::combine_fragments() should compile the Less fragments and return the compiled CSS.
	 */
	public function test_should_compile_less_and_return_css() {
		$compiler = new _Beans_Compiler(
			[
				'id'        => 'test',
				'type'      => 'style',
				'format'    => 'less',
				'fragments' => [
					vfsStream::url( 'compiled/fixtures/variables.less' ),
					vfsStream::url( 'compiled/fixtures/test.less' ),
				],
			]
		);

		// Set up the test.
		$this->set_up_wp_filesystem( $compiler );
		$this->set_dev_mode( true );

		// Run the test.
		$compiler->combine_fragments();
		$expected_css = <<<EOB
body {
  background-color: #fff;
  color: #000;
  font-size: 18px;
}

EOB;
		$this->assertSame( $expected_css, $compiler->compiled_content );
	}

	/**
	 * Test _Beans_Compiler::combine_fragments() should return minified, compiled Less from the Less combined fragments.
	 */
	public function test_should_return_minified_compiled_less() {
		$compiler = new _Beans_Compiler(
			[
				'id'        => 'test',
				'type'      => 'style',
				'format'    => 'less',
				'fragments' => [
					vfsStream::url( 'compiled/fixtures/variables.less' ),
					vfsStream::url( 'compiled/fixtures/test.less' ),
				],
			]
		);

		// Set up the test.
		$this->set_up_wp_filesystem( $compiler );
		$this->set_dev_mode( false );

		// Run the test.
		$compiler->combine_fragments();
		$this->assertContains( $this->get_compiled_less(), $compiler->compiled_content );
	}

	/**
	 * Test _Beans_Compiler::combine_fragments() should return the original jQuery when site is not in development mode,
	 * but "minify_js" is disabled.
	 */
	public function test_should_return_original_jquery_when_minify_js_disabled() {
		$compiler = new _Beans_Compiler(
			[
				'id'           => 'test',
				'type'         => 'script',
				'minify_js'    => false,
				'fragments'    => [
					vfsStream::url( 'compiled/fixtures/jquery.test.js' ),
				],
				'dependencies' => [ 'jquery' ],
			]
		);

		// Set up the test.
		$this->set_up_wp_filesystem( $compiler );
		$this->set_dev_mode( false );

		// Run the test.
		$compiler->combine_fragments();
		$this->assertSame( $this->jquery, $compiler->compiled_content );
	}

	/**
	 * Test _Beans_Compiler::combine_fragments() should return the original jQuery when "minify_js" is enabled,
	 * but the site is in development mode.
	 */
	public function test_should_always_return_original_jquery_when_in_dev_mode() {
		$compiler = new _Beans_Compiler(
			[
				'id'           => 'test',
				'type'         => 'script',
				'minify_js'    => true,
				'fragments'    => [
					vfsStream::url( 'compiled/fixtures/jquery.test.js' ),
				],
				'dependencies' => [ 'jquery' ],
			]
		);

		// Set up the test.
		$this->set_up_wp_filesystem( $compiler );
		$this->set_dev_mode( true );

		// Run the test.
		$compiler->combine_fragments();
		$this->assertSame( $this->jquery, $compiler->compiled_content );
	}

	/**
	 * Test _Beans_Compiler::combine_fragments() should return minified jQuery.
	 */
	public function test_should_return_minified_jquery() {
		$compiler = new _Beans_Compiler(
			[
				'id'           => 'test',
				'type'         => 'script',
				'minify_js'    => true,
				'fragments'    => [
					vfsStream::url( 'compiled/fixtures/jquery.test.js' ),
				],
				'dependencies' => [ 'jquery' ],
			]
		);

		// Set up the test.
		$this->set_up_wp_filesystem( $compiler );
		$this->set_dev_mode( false );

		// Run the test.
		$compiler->combine_fragments();
		$this->assertSame( $this->get_compiled_jquery(), $compiler->compiled_content );
	}

	/**
	 * Test _Beans_Compiler::combine_fragments() should return the original JavaScript when site is not in development
	 * mode, but "minify_js" is disabled.
	 */
	public function test_should_return_original_js_when_minify_js_disabled() {
		$compiler = new _Beans_Compiler(
			[
				'id'        => 'test',
				'type'      => 'script',
				'minify_js' => false,
				'fragments' => [
					vfsStream::url( 'compiled/fixtures/my-game-clock.js' ),
				],
			]
		);

		// Set up the test.
		$this->set_up_wp_filesystem( $compiler );
		$this->set_dev_mode( false );

		// Run the test.
		$compiler->combine_fragments();
		$this->assertSame( $this->js, $compiler->compiled_content );

		// Clean up.
		unset( $GLOBALS['wp_filesystem'] );
		remove_filter( 'filesystem_method', [ $compiler, 'modify_filesystem_method' ] );
	}

	/**
	 * Test _Beans_Compiler::combine_fragments() should return the original JavaScript when "minify_js" is enabled,
	 * but the site is in development mode.
	 */
	public function test_should_always_return_original_js_when_in_dev_mode() {
		$compiler = new _Beans_Compiler(
			[
				'id'        => 'test',
				'type'      => 'script',
				'minify_js' => true,
				'fragments' => [
					vfsStream::url( 'compiled/fixtures/my-game-clock.js' ),
				],
			]
		);

		// Set up the test.
		$this->set_up_wp_filesystem( $compiler );
		$this->set_dev_mode( true );

		// Run the test.
		$compiler->combine_fragments();
		$this->assertSame( $this->js, $compiler->compiled_content );

		// Clean up.
		remove_filter( 'filesystem_method', [ $compiler, 'modify_filesystem_method' ] );
	}

	/**
	 * Test _Beans_Compiler::combine_fragments() should return minified JavaScript.
	 */
	public function test_should_return_minified_javascript() {
		$compiler = new _Beans_Compiler(
			[
				'id'        => 'test',
				'type'      => 'script',
				'minify_js' => true,
				'fragments' => [
					vfsStream::url( 'compiled/fixtures/my-game-clock.js' ),
				],
			]
		);

		// Set up the test.
		$this->set_up_wp_filesystem( $compiler );
		$this->set_dev_mode( false );

		// Run the test.
		$compiler->combine_fragments();
		$this->assertSame( $this->get_compiled_js(), $compiler->compiled_content );

		// Clean up.
		remove_filter( 'filesystem_method', [ $compiler, 'modify_filesystem_method' ] );
	}

	/**
	 * Set the WP Filesystem.
	 *
	 * @since 1.5.0
	 *
	 * @param _Beans_Compiler $compiler Instance of the compiler.
	 *
	 * @return void
	 */
	private function set_up_wp_filesystem( $compiler ) {
		add_filter( 'filesystem_method', [ $compiler, 'modify_filesystem_method' ] );
		$compiler->filesystem();
		$this->assertInstanceOf( 'WP_Filesystem_Direct', $GLOBALS['wp_filesystem'] );
	}
}
