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


// Add Meta Boxes for Summary and Link
function pk_add_news_meta_boxes() {
    add_meta_box('pk_news_meta', 'Highlight Details', 'pk_news_meta_box_callback', 'pk_news_highlight', 'normal', 'high');
}

// Callback function for meta box
function pk_news_meta_box_callback($post) {
    $summary = get_post_meta($post->ID, '_pk_summary', true);
    $link = get_post_meta($post->ID, '_pk_link', true);
    ?>
    <p>
        <label for="pk_summary">Summary:</label><br>
        <textarea id="pk_summary" name="pk_summary" rows="4" style="width:100%;"><?php echo esc_textarea($summary); ?></textarea>
    </p>
    <p>
        <label for="pk_link">Link:</label><br>
        <input type="url" id="pk_link" name="pk_link" value="<?php echo esc_url($link); ?>" style="width:100%;" />
    </p>
    <?php
}

function pk_save_news_meta($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (isset($_POST['pk_summary'])) {
        update_post_meta($post_id, '_pk_summary', sanitize_text_field($_POST['pk_summary']));
    }
    if (isset($_POST['pk_link'])) {
        update_post_meta($post_id, '_pk_link', esc_url_raw($_POST['pk_link']));
    }
}
add_action('add_meta_boxes', 'pk_add_news_meta_boxes');
add_action('save_post', 'pk_save_news_meta');


?>