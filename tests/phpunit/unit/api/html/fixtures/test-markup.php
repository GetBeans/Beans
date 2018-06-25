<?php
/**
 * Array of test HTML markup and attributes.
 *
 * @package Beans\Framework\Tests\Unit\API\HTML\Fixtures
 *
 * @since   1.5.0
 */

return [
	'beans_post'                    => [
		'id'         => 'beans_post',
		'tag'        => 'article',
		'attributes' => [
			'id'        => 47,
			'class'     => 'uk-article uk-panel-box post-47 post type-post status-publish format-standard has-post-thumbnail hentry category-beans',
			'itemscope' => 'itemscope',
			'itemtype'  => 'https://schema.org/blogPost',
			'itemprop'  => 'beans_post',
		],
	],
	'beans_post_header'             => [
		'id'  => 'beans_post_header',
		'tag' => 'header',
	],
	'beans_post_body'               => [
		'id'         => 'beans_post_body',
		'tag'        => 'div',
		'attributes' => [
			'itemprop' => 'articleBody',
		],
	],
	'beans_post_title'              => [
		'id'         => 'beans_post_title',
		'tag'        => 'h1',
		'attributes' => [
			'class'    => 'uk-article-title',
			'itemprop' => 'headline',
		],
	],
	'beans_post_meta'               => [
		'id'         => 'beans_post_meta',
		'tag'        => 'ul',
		'attributes' => [
			'class' => 'uk-article-meta uk-subnav uk-subnav-line',
		],
	],
	'beans_post_meta_item[_date]'   => [
		'id'  => 'beans_post_meta_item[_date]',
		'tag' => 'li',
	],
	'beans_post_meta_item[_author]' => [
		'id'  => 'beans_post_meta_item[_author]',
		'tag' => 'li',
	],
	'beans_post_image_link'         => [
		'id'         => 'beans_post_image_link',
		'tag'        => 'a',
		'attributes' => [
			'href'  => 'http://example.com/image.png',
			'title' => 'Some cool image',
		],
	],
];
