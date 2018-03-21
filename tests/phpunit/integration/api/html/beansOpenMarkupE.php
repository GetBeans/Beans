<?php
/**
 * Tests for beans_open_markup_e().
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 *
 * @since   1.5.0
 */

namespace Beans\Framework\Tests\Integration\API\HTML;

use Beans\Framework\Tests\Integration\API\HTML\Includes\HTML_Test_Case;

require_once __DIR__ . '/includes/class-html-test-case.php';

/**
 * Class Tests_BeansOpenMarkupE
 *
 * @package Beans\Framework\Tests\Integration\API\HTML
 * @group   api
 * @group   api-html
 */
class Tests_BeansOpenMarkupE extends HTML_Test_Case {

	/**
	 * Test beans_open_markup_e() should echo an empty string when the tag is set to null.
	 */
	public function test_should_echo_empty_when_tag_set_to_null() {
		ob_start();
		beans_open_markup_e( 'beans_archive_title', null, array( 'class' => 'uk-article-title' ) );
		$this->assertEquals( '', ob_get_clean() );
	}

	/**
	 * Test beans_open_markup_e() should echo the HTML element only when neither the before nor prepend hooks are not
	 * registered.
	 */
	public function test_should_echo_html_element_when_hooks_not_registered() {
		ob_start();
		beans_open_markup_e( 'beans_archive_title', 'h1', array( 'class' => 'uk-article-title' ) );
		$actual = ob_get_clean();

		$this->assertSame( '<h1 class="uk-article-title">', $actual );
		$this->assertEquals( 0, did_action( 'beans_archive_title_before_markup' ) );
		$this->assertEquals( 0, did_action( 'beans_archive_title_prepend_markup' ) );
		$this->assertEquals( 0, did_action( 'beans_archive_title_after_markup' ) );
	}

	/**
	 * Test beans_open_markup_e() should echo the HTML element with the "data-markup-id" when in development mode.
	 */
	public function test_should_echo_html_element_with_data_markup_id_when_in_dev_mode() {
		add_option( 'beans_dev_mode', 1 );

		ob_start();
		beans_open_markup_e( 'beans_archive_title', 'h1', array( 'class' => 'uk-article-title' ) );
		$this->assertSame( '<h1 class="uk-article-title" data-markup-id="beans_archive_title">', ob_get_clean() );
	}

	/**
	 * Test beans_open_markup_e() should echo the before, element, and prepend HTML when callbacks are registered to
	 * the "_before_markup" and "_prepend_markup" hooks.
	 */
	public function test_should_echo_before_element_prepend_html_when_before_or_prepend_hooks() {
		add_action( 'beans_archive_title_before_markup', function() {
			echo '<!-- _before_markup fired -->';
		} );
		add_action( 'beans_archive_title_prepend_markup', function() {
			echo '<!-- _prepend_markup fired -->';
		} );

		ob_start();
		beans_open_markup_e( 'beans_archive_title', 'h1', array( 'class' => 'uk-article-title' ) );
		$actual = ob_get_clean();

		$expected = <<<EOB
<!-- _before_markup fired --><h1 class="uk-article-title"><!-- _prepend_markup fired -->
EOB;

		// Run the tests.
		$this->assertSame( $expected, $actual );
		$this->assertEquals( 1, did_action( 'beans_archive_title_before_markup' ) );
		$this->assertEquals( 1, did_action( 'beans_archive_title_prepend_markup' ) );
		$this->assertEquals( 0, did_action( 'beans_archive_title_after_markup' ) );
	}
}
