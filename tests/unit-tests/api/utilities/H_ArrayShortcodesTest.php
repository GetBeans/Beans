<?php

namespace Beans\BeansFramework\Tests\API\Utilities;

use Brain\Monkey\Functions;
use Beans\BeansFramework\Tests\BeansTestCase;

class H_ArrayShortcodesTest extends BeansTestCase {

	protected function setUp() {
		parent::setUp();
		// Make sure the file is loaded first.
		require_once BEANS_TESTS_LIB_DIR . '/api/utilities/functions.php';
	}

	public function testThrowError() {
		$this->expectException(\TypeError::class);
		$this->assertTrue( beans_multi_array_key_exists( 0, 'bar' ) );
	}

	public function testReturnsTrue() {
		$this->assertTrue( beans_multi_array_key_exists( 'oof', array( 'oof' => 'found me' ) ) );
		$this->assertTrue( beans_multi_array_key_exists( 1, array( 10, 'bar', 'baz' ) ) );
		$this->assertTrue( beans_multi_array_key_exists( 'blue', array( 'green' => 'grass', 'blue' => 'sky' ) ) );
	}

	public function testReturnsFalse() {
		$this->assertFalse( beans_multi_array_key_exists( 'foo', array( 'oof' => 'found me' ) ) );
		$this->assertFalse( beans_multi_array_key_exists( 'bar', array( 10, 'bar', 'baz' ) ) );
		$this->assertFalse( beans_multi_array_key_exists( 'red', array( 'green' => 'grass', 'blue' => 'sky' ) ) );
	}

	public function testReturnsTrueInMultidimensional() {
		$this->assertTrue( beans_multi_array_key_exists( 'zab', array( 'bar', array( 'zab' => 'foo' ) ) ) );
		$this->assertTrue( beans_multi_array_key_exists( 'wordpress', array(
				'bar',
				'skill' => array(
					'javascript' => true,
					'php'        => true,
					'sql'        => true,
					'wordpress'  => true,
				),
			) )
		);
	}
}
