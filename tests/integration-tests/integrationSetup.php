<?php

/**
 * DO NOT REMOVE THIS FILE!
 *
 * This file is needed to trigger the PHPUnit's testsuite listener.  The listener fires once PHPUnit has discovered
 * there are tests to run in the integration folder.  That listener then loads the bootstrap when:
 *
 * `phpunit --testsuite integration`
 *
 * @package Beans\Framework\Tests\IntegrationTests
 */

namespace Beans\Framework\Tests\IntegrationTests;

use PHPUnit\Framework\TestCase;

/**
 * Class Integration_Sample
 *
 * @package Beans\Framework\Tests\IntegrationTests
 */
class Tests_Integration_Setup extends TestCase {
	public function test_trigger_the_listener() {
		$this->assertTrue( true );
	}
}
