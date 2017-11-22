<?php
/**
 * This file listens to when a test suite starts in order to load the appropriate bootstrap file.
 *
 * @since   1.5.0
 *
 * @package Beans\Framework\Tests
 */

namespace Beans\Framework\Tests;

use PHPUnit_Framework_BaseTestListener;
use PHPUnit_Framework_TestSuite;

/**
 * Class Bootstrap
 *
 * @package Beans\Framework\Tests
 */
class Bootstrap extends PHPUnit_Framework_BaseTestListener {

	/**
	 * Start the test suite.
	 *
	 * @param \PHPUnit_Framework_TestSuite $suite Test suite instance.
	 */
	public function startTestSuite( PHPUnit_Framework_TestSuite $suite ) {

		// Load the integration testsuite's bootstrap file.
		if ( 'integration' === $suite->getName() ) {
			require_once __DIR__ . '/integration-tests/bootstrap.php';
		}
	}
}
