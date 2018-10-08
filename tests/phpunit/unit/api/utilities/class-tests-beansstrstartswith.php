<?php
/**
 * Tests for beans_str_starts_with()
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Utilities;

use Beans\Framework\Tests\Unit\Test_Case;

/**
 * Class Tests_BeansStartsWith
 *
 * @package Beans\Framework\Tests\Unit\API\Utilities
 * @group   api
 * @group   api-utilities
 */
class Tests_BeansStrStartsWith extends Test_Case {

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		require_once BEANS_TESTS_LIB_DIR . 'api/utilities/functions.php';
	}

	/**
	 * Test beans_str_starts_with() should return false when case does not match.
	 */
	public function test_should_return_false_when_case_does_not_match() {
		$this->assertFalse( beans_str_starts_with( 'Foo', 'f' ) );
		$this->assertFalse( beans_str_starts_with( 'Checking the Case?', 'check' ) );
		$this->assertFalse(
			beans_str_starts_with(
				'WordPress Community Rocks!',
				[
					'wordpress',
					'wordPress',
					'WORDPRESS',
				]
			)
		);
	}

	/**
	 * Test beans_str_starts_with() should return true when the case matches.
	 */
	public function test_should_return_true_when_case_matches() {
		$this->assertTrue( beans_str_starts_with( 'Foo', 'F' ) );
		$this->assertTrue( beans_str_starts_with( 'Checking the Case?', 'Checking' ) );
		$this->assertTrue(
			beans_str_starts_with(
				'WordPress Community Rocks!',
				[
					'wordpress',
					'WordPress',
					'WORDPRESS',
				]
			)
		);
	}

	/**
	 * Test beans_str_starts_with() should correctly identify the starting string pattern.
	 */
	public function test_should_correctly_identify_starting_string_pattern() {
		$this->assertTrue( beans_str_starts_with( 'This is a string test.', 'This is' ) );
		$this->assertFalse( beans_str_starts_with( 'This is a string test.', 'this is' ) );
		$this->assertTrue( beans_str_starts_with( 'This is a string test.', 'This is a ' ) );
		$this->assertFalse( beans_str_starts_with( 'Really dig the Beans framework! ', 'dig' ) );
		$this->assertTrue( beans_str_starts_with( 'Really dig the Beans framework! ', 'Really ' ) );
		$this->assertTrue( beans_str_starts_with( 'The WordPress Community Rocks!', 'The W' ) );
		$this->assertFalse( beans_str_starts_with( 'The WordPress Community Rocks!', ' The ' ) );
		$this->assertTrue( beans_str_starts_with( ' This is a string test ', ' ' ) );
		$this->assertFalse( beans_str_starts_with( '. This is a string test', '.This' ) );
		$this->assertTrue( beans_str_starts_with( 'SomeClass::someMethod', 'SomeClass::someMethod' ) );
		$this->assertFalse( beans_str_starts_with( 'Fulcrum\Extender\Str\StrChecker::startsWith', 'startsWith' ) );
	}

	/**
	 * Test beans_str_starts_with() should correctly identify when a different data type is given.
	 */
	public function test_should_correctly_identify_when_different_data_type_given() {
		$this->assertTrue( beans_str_starts_with( 104, '1' ) );
		$this->assertFalse( beans_str_starts_with( 1247.86, '1248' ) );
		$this->assertTrue( beans_str_starts_with( 1247.86, '1247.' ) );
		$this->assertTrue( beans_str_starts_with( 85.3002, '85.' ) );
		$this->assertFalse( beans_str_starts_with( -85.3002, '85.' ) );
		$this->assertTrue( beans_str_starts_with( 0, '0' ) );
		$this->assertTrue( beans_str_starts_with( 0.213, '0.2' ) );

		$this->assertFalse( beans_str_starts_with( false, 'ls' ) );
		$this->assertTrue( beans_str_starts_with( true, '1' ) );
		$this->assertFalse( beans_str_starts_with( false, '0' ) );
	}

	/**
	 * Test beans_str_starts_with() should correctly identify when an array of needles is given.
	 */
	public function test_should_correctly_identify_when_array_of_needles_given() {
		$this->assertTrue(
			beans_str_starts_with(
				'This is a string test',
				[ 'this', ' this', ' This', 'This' ]
			)
		);
		$this->assertFalse( beans_str_starts_with( 'This is a string test', [ 'These', 'are' ] ) );
		$this->assertTrue(
			beans_str_starts_with(
				'.... This is a string test',
				[
					'... This',
					'this',
					'. This',
					'.... This',
				]
			)
		);
		$this->assertFalse(
			beans_str_starts_with(
				'... this is a string test',
				[
					'this',
					' this',
					'.. this',
					'...this',
				]
			)
		);
		$this->assertTrue( beans_str_starts_with( 'Hello from Tonya', [ 'Hello', 'Tonya' ] ) );
		$this->assertFalse( beans_str_starts_with( 'Hello from Tonya', [ 'hello', 'From' ] ) );
		$this->assertTrue(
			beans_str_starts_with(
				'WordPress Community Rocks!',
				[ 'wordpress', 'wordPress', 'WordPress', 'WORDPRESS' ]
			)
		);
		$this->assertFalse(
			beans_str_starts_with(
				'The WordPress Community Rocks!',
				[ 'the', 'WordPress', 'Community', 'Rocks!' ]
			)
		);
		$this->assertTrue(
			beans_str_starts_with(
				'The WordPress Community Rocks!',
				[ 'The WordPress c', 'The WordPress' ]
			)
		);
	}

	/**
	 * Test beans_str_starts_with() should correctly identify when non-Latin is given.
	 */
	public function test_should_correctly_identify_when_non_latin_given() {
		$string = 'Τάχιστη αλώπηξ βαφής ψημένη γη, δρασκελίζει υπέρ νωθρού κυνός';

		$this->assertTrue( beans_str_starts_with( $string, 'Τάχιστη' ) );
		$this->assertFalse( beans_str_starts_with( $string, 'Δρασκελίζει' ) );
		$this->assertTrue( beans_str_starts_with( $string, 'Τάχιστη αλώπηξ βαφής ψημένη γη,' ) );
		$this->assertTrue( beans_str_starts_with( $string, 'Τάχιστη αλώπηξ βαφής ψημένη γη, δρασκε' ) );
		$this->assertFalse( beans_str_starts_with( $string, [ 'tάχιστη', 'υπέρ' ] ) );
		$this->assertFalse( beans_str_starts_with( $string, [ 'Υπέρ', 'ψημένη' ] ) );
		$this->assertTrue( beans_str_starts_with( $string, [ 'tάχιστη', 'Τάχιστη', 'κυνός' ] ) );
		$this->assertTrue( beans_str_starts_with( $string, [ 'Υπέρ', 'ψημένη', 'Τάχιστη ' ] ) );
	}
}
