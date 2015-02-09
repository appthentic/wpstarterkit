<?php
// require the post type generator
require_once 'lib/PostType.php';

// enable support for featured images
add_theme_support('post-thumbnails');

// enable support for html5
add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));

// enable support for title tags
add_theme_support('title-tag');

// enable automatic feed links
add_theme_support('automatic-feed-links');

// register theme menus
function register_theme_menus()
{
    register_nav_menus(
        array(
            'main-menu' => __('Main Menu'),
            'sub-menu' => __('Sub Menu'),
            'sidebar-menu' => __('Sidebar Menu'),
            'footer-menu' => __('Footer Menu'),
        )
    );
}
add_action('init', 'register_theme_menus');

// register theme sidebars
function register_theme_sidebars()
{
	$sidebars = array(
		'home',
		'page',
		'blog'
	);
    foreach($sidebars as $sidebar) {
    	$sidebar_args = array(
	        'name' => __(ucfirst($sidebar .' sidebar')),
	        'id' => $sidebar .'-sidebar',
	        'description' => '',
	        'class' => '',
	        'before_widget' => '<li id="%1$s" class="widget %2$s">',
	        'after_widget' => '</li>',
	        'before_title' => '<h2 class="widgettitle">',
	        'after_title' => '</h2>'
        );
        register_sidebar($sidebar_args);
    }
}
add_action('init', 'register_theme_sidebars');

// register theme post types
function register_theme_post_types()
{
	// testimonials
	//$testimonials = new PostType('testimonial');
}
register_theme_post_types();



