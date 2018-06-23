<?php
/**
 * Tests for beans_load_fragment_file()
 *
 * @package Beans\Framework\Tests\Unit\API\Template
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Template;

use Beans\Framework\Tests\Unit\API\Template\Includes\Template_Test_Case;
use Brain\Monkey;

require_once __DIR__ . '/includes/class-template-test-case.php';

/**
 * Class Tests_BeansLoadFragmentFile
 *
 * @package Beans\Framework\Tests\Unit\API\Template
 * @group   api
 * @group   api-template
 */
class Tests_BeansLoadFragmentFile extends Template_Test_Case {

	/**
	 * Test beans_load_fragment_file() should return false when short-circuiting the function.
	 */
	public function test_should_return_false_when_short_circuiting() {

		foreach ( [ 'branding', 'post-body' ] as $fragment ) {
			Monkey\Filters\expectApplied( "beans_pre_load_fragment_{$fragment}" )
				->with( false )
				->once()
				->andReturn( true );

			$this->assertFalse( beans_load_fragment_file( $fragment ) );
		}
	}

	/**
	 * Test beans_load_fragment_file() should return false when the fragment does not exist.
	 */
	public function test_should_return_false_when_fragment_does_not_exist() {
		Monkey\Filters\expectApplied( 'beans_pre_load_fragment_does-not-exist' )
			->with( false )
			->once()
			->andReturn( false );

		$this->assertFileNotExists( BEANS_FRAGMENTS_PATH . 'does-not-exist.php' );
		$this->assertFalse( beans_load_fragment_file( 'does-not-exist' ) );
	}

	/**
	 * Test beans_load_fragment_file() should return true after loading the fragment.
	 */
	public function test_should_return_true_after_loading_fragment() {

		foreach ( [ 'branding', 'post-body' ] as $fragment ) {
			Monkey\Filters\expectApplied( "beans_pre_load_fragment_{$fragment}" )
				->with( false )
				->once()
				->andReturn( false );

			$this->assertFileExists( BEANS_FRAGMENTS_PATH . "{$fragment}.php" );
			ob_start();
			$this->assertTrue( beans_load_fragment_file( $fragment ) );
			$this->assertSame( $this->mock_filesystem->getChild( "fragments/{$fragment}.php" )->getContent(), ob_get_clean() );
		}
	}
}
