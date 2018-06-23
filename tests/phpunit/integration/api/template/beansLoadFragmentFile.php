<?php
/**
 * Tests for beans_load_fragment_file()
 *
 * @package Beans\Framework\Tests\Integration\API\Template
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Template;

use Brain\Monkey;
use Beans\Framework\Tests\Integration\Test_Case;

/**
 * Class Tests_BeansLoadFragmentFile
 *
 * @package Beans\Framework\Tests\Integration\API\Template
 * @group   api
 * @group   api-template
 */
class Tests_BeansLoadFragmentFile extends Test_Case {

	/**
	 * Test beans_load_fragment_file() should return false when short-circuiting the function.
	 */
	public function test_should_return_false_when_short_circuiting() {

		foreach ( [ 'branding', 'post-body' ] as $fragment ) {
			Monkey\Functions\expect( 'beans_test_fragment_short_circuit' )
				->with( false )
				->once()
				->andReturn( true );

			add_filter( "beans_pre_load_fragment_{$fragment}", 'beans_test_fragment_short_circuit' );
			$this->assertFalse( beans_load_fragment_file( $fragment ) );
			remove_filter( "beans_pre_load_fragment_{$fragment}", 'beans_test_fragment_short_circuit' );
		}
	}

	/**
	 * Test beans_load_fragment_file() should return false when the fragment does not exist.
	 */
	public function test_should_return_false_when_fragment_does_not_exist() {
		$this->assertFileNotExists( BEANS_FRAGMENTS_PATH . 'does-not-exist.php' );
		$this->assertFalse( beans_load_fragment_file( 'does-not-exist' ) );
	}

	/**
	 * Test beans_load_fragment_file() should return true after loading the fragment.
	 */
	public function test_should_return_true_after_loading_fragment() {
		$this->assertFileExists( BEANS_FRAGMENTS_PATH . 'header.php' );
		$this->assertTrue( beans_load_fragment_file( 'header' ) );
		$this->assertTrue( function_exists( 'beans_head_meta' ) );

		$this->assertFileExists( BEANS_FRAGMENTS_PATH . 'post.php' );
		$this->assertTrue( beans_load_fragment_file( 'post' ) );
		$this->assertTrue( function_exists( 'beans_post_title' ) );
	}
}
