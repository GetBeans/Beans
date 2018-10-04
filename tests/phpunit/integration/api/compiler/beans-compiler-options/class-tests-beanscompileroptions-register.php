<?php
/**
 * Tests for the register() method of _Beans_Compiler_Options.
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Compiler;

use _Beans_Compiler_Options;
use Beans\Framework\Tests\Integration\API\Compiler\Includes\Compiler_Options_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-compiler-options-test-case.php';

/**
 * Class Tests_BeansCompilerOptions_Register
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansCompilerOptions_Register extends Compiler_Options_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();
		require_once BEANS_THEME_DIR . '/lib/api/options/functions.php';
	}

	/**
	 * Test _Beans_Compiler_Options::register() should not register when not on the Beans Settings page.
	 */
	public function test_should_not_register_when_not_on_beans_settings_page() {
		set_current_screen( 'edit' );
		$this->assertNull( ( new _Beans_Compiler_Options() )->register() );
	}

	/**
	 * Test _Beans_Compiler_Options::register() should not register when not is_admin(), i.e. not in the backend.
	 */
	public function test_should_not_register_when_not_is_admin() {
		set_current_screen( 'front' );
		$_GET['page'] = 'beans_settings';
		$this->assertFalse( is_admin() );
		$this->assertNull( ( new _Beans_Compiler_Options() )->register() );
	}

	/**
	 * Test _Beans_Compiler_Options::register() should register only the flush button when the styles and scripts are
	 * not supported.
	 */
	public function test_should_only_register_only_flush_button_when_styles_scripts_not_supported() {
		$this->go_to_settings_page();

		// Set up the tests by removing these API components.
		beans_remove_api_component_support( 'wp_styles_compiler' );
		beans_remove_api_component_support( 'wp_scripts_compiler' );
		$this->assertFalse( beans_get_component_support( 'wp_styles_compiler' ) );
		$this->assertFalse( beans_get_component_support( 'wp_scripts_compiler' ) );

		// Run the registration.
		$this->assertTrue( ( new _Beans_Compiler_Options() )->register() );

		// Check that the fields did get registered.
		$registered_fields = beans_get_fields( 'option', 'compiler_options' );
		$this->assertNotEmpty( $registered_fields );
		$this->assertCount( 1, $registered_fields );
		$this->assertArraySubset(
			[
				'id'   => 'beans_compiler_items',
				'type' => 'flush_cache',
			],
			current( $registered_fields )
		);

		// Check that the metabox did get registered.
		global $wp_meta_boxes;
		$this->assertArrayHasKey( 'compiler_options', $wp_meta_boxes['beans_settings']['normal']['default'] );
		$this->assertEquals( 'Compiler options', $wp_meta_boxes['beans_settings']['normal']['default']['compiler_options']['title'] );
	}

	/**
	 * Test _Beans_Compiler_Options::register() should not register the styles options when not supported.
	 */
	public function test_should_not_register_styles_options_when_not_supported() {
		$this->go_to_settings_page();

		// Set up API components.
		beans_remove_api_component_support( 'wp_styles_compiler' );
		beans_add_api_component_support( 'wp_scripts_compiler' );
		$this->assertFalse( beans_get_component_support( 'wp_styles_compiler' ) );
		$this->assertNotEmpty( beans_get_component_support( 'wp_scripts_compiler' ) );

		// Run the registration.
		$this->assertTrue( ( new _Beans_Compiler_Options() )->register() );

		// Check that the right fields did get registered.
		$registered_fields = beans_get_fields( 'option', 'compiler_options' );
		$this->assertCount( 2, $registered_fields );
		$this->assertArraySubset(
			[
				'id'   => 'beans_compiler_items',
				'type' => 'flush_cache',
			],
			current( $registered_fields )
		);
		$this->assertArraySubset(
			[
				'id'    => 'beans_compile_all_scripts_group',
				'label' => 'Compile all WordPress scripts',
				'type'  => 'group',
			],
			next( $registered_fields )
		);

		// Check that the metabox did get registered.
		global $wp_meta_boxes;
		$this->assertArrayHasKey( 'compiler_options', $wp_meta_boxes['beans_settings']['normal']['default'] );
		$this->assertEquals( 'Compiler options', $wp_meta_boxes['beans_settings']['normal']['default']['compiler_options']['title'] );
	}

	/**
	 * Test _Beans_Compiler_Options::register() should not register the scripts options when not supported.
	 */
	public function test_should_not_register_scripts_options_when_not_supported() {
		$this->go_to_settings_page();

		// Set up API components.
		beans_add_api_component_support( 'wp_styles_compiler' );
		beans_remove_api_component_support( 'wp_scripts_compiler' );
		$this->assertNotEmpty( beans_get_component_support( 'wp_styles_compiler' ) );
		$this->assertFalse( beans_get_component_support( 'wp_scripts_compiler' ) );

		// Run the registration.
		$this->assertTrue( ( new _Beans_Compiler_Options() )->register() );

		// Check that the right fields did get registered.
		$registered_fields = beans_get_fields( 'option', 'compiler_options' );
		$this->assertCount( 2, $registered_fields );
		$this->assertArraySubset(
			[
				'id'   => 'beans_compiler_items',
				'type' => 'flush_cache',
			],
			current( $registered_fields )
		);
		$this->assertArraySubset(
			[
				'id'             => 'beans_compile_all_styles',
				'label'          => 'Compile all WordPress styles',
				'checkbox_label' => 'Select to compile styles.',
				'type'           => 'checkbox',
				'default'        => false,
			],
			next( $registered_fields )
		);

		// Check that the metabox did get registered.
		global $wp_meta_boxes;
		$this->assertArrayHasKey( 'compiler_options', $wp_meta_boxes['beans_settings']['normal']['default'] );
		$this->assertEquals( 'Compiler options', $wp_meta_boxes['beans_settings']['normal']['default']['compiler_options']['title'] );
	}

	/**
	 * Test _Beans_Compiler_Options::register() should register all options when styles and scripts are supported.
	 */
	public function test_should_register_all_options_when_styles_scripts_supported() {
		$this->go_to_settings_page();

		// Set up API components.
		beans_add_api_component_support( 'wp_styles_compiler' );
		beans_add_api_component_support( 'wp_scripts_compiler' );
		$this->assertNotEmpty( beans_get_component_support( 'wp_styles_compiler' ) );
		$this->assertNotEmpty( beans_get_component_support( 'wp_scripts_compiler' ) );

		// Run the registration.
		$this->assertTrue( ( new _Beans_Compiler_Options() )->register() );

		// Check that the right fields did get registered.
		$registered_fields = beans_get_fields( 'option', 'compiler_options' );
		$this->assertCount( 3, $registered_fields );
		$this->assertArraySubset(
			[
				'id'   => 'beans_compiler_items',
				'type' => 'flush_cache',
			],
			current( $registered_fields )
		);
		$this->assertArraySubset(
			[
				'id'             => 'beans_compile_all_styles',
				'label'          => 'Compile all WordPress styles',
				'checkbox_label' => 'Select to compile styles.',
				'type'           => 'checkbox',
				'default'        => false,
			],
			next( $registered_fields )
		);
		$this->assertArraySubset(
			[
				'id'    => 'beans_compile_all_scripts_group',
				'label' => 'Compile all WordPress scripts',
				'type'  => 'group',
			],
			next( $registered_fields )
		);

		// Check that the metabox did get registered.
		global $wp_meta_boxes;
		$this->assertArrayHasKey( 'compiler_options', $wp_meta_boxes['beans_settings']['normal']['default'] );
		$this->assertEquals( 'Compiler options', $wp_meta_boxes['beans_settings']['normal']['default']['compiler_options']['title'] );
	}
}
