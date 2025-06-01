<?php
/**
 * Plugin Name: PK News Highlights
 * Description: Add and display news headlines with summaries and links.
 * Version: 1.0
 * Author: Your Name
 * Text Domain: pk-news-highlights
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Register Custom Post Type
function pk_register_news_post_type() {
    $labels = array(
        'name' => 'News Highlights',
        'singular_name' => 'News Highlight',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Highlight',
        'edit_item' => 'Edit Highlight',
        'new_item' => 'New Highlight',
        'view_item' => 'View Highlight',
        'search_items' => 'Search Highlights',
        'not_found' => 'No Highlights Found',
    );

    $args = array(
        'label' => 'News Highlights',
        'labels' => $labels,
        'public' => false,
        'show_ui' => true,
        'menu_icon' => 'dashicons-megaphone',
        'supports' => array('title'),
    );

    register_post_type('pk_news_highlight', $args);
}
add_action('init', 'pk_register_news_post_type');


?>