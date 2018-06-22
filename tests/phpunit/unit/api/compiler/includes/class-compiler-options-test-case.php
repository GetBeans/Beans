<?php
/**
 * Test Case for Beans Compiler API Options unit tests.
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Compiler\Includes;

require_once __DIR__ . '/class-base-test-case.php';

/**
 * Abstract Class Compiler_Options_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\Compiler\Includes
 */
abstract class Compiler_Options_Test_Case extends Base_Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->load_original_functions( [
			'api/compiler/class-beans-compiler-options.php',
			'api/options/functions.php',
		] );

		$this->setup_common_wp_stubs();
	}
}
