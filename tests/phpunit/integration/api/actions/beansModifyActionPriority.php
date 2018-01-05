<?php
/**
 * Tests for beans_modify_action_priority()
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Actions;

use Beans\Framework\Tests\Integration\API\Actions\Includes\Actions_Test_Case;

require_once __DIR__ . '/includes/class-actions-test-case.php';

/**
 * Class Tests_BeansModifyActionPriority
 *
 * @package Beans\Framework\Tests\Integration\API\Actions
 * @group   unit-integration
 * @group   api
 */
class Tests_BeansModifyActionPriority extends Actions_Test_Case {

	/**
	 * Test beans_modify_action_priority() should return false when the ID is not registered.
	 */
	public function test_should_return_false_when_id_not_registered() {
		$ids = array(
			'foo'   => null,
			'bar'   => 0,
			'baz'   => 10,
			'beans' => '20',
		);

		foreach ( $ids as $id => $priority ) {
			$this->assertFalse( beans_modify_action_priority( $id, $priority ) );
		}
	}

	/**
	 * Test beans_modify_action_priority() should return false when new priority is a non-integer.
	 */
	public function test_should_return_false_when_priority_is_non_integer() {
		$ids = array(
			'foo'   => null,
			'bar'   => array( 10 ),
			'baz'   => false,
			'beans' => '',
		);

		foreach ( $ids as $id => $priority ) {
			$action = $this->setup_original_action( $id, true );

			$this->assertFalse( beans_modify_action_priority( $id, $priority ) );
			$this->assertTrue( has_action( $action['hook'] ) );

			// Check that the parameters did not change.
			$this->check_parameters_registered_in_wp( $action );
		}
	}

	/**
	 * Test beans_modify_action_priority() should modify the action's priority when the new one is zero.
	 */
	public function test_should_modify_action_when_priority_is_zero() {
		$ids = array(
			'foo'   => 0,
			'bar'   => 0.0,
			'baz'   => '0',
			'beans' => '0.0',
		);

		foreach ( $ids as $id => $priority ) {
			$action = $this->setup_original_action( $id );
			$this->assertTrue( beans_modify_action_priority( $id, $priority ) );

			// Manually change the original to the new one.  Then test that it did change in WordPress.
			$action['priority'] = (int) $priority;
			$this->check_parameters_registered_in_wp( $action );
		}
	}

	/**
	 * Test beans_modify_action_priority() should modify the registered action's priority.
	 */
	public function test_should_modify_the_action_priority() {
		$action          = $this->setup_original_action( 'beans' );
		$modified_action = array(
			'priority' => 20,
		);
		$this->assertTrue( beans_modify_action_priority( 'beans', $modified_action['priority'] ) );
		$this->assertEquals( $modified_action, _beans_get_action( 'beans', 'modified' ) );
		$this->assertTrue( has_action( $action['hook'] ) );
		$this->check_parameters_registered_in_wp( array_merge( $action, $modified_action ) );
	}
}
