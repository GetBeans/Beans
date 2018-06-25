<?php
/**
 * Tests for beans_load_default_template()
 *
 * @package Beans\Framework\Tests\Integration\API\Template
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Template;

use Beans\Framework\Tests\Integration\Test_Case;
use Brain\Monkey;

/**
 * Class Tests_BeansLoadDefaultTemplate
 *
 * @package Beans\Framework\Tests\Integration\API\Template
 * @group   api
 * @group   api-template
 */
class Tests_BeansLoadDefaultTemplate extends Test_Case {

	/**
	 * Test beans_load_default_template() should return false when the template does not exist.
	 */
	public function test_should_return_false_when_template_does_not_exist() {
		$this->assertFileNotExists( BEANS_STRUCTURE_PATH . 'does-not-exist.php' );
		$this->assertFalse( beans_load_default_template( 'path/to/does-not-exist.php' ) );

		$this->assertFileNotExists( BEANS_STRUCTURE_PATH . basename( __FILE__ ) );
		$this->assertFalse( beans_load_default_template( __FILE__ ) );
	}

	/**
	 * Test beans_load_default_template() should load the default structure when the given file does not exist, but its
	 * basename is a Beans' structure.
	 */
	public function test_should_load_default_structure_when_given_file_does_not_exist_but_basename_is_beans_structure() {
		// Check with a relative invalid path.
		$file = '/path/to/structure/content.php';

		// Check that the given file does not exist.
		$this->assertFileNotExists( $file );

		// Check that 'content.php' does exist in Beans.
		$this->assertFileExists( BEANS_STRUCTURE_PATH . basename( $file ) );

		// Check that it renders.
		$this->go_to( '/' );
		Monkey\Functions\expect( 'do_action' )->once()->with( 'beans_content' )->andReturn();
		ob_start();
		$this->assertTrue( beans_load_default_template( $file ) );
		$html = ob_get_clean();

		$expected = <<<EOB
<div class="tm-content" id="beans-content" role="main" itemprop="mainEntityOfPage" tabindex="-1" itemscope="itemscope" itemtype="https://schema.org/Blog">
EOB;
		$this->assertStringStartsWith( $expected, trim( $html ) );

		// Check with an invalid absolute path.
		$file = __DIR__ . '/fixtures/structure/header.php';

		// Check that the given file does not exist.
		$this->assertFileNotExists( $file );

		// Check that 'header.php' does exist in Beans.
		$this->assertFileExists( BEANS_STRUCTURE_PATH . basename( $file ) );

		// Check that it renders.
		ob_start();
		Monkey\Functions\expect( 'do_action' )->with( 'beans_head' )->andReturn();
		Monkey\Functions\expect( 'wp_head' )->andReturn();
		$this->assertTrue( beans_load_default_template( $file ) );
		$this->assertStringStartsWith( '<!DOCTYPE html>', ob_get_clean() );
	}
}
