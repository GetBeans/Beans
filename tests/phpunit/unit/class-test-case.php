<?php
/**
 * Test Case for the unit tests.
 *
 * @package Beans\Framework\Tests\Unit
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit;

use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

/**
 * Abstract Class Test_Case
 *
 * @package Beans\Framework\Tests\Unit
 */
abstract class Test_Case extends TestCase {

	/**
	 * When true, return the given path when doing wp_normalize_path().
	 *
	 * @var bool
	 */
	protected $just_return_path = false;

	/**
	 * Reset flag.
	 *
	 * @var bool
	 */
	protected $was_reset = false;

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();
		Monkey\setUp();

		Functions\when( 'wp_normalize_path' )->alias( function( $path ) {

			if ( true === $this->just_return_path ) {
				return $path;
			}

			$path = str_replace( '\\', '/', $path );
			$path = preg_replace( '|(?<=.)/+|', '/', $path );

			if ( ':' === substr( $path, 1, 1 ) ) {
				$path = ucfirst( $path );
			}

			return $path;
		} );

		Functions\when( 'wp_json_encode' )->alias( function( $array ) {
			return json_encode( $array ); // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode -- Required as part of our mock.
		} );
	}

	/**
	 * Setup the stubs for the common WordPress escaping and internationalization functions.
	 */
	protected function setup_common_wp_stubs() {
		// Common escaping functions.
		Monkey\Functions\stubs( array(
			'esc_attr',
			'esc_html',
			'esc_textarea',
			'esc_url',
			'wp_kses_post',
		) );

		// Common internationalization functions.
		Monkey\Functions\stubs( array(
			'__',
			'esc_html__',
			'esc_html_x',
			'esc_attr_x',
		) );

		foreach ( array( 'esc_attr_e', 'esc_html_e', '_e' ) as $wp_function ) {
			Monkey\Functions\when( $wp_function )->echoArg();
		}

		if ( ! $this->was_reset ) {
			$this->reset_actions_container();
			$this->reset_fields_container();
			$this->was_reset = true;
		}
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	protected function tearDown() {
		$this->reset_actions_container();
		$this->reset_fields_container();

		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Load the original Beans' functions into memory before we start.
	 *
	 * Then in our tests, we monkey patch via Brain Monkey, which redefines the original function.
	 * At tear down, the original function is restored in Brain Monkey, by calling Patchwork\restoreAll().
	 *
	 * @since 1.5.0
	 *
	 * @param array $files Array of files to load into memory.
	 *
	 * @return void
	 */
	protected function load_original_functions( array $files ) {

		foreach ( $files as $file ) {
			require_once BEANS_TESTS_LIB_DIR . $file;
		}
	}

	/**
	 * Format the HTML by stripping out the whitespace between the HTML tags and then putting each tag on a separate
	 * line.
	 *
	 * Why? We can then compare the actual vs. expected HTML patterns without worrying about tabs, new lines, and extra
	 * spaces.
	 *
	 * @since 1.5.0
	 *
	 * @param string $html HTML to strip.
	 *
	 * @return string
	 */
	protected function format_the_html( $html ) {
		$html = trim( $html );

		// Strip whitespace between the tags.
		$html = preg_replace( '/(\>)\s*(\<)/m', '$1$2', $html );

		// Strip whitespace at the end of a tag.
		$html = preg_replace( '/(\>)\s*/m', '$1$2', $html );

		// Strip whitespace at the start of a tag.
		$html = preg_replace( '/\s*(\<)/m', '$1$2', $html );

		return str_replace( '>', ">\n", $html );
	}

	/**
	 * Reset the Actions API container.
	 */
	protected function reset_actions_container() {
		global $_beans_registered_actions;
		$_beans_registered_actions = array(
			'added'    => array(),
			'modified' => array(),
			'removed'  => array(),
			'replaced' => array(),
		);
	}

	/**
	 * Reset the Fields API container, i.e. static memories.
	 */
	protected function reset_fields_container() {
		if ( ! class_exists( '_Beans_Fields' ) ) {
			return;
		}
		// Reset the "registered" container.
		$registered = $this->get_reflective_property( 'registered', '_Beans_Fields' );
		$registered->setValue( new \_Beans_Fields(), array(
			'option'       => array(),
			'post_meta'    => array(),
			'term_meta'    => array(),
			'wp_customize' => array(),
		) );
		// Reset the other static properties.
		foreach ( array( 'field_types_loaded', 'field_assets_hook_loaded' ) as $property_name ) {
			$property = $this->get_reflective_property( $property_name, '_Beans_Fields' );
			$property->setValue( new \_Beans_Fields(), array() );
		}
	}

	/**
	 * Get reflective access to the private method.
	 *
	 * @since 1.5.0
	 *
	 * @param string $method_name Method name for which to gain access.
	 * @param string $class_name  Name of the target class.
	 *
	 * @return \ReflectionMethod
	 * @throws \ReflectionException Throws an exception if method does not exist.
	 */
	protected function get_reflective_method( $method_name, $class_name ) {
		$class  = new \ReflectionClass( $class_name );
		$method = $class->getMethod( $method_name );
		$method->setAccessible( true );

		return $method;
	}

	/**
	 * Get reflective access to the private property.
	 *
	 * @since 1.5.0
	 *
	 * @param string       $property Property name for which to gain access.
	 * @param string|mixed $class    Class name or instance.
	 *
	 * @return \ReflectionProperty|string
	 * @throws \ReflectionException Throws an exception if property does not exist.
	 */
	protected function get_reflective_property( $property, $class ) {
		$class    = new \ReflectionClass( $class );
		$property = $class->getProperty( $property );
		$property->setAccessible( true );

		return $property;
	}

	/**
	 * Get the value of the private or protected property.
	 *
	 * @since 1.5.0
	 *
	 * @param string $property   Property name for which to gain access.
	 * @param string $class_name Class name.
	 *
	 * @return mixed
	 * @throws \ReflectionException Throws an exception if property does not exist.
	 */
	protected function get_reflective_property_value( $property, $class_name ) {
		$reflective = $this->get_reflective_property( $property, $class_name );

		return $reflective->getValue( new $class_name() );
	}

	/**
	 * Set the value of a property or private property.
	 *
	 * @since 1.5.0
	 *
	 * @param mixed  $value    The value to set for the property.
	 * @param string $property Property name for which to gain access.
	 * @param mixed  $instance Instance of the target object.
	 *
	 * @return \ReflectionProperty|string
	 * @throws \ReflectionException Throws an exception if property does not exist.
	 */
	protected function set_reflective_property( $value, $property, $instance ) {
		$property = $this->get_reflective_property( $property, $instance );
		$property->setValue( $instance, $value );
		$property->setAccessible( false );

		return $property;
	}
}
