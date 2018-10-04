<?php
/**
 * Resets the Beans framework back to its original starting point for each test.
 *
 * @package Beans\Framework\Tests
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests;

use Beans_Fields;

/**
 * Beans Resetter
 *
 * @package Beans\Framework\Tests
 */
class Beans_Resetter {

	use Test_Case_Trait;

	/**
	 * Reset Beans back to its original starting point.
	 */
	public function reset() {
		$this->reset_global_state();

		// Reset APIs.
		$this->reset_components();
		$this->reset_actions_api();
		$this->reset_compiler_api();
		$this->reset_fields_api();
	}

	/**
	 * Reset the global state.
	 */
	protected function reset_global_state() {
		$_GET  = [];
		$_POST = [];
		unset( $GLOBALS['current_screen'] );
	}

	/**
	 * Resets the Beans' API Components.
	 */
	protected function reset_components() {

		if ( ! function_exists( 'beans_get_component_support' ) ) {
			return;
		}

		global $_beans_api_components_support;
		$_beans_api_components_support = [];
	}

	/**
	 * Reset the Actions API.
	 */
	protected function reset_actions_api() {
		global $_beans_registered_actions;
		$_beans_registered_actions = [
			'added'    => [],
			'modified' => [],
			'removed'  => [],
			'replaced' => [],
		];
	}

	/**
	 * Reset the Compiler API.
	 */
	protected function reset_compiler_api() {
		global $_beans_compiler_added_fragments;
		$_beans_compiler_added_fragments = [
			'css'  => [],
			'less' => [],
			'js'   => [],
		];

		unset( $GLOBALS['wp_filesystem'] );
	}

	/**
	 * Reset the Fields API.
	 */
	protected function reset_fields_api() {

		if ( ! class_exists( '_Beans_Fields' ) ) {
			return;
		}

		// Reset the "registered" container.
		$registered = $this->get_reflective_property( 'registered', '_Beans_Fields' );
		$registered->setValue( new _Beans_Fields(), [
			'option'       => [],
			'post_meta'    => [],
			'term_meta'    => [],
			'wp_customize' => [],
		] );

		// Reset the other static properties.
		foreach ( [ 'field_types_loaded', 'field_assets_hook_loaded' ] as $property_name ) {
			$property = $this->get_reflective_property( $property_name, '_Beans_Fields' );
			$property->setValue( new _Beans_Fields(), [] );
		}
	}
}
