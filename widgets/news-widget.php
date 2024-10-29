<?php
class News_RSS_Feed_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'news_rss_feed_widget',
            __('Display News With Photo', 'text_domain'),
            array( 'description' => __( 'Displays news from an RSS feed', 'text_domain' ), )
        );
    }

    // Bangla Press Pro - Front-end display of widget
    public function widget( $args, $instance ) {
        $news_feeds = get_option('news_rss_feeds', []);

        if (!empty($news_feeds)) {
            echo $args['before_widget'];

            if ( ! empty( $instance['title'] ) ) {
                echo '<div class="rss-news-title-wrapper">';
                echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
                echo '</div>';
            }

            $selected_feed = $instance['rss_feed'];

            // Find the feed URL based on the selected newspaper name
            $feed_url = '';
            foreach ($news_feeds as $feed) {
                if ($feed['name'] == $selected_feed) {
                    $feed_url = $feed['url'];
                    $num_items = isset($feed['num_items']) ? $feed['num_items'] : 5;
                    $show_photos = isset($feed['show_photos']) ? $feed['show_photos'] : 0;
                    $word_count = isset($feed['word_count']) ? $feed['word_count'] : 20;
                    break;
                }
            }

            // Fetch and display the RSS feed
            if ($feed_url) {
                $rss = fetch_feed($feed_url);

                if (!is_wp_error($rss)) {
                    $max_items = $rss->get_item_quantity($num_items);
                    $rss_items = $rss->get_items(0, $max_items);

                    if ($max_items == 0) {
                        echo '<p>No news items available.</p>';
                    } else {
                        echo '<div class="rss-news-widget">'; // Start news widget container

                        foreach ($rss_items as $item) {
                            $title = $item->get_title();
                            $description = wp_trim_words($item->get_description(), $word_count);
                            $link = $item->get_permalink();

                            // Extract first image from the content if show_photos is enabled
                            $content = $item->get_content(); // Get the full content of the feed item
                            $image_url = '';

                            if ($show_photos && !empty($content)) {
                                $image_url = $this->extract_first_image($content);
                            }

                            echo '<div class="rss-news-item">';

                            // Display the news title at the top and make it clickable
                            echo '<div class="rss-news-title-wrapper">';
                            echo '<h3 class="rss-news-title"><a href="' . esc_url($link) . '" target="_blank">' . esc_html($title) . '</a></h3>';
                            echo '</div>'; // End of title wrapper

                            // Display the extracted image if found
                            if ($show_photos && !empty($image_url)) {
                                echo '<div class="rss-news-image-wrapper" style="background-image: url(' . esc_url($image_url) . ');">';
                                echo '<div class="rss-news-title-overlay">';
                                echo '</div>'; // End of title overlay
                                echo '</div>'; // End of image wrapper
                            }

                            // Display the news summary below the photo or at the top if no photo
                            echo '<div class="rss-news-content">';
                            echo '<p class="rss-news-description">' . esc_html($description) . '</p>';
                            echo '</div>'; // End of news content

                            echo '</div>'; // End of news item
                        }

                        echo '</div>'; // End of news widget container
                    }
                } else {
                    echo '<p>Failed to fetch RSS feed. Please check the feed URL.</p>';
                }
            } else {
                echo '<p>No RSS feed selected.</p>';
            }

            echo $args['after_widget'];
        }
    }

    // Function to extract the first image from the RSS item content
    private function extract_first_image($content) {
        // Use a regular expression to find the first <img> tag and extract the src attribute
        preg_match('/<img[^>]+src=["\']([^"\']+)["\']/', $content, $matches);
        if (isset($matches[1])) {
            return $matches[1]; // Return the first image URL found
        }
        return ''; // Return empty string if no image is found
    }

    // Widget settings form in WP admin
    public function form( $instance ) {
        $news_feeds = get_option('news_rss_feeds', []);
        $selected_feed = ! empty( $instance['rss_feed'] ) ? $instance['rss_feed'] : '';

        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:' ); ?></label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
        </p>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'rss_feed' ) ); ?>"><?php _e( 'Select Newspaper:' ); ?></label>
            <select id="<?php echo esc_attr( $this->get_field_id( 'rss_feed' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'rss_feed' ) ); ?>" class="widefat">
                <?php foreach ($news_feeds as $feed) : ?>
                    <option value="<?php echo esc_attr($feed['name']); ?>" <?php selected($selected_feed, $feed['name']); ?>><?php echo esc_html($feed['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php 
    }

    // Save widget settings
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['rss_feed'] = (!empty($new_instance['rss_feed'])) ? strip_tags($new_instance['rss_feed']) : '';
        return $instance;
    }
}

// Register the widget
function register_news_rss_feed_widget() {
    register_widget('News_RSS_Feed_Widget');
}
add_action('widgets_init', 'register_news_rss_feed_widget');

class News_RSS_Title_Only_Widget extends WP_Widget {

    function __construct() {
        parent::__construct(
            'news_rss_title_only_widget',
            __('Display News Title Only', 'text_domain'),
            array( 'description' => __( 'Displays only news titles from an RSS feed', 'text_domain' ), )
        );
    }

    // Front-end display of the widget
 public function widget( $args, $instance ) {
    $news_feeds = get_option('news_rss_feeds', []);

    if (!empty($news_feeds)) {
        echo $args['before_widget'];

        if ( ! empty( $instance['title'] ) ) {
            echo '<div class="rss-news-title-wrapper">';
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
            echo '</div>';
        }

        $selected_feed = $instance['rss_feed'];

        // Find the feed URL based on the selected newspaper name
        $feed_url = '';
        foreach ($news_feeds as $feed) {
            if ($feed['name'] == $selected_feed) {
                $feed_url = $feed['url'];
                $num_items = isset($feed['num_items']) ? $feed['num_items'] : 5;
                break;
            }
        }

        // Fetch and display the RSS feed titles
        if ($feed_url) {
            $rss = fetch_feed($feed_url);

            if (!is_wp_error($rss)) {
                $max_items = $rss->get_item_quantity($num_items);
                $rss_items = $rss->get_items(0, $max_items);

                if ($max_items == 0) {
                    echo '<p>No news items available.</p>';
                } else {
                    echo '<div class="rss-news-title-only-widget">'; // Start the box container
                    echo '<ul>'; // Start the bullet list

                    foreach ($rss_items as $item) {
                        $title = $item->get_title();
                        $link = $item->get_permalink();

                        // Display the news title as a list item
                        echo '<li>';
                        echo '<h3 class="rss-news-title"><a href="' . esc_url($link) . '" target="_blank">' . esc_html($title) . '</a></h3>';
                        echo '</li>'; // End of news title-only item
                    }

                    echo '</ul>'; // End the bullet list
                    echo '</div>'; // End the box container
                }
            } else {
                echo '<p>Failed to fetch RSS feed. Please check the feed URL.</p>';
            }
        } else {
            echo '<p>No RSS feed selected.</p>';
        }

        echo $args['after_widget'];
    }
}


    // Widget settings form in WP admin
    public function form( $instance ) {
        $news_feeds = get_option('news_rss_feeds', []);
        $selected_feed = ! empty( $instance['rss_feed'] ) ? $instance['rss_feed'] : '';

        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:' ); ?></label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
        </p>

        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'rss_feed' ) ); ?>"><?php _e( 'Select Newspaper:' ); ?></label>
            <select id="<?php echo esc_attr( $this->get_field_id( 'rss_feed' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'rss_feed' ) ); ?>" class="widefat">
                <?php foreach ($news_feeds as $feed) : ?>
                    <option value="<?php echo esc_attr($feed['name']); ?>" <?php selected($selected_feed, $feed['name']); ?>><?php echo esc_html($feed['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php 
    }

    // Save widget settings
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['rss_feed'] = (!empty($new_instance['rss_feed'])) ? strip_tags($new_instance['rss_feed']) : '';
        return $instance;
    }
}

// Register the new widget
function register_news_rss_title_only_widget() {
    register_widget('News_RSS_Title_Only_Widget');
}
add_action('widgets_init', 'register_news_rss_title_only_widget');

// Enqueue widget styles
function news_rss_widget_styles() {
    ?>
    <style type="text/css">
	.rss-news-title-wrapper {
    margin-bottom: 20px;
}

.rss-news-title-only-widget {
    padding: 10px;
    background-color: #f9f9f9;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    max-height: 400px; /* Adjust height as needed */
    overflow-y: auto; /* Add vertical scroll if content exceeds max height */
}

.rss-news-title-only-widget ul {
    list-style-type: disc; /* Bullet points */
    margin: 0;
    padding-left: 20px; /* Space for bullets */
}

.rss-news-title-only-widget li {
    margin-bottom: 10px;
    font-size: 16px;
}

.rss-news-title-only-widget h3.rss-news-title {
    margin: 0;
    font-size: 16px;
}

.rss-news-title-only-widget h3.rss-news-title a {
    color: #0073aa;
    text-decoration: none;
}

.rss-news-title-only-widget h3.rss-news-title a:hover {
    text-decoration: underline;
}

        .rss-news-title-wrapper {
            text-align: center;
            margin-bottom: 10px;
        }

        .rss-news-widget {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .rss-news-item {
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden; /* Ensure content stays within bounds */
        }
		
		.rss-news-item-short {
    border: 1px solid #ddd;
    border-radius: 10px;
    background-color: #f9f9f9;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-top: 20px;
	padding-bottom: 5px;
			}
		
		.rss-news-shortcode {
           
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #f9f9f9;
            padding: 10px;
            box-sizing: border-box;
            margin-bottom: 15px;
            text-align: center;
        }
		
		.rss-news-shortcode p {
           margin:15px;
            text-align: center;
        }
		
			.rss-news-shortcode	img {
    display: block;
    margin-left: auto;
    margin-right: auto;
    height: auto;
    max-width: 100%;
}

		
		.rss-news-shortcode a {
           margin:15px;
           text-align: center;
        }

        .rss-news-image-wrapper {
            background-size: cover;
            background-position: center;
            width: 100%;
            height: 200px;
            position: relative;
        }

        .rss-news-title-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            padding: 10px;
            box-sizing: border-box;
            color: #fff;
            text-align: center;
        }

        .rss-news-title a {
            text-decoration: none; /* Remove underline from links */
        }

        .rss-news-title a:hover {
            text-decoration: underline; /* Underline on hover */
        }

        .rss-news-content {
            padding: 10px;
        }

        .rss-news-description {
            font-size: 14px;
            margin: 0;
        }
    </style>
    <?php
}
add_action('wp_head', 'news_rss_widget_styles');
