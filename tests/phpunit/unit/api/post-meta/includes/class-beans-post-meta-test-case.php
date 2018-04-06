<?php
/**
 * Test Case for Beans' Post_Meta API unit tests.
 *
 * @package Beans\Framework\Tests\Unit\API\Post_Meta\Includes
 *
 * @since 1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Post_Meta\Includes;

use Beans\Framework\Tests\Unit\Test_Case;

/**
 * Abstract Class Beans_Post_Meta_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\Post_Meta\Includes
 */
abstract class Beans_Post_Meta_Test_Case extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		$this->load_original_functions( array(
			'api/post-meta/class-beans-post-meta.php',
			'api/post-meta/functions.php',
			'api/post-meta/functions-admin.php',
			'api/fields/functions.php',
			'api/utilities/functions.php',
		) );

		$this->setup_common_wp_stubs();
	}

}
