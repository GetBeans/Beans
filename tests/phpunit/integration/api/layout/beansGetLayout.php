<?php
/**
 * Tests for beans_get_layout()
 *
 * @package Beans\Framework\Tests\Integration\API\Layout
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\Layout;

use Beans\Framework\Tests\Integration\Test_Case;

/**
 * Class Tests_BeansGetLayout
 *
 * @package Beans\Framework\Tests\Integration\API\Layout
 * @group   api
 * @group   api-layout
 */
class Tests_BeansGetLayout extends Test_Case {

	/**
	 * Test beans_get_layout() should return the layout for a single post or page.
	 */
	public function test_should_return_layout_for_singular() {
		$post_id = self::factory()->post->create();

		// Run our tests for when the layout is set.
		update_post_meta( $post_id, 'beans_layout', 'c_sp_ss' );
		$this->go_to( get_permalink( $post_id ) );
		$this->assertQueryTrue( 'is_singular', 'is_single' );
		$this->assertTrue( is_singular() );
		$this->assertSame( 'c_sp_ss', beans_get_layout() );

		// Run our tests for when the layout is not set.
		delete_post_meta( $post_id, 'beans_layout' );
		$this->go_to( get_permalink( $post_id ) );
		$this->assertSame( 'c_sp', beans_get_layout() );

		// Run our tests for when the layout is set to fallback to the default.
		update_post_meta( $post_id, 'beans_layout', 'default_fallback' );
		$this->go_to( get_permalink( $post_id ) );
		$this->assertSame( 'c_sp', beans_get_layout() );
	}

	/**
	 * Test beans_get_layout() should return the layout for the static posts page.
	 */
	public function test_should_return_layout_for_static_posts_page() {
		// Configure the Posts Page for our static page.
		$posts_page_id = self::factory()->post->create( [
			'post_type' => 'page',
		] );
		update_option( 'show_on_front', 'page' );
		update_option( 'page_for_posts', $posts_page_id );

		// Run our tests for when the layout is set.
		update_post_meta( $posts_page_id, 'beans_layout', 'ss_c' );
		$this->go_to( get_permalink( $posts_page_id ) );
		$this->assertQueryTrue( 'is_home', 'is_posts_page' );
		$this->assertTrue( is_home() );
		$this->assertSame( 'ss_c', beans_get_layout() );

		// Run our tests for when the layout is not set.
		delete_post_meta( $posts_page_id, 'beans_layout' );
		$this->go_to( get_permalink( $posts_page_id ) );
		$this->assertSame( 'c_sp', beans_get_layout() );

		// Run our tests for when the layout is set to fallback to the default.
		update_post_meta( $posts_page_id, 'beans_layout', 'default_fallback' );
		$this->go_to( get_permalink( $posts_page_id ) );
		$this->assertSame( 'c_sp', beans_get_layout() );
	}

	/**
	 * Test beans_get_layout() should return the theme's default layout when a the static page is not configured for
	 * the Posts Page.
	 */
	public function test_should_return_default_layout_when_no_static_posts_page() {
		// Run our tests without changing the default.
		$this->go_to( get_permalink( '/' ) );
		$this->assertQueryTrue( 'is_home', 'is_front_page' );
		$this->assertTrue( is_home() );
		$this->assertSame( 'c_sp', beans_get_layout() );

		// Run our tests when the theme's mod is set.
		set_theme_mod( 'beans_layout', 'sp_ss_c' );
		$this->go_to( get_permalink( '/' ) );
		$this->assertSame( 'sp_ss_c', beans_get_layout() );
	}

	/**
	 * Test beans_get_layout() should return layout for a category.
	 */
	public function test_should_return_layout_for_category() {
		$post_id     = self::factory()->post->create();
		$category_id = self::factory()->category->create( [ 'slug' => 'test-cat' ] );
		wp_set_object_terms( $post_id, $category_id, 'category' );
		$meta_key = "beans_term_{$category_id}_beans_layout";

		// Run our tests for when the layout is not set.
		$this->go_to( "/?cat={$category_id}" );
		$this->assertQueryTrue( 'is_category', 'is_archive' );
		$this->assertTrue( is_category() );
		$this->assertSame( 'c_sp', beans_get_layout() );

		// Run our tests for when the layout is set.
		update_option( $meta_key, 'sp_c' );
		$this->go_to( "/?cat={$category_id}" );
		$this->assertSame( 'sp_c', beans_get_layout() );

		// Run our tests for when the layout is set to fallback to the default.
		update_option( $meta_key, 'default_fallback' );
		$this->go_to( "/?cat={$category_id}" );
		$this->assertSame( 'c_sp', beans_get_layout() );
	}

	/**
	 * Test beans_get_layout() should return layout for a tag.
	 */
	public function test_should_return_layout_for_tag() {
		$post_id = self::factory()->post->create();
		$tag_id  = self::factory()->tag->create( [ 'slug' => 'test-tag' ] );
		wp_set_object_terms( $post_id, $tag_id, 'post_tag' );
		$meta_key = "beans_term_{$tag_id}_beans_layout";

		// Run our tests for when the layout is not set.
		$this->go_to( '/?tag=test-tag' );
		$this->assertQueryTrue( 'is_tag', 'is_archive' );
		$this->assertTrue( is_tag() );
		$this->assertSame( 'c_sp', beans_get_layout() );

		// Run our tests for when the layout is set.
		update_option( $meta_key, 'sp_c' );
		$this->go_to( '/?tag=test-tag' );
		$this->assertSame( 'sp_c', beans_get_layout() );

		// Run our tests for when the layout is set to fallback to the default.
		update_option( $meta_key, 'default_fallback' );
		$this->go_to( '/?tag=test-tag' );
		$this->assertSame( 'c_sp', beans_get_layout() );
	}

	/**
	 * Test beans_get_layout() should return layout for a custom taxonomy.
	 */
	public function test_should_return_layout_for_custom_tax() {
		register_taxonomy( 'test_tax', 'post', [ 'public' => true ] );
		$post_id = self::factory()->post->create();
		$term_id = self::factory()->term->create( [
			'taxonomy' => 'test_tax',
			'slug'     => 'custom-term',
		] );
		wp_set_object_terms( $post_id, $term_id, 'test_tax' );
		$meta_key = "beans_term_{$term_id}_beans_layout";

		// Run our tests for when the layout is not set.
		$this->go_to( '/?test_tax=custom-term' );
		$this->assertQueryTrue( 'is_tax', 'is_archive' );
		$this->assertTrue( is_tax() );
		$this->assertSame( 'c_sp', beans_get_layout() );

		// Run our tests for when the layout is set.
		update_option( $meta_key, 'sp_c' );
		$this->go_to( '/?test_tax=custom-term' );
		$this->assertSame( 'sp_c', beans_get_layout() );

		// Run our tests for when the layout is set to fallback to the default.
		update_option( $meta_key, 'default_fallback' );
		$this->go_to( '/?test_tax=custom-term' );
		$this->assertSame( 'c_sp', beans_get_layout() );

		// Clean up.
		_unregister_taxonomy( 'test_tax' );
	}
}
