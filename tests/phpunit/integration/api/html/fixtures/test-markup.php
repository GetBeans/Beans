<?php
/**
 * Array of test HTML markup and attributes.
 *
 * @package Beans\Framework\Tests\Integration\API\HTML\Fixtures
 *
 * @since   1.5.0
 */

return array(
	'beans_post'                    => array(
		'id'         => 'beans_post',
		'tag'        => 'article',
		'attributes' => array(
			'id'        => 47,
			'class'     => 'uk-article uk-panel-box post-47 post type-post status-publish format-standard has-post-thumbnail hentry category-beans',
			'itemscope' => 'itemscope',
			'itemtype'  => 'http://schema.org/blogPost',
			'itemprop'  => 'beans_post',
		),
	),
	'beans_post_header'             => array(
		'id'  => 'beans_post_header',
		'tag' => 'header',
	),
	'beans_post_body'               => array(
		'id'         => 'beans_post_body',
		'tag'        => 'div',
		'attributes' => array(
			'itemprop' => 'articleBody',
		),
	),
	'beans_post_title'              => array(
		'id'         => 'beans_post_title',
		'tag'        => 'h1',
		'attributes' => array(
			'class'    => 'uk-article-title',
			'itemprop' => 'headline',
		),
	),
	'beans_post_meta'               => array(
		'id'         => 'beans_post_meta',
		'tag'        => 'ul',
		'attributes' => array(
			'class' => 'uk-article-meta uk-subnav uk-subnav-line',
		),
	),
	'beans_post_meta_item[_date]'   => array(
		'id'  => 'beans_post_meta_item[_date]',
		'tag' => 'li',
	),
	'beans_post_meta_item[_author]' => array(
		'id'  => 'beans_post_meta_item[_author]',
		'tag' => 'li',
	),
	'beans_post_image_link'         => array(
		'id'         => 'beans_post_image_link',
		'tag'        => 'a',
		'attributes' => array(
			'href'  => 'http://example.com/image.png',
			'title' => 'Some cool image',
		),
	),
);
