<?php
/**
 * Tests for the process_actions() method of _Beans_Options.
 *
 * @package Beans\Framework\Tests\Integration\API\Options
 *
 * @since   1.5.0
 *
 * phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification -- Nonce verification is not needed in the test
 * suite.
 */

namespace Beans\Framework\Tests\Integration\API\Options;

use _Beans_Options;
use Beans\Framework\Tests\Integration\API\Options\Includes\Options_Test_Case;

require_once dirname( __DIR__ ) . '/includes/class-options-test-case.php';

/**
 * Class Tests_BeansOptions_ProcessActions
 *
 * @package Beans\Framework\Tests\Integration\API\Options
 * @group   api
 * @group   api-options
 */
class Tests_BeansOptions_ProcessActions extends Options_Test_Case {

	/**
	 * Test _Beans_Options::process_actions() should do nothing when it's not a save or reset action.
	 */
	public function test_should_do_nothing_when_no_save_or_reset_action() {
		$instance = new _Beans_Options();

		$this->assertArrayNotHasKey( 'beans_save_options', $_POST );
		$this->assertArrayNotHasKey( 'beans_reset_options', $_POST );
		$this->assertNull( $instance->process_actions() );
		$this->assertFalse( has_action( 'admin_notices', [ $instance, 'render_save_notice' ] ) );
		$this->assertFalse( has_action( 'admin_notices', [ $instance, 'render_reset_notice' ] ) );
	}

	/**
	 * Test _Beans_Options::process_actions() should not save options when the nonce fails.
	 */
	public function test_should_not_save_options_when_nonce_fails() {
		$_POST['beans_save_options'] = 1;
		$test_data                   = [
			'beans_compile_all_styles'  => 1,
			'beans_compile_all_scripts' => 1,
			'beans_dev_mode'            => 1,
		];
		$_POST['beans_fields']       = $test_data;
		$success_property            = $this->get_reflective_property( 'success', '_Beans_Options' );
		$instance                    = new _Beans_Options();

		// Check with no nonce.
		$instance->process_actions();
		$this->assertFalse( $success_property->getValue( $instance ) );

		foreach ( $test_data as $option ) {
			// Check that the value was not saved.
			$this->assertNull( get_option( $option, null ) );
		}

		// Check with an invalid nonce.
		$_POST['beans_options_nonce'] = 'invalid-nonce';

		$instance->process_actions();
		$this->assertFalse( $success_property->getValue( $instance ) );

		foreach ( $test_data as $option ) {
			// Check that the value was not saved.
			$this->assertNull( get_option( $option, null ) );
		}
	}

	/**
	 * Test _Beans_Options::process_actions() should save the field values when a save action is passed.
	 */
	public function test_should_save_field_values_when_save_action() {
		// Set up the test.
		$nonce                        = wp_create_nonce( 'beans_options_nonce' );
		$_POST['beans_options_nonce'] = $nonce;
		$_POST['beans_save_options']  = 1;
		$test_data                    = [
			'beans_compile_all_styles'  => 1,
			'beans_compile_all_scripts' => 1,
			'beans_dev_mode'            => 1,
		];
		$_POST['beans_fields']        = $test_data;

		$success_property = $this->get_reflective_property( 'success', '_Beans_Options' );
		$instance         = new _Beans_Options();
		$instance->process_actions();

		// Check that the success property was set.
		$this->assertTrue( $success_property->getValue( $instance ) );

		// Check the save.
		$this->assertArrayHasKey( 'beans_save_options', $_POST );
		$this->assertEquals( 10, has_action( 'admin_notices', [ $instance, 'render_save_notice' ] ) );

		// Check the reset.
		$this->assertArrayNotHasKey( 'beans_reset_options', $_POST );
		$this->assertFalse( has_action( 'admin_notices', [ $instance, 'render_reset_notice' ] ) );

		foreach ( $test_data as $option => $value ) {
			// Check that the value was saved.
			$this->assertEquals( $value, get_option( $option ) );

			// Clean up.
			delete_option( $option );
		}
	}

	/**
	 * Test _Beans_Options::process_actions() should not reset options when the nonce fails.
	 */
	public function test_should_not_reset_options_when_nonce_fails() {
		$_POST['beans_reset_options'] = 1;
		$test_data                    = [
			'beans_compile_all_styles'  => 1,
			'beans_compile_all_scripts' => 1,
			'beans_dev_mode'            => 1,
		];

		// Add the options.
		foreach ( $test_data as $option => $value ) {
			add_option( $option, $value );
		}

		$_POST['beans_fields'] = $test_data;
		$success_property      = $this->get_reflective_property( 'success', '_Beans_Options' );
		$instance              = new _Beans_Options();

		// Check with no nonce.
		$instance->process_actions();
		$this->assertFalse( $success_property->getValue( $instance ) );

		foreach ( $test_data as $option => $value ) {
			// Check that the value was not reset.
			$this->assertEquals( $value, get_option( $option ) );
		}

		// Check with an invalid nonce.
		$_POST['beans_options_nonce'] = 'invalid-nonce';

		$instance->process_actions();
		$this->assertFalse( $success_property->getValue( $instance ) );

		foreach ( $test_data as $option => $value ) {
			// Check that the value was not reset.
			$this->assertEquals( $value, get_option( $option ) );

			// Clean up.
			delete_option( $option );
		}
	}

	/**
	 * Test _Beans_Options::process_actions() should delete the options when it's a reset action.
	 */
	public function test_should_delete_options_when_reset_action() {
		// Set up the test.
		$nonce                        = wp_create_nonce( 'beans_options_nonce' );
		$_POST['beans_options_nonce'] = $nonce;
		$_POST['beans_reset_options'] = 1;
		$test_data                    = [
			'beans_compile_all_styles'  => 1,
			'beans_compile_all_scripts' => 1,
			'beans_dev_mode'            => 1,
		];
		$_POST['beans_fields']        = $test_data;

		// Add the options.
		foreach ( $test_data as $option => $value ) {
			add_option( $option, $value );
		}

		$success_property = $this->get_reflective_property( 'success', '_Beans_Options' );
		$instance         = new _Beans_Options();
		$instance->process_actions();

		// Check that the success property was set.
		$this->assertTrue( $success_property->getValue( $instance ) );

		// Check the reset.
		$this->assertArrayHasKey( 'beans_options_nonce', $_POST );
		$this->assertEquals( 10, has_action( 'admin_notices', [ $instance, 'render_reset_notice' ] ) );

		// Check the save.
		$this->assertArrayNotHasKey( 'beans_save_options', $_POST );
		$this->assertFalse( has_action( 'admin_notices', [ $instance, 'render_save_notice' ] ) );

		// Check that the option was deleted.
		foreach ( $test_data as $option ) {
			$this->assertNull( get_option( $option, null ) );
		}
	}
}
