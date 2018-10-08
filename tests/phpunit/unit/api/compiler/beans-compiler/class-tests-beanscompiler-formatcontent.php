<?php
/**
 * Tests for the format_content() method of _Beans_Compiler.
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
 * Class Tests_BeansCompiler_FormatContent
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompiler_FormatContent extends Compiler_Test_Case {

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
	protected function setUp() {
		parent::setUp();

		$fixtures     = $this->mock_filesystem->getChild( 'fixtures' );
		$this->less   = $fixtures->getChild( 'variables.less' )->getContent() . $fixtures->getChild( 'test.less' )->getContent();
		$this->jquery = $fixtures->getChild( 'jquery.test.js' )->getContent();
		$this->js     = $fixtures->getChild( 'my-game-clock.js' )->getContent();
	}

	/**
	 * Test _Beans_Compiler::format_content() should return original content when the "type" is not
	 * a style or script (per the configuration).
	 */
	public function test_should_return_original_content_when_type_not_style_or_script() {
		$compiler = $this->create_compiler(
			[
				'type' => 'foo',
			]
		);

		Monkey\Functions\expect( '_beans_is_compiler_dev_mode' )->never();

		// Run the tests.
		$this->assertSame( $this->less, $compiler->format_content( $this->less ) );
		$this->assertSame( $this->jquery, $compiler->format_content( $this->jquery ) );
		$this->assertSame( $this->js, $compiler->format_content( $this->js ) );
	}

	/**
	 * Test _Beans_Compiler::format_content() should return compiled CSS (not minified) from the Less combined
	 * fragments.
	 */
	public function test_should_return_compiled_css() {
		$compiler = $this->create_compiler(
			[
				'id'     => 'test',
				'type'   => 'style',
				'format' => 'less',
			]
		);

		$compiled_css = <<<EOB
body {
  background-color: #fff;
  color: #000;
  font-size: 18px;
}

EOB;
		// Set up the mocks.
		Monkey\Functions\expect( '_beans_is_compiler_dev_mode' )->once()->andReturn( true );
		$mock = \Mockery::mock( 'Beans_Lessc' );
		$mock->shouldReceive( 'compile' )->andReturn( $compiled_css );

		// Run the tests.
		$this->assertSame( $compiled_css, $compiler->format_content( $this->less ) );
	}

	/**
	 * Test _Beans_Compiler::format_content() should return minified, compiled CSS from the Less combined fragments.
	 */
	public function test_should_return_minified_compiled_css() {
		$compiler = $this->create_compiler(
			[
				'id'     => 'test',
				'type'   => 'style',
				'format' => 'less',
			]
		);

		$compiled_css = <<<EOB
body {
  background-color: #fff;
  color: #000;
  font-size: 18px;
}

EOB;
		// Set up the mocks.
		Monkey\Functions\expect( '_beans_is_compiler_dev_mode' )->once()->andReturn( false );
		$mock = \Mockery::mock( 'Beans_Lessc' );
		$mock->shouldReceive( 'compile' )->andReturn( $compiled_css );

		// Run the test.
		$expected_css = <<<EOB
body{background-color:#fff;color:#000;font-size:18px}
EOB;
		$this->assertSame( $expected_css, $compiler->format_content( $this->less ) );
	}

	/**
	 * Test _Beans_Compiler::format_content() should return the original jQuery when site is not in development mode,
	 * but "minify_js" is disabled.
	 */
	public function test_should_return_original_jquery_when_minify_js_disabled() {
		$compiler = $this->create_compiler(
			[
				'id'        => 'test',
				'type'      => 'script',
				'minify_js' => false,
			]
		);

		// Set up the mocks.
		Monkey\Functions\expect( '_beans_is_compiler_dev_mode' )->once()->andReturn( false );
		\Mockery::mock( 'JSMin' )->shouldNotReceive( 'min' );

		// Run the test.
		$this->assertSame( $this->jquery, $compiler->format_content( $this->jquery ) );
	}

	/**
	 * Test _Beans_Compiler::format_content() should return the original jQuery when "minify_js" is enabled,
	 * but the site is in development mode.
	 */
	public function test_should_always_return_original_jquery_when_in_dev_mode() {
		$compiler = $this->create_compiler(
			[
				'id'        => 'test',
				'type'      => 'script',
				'minify_js' => true,
			]
		);

		// Set up the mocks.
		Monkey\Functions\expect( '_beans_is_compiler_dev_mode' )->once()->andReturn( true );
		\Mockery::mock( 'JSMin' )->shouldNotReceive( 'min' );

		// Run the test.
		$this->assertSame( $this->jquery, $compiler->format_content( $this->jquery ) );
	}

	/**
	 * Test _Beans_Compiler::format_content() should return minified jQuery when "minify_js" is enabled
	 * and the site is not in development mode.
	 */
	public function test_should_return_minified_jquery_when_not_in_dev_mode_and_minify_js_enabled() {
		$compiler = $this->create_compiler(
			[
				'id'        => 'test',
				'type'      => 'script',
				'minify_js' => true,
			]
		);

		$compiled_jquery = <<<EOB
(function($){'use strict';var init=function(){/$('some-button').on('click',clickHandler);}
var clickHandler=function(event){event.preventDefault();}
$(document).ready(function(){init();});})(jQuery);
EOB;
		$compiled_jquery = str_replace( '/$', '$', $compiled_jquery );

		// Set up the mocks.
		Monkey\Functions\expect( '_beans_is_compiler_dev_mode' )->once()->andReturn( false );
		$mock = \Mockery::mock( 'JSMin' );
		$mock->shouldReceive( 'min' )->andReturn( $compiled_jquery );

		// Run the test.
		$this->assertSame( $compiled_jquery, $compiler->format_content( $this->jquery ) );
	}

	/**
	 * Test _Beans_Compiler::format_content() should return the original JavaScript when site is not in development
	 * mode, but "minify_js" is disabled.
	 */
	public function test_should_return_original_js_when_minify_js_disabled() {
		$compiler = $this->create_compiler(
			[
				'id'        => 'test',
				'type'      => 'script',
				'minify_js' => false,
			]
		);

		// Set up the mocks.
		Monkey\Functions\expect( '_beans_is_compiler_dev_mode' )->once()->andReturn( false );
		\Mockery::mock( 'JSMin' )->shouldNotReceive( 'min' );

		// Run the test.
		$this->assertSame( $this->js, $compiler->format_content( $this->js ) );
	}

	/**
	 * Test _Beans_Compiler::format_content() should return the original JavaScript when "minify_js" is enabled,
	 * but the site is in development mode.
	 */
	public function test_should_always_return_original_js_when_in_dev_mode() {
		$compiler = $this->create_compiler(
			[
				'id'        => 'test',
				'type'      => 'script',
				'minify_js' => true,
			]
		);

		// Set up the mocks.
		Monkey\Functions\expect( '_beans_is_compiler_dev_mode' )->once()->andReturn( true );
		\Mockery::mock( 'JSMin' )->shouldNotReceive( 'min' );

		// Run the test.
		$this->assertSame( $this->js, $compiler->format_content( $this->js ) );
	}

	/**
	 * Test _Beans_Compiler::format_content() should return minified JavaScript when "minify_js" is enabled
	 * and the site is not in development mode.
	 */
	public function test_should_return_minified_js_when_not_in_dev_mode_and_minify_js_enabled() {
		$compiler = $this->create_compiler(
			[
				'id'        => 'test',
				'type'      => 'script',
				'minify_js' => true,
			]
		);

		$compiled_js = <<<EOB
class MyGameClock{constructor(maxTime){this.maxTime=maxTime;this.currentClock=0;}
getRemainingTime(){return this.maxTime-this.currentClock;}}
EOB;
		// Set up the mocks.
		Monkey\Functions\expect( '_beans_is_compiler_dev_mode' )->once()->andReturn( false );
		$mock = \Mockery::mock( 'JSMin' );
		$mock->shouldReceive( 'min' )->andReturn( $compiled_js );

		// Run the test.
		$this->assertSame( $compiled_js, $compiler->format_content( $this->js ) );
	}
}
