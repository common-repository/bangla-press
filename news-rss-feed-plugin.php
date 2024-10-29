<?php
/*
Plugin Name: Bangla Press Pro
Plugin URI: https://cxrana.wordpress.com
Description: Bangla Press Pro displays news titles, thumbnails, and summaries from RSS feeds. Customize the number of items, whether to show photos, and the summary word count. Easily integrate news feeds into posts, pages, or widgets.
Version: 1.1
Author: Anowar Hossain Rana
Author URI: https://cxrana.wordpress.com
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html

*/


// Register settings page
require_once plugin_dir_path(__FILE__) . 'admin/settings-page.php';

// Register widget
require_once plugin_dir_path(__FILE__) . 'widgets/news-widget.php';
// Include the documentation file
require_once plugin_dir_path(__FILE__) . 'admin/documentation.php';


function display_rss_titles_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'feed_url' => '',  // URL of the RSS feed
            'num_items' => 5,  // Number of titles to display
        ), 
        $atts
    );

    $feed_url = esc_url($atts['feed_url']);
    $num_items = intval($atts['num_items']);

    // Fetch RSS feed items
    $rss = fetch_feed($feed_url);
    if (is_wp_error($rss)) {
        return '<p>Error fetching the RSS feed.</p>';
    }

    $output = '<div class="rss-title-only-shortcode">';
    $output .= '<ul class="rss-title-list">'; // Start the unordered list
    
    if ($rss->get_item_quantity($num_items) > 0) {
        foreach ($rss->get_items(0, $num_items) as $item) {
            $title = $item->get_title();
            $link = $item->get_link();
            
            // Display title with bullet
            $output .= '<li><a href="' . esc_url($link) . '" target="_blank">' . esc_html($title) . '</a></li>';
        }
    } else {
        $output .= '<li>No titles found.</li>';
    }

    $output .= '</ul>'; // End the unordered list
    $output .= '</div>';
    
    return $output;
}

// Bangla Press Pro-Register the shortcode for titles only
add_shortcode('rss_titles', 'display_rss_titles_shortcode');

function display_rss_news_with_photos_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'feed_url' => '',  // URL of the RSS feed
            'num_items' => 5,  // Number of news items to display
            'show_photos' => true, // Show or hide photos
            'word_count' => 50, // Word count for news summary
        ), 
        $atts
    );

    $feed_url = esc_url($atts['feed_url']);
    $num_items = intval($atts['num_items']);
    $show_photos = filter_var($atts['show_photos'], FILTER_VALIDATE_BOOLEAN);
    $word_count = intval($atts['word_count']);

    // Fetch RSS feed items
    $rss = fetch_feed($feed_url);
    if (is_wp_error($rss)) {
        return '<p>Error fetching the RSS feed.</p>';
    }

    $output = '<div class="rss-news-shortcode">';
    
    if ($rss->get_item_quantity($num_items) > 0) {
        foreach ($rss->get_items(0, $num_items) as $item) {
            $title = $item->get_title();
            $link = $item->get_link();
            $description = wp_trim_words($item->get_description(), $word_count);
            $output .= '<div class="rss-news-item-short">';
            
            // Display title
            $output .= '<h3><a href="' . esc_url($link) . '" target="_blank">' . esc_html($title) . '</a></h3>';
            
            // Show photos if enabled and available
            if ($show_photos && $enclosure = $item->get_enclosure()) {
                $image_url = $enclosure->get_link();
                if ($image_url) {
                    $output .= '<div class="rss-news-thumbnail"><img src="' . esc_url($image_url) . '" alt="' . esc_attr($title) . '"></div>';
                }
            }
            
            // Show the news summary
            $output .= '<p>' . esc_html($description) . '</p>';
            $output .= '</div>';
        }
    } else {
        $output = '<p>No news items found.</p>';
    }

    $output .= '</div>';
    
    return $output;
}

// Bangla Press Pro - Register the shortcode for news with photos and summary
add_shortcode('rss_news_with_photos', 'display_rss_news_with_photos_shortcode');


// Activate settings
function news_rss_feed_plugin_activate() {
    add_option('news_rss_feeds', []);
}
register_activation_hook(__FILE__, 'news_rss_feed_plugin_activate');

// Deactivate settings
function news_rss_feed_plugin_deactivate() {
    delete_option('news_rss_feeds');
}
register_deactivation_hook(__FILE__, 'news_rss_feed_plugin_deactivate');
?>
