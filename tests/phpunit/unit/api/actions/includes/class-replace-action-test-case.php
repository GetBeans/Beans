<?php
/**
 * Tests Case for Beans' Action API "replace" action unit tests.
 *
 * @package Beans\Framework\Tests\Unit\API\Actions\Includes
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Actions\Includes;

/**
 * Abstract Class Replace_Action_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\Actions\Includes
 */
abstract class Replace_Action_Test_Case extends Actions_Test_Case {

	/**
	 * Check that the "replaced" action has been stored in Beans.
	 *
	 * @since 1.5.0
	 *
	 * @param string $beans_id        The Beans unique ID.
	 * @param array  $replaced_action The "replaced" action's configuration.
	 *
	 * @return void
	 */
	protected function check_stored_in_beans( $beans_id, array $replaced_action ) {
		$this->assertEquals( $replaced_action, _beans_get_action( $beans_id, 'replaced' ) );
		$this->assertEquals( $replaced_action, _beans_get_action( $beans_id, 'modified' ) );
	}
}
