<?php

namespace Beans\BeansFramework\Tests;

use Brain\Monkey;
use PHPUnit\Framework\TestCase;

abstract class Beans_Test_Case extends TestCase {

	/**
	 * Prepares the test environment before each test.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function setUp() {
		parent::setUp();
		Monkey\setUp();

		require_once __DIR__ . '/polyfills/functions.php';
	}

	/**
	 * Cleans up the test environment after each test.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function tearDown() {
		Monkey\tearDown();
		parent::tearDown();
	}
}
