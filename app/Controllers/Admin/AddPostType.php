<?php
namespace WPEBCalender\App\Controllers\Admin;

class AddPostType
{
	public function __construct()
	{
		add_action('init', [$this, 'init']);
	}
	public function init()
	{
		$this->register_event_calender();
		$this->register_events_taxonomy();
	}
	public function register_events_taxonomy()
	{
		register_taxonomy("category", "wpebcalender_events", [
			"label" => esc_html__("Event Category", "wpebcalender"),
			'hierarchical' => true,
			'rewrite' => ['slug' => 'wpebcalender_category'],
		]);
	}
	public function register_event_calender()
	{
		$labels = [
			'name' => _x('Events', 'Post Type General Name', 'wpebcalender'),
			'singular_name' => _x('Event', 'Post Type Singular Name', 'wpebcalender'),
			'menu_name' => __('WPEvent', 'wpebcalender'),
			'parent_item_colon' => __('Parent Event', 'wpebcalender'),
			'all_items' => __('All Events', 'wpebcalender'),
			'view_item' => __('View Event', 'wpebcalender'),
			'add_new_item' => __('Add Event', 'wpebcalender'),
			'add_new' => __('Add New Event', 'wpebcalender'),
			'edit_item' => __('Edit Event', 'wpebcalender'),
			'update_item' => __('Update Event', 'wpebcalender'),
			'search_items' => __('Search Event', 'wpebcalender'),
			'not_found' => __('Not Event found', 'wpebcalender'),
			'not_found_in_trash' => __('Not found in Trash', 'wpebcalender'),
		];
		$args = [
			'label' => esc_html__('Events', 'wpebcalender'),
			'labels' => $labels,
			'supports' => ['title', 'editor', 'thumbnail', 'revisions', 'page-attributes', 'comments', 'author', 'blocks'],
			'hierarchical' => true,
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => true,
			'show_in_admin_bar' => true,
			'menu_icon' => 'dashicons-format-gallery',
			'can_export' => true,
			'has_archive' => 'Events',
			'exclude_from_search' => false,
			'publicly_queryable' => true,
			'show_in_rest' => true,
			"rewrite" => ["slug" => "wpebcalender_events", "with_front" => true],
			"query_var" => true,
			'map_meta_cap' => true,
			"show_in_graphql" => false,
		];
		register_post_type("wpebcalender_events", $args);
	}
}
