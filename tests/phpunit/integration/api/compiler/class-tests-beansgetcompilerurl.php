<?php
/**
 * Tests for beans_get_compiler_url()
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Compiler;

use Beans\Framework\Tests\Integration\API\Compiler\Includes\Compiler_Test_Case;

require_once __DIR__ . '/includes/class-compiler-test-case.php';

/**
 * Class Test_BeansGetCompilerUrl
 *
 * @package Beans\Framework\Tests\Integration\API\Compiler
 * @group   api
 * @group   api-compiler
 */
class Tests_BeansGetCompilerUrl extends Compiler_Test_Case {

	/**
	 * Test beans_get_compiler_url() should return the URL to the Beans' compiler folder.
	 */
	public function test_should_return_url_to_compiler_folder() {
		$this->assertSame(
			site_url( 'compiled/beans/compiler/' ),
			beans_get_compiler_url()
		);
	}

	/**
	 * Test should return the URL to the Beans' admin compiler folder.
	 */
	public function test_should_return_url_to_compiler_admin_folder() {
		$this->assertSame(
			site_url( 'compiled/beans/admin-compiler/' ),
			beans_get_compiler_url( true )
		);
	}
}
