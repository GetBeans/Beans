<?php

namespace Beans\Framework\Tests\IntegrationTests;

/**
 * If BEANS_INTEGRATION_TESTS_DIR is not defined, then the integration tests are not bootstrapped yet.
 * This happens when phpunit tests up, but before the test suites are started.
 */
if ( ! defined( 'BEANS_INTEGRATION_TESTS_DIR' ) ) {
	return;
}

use WP_UnitTestCase;

class Test_Integration_Sample extends WP_UnitTestCase {
	public function test_sample() {
		$this->assertTrue( true );
	}
}
