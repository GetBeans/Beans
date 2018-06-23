<?php
/**
 * Tests for _beans_is_post_meta_conditions()
 *
 * @package Beans\Framework\Tests\Unit\API\Post_Meta
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\Post_Meta;

use Beans\Framework\Tests\Unit\Test_Case;
use Brain\Monkey;

/**
 * Class Tests_BeansIsPostMetaConditions
 *
 * @package Beans\Framework\Tests\Unit\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_BeansIsPostMetaConditions extends Test_Case {

	/**
	 * Test _beans_is_post_meta_conditions() should return true when $conditions are a boolean true.
	 */
	public function test_should_return_true_for_boolean_true_condition() {
		$this->assertTrue( _beans_is_post_meta_conditions( true ) );
	}

	/**
	 * Test _beans_is_post_meta_conditions() should return true when it's a new post and $conditions include 'post'.
	 */
	public function test_shold_return_true_when_new_post_and_conditions_include_post() {
		$_SERVER['REQUEST_URI'] = 'post-new.php';
		Monkey\Functions\expect( 'beans_get' )->once()->with( 'post_type' )->andReturn( 'post' );

		$this->assertTrue( _beans_is_post_meta_conditions( [ 'post' ] ) );

		// Clean up server globals.
		$_SERVER['REQUEST_URI'] = '';
	}

	/**
	 * Test _beans_is_post_meta_conditions() should return false when it's a new post and $conditions don't include 'post'.
	 */
	public function test_should_return_false_when_new_post_and_conditions_do_not_include_post() {
		$_SERVER['REQUEST_URI'] = 'post-new.php';
		Monkey\Functions\expect( 'beans_get' )->once()->with( 'post_type' )->andReturn( 'page' );

		$this->assertFalse( _beans_is_post_meta_conditions( [ 'post' ] ) );

		// Clean up server globals.
		$_SERVER['REQUEST_URI'] = '';
	}

	/**
	 * Test _beans_is_post_meta_conditions() should return false when post_id can't be found.
	 */
	public function test_should_return_false_when_post_id_not_found() {

		Monkey\Functions\expect( 'beans_get' )->once()->with( 'post' )->andReturn( false );
		Monkey\Functions\expect( 'beans_post' )->once()->with( 'post_ID' )->andReturn( false );

		$this->assertFalse( _beans_is_post_meta_conditions( [ 'post' ] ) );
	}

	/**
	 * Test _beans_is_post_meta_conditions() should return true when $conditions match post type.
	 */
	public function test_should_return_true_when_conditions_match_post_type() {

		// Setup for when post_ID is in GET.
		Monkey\Functions\expect( 'beans_get' )->once()->with( 'post' )->andReturn( 25 );
		Monkey\Functions\expect( 'beans_post' )->once()->with( 'post_ID' )->andReturn( false );
		Monkey\Functions\expect( 'get_post_type' )->once()->with( 25 )->andReturn( 'cpt' );
		Monkey\Functions\expect( 'get_post_meta' )->once()->andReturn( false );

		$this->assertTrue( _beans_is_post_meta_conditions( [ 'cpt' ] ) );

		// Setup for when post_ID is in POST.
		Monkey\Functions\expect( 'beans_get' )->once()->with( 'post' )->andReturn( false );
		Monkey\Functions\expect( 'beans_post' )->once()->with( 'post_ID' )->andReturn( 25 );
		Monkey\Functions\expect( 'get_post_type' )->once()->with( 25 )->andReturn( 'cpt' );
		Monkey\Functions\expect( 'get_post_meta' )->once()->andReturn( false );

		$this->assertTrue( _beans_is_post_meta_conditions( [ 'cpt' ] ) );
	}

	/**
	 * Test _beans_is_post_meta_conditions() should return true when conditions match post_ID.
	 */
	public function test_should_return_true_when_conditions_match_post_id() {

		// Setup for when post_ID is in GET.
		Monkey\Functions\expect( 'beans_get' )->once()->with( 'post' )->andReturn( 1 );
		Monkey\Functions\expect( 'beans_post' )->once()->with( 'post_ID' )->andReturn( false );
		Monkey\Functions\expect( 'get_post_type' )->once()->with( 1 )->andReturn( 'cpt' );
		Monkey\Functions\expect( 'get_post_meta' )->once()->andReturn( false );

		$this->assertTrue( _beans_is_post_meta_conditions( [ 1 ] ) );

		// Setup for when post_ID is in POST.
		Monkey\Functions\expect( 'beans_get' )->once()->with( 'post' )->andReturn( false );
		Monkey\Functions\expect( 'beans_post' )->once()->with( 'post_ID' )->andReturn( 2 );
		Monkey\Functions\expect( 'get_post_type' )->once()->with( 2 )->andReturn( 'cpt' );
		Monkey\Functions\expect( 'get_post_meta' )->once()->andReturn( false );

		$this->assertTrue( _beans_is_post_meta_conditions( [ 2 ] ) );
	}

	/**
	 * Test _beans_is_post_meta_conditions() should return true when conditions match a page template name.
	 */
	public function test_should_return_true_when_conditions_match_page_template_name() {

		// Setup for when post_ID is in GET.
		Monkey\Functions\expect( 'beans_get' )->once()->with( 'post' )->andReturn( 345 );
		Monkey\Functions\expect( 'beans_post' )->once()->with( 'post_ID' )->andReturn( false );
		Monkey\Functions\expect( 'get_post_type' )->once()->with( 345 )->andReturn( 'page' );
		Monkey\Functions\expect( 'get_post_meta' )
			->once()
			->with( '345', '_wp_page_template', true )
			->andReturn( 'page-template-name' );

		$this->assertTrue( _beans_is_post_meta_conditions( [ 'page-template-name' ] ) );

		// Setup for when post_ID is in POST.
		Monkey\Functions\expect( 'beans_get' )->once()->with( 'post' )->andReturn( false );
		Monkey\Functions\expect( 'beans_post' )->once()->with( 'post_ID' )->andReturn( 543 );
		Monkey\Functions\expect( 'get_post_type' )->once()->with( 543 )->andReturn( 'page' );
		Monkey\Functions\expect( 'get_post_meta' )
			->once()
			->with( '543', '_wp_page_template', true )
			->andReturn( 'page-template-name' );

		$this->assertTrue( _beans_is_post_meta_conditions( [ 'page-template-name' ] ) );
	}

	/**
	 * Test _beans_is_post_meta_conditions() should return false when no conditions match.
	 */
	public function test_should_return_false_when_no_conditions_match() {

		// Setup for when post_ID is in GET.
		Monkey\Functions\expect( 'beans_get' )->once()->with( 'post' )->andReturn( 345 );
		Monkey\Functions\expect( 'beans_post' )->once()->with( 'post_ID' )->andReturn( false );
		Monkey\Functions\expect( 'get_post_type' )->once()->with( 345 )->andReturn( 'page' );
		Monkey\Functions\expect( 'get_post_meta' )
			->once()
			->with( '345', '_wp_page_template', true )
			->andReturn( 'page-template-name' );

		$this->assertFalse( _beans_is_post_meta_conditions( [ 'some-other-conditions' ] ) );

		// Setup for when post_ID is in POST.
		Monkey\Functions\expect( 'beans_get' )->once()->with( 'post' )->andReturn( false );
		Monkey\Functions\expect( 'beans_post' )->once()->with( 'post_ID' )->andReturn( 543 );
		Monkey\Functions\expect( 'get_post_type' )->once()->with( 543 )->andReturn( 'page' );
		Monkey\Functions\expect( 'get_post_meta' )
			->once()
			->with( '543', '_wp_page_template', true )
			->andReturn( 'page-template-name' );

		$this->assertFalse( _beans_is_post_meta_conditions( [ 'some-other-conditions' ] ) );
	}
}
