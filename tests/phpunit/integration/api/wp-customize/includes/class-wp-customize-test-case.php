<?php
/**
 * Test Case for Beans' WP Customize API integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\WP_Customize\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\WP_Customize\Includes;

use Beans\Framework\Tests\Integration\Test_Case;
use WP_Customize_Manager;

require_once dirname( dirname( dirname( getcwd() ) ) ) . '/wp-includes/class-wp-customize-manager.php';

/**
 * Abstract Class WP_Customize_Test_Case
 *
 * @package Beans\Framework\Tests\Integration\API\WP_Customize\Includes
 */
abstract class WP_Customize_Test_Case extends Test_Case {

	/**
	 * An array of test data.
	 *
	 * @var array
	 */
	protected static $test_data;

	/**
	 * Set up the test before we run the test setups.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		static::$test_data = require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fixtures/test-fields.php';
	}

	/**
	 * WP Customizer Manager object.
	 *
	 * @var WP_Customize_Manager
	 */
	protected $wp_customize;

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		require_once BEANS_THEME_DIR . '/lib/api/wp-customize/class-beans-wp-customize.php';

		global $wp_customize;
		$this->wp_customize = new WP_Customize_Manager();
		$wp_customize       = $this->wp_customize; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited -- Limited to test function scope.
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {
		global $wp_customize;
		$wp_customize = null; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited -- Limited to test function scope.

		parent::tearDown();
	}

	/**
	 * Merge the given field with the default structure.
	 *
	 * @since 1.5.0
	 *
	 * @param array $field     The given field to merge.
	 * @param bool  $set_value Optional. When true, sets the "value" to the "default".
	 *
	 * @return array
	 */
	protected function merge_field_with_default( array $field, $set_value = true ) {
		$field         = array_merge(
			[
				'label'       => false,
				'description' => false,
				'default'     => false,
				'context'     => 'wp_customize',
				'attributes'  => [
					'data-customize-setting-link' => $field['id'],
				],
				'db_group'    => false,
			],
			$field
		);
		$field['name'] = $field['id'];

		if ( 'group' === $field['type'] ) {

			foreach ( $field['fields'] as $index => $_field ) {
				$field['fields'][ $index ] = $this->merge_field_with_default( $_field, $set_value );
			}
		} elseif ( $set_value ) {
			$field['value'] = $field['default'];
		}

		return $field;
	}
}
