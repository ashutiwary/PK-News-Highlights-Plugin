<?php
/**
 * Plugin Name: PK News Highlights
 * Description: Add and display news headlines with summaries and links.
 * Version: 1.0
 * Author: Ashu Tiwary
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
    <p style="margin-top:20px; color:#555;">
        For Display these Posts on Frontend use Shortcode <code>[pk_news_highlights]</code>
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

// Shortcode to Display News Highlights
function pk_display_news_highlights() {
    $args = array(
        'post_type' => 'pk_news_highlight',
        'posts_per_page' => get_option('pk_news_count', 5),
        'orderby' => 'date',
        'order' => 'DESC',
    );
    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $output = '<div class="pk-news-highlights">';
        while ($query->have_posts()) {
            $query->the_post();
            $summary = get_post_meta(get_the_ID(), '_pk_summary', true);
            $link = get_post_meta(get_the_ID(), '_pk_link', true);
            $output .= '<div class="pk-news-item">';
            $output .= '<h3><a href="' . esc_url($link) . '" target="_blank">' . get_the_title() . '</a></h3>';
            $output .= '<p>' . esc_html($summary) . '</p>';
            $output .= '</div>';
        }
        wp_reset_postdata();
        $output .= '</div>';
        return $output;
    } else {
        return '<p>No news highlights found.</p>';
    }
}
add_shortcode('pk_news_highlights', 'pk_display_news_highlights');


// Admin Settings Page for manage post count for display in frontend
function pk_register_settings() {
    add_submenu_page('edit.php?post_type=pk_news_highlight', 'PK News Settings', 'Settings','manage_options','pk-news-settings','pk_news_settings_page');

    function pk_news_settings_page() {
    ?>
    <div class="wrap">
        <h1>PK News Highlights Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('pk_news_settings_group'); ?>
            <?php do_settings_sections('pk_news_settings_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Number of Highlights to Display</th>
                    <td><input type="number" name="pk_news_count" value="<?php echo esc_attr(get_option('pk_news_count', 5)); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
    }
    register_setting('pk_news_settings_group', 'pk_news_count');
}
add_action('admin_menu', 'pk_register_settings');

?>