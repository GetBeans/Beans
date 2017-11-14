<?php

namespace Beans\BeansFramework\Tests\API\Utilities;

use Beans\BeansFramework\Tests\BeansTestCase;

class I_CountRecursiveTest extends BeansTestCase {

	protected function setUp() {
		parent::setUp();
		// Make sure the file is loaded first.
		require_once BEANS_TESTS_LIB_DIR . '/api/utilities/functions.php';
	}

	public function testReturnsZero() {
		$this->assertEquals( 0, beans_count_recursive( 'not-an-array' ) );
		$this->assertEquals( 0, beans_count_recursive( new \stdClass() ) );
		$this->assertEquals( 0, beans_count_recursive( 10 ) );
	}

	public function testDepthOne() {
		$this->assertEquals( 2, beans_count_recursive( array( 'green' => 'grass', 'blue' => 'sky' ), 1 ) );
		$this->assertEquals( 3, beans_count_recursive( array( 10, 'bar', 'baz' ), 1 ) );
		$this->assertEquals( 1, beans_count_recursive( array( 'hi there' ), 1 ) );
	}

	public function testNonNumericDepth() {
		$this->assertEquals( 1, beans_count_recursive( array( 'oof' => 'found me' ), 'hi' ) );
		$this->assertEquals( 3, beans_count_recursive( array( 10, 'bar', 'baz' ), null ) );
		$this->assertEquals( 2, beans_count_recursive( array( 'green' => 'grass', 'blue' => 'sky' ) ) );
		$this->assertEquals( 10, beans_count_recursive( array( 1, 2, 3, 4, 5, array( 6, 7, 8, 9 ) ) ) );
	}


	public function testCount() {
		$this->assertEquals( 6, beans_count_recursive( array( 1, 2, 3, 4, 5, array( 6, 7, 8, 9 ) ), 1 ) );
		$this->assertEquals( 15, beans_count_recursive( array( 1, 2, 3, 4, 5, array( 6, 7, 8, 9 ) ), 2 ) );
		$this->assertEquals( 18, beans_count_recursive( array(
			array( 1, 2, ),
			3,
			array(
				4,
				5,
				array( 6, 7, 8, 9, array( 10, 11 ) ),
			),
		), 3 ) );
	}
}
