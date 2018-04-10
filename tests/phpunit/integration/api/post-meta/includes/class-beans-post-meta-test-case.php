<?php
/**
 * Test Case for Beans' Post_Meta API integration tests.
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta\Includes
 *
 * @since 1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Post_Meta\Includes;

use Beans\Framework\Tests\Integration\Test_Case;

/**
 * Abstract Class Beans_Post_Meta_Test_Case
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta\Includes
 */
abstract class Beans_Post_Meta_Test_Case extends Test_Case {

	public function tearDown() {

		unset( $GLOBALS['current_screen'] );
		$this->clean_up_global_scope();
		parent::tearDown();
	}

}