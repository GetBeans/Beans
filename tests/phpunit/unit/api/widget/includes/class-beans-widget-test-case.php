<?php
/**
 * Test Case for the Beans Widget API unit tests.
 *
 * @package Beans\Framework\Tests\Unit\API\Widget\Includes
 *
 * @since 1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Widget\Includes;

use Beans\Framework\Tests\Unit\Test_Case;

/**
 * Abstract Class Beans_Widget_Test_Case
 *
 * @package Beans\Framework\Tests\Unit\API\Widget\Includes
 */
abstract class Beans_Widget_Test_Case extends Test_Case {

	/**
	 * Setup test fixture.
	 */
	protected function setUp() {
		parent::setUp();

		$this->load_original_functions(
			[
				'api/widget/functions.php',
				'api/utilities/functions.php',
			]
		);

		$this->setup_common_wp_stubs();
	}

}
