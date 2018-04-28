<?php
/**
 * Tests for beans_is_post_meta_conditions()
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Post_Meta;

use Beans\Framework\Tests\Integration\API\Post_Meta\Includes\Beans_Post_Meta_Test_Case;

require_once BEANS_API_PATH . 'post-meta/functions-admin.php';
require_once dirname( __FILE__ ) . '/includes/class-beans-post-meta-test-case.php';

/**
 * Class Tests_BeansGetPostMeta
 *
 * @package Beans\Framework\Tests\Integration\API\Post_Meta
 * @group   api
 * @group   api-post-meta
 */
class Tests_BeansIsPostMetaConditions extends Beans_Post_Meta_Test_Case {

	/**
	 * Tests is_post_meta_conditions() should return true when $conditions are a boolean true.
	 */
	public function test_should_return_true_for_boolean_true_condition() {
		$this->assertTrue( _beans_is_post_meta_conditions( true ) );
	}

	/**
	 * Tests is_post_meta_conditions() should return true when is a new post and $conditions include 'post'.
	 */
	public function test_should_return_true_when_new_post_and_conditions_include_post() {
		set_current_screen( 'post' );
		$_SERVER['REQUEST_URI'] = 'post-new.php';

		$this->assertTrue( _beans_is_post_meta_conditions( array( 'post' ) ) );

		// Clean up server globals.
		$_SERVER['REQUEST_URI'] = '';
	}

	/**
	 * Tests is_post_meta_conditions() should return false when is a new post and $conditions don't include 'post'.
	 */
	public function test_should_return_false_when_new_post_and_conditions_do_not_include_post() {
		set_current_screen( 'post' );
		$_SERVER['REQUEST_URI'] = 'post-new.php';

		$this->assertFalse( _beans_is_post_meta_conditions( array( 'page' ) ) );

		// Clean up server globals.
		$_SERVER['REQUEST_URI'] = '';
	}

	/**
	 * Tests is_post_meta_conditions() should return false when post_id can't be found.
	 */
	public function test_should_return_false_when_post_id_not_found() {
		set_current_screen( 'edit' );

		$this->assertFalse( _beans_is_post_meta_conditions( array( 'post' ) ) );
	}

	/**
	 * Tests is_post_meta_conditions() should return true when $conditions match post type.
	 */
	public function test_should_return_true_when_conditions_match_post_type() {
		$post_id = $this->factory()->post->create( array( 'post_type' => 'cpt' ) );
		set_current_screen( 'cpt' );

		// Setup for when post_id is in GET.
		$_GET['post'] = $post_id;

		$this->assertTrue( _beans_is_post_meta_conditions( array( 'cpt' ) ) );

		// Clear Global GET.
		$_GET['post'] = null;

		// Set up for when post_id is in POST.
		$_POST['post_ID'] = $post_id;

		$this->assertTrue( _beans_is_post_meta_conditions( array( 'cpt' ) ) );

		// Clear Global POST.
		$_POST['post_ID'] = null;
	}

	/**
	 * Tests is_post_meta_conditions() should return true when conditions match post ID.
	 */
	public function test_should_return_true_when_conditions_match_post_id() {
		$post_id = $this->factory()->post->create();
		set_current_screen( 'edit' );

		// Setup for when post_id is in GET.
		$_GET['post'] = $post_id;

		$this->assertTrue( _beans_is_post_meta_conditions( array( $post_id ) ) );

		// Clear Global GET.
		$_GET['post'] = null;

		// Set up for when post_id is in POST.
		$_POST['post_ID'] = $post_id;

		$this->assertTrue( _beans_is_post_meta_conditions( array( $post_id ) ) );

		// Clear Global POST.
		$_POST['post_ID'] = null;
	}

	/**
	 * Tests is_post_meta_conditions() should return true when conditions match a page template name.
	 */
	public function test_should_return_true_when_conditions_match_page_template_name() {
		$page_id = $this->factory()->post->create( array( 'post_type' => 'page' ) );
		set_current_screen( 'edit' );
		add_post_meta( $page_id, '_wp_page_template', 'page-template-name' );

		// Setup for when post_id is in GET.
		$_GET['post'] = $page_id;

		$this->assertTrue( _beans_is_post_meta_conditions( array( 'page-template-name' ) ) );

		// Clear Global GET.
		$_GET['post'] = null;

		// Set up for when post_id is in POST.
		$_POST['post_ID'] = $page_id;

		$this->assertTrue( _beans_is_post_meta_conditions( array( 'page-template-name' ) ) );

		// Clear Global POST.
		$_POST['post_ID'] = null;
	}

	/**
	 * Tests is_post_meta_conditions() should return false when no conditions match.
	 */
	public function test_should_return_false_when_no_conditions_match() {

		$page_id = $this->factory()->post->create( array( 'post_type' => 'page' ) );
		set_current_screen( 'edit' );
		add_post_meta( $page_id, '_wp_page_template', 'page-template-name' );

		// Setup for when post_id is in GET.
		$_GET['post'] = $page_id;

		$this->assertFalse( _beans_is_post_meta_conditions( array( 'some-other-conditions' ) ) );

		// Clear Global GET.
		$_GET['post'] = null;

		// Set up for when post_id is in POST.
		$_POST['post_ID'] = $page_id;

		$this->assertFalse( _beans_is_post_meta_conditions( array( 'some-other-conditions' ) ) );

		// Clear Global POST.
		$_POST['post_ID'] = null;
	}
}
