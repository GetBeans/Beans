<?php
/**
 * Tests for beans_open_markup().
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Unit\API\HTML;

use Beans\Framework\Tests\Unit\API\HTML\Includes\HTML_Test_Case;
use Brain\Monkey;

require_once __DIR__ . '/includes/class-html-test-case.php';

/**
 * Class Tests_BeansOpenMarkup
 *
 * @package Beans\Framework\Tests\Unit\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansOpenMarkup extends HTML_Test_Case {

	/**
	 * Test beans_open_markup() should return null when the tag is set to null.
	 */
	public function test_should_return_null_when_tag_set_to_null() {
		Monkey\Functions\expect( 'beans_apply_filters' )
			->once()
			->with( 'beans_archive_title_markup', null )
			->andReturnNull();

		$this->assertNull( beans_open_markup( 'beans_archive_title', null, [ 'class' => 'uk-article-title' ] ) );
	}

	/**
	 * Test beans_open_markup() should fire "{$id}_before_markup" hooks and include the content in the returned HTML.
	 */
	public function test_should_fire_before_markup_hooks_and_include_content_in_returned_html() {
		Monkey\Functions\when( 'beans_add_attributes' )->justReturn( 'class="uk-article-title"' );
		Monkey\Functions\when( '_beans_is_html_dev_mode' )->justReturn( false );
		Monkey\Functions\expect( '_beans_render_action' )
			->once()
			->with( 'beans_archive_title_before_markup' )
			->andReturn( '<!-- _before_markup fired -->' );

		$actual = beans_open_markup( 'beans_archive_title', 'h1', [ 'class' => 'uk-article-title' ] );
		$this->assertStringStartsWith( '<!-- _before_markup fired -->', $actual );
	}

	/**
	 * Test beans_open_markup() should fire "{$id}_prepend_markup" hooks and include the content in the returned HTML.
	 */
	public function test_should_fire_prepend_markup_hooks_and_include_content_in_returned_html() {
		Monkey\Functions\when( 'beans_add_attributes' )->justReturn( 'class="uk-article-title"' );
		Monkey\Functions\when( '_beans_is_html_dev_mode' )->justReturn( false );
		Monkey\Functions\expect( '_beans_render_action' )
			->once()
			->with( 'beans_archive_title_prepend_markup' )
			->andReturn( '<!-- _prepend_markup fired -->' );

		$actual = beans_open_markup( 'beans_archive_title', 'h1', [ 'class' => 'uk-article-title' ] );
		$this->assertStringEndsWith( '<!-- _prepend_markup fired -->', $actual );
	}

	/**
	 * Test beans_open_markup() should fire _beans_render_action() for the "_after_markup" hooks when the global
	 * $_beans_is_selfclose_markup is set to true.
	 */
	public function test_should_fire_after_markup_hooks_when_selfclose_is_true() {
		Monkey\Functions\when( 'beans_add_attributes' )->justReturn( 'class="uk-article-title"' );
		Monkey\Functions\when( '_beans_is_html_dev_mode' )->justReturn( false );
		Monkey\Functions\expect( '_beans_render_action' )
			->once()
			->with( 'beans_archive_title_after_markup' )
			->andReturn( 'Worked!' );

		global $_beans_is_selfclose_markup;
		$_beans_is_selfclose_markup = true;

		$this->assertContains( 'Worked!', beans_open_markup( 'beans_archive_title', 'h1', [ 'class' => 'uk-article-title' ] ) );

		// Check that the global was unset.
		$this->assertArrayNotHasKey( '_beans_is_selfclose_markup', $GLOBALS );
	}

	/**
	 * Test beans_open_markup() should return the built HTML element when before or prepend hooks are not registered.
	 */
	public function test_should_return_built_html_when_before_or_prepend_hooks_not_registered() {
		Monkey\Functions\when( '_beans_render_action' )->justReturn( '' );
		Monkey\Functions\expect( 'beans_add_attributes' )
			->once()
			->with( 'beans_archive_title', [ 'class' => 'uk-article-title' ] )
			->andReturn( 'class="uk-article-title"' );
		Monkey\Functions\expect( '_beans_is_html_dev_mode' )->once()->andReturn( false );

		$actual = beans_open_markup( 'beans_archive_title', 'h1', [ 'class' => 'uk-article-title' ] );
		$this->assertSame( '<h1 class="uk-article-title">', $actual );
	}

	/**
	 * Test beans_open_markup() should return the built HTML element with the "data-markup-id" when in development mode.
	 */
	public function test_should_return_built_html_with_data_markup_id_when_in_dev_mode() {
		Monkey\Functions\when( '_beans_render_action' )->justReturn( '' );
		Monkey\Functions\expect( 'beans_add_attributes' )
			->once()
			->with( 'beans_archive_title', [ 'class' => 'uk-article-title' ] )
			->andReturn( 'class="uk-article-title"' );
		Monkey\Functions\expect( '_beans_is_html_dev_mode' )->once()->andReturn( true );

		$actual = beans_open_markup( 'beans_archive_title', 'h1', [ 'class' => 'uk-article-title' ] );
		$this->assertSame( '<h1 class="uk-article-title" data-markup-id="beans_archive_title">', $actual );
	}

	/**
	 * Test beans_open_markup() should return the built HTML when before or prepend hooks are registered.
	 */
	public function test_should_return_built_html_when_before_or_prepend_hooks() {
		Monkey\Functions\expect( 'beans_add_attributes' )
			->once()
			->with( 'beans_archive_title', [ 'class' => 'uk-article-title' ] )
			->andReturn( 'class="uk-article-title"' );
		Monkey\Functions\expect( '_beans_is_html_dev_mode' )->once()->andReturn( false );
		Monkey\Functions\expect( '_beans_render_action' )
			->once()
			->with( 'beans_archive_title_before_markup' )
			->andReturn( '<!-- _before_markup fired -->' )
			->andAlsoExpectIt()
			->once()
			->with( 'beans_archive_title_prepend_markup' )
			->andReturn( '<!-- _prepend_markup fired -->' );

		$actual   = beans_open_markup( 'beans_archive_title', 'h1', [ 'class' => 'uk-article-title' ] );
		$expected = <<<EOB
<!-- _before_markup fired --><h1 class="uk-article-title"><!-- _prepend_markup fired -->
EOB;
		$this->assertSame( $expected, $actual );
	}

	/**
	 * Test beans_open_markup() should return a built self-closing HTML element when the global
	 * $_beans_is_selfclose_markup is set to true.
	 */
	public function test_should_return_built_self_closing_html_when_selfclose_markup_is_true() {
		$args = [
			'width'    => 800,
			'height'   => 500,
			'src'      => 'http://example.com/image.png',
			'alt'      => 'Some image',
			'itemprop' => 'image',
		];

		Monkey\Functions\when( '_beans_render_action' )->justReturn( '' );
		Monkey\Functions\expect( 'beans_add_attributes' )
			->twice()
			->with( 'beans_post_image_item', $args, 'http://example.com/image.png' )
			->andReturnUsing( function( $id, $attributes ) {
				return $this->convert_attributes_into_html( $attributes );
			} );

		// Run it with development mode off.
		Monkey\Functions\expect( '_beans_is_html_dev_mode' )->once()->andReturn( false );
		global $_beans_is_selfclose_markup;
		$_beans_is_selfclose_markup = true;

		$actual   = beans_open_markup( 'beans_post_image_item', 'img', $args, 'http://example.com/image.png' );
		$expected = <<<EOB
<img width="800" height="500" src="http://example.com/image.png" alt="Some image" itemprop="image"/>
EOB;
		$this->assertSame( $expected, $actual );

		// Run it with development mode on.
		Monkey\Functions\expect( '_beans_is_html_dev_mode' )->once()->andReturn( true );
		global $_beans_is_selfclose_markup;
		$_beans_is_selfclose_markup = true;

		$actual   = beans_open_markup( 'beans_post_image_item', 'img', $args, 'http://example.com/image.png' );
		$expected = <<<EOB
<img width="800" height="500" src="http://example.com/image.png" alt="Some image" itemprop="image" data-markup-id="beans_post_image_item"/>
EOB;
		$this->assertSame( $expected, $actual );
	}

	/**
	 * Test beans_open_markup() should return only the output from the hooked callbacks and not the HTML element when
	 * the tag is empty.
	 */
	public function test_should_return_only_hooked_callbacks_output_and_no_html_element_when_tag_is_empty() {
		Monkey\Functions\expect( 'beans_add_attributes' )->never();
		Monkey\Functions\expect( '_beans_is_html_dev_mode' )->never();
		Monkey\Functions\expect( '_beans_render_action' )
			->twice()
			->with( 'beans_archive_title_before_markup' )
			->andReturn( '<!-- _before_markup fired -->' )
			->andAlsoExpectIt()
			->twice()
			->with( 'beans_archive_title_prepend_markup' )
			->andReturn( '<!-- _prepend_markup fired -->' );

		// Check with an empty string.
		$actual = beans_open_markup( 'beans_archive_title', '', [ 'class' => 'uk-article-title' ] );
		$this->assertSame( '<!-- _before_markup fired --><!-- _prepend_markup fired -->', $actual );

		// Check with false.
		$actual = beans_open_markup( 'beans_archive_title', false, [ 'class' => 'uk-article-title' ] );
		$this->assertSame( '<!-- _before_markup fired --><!-- _prepend_markup fired -->', $actual );
	}
}
