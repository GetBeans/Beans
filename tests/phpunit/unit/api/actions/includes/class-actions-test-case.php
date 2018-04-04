<?php
/**
 * Test Case for Beans' Action API unit tests.
 *
 * @package Beans\Framework\Tests\Unit\API\Actions\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions\Includes;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey;

/**
 * Abstract Class Actions_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\Actions\Includes
 */
abstract class Actions_Test_Case extends Test_Case {

	/**
	 * An array of actions to test.
	 *
	 * @var array
	 */
	protected static $test_actions;

	/**
	 * An array of Beans' IDs for our test actions.
	 *
	 * @var array
	 */
	protected static $test_ids;

	/**
	 * Set up the test before we run the test setups.
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		static::$test_actions = require dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'fixtures/test-actions.php';
		static::$test_ids     = array_keys( static::$test_actions );
	}

	/**
	 * Tear down the test before we exit this class.
	 */
	public static function tearDownAfterClass() {
		parent::tearDownAfterClass();

		global $_beans_registered_actions;
		$_beans_registered_actions = array(
			'added'    => array(),
			'modified' => array(),
			'removed'  => array(),
			'replaced' => array(),
		);

		// Remove the test actions.
		foreach ( static::$test_actions as $beans_id => $action ) {
			remove_action( $action['hook'], $action['callback'], $action['priority'] );
		}

		static::$test_actions = null;
		static::$test_ids     = null;
	}

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		$this->load_original_functions( array(
			'api/actions/functions.php',
			'api/utilities/functions.php',
		) );

		Monkey\Functions\when( 'beans_get' )->alias( function ( $needle, $haystack ) {

			if ( isset( $haystack[ $needle ] ) ) {
				return $haystack[ $needle ];
			}
		} );
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	protected function tearDown() {
		global $_beans_registered_actions;
		$_beans_registered_actions = array(
			'added'    => array(),
			'modified' => array(),
			'removed'  => array(),
			'replaced' => array(),
		);

		$this->remove_test_actions();

		parent::tearDown();
	}

	/**
	 * Simulate going to the post and loading in the template and fragments.
	 *
	 * @since 1.5.0
	 *
	 * @param bool $expect_added Optional. When true, runs tests to ensure it's been added.
	 *
	 * @return void
	 * @throws Monkey\Expectation\Exception\NotAllowedMethod Thrown from Monkey.
	 */
	protected function go_to_post( $expect_added = false ) {

		foreach ( static::$test_actions as $beans_id => $action ) {

			if ( $expect_added ) {
				Monkey\Actions\expectAdded( $action['hook'] )
					->once()
					->whenHappen( function ( $callback, $priority, $args ) use ( $action ) {
						$this->assertSame( $action['callback'], $callback );
						$this->assertSame( $action['priority'], $priority );
						$this->assertSame( $action['args'], $args );
					} );
			}

			beans_add_action( $beans_id, $action['hook'], $action['callback'], $action['priority'], $action['args'] );

			if ( $expect_added ) {
				$this->assertTrue( has_action( $action['hook'], $action['callback'] ) !== false );
			}
		}
	}

	/**
	 * Remove the test actions.
	 */
	protected function remove_test_actions() {

		foreach ( static::$test_actions as $beans_id => $action ) {
			_beans_unset_action( $beans_id, 'added' );
			remove_action( $action['hook'], $action['callback'], $action['priority'] );
		}
	}
}
