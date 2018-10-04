<?php
/**
 * Test Case for Beans' WP Customize API unit tests.
 *
 * @package Beans\Framework\Tests\Unit\API\WP_Customize\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\WP_Customize\Includes;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey;
use Mockery;

/**
 * Abstract Class WP_Customize_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\WP_Customize\Includes
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
	 * Mocked WP Customizer Manager object.
	 *
	 * @var WP_Customize_Manager
	 */
	protected $wp_customize_mock;

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();

		$this->load_original_functions(
			[
				'api/wp-customize/functions.php',
				'api/wp-customize/class-beans-wp-customize.php',
				'api/wp-customize/class-beans-wp-customize-control.php',
			]
		);

		$this->setup_common_wp_stubs();

		$this->wp_customize_mock = Mockery::mock( 'WP_Customize_Manager' );
		global $wp_customize;
		$wp_customize = $this->wp_customize_mock; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited -- Limited to test function scope.
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
		$merged_field         = array_merge(
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
		$merged_field['name'] = $field['id'];

		if ( $set_value ) {
			$merged_field['value'] = $field['default'];
		}

		return $merged_field;
	}
}
