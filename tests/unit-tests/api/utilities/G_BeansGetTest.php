<?php

namespace Beans\BeansFramework\Tests\API\Utilities;

use Brain\Monkey\Functions;
use Beans\BeansFramework\Tests\BeansTestCase;

class G_BeansGetTest extends BeansTestCase {

	protected function setUp() {
		parent::setUp();
		// Make sure the file is loaded first.
		require_once BEANS_TESTS_LIB_DIR . '/api/utilities/functions.php';
	}

	public function testReturnsDefault() {
		$this->assertEquals( 10, beans_get( 'foo', 'bar', 10 ) );
		$this->assertNull( beans_get( 'foo', array( 'oof' => 'found me' ) ) );
		$this->assertNull( beans_get( 'foo', array( 10, 'bar', 'baz' ) ) );
		$this->assertFalse( beans_get( 'foo', (object) array( 'bar', 'baz' ), false ) );
	}

	public function testFindsNeedle() {
		$this->assertEquals( 'bar', beans_get( 0, 'bar', 10 ) );
		$this->assertEquals( 'found me', beans_get( 'foo', array( 'foo' => 'found me' ), 10 ) );
		$this->assertEquals( 'red', beans_get( 0, array( 'baz' => 'zab', 'rab' => 'bar', 'red' ) ) );
		$this->assertEquals( 'zab', beans_get( 'baz', array( 'baz' => 'zab', 'rab' => 'bar', 'red' ) ) );

		$this->assertEquals( 'found me', beans_get( 'foo', (object) array( 'foo' => 'found me' ), 10 ) );
		$this->assertEquals( 'red', beans_get( 0, (object) array( 'baz' => 'zab', 'rab' => 'bar', 'red' ) ) );
		$this->assertEquals( 'zab', beans_get( 'baz', (object) array( 'baz' => 'zab', 'rab' => 'bar', 'red' ) ) );

	}
}
