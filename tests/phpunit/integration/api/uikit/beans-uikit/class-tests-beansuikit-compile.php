<?php
/**
 * Tests for the compile() method of _Beans_Uikit.
 *
 * @package Beans\Framework\Tests\Integration\API\UIkit
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\UIkit;

use _Beans_Uikit;
use Beans\Framework\Tests\Integration\API\UIkit\Includes\UIkit_Test_Case;
use Brain\Monkey;
use org\bovigo\vfs\vfsStream;

require_once dirname( __DIR__ ) . '/includes/class-uikit-test-case.php';

/**
 * Class Tests_BeansUikit_Compile
 *
 * @package Beans\Framework\Tests\Integration\API\UIkit
 * @group   api
 * @group   api-uikit
 */
class Tests_BeansUikit_Compile extends UIkit_Test_Case {

	/**
	 * Path to the compiled UIkit files.
	 *
	 * @var string
	 */
	protected $compiled_uikit_path;

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		$this->compiled_uikit_path = vfsStream::url( 'virtual-wp-content/uploads/beans/compiler/uikit/' );

		global $_beans_uikit_enqueued_items;
		$_beans_uikit_enqueued_items['themes'] = [];
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {
		remove_all_filters( 'beans_uikit_euqueued_styles' );
		remove_all_filters( 'beans_uikit_euqueued_styles_args' );
		remove_all_filters( 'beans_uikit_euqueued_scripts' );
		remove_all_filters( 'beans_uikit_euqueued_scripts_args' );

		parent::tearDown();
	}

	/**
	 * Test _Beans_Uikit::compile() should not compile when there are no assets to compile.
	 */
	public function test_should_not_compile_when_no_assets_to_compile() {
		add_filter( 'beans_uikit_euqueued_styles', '__return_empty_array' );
		add_filter( 'beans_uikit_euqueued_scripts', '__return_empty_array' );

		// Make sure the compilers do not get called.
		Monkey\Functions\expect( 'beans_compile_less_fragments' )->never();
		Monkey\Functions\expect( 'beans_compile_js_fragments' )->never();

		$this->assertNull( ( new _Beans_Uikit() )->compile() );
	}

	/**
	 * Test _Beans_Uikit::compile() should compile the styles.
	 */
	public function test_should_compile_styles() {
		beans_uikit_enqueue_components(
			[
				'alert',
				'button',
				'overlay',
			],
			'core',
			false
		);

		add_filter(
			'beans_uikit_euqueued_styles',
			function( $styles ) {
				$this->assertSame(
					[
						BEANS_API_PATH . 'uikit/src/less/core/variables.less',
						BEANS_API_PATH . 'uikit/src/less/core/alert.less',
						BEANS_API_PATH . 'uikit/src/less/core/button.less',
						BEANS_API_PATH . 'uikit/src/less/core/overlay.less',
						BEANS_API_PATH . 'uikit/src/fixes.less',
					],
					$styles
				);

				return $styles;
			},
			9999
		);
		add_filter( 'beans_uikit_euqueued_scripts', '__return_empty_array' );
		Monkey\Functions\expect( 'beans_compile_js_fragments' )->never();

		$this->assertEmpty( $this->get_compiled_filename( $this->compiled_uikit_path ) );
		( new _Beans_Uikit() )->compile();
		$filename = $this->get_compiled_filename( $this->compiled_uikit_path );
		$this->assertFileExists( $this->compiled_uikit_path . $filename );
		$this->assertStringEndsWith( '.css', $filename );

		// Check the compiled CSS.
		$compiled_css = $this->get_cached_contents( $filename, 'uikit' );
		$alert_css    = <<<EOB
.uk-alert-success{background:#f2fae3;color:#659f13}
.uk-alert-warning{background:#fffceb;color:#e28327}
.uk-alert-danger{background:#fff1f0;color:#d85030}
.uk-alert-large{padding:20px}
EOB;
		$this->assertContains( $alert_css, $compiled_css );

		$button_css = <<<EOB
.uk-button{-webkit-appearance:none;margin:0;border:none;overflow:visible;font:inherit;color:#444;text-transform:none;display:inline-block;box-sizing:border-box;padding:0 12px;background:#eee;vertical-align:middle;line-height:30px;min-height:30px;font-size:1rem;text-decoration:none;text-align:center}
.uk-button:not(:disabled){cursor:pointer}
.uk-button:hover,.uk-button:focus{background-color:#f5f5f5;color:#444;outline:none;text-decoration:none}
.uk-button:active,.uk-button.uk-active{background-color:#ddd;color:#444}
.uk-button-primary{background-color:#00a8e6;color:#fff}
.uk-button-primary:hover,.uk-button-primary:focus{background-color:#35b3ee;color:#fff}
.uk-button-primary:active,.uk-button-primary.uk-active{background-color:#0091ca;color:#fff}
.uk-button-success{background-color:#8cc14c;color:#fff}
.uk-button-success:hover,.uk-button-success:focus{background-color:#8ec73b;color:#fff}
.uk-button-success:active,.uk-button-success.uk-active{background-color:#72ae41;color:#fff}
.uk-button-danger{background-color:#da314b;color:#fff}
.uk-button-danger:hover,.uk-button-danger:focus{background-color:#e4354f;color:#fff}
.uk-button-danger:active,.uk-button-danger.uk-active{background-color:#c91032;color:#fff}
.uk-button:disabled{background-color:#f5f5f5;color:#999}
EOB;
		$this->assertContains( $button_css, $compiled_css );

		$overlay_css = <<<EOB
.uk-overlay{display:inline-block;position:relative;max-width:100%;vertical-align:middle;overflow:hidden;-webkit-transform:translateZ(0);margin:0}
.uk-overlay.uk-border-circle{-webkit-mask-image:-webkit-radial-gradient(circle,white 100%,black 100%)}
.uk-overlay > :first-child{margin-bottom:0}
.uk-overlay-panel{position:absolute;top:0;bottom:0;left:0;right:0;padding:20px;color:#fff}
EOB;
		$this->assertContains( $overlay_css, $compiled_css );
	}

	/**
	 * Test _Beans_Uikit::compile() should compile the styles with the default theme.
	 */
	public function test_should_compile_styles_with_default_theme() {
		$theme_path = BEANS_API_PATH . 'uikit/src/themes/default/';
		beans_uikit_enqueue_theme( 'default', $theme_path );
		beans_uikit_enqueue_components( 'alert', 'core', false );

		add_filter(
			'beans_uikit_euqueued_styles',
			function( $styles ) use ( $theme_path ) {
				$this->assertSame(
					[
						BEANS_API_PATH . 'uikit/src/less/core/variables.less',
						$theme_path . 'variables.less',
						BEANS_API_PATH . 'uikit/src/less/core/alert.less',
						$theme_path . 'alert.less',
						BEANS_API_PATH . 'uikit/src/fixes.less',
					],
					$styles
				);

				return $styles;
			},
			9999
		);
		add_filter( 'beans_uikit_euqueued_scripts', '__return_empty_array' );
		Monkey\Functions\expect( 'beans_compile_js_fragments' )->never();

		$this->assertEmpty( $this->get_compiled_filename( $this->compiled_uikit_path ) );
		( new _Beans_Uikit() )->compile();
		$filename = $this->get_compiled_filename( $this->compiled_uikit_path );
		$this->assertFileExists( $this->compiled_uikit_path . $filename );
		$this->assertStringEndsWith( '.css', $filename );

		// Check the compiled CSS.
		$compiled_css = $this->get_cached_contents( $filename, 'uikit' );
		$alert_css    = <<<EOB
.uk-alert-success{background:#f2fae3;color:#659f13}
.uk-alert-warning{background:#fffceb;color:#e28327}
.uk-alert-danger{background:#fff1f0;color:#d85030}
.uk-alert-large{padding:20px}
EOB;
		$this->assertContains( $alert_css, $compiled_css );
	}

	/**
	 * Test _Beans_Uikit::compile() should compile the styles with the child theme.
	 */
	public function test_should_compile_styles_with_child_theme() {
		$theme_path = dirname( __DIR__ ) . '/fixtures/less/';
		beans_uikit_enqueue_theme( 'beans-child', $theme_path );
		beans_uikit_enqueue_components( [ 'alert', 'close', 'panel' ], 'core', false );

		add_filter(
			'beans_uikit_euqueued_styles',
			function( $styles ) use ( $theme_path ) {
				$this->assertSame(
					[
						BEANS_API_PATH . 'uikit/src/less/core/variables.less',
						$theme_path . 'variables.less',
						BEANS_API_PATH . 'uikit/src/less/core/alert.less',
						$theme_path . 'alert.less',
						BEANS_API_PATH . 'uikit/src/less/core/close.less',
						$theme_path . 'close.less',
						BEANS_API_PATH . 'uikit/src/less/core/panel.less',
						$theme_path . 'panel.less',
						BEANS_API_PATH . 'uikit/src/fixes.less',
					],
					$styles
				);

				return $styles;
			},
			9999
		);
		add_filter( 'beans_uikit_euqueued_scripts', '__return_empty_array' );
		Monkey\Functions\expect( 'beans_compile_js_fragments' )->never();

		$this->assertEmpty( $this->get_compiled_filename( $this->compiled_uikit_path ) );
		( new _Beans_Uikit() )->compile();
		$filename = $this->get_compiled_filename( $this->compiled_uikit_path );
		$this->assertFileExists( $this->compiled_uikit_path . $filename );
		$this->assertStringEndsWith( '.css', $filename );

		// Check the compiled CSS.
		$compiled_css = $this->get_cached_contents( $filename, 'uikit' );
		$alert_css    = <<<EOB
.uk-alert{margin-bottom:15px;padding:10px;background:#ccc;color:#2d7091}
EOB;
		$this->assertContains( $alert_css, $compiled_css );

		$alert_css = <<<EOB
.uk-alert-success{background:#627f00;color:#659f13}
.uk-alert-warning{background:#faa732;color:#e28327}
.uk-alert-danger{background:#cc0000;color:#d85030}
.uk-alert-large{padding:40px}
EOB;
		$this->assertContains( $alert_css, $compiled_css );

		$close_css = <<<EOB
.uk-close{-webkit-appearance:none;margin:0;border:none;overflow:visible;font:inherit;color:inherit;text-transform:none;padding:0;background:transparent;display:inline-block;box-sizing:content-box;width:40px;line-height:40px;text-align:center;vertical-align:middle;opacity:0.3;background:#ccc}
EOB;
		$this->assertContains( $close_css, $compiled_css );

		$close_css = <<<EOB
.uk-close:hover,.uk-close:focus{opacity:0.5;outline:none;color:inherit;text-decoration:none;cursor:pointer}
.uk-close-alt{padding:10px;border-radius:50%;background:#cc0000;opacity:1}
EOB;
		$this->assertContains( $close_css, $compiled_css );

		$panel_css = <<<EOB
.uk-panel-body{padding:15px}
.uk-panel-box{padding:15px;background:#cb4b14;color:#444}
.uk-panel-box-hover:hover{color:#444}
EOB;
		$this->assertContains( $panel_css, $compiled_css );
	}

	/**
	 * Test _Beans_Uikit::compile() should compile the scripts.
	 */
	public function test_should_compile_scripts() {
		beans_uikit_enqueue_components(
			[
				'alert',
				'button',
			],
			'core',
			false
		);

		add_filter( 'beans_uikit_euqueued_styles', '__return_empty_array' );
		Monkey\Functions\expect( 'beans_compile_less_fragments' )->never();
		add_filter(
			'beans_uikit_euqueued_scripts',
			function( $scripts ) {
				$this->assertSame(
					[
						BEANS_API_PATH . 'uikit/src/js/core/core.min.js',
						BEANS_API_PATH . 'uikit/src/js/core/utility.min.js',
						BEANS_API_PATH . 'uikit/src/js/core/touch.min.js',
						BEANS_API_PATH . 'uikit/src/js/core/alert.min.js',
						BEANS_API_PATH . 'uikit/src/js/core/button.min.js',
					],
					$scripts
				);

				return $scripts;
			},
			9999
		);

		$this->assertEmpty( $this->get_compiled_filename( $this->compiled_uikit_path ) );
		( new _Beans_Uikit() )->compile();
		$filename = $this->get_compiled_filename( $this->compiled_uikit_path );
		$this->assertFileExists( $this->compiled_uikit_path . $filename );
		$this->assertStringEndsWith( '.js', $filename );

		// Check the compiled JavaScript.
		$compiled_js = $this->get_cached_contents( $filename, 'uikit' );
		$this->assertContains( '!function(t){"use strict";t.component("alert"', $compiled_js );
		$this->assertContains( '!function(t){"use strict";t.component("buttonRadio",', $compiled_js );
		$this->assertContains( '!function(e){function t(e,t,n,o){return Math.abs(e-t)>=Math.abs(n-o)?e-t>0?"Left":"Right"', $compiled_js );
	}
}
