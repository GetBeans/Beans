<?php
/**
 * Test Case for the integration tests.
 *
 * @package Beans\Framework\Tests\Integration
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration;

use Brain\Monkey;
use WP_UnitTestCase;

/**
 * Abstract Class Test_Case
 *
 * @package Beans\Framework\Tests\Integration
 */
abstract class Test_Case extends WP_UnitTestCase {

	/**
	 * Reset flag.
	 *
	 * @var bool
	 */
	protected $was_reset = false;

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();
		Monkey\setUp();

		if ( ! $this->was_reset ) {
			$this->reset_fields_container();
			$this->reset_actions_container();
			$this->was_reset = true;
		}
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {
		$this->reset_fields_container();
		$this->reset_actions_container();

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
	 * @param string $property   Property name for which to gain access.
	 * @param string $class_name Name of the target class.
	 *
	 * @return \ReflectionProperty|string
	 * @throws \ReflectionException Throws an exception if property does not exist.
	 */
	protected function get_reflective_property( $property, $class_name ) {
		$class    = new \ReflectionClass( $class_name );
		$property = $class->getProperty( $property );
		$property->setAccessible( true );

		return $property;
	}
}
