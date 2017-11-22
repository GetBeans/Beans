<?php
/**
 * Sample integration test.  This file exists as a template for our integration tests.
 *
 * Note: Once there are integration tests, this file will be removed.  Until then, this file ensures test suites
 * do not cause an error.
 *
 * @package Beans\Framework\Tests\IntegrationTests
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\IntegrationTests;

/**
 * If BEANS_INTEGRATION_TESTS_DIR is not defined, then the integration tests are not bootstrapped yet.
 * This happens when phpunit tests up, but before the test suites are started.
 */
if ( ! defined( 'BEANS_INTEGRATION_TESTS_DIR' ) ) {
	return;
}

use WP_UnitTestCase;

/**
 * Class Test_Integration_Sample
 *
 * @package Beans\Framework\Tests\IntegrationTests
 */
class Test_Integration_Sample extends WP_UnitTestCase {

	/**
	 * Sample test.  Will be replaced by real tests.
	 */
	public function test_sample() {
		$this->assertTrue( true );
	}
}
