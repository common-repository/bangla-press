<?php
function news_rss_feed_plugin_menu() {
    // Add main menu item
    add_menu_page('Bangla Press Pro', 'Bangla Press Pro', 'manage_options', 'news-rss-feed-plugin', 'news_rss_feed_plugin_settings_page');
    
    // Add submenu for documentation
    add_submenu_page('news-rss-feed-plugin', 'Bangla Press Pro-Documentation', 'Documentation', 'manage_options', 'news-rss-feed-plugin-documentation', 'news_rss_feed_plugin_documentation_page');
}
add_action('admin_menu', 'news_rss_feed_plugin_menu');

function news_rss_feed_plugin_settings_page() {
    $error_message = '';

    // Ensure $news_feeds is an array
    $news_feeds = get_option('news_rss_feeds', []);
    if (!is_array($news_feeds)) {
        $news_feeds = [];  // Convert to an array if it is not
    }

      // Handle form submission for adding feeds
    if (isset($_POST['save_rss_feeds'])) {
        foreach ($_POST['newspaper_name'] as $key => $name) {
            if (!empty($_POST['rss_feed_url'][$key]) && !empty($name)) {
                $news_feeds[] = [
                    'name' => sanitize_text_field($name),
                    'url' => esc_url($_POST['rss_feed_url'][$key]),
                    'num_items' => intval($_POST['num_items'][$key]),
                    'show_photos' => isset($_POST['show_photos'][$key]) ? 1 : 0,
                    'word_count' => intval($_POST['word_count'][$key])
                ];
            } else {
                $error_message = 'Please fill in all the fields before saving.';
            }
        }

        // Update option if no errors
        if (!$error_message) {
            update_option('news_rss_feeds', $news_feeds);
            $success_message = 'Feeds have been saved successfully.';
        }
    }

    // Handle deleting an RSS feed
    if (isset($_GET['action']) && $_GET['action'] == 'delete_feed' && isset($_GET['feed_index']) && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'delete_feed_nonce')) {
        $feed_index = intval($_GET['feed_index']);
        if (isset($news_feeds[$feed_index])) {
            unset($news_feeds[$feed_index]);
            $news_feeds = array_values($news_feeds); // Reindex array
            update_option('news_rss_feeds', $news_feeds);
            $success_message = 'Feed has been deleted successfully.';
        } else {
            $error_message = 'Feed not found.';
        }
    }

    // Get the updated news_feeds option
    $news_feeds = get_option('news_rss_feeds', []);
    ?>
    <div class="wrap"> 
        <h1 class="page-title">Bangla Press Pro Settings</h1>
        
        <?php if ($error_message) : ?>
            <div class="notice notice-error"><p><?php echo esc_html($error_message); ?></p></div>
        <?php endif; ?>

        <form method="post" action="" class="rss-feed-form">
            <div id="rss-feed-forms">
                <h2>Add Newspaper/Website Feed</h2>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Newspaper Name</th>
                        <td><input type="text" name="newspaper_name[]" class="regular-text" required /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">RSS Feed URL</th>
                        <td><input type="url" name="rss_feed_url[]" class="regular-text" required /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Number of News Items</th>
                        <td><input type="number" name="num_items[]" class="small-text" value="5" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Show Photos</th>
                        <td><input type="checkbox" name="show_photos[]" /></td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Word Count for Summary</th>
                        <td><input type="number" name="word_count[]" class="small-text" value="20" /></td>
                    </tr>
                </table>
            </div>

            <p><input type="submit" name="save_rss_feeds" value="Save" class="button button-primary" /></p>
        </form>

        <button id="add-feed-button" class="button button-secondary">Add New RSS Feed</button>

        <h2 class="section-title">Current Newspapers/Websites Feeds</h2>
        <ul class="rss-feed-list">
            <?php foreach ($news_feeds as $index => $feed) : ?>
                <li class="rss-feed-item">
                    <?php echo esc_html($feed['name']); ?> - 
                    <a href="<?php echo esc_url($feed['url']); ?>" target="_blank"><?php echo esc_url($feed['url']); ?></a>
                    - <a href="<?php echo esc_url(add_query_arg(array('action' => 'delete_feed', 'feed_index' => $index, '_wpnonce' => wp_create_nonce('delete_feed_nonce')), admin_url('admin.php?page=news-rss-feed-plugin'))); ?>" 
                         onclick="return confirm('Are you sure you want to delete this feed?');">
                         Delete
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="rss-list-section">
    <h2 class="rss-list-heading">Top Global Newspapers: Must-Read RSS Feeds!</h2>
    <div class="rss-list-content">
    <?php
    $websites_rss = [
		'NBC News' => 'https://www.nbcnews.com/feed',
		'Fox News' => 'https://moxie.foxnews.com/google-publisher/latest.xml',
		'NDTV-India' => 'https://feeds.feedburner.com/NDTV-LatestNews',
        'Prothom Alo-Bangla' => 'https://www.prothomalo.com/rss',
        'The Daily Star' => 'https://www.thedailystar.net/rss.xml',
        'The New York Times' => 'https://www.nytimes.com/section/world/rss.xml',
        'Dhaka Tribune-Bangla' => 'https://www.dhakatribune.com/rss',
        'Daily Ittefaq' => 'https://www.ittefaq.com.bd/rss',
        'Kaler Kantho' => 'https://www.kalerkantho.com/rss',
        'Bangladesh Pratidin' => 'https://www.bd-pratidin.com/rss',
        'Naya Diganta' => 'https://www.nayadiganta.com/rss',
        'The Independent' => 'https://www.theindependentbd.com/rss',
        'Daily Jugantor' => 'https://www.jugantor.com/rss',
        'The Financial Express' => 'https://www.thefinancialexpress.com.bd/rss',
        'BBC News - BBC' => 'https://rssgenerator.mooo.com/feeds/?p=aaHR0cHM6Ly93d3cuYmJjLmNvbS9uZXdzL3dvcmxk',
        'AP News' => 'https://apnews.com/index.rss',
        'Jago News' => 'https://www.jagonews24.com/rss/rss.xml',
        'Bangla Tribune' => 'https://www.banglatribune.com/feed/',
        'RisingBD' => 'https://www.risingbd.com/rss/rss.xml',
        'Bangladesh Sangbad Sangstha - BSS News' => 'https://rssgenerator.mooo.com/feeds/?p=aaHR0cHM6Ly93d3cuYnNzbmV3cy5uZXQv',
        'Time - World' => 'https://feeds.feedburner.com/time/world',
        'The Washington Times' => 'https://www.washingtontimes.com/rss/headlines/news/world'
    ];

    foreach ($websites_rss as $name => $url) {
        echo '<div class="rss-feed-item">';
        echo '<strong>' . esc_html($name) . '</strong> - <a href="' . esc_url($url) . '" target="_blank">' . esc_url($url) . '</a>';
        echo '</div>';
    }
    ?>
</div>

</div>
 <!-- Floating Button -->
<a href="https://rssgenerator.mooo.com/rss-generator/" class="rss-feed-maker-button" target="_blank">
    <i class="dashicons dashicons-rss"></i> <span>RSS Feed Generator</span>
</a>


        <!-- Developer Credit Section -->
        <div class="ecmt-credit">
            <div class="ecmt-credit-bar"></div>
           <h2 class="ecmt-subheading">Developer</h2>
            <div class="wp-ecmt-social-icons">
                Contact with the developer via WhatsApp - 
                <a href="https://wa.me/+8801811355151" class="social-icon whatsapp-icon"><i class="dashicons dashicons-whatsapp"></i></a>
                <a href="https://facebook.com/cxrana" class="social-icon facebook-icon"><i class="dashicons dashicons-facebook-alt"></i></a>
                <a href="https://www.linkedin.com/in/ahrana/" class="social-icon linkedin-icon"><i class="dashicons dashicons-linkedin"></i></a>
                <a href="https://cxrana.wordpress.com/" class="social-icon wordpress-icon"><i class="dashicons dashicons-wordpress"></i></a>
            </div>
            <!-- Developer Logo -->
			<div class="ecmt-developer-logo">
				<a href="https://cxrana.wordpress.com/">
        <img src="<?php echo plugin_dir_url(__FILE__) . 'learn-with-rana.png'; ?>" alt="Learn with Rana">
		</a>
		</div>
        </div>
    </div>

    <style>
	
	 .rss-feed-maker-button {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #0073aa;
    color: #ffffff;
    padding: 12px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    font-size: 16px;
    text-align: center;
    line-height: 1.4;
    text-decoration: none;
    z-index: 1000;
    transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s, color 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.rss-feed-maker-button:hover {
    background-color: #005a87;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4);
    transform: translateY(-2px);
    color: #ffffff; /* Ensure text color is white on hover */
}

.rss-feed-maker-button i {
    font-size: 20px;
    margin-right: 8px;
    transition: color 0.3s; /* Smooth color transition */
}

.rss-feed-maker-button:hover i {
    color: #ffffff; /* Ensure icon color is white on hover */
}

.rss-feed-maker-button span {
    font-weight: bold;
}

	
	.rss-list-section {
    background-color: #ffffff;
    border: 1px solid #dcdcdc;
    border-radius: 5px;
    padding: 20px;
    margin-top: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.rss-list-heading {
    font-size: 24px;
    margin-bottom: 15px;
    color: #0073aa;
    border-bottom: 2px solid #0073aa;
    padding-bottom: 10px;
}

.rss-list-content {
    max-height: 200px; /* Adjust as needed */
    overflow-y: auto;
    padding: 10px;
    border: 1px solid #dcdcdc;
    border-radius: 5px;
    background-color: #f9f9f9;
}

.rss-feed-item {
    margin-bottom: 10px;
    padding: 5px 0;
}

.rss-feed-item a {
    color: #0073aa;
    text-decoration: none;
}

.rss-feed-item a:hover {
    text-decoration: underline;
}

 .wp-ecmt-social-icons {
            margin-top: 30px;
            display: flex;
            align-items: center;
        }

        .wp-ecmt-social-icons .social-icon {
            font-size: 24px;
            margin-right: 15px;
            transition: color 0.3s, transform 0.3s;
            text-decoration: none; /* Remove underline */
        }

        .wp-ecmt-social-icons .social-icon:hover {
            transform: scale(1.2);
        }

        .whatsapp-icon {
            color: #25D366;
        }

        .facebook-icon {
            color: #4267B2;
        }

        .linkedin-icon {
            color: #0A66C2;
        }

        .wordpress-icon {
            color: #21759B;
        }

        .whatsapp-icon:hover {
            color: #128C7E;
        }

        .facebook-icon:hover {
            color: #3B5998;
        }

        .linkedin-icon:hover {
            color: #0A43A6;
        }

        .wordpress-icon:hover {
            color: #1E7C8C;
        }

        .ecmt-credit {
            margin-top: 40px;
            padding: 10px;
            background-color: #f1f1f1;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .ecmt-credit-bar {
            height: 5px;
            background: linear-gradient(90deg, #25D366, #4267B2, #0A66C2, #21759B);
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .ecmt-heading {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .ecmt-subheading {
            font-size: 20px;
            margin-top: 40px;
            margin-bottom: 20px;
        }

        .ecmt-form {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .ecmt-table th {
            width: 250px;
        }

        .ecmt-select, .ecmt-file-input, .ecmt-input {
            width: 100%;
        }

        .ecmt-button {
            background-color: #0073aa;
            border-color: #0073aa;
            color: #fff;
        }

        .ecmt-button:hover {
            background-color: #006799;
            border-color: #006799;
        }

        .ecmt-success-message {
            margin-top: 20px;
            padding: 10px;
            background-color: #dff0d8;
            border-left: 4px solid #d0e9c6;
        }

        .ecmt-warning-message {
            margin-top: 20px;
            padding: 10px;
            background-color: #fff3cd;
            border-left: 4px solid #ffeeba;
        }

        .ecmt-developer-logo {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 100px; /* Adjust the width */
            height: 100px; /* Adjust the height */
            overflow: hidden;
        }

        .ecmt-developer-logo img {
            width: 100%;
            height: auto;
        }

        .wrap {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .page-title {
            font-size: 28px;
            margin-bottom: 20px;
            color: #0073aa;
        }

        .rss-feed-form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
			margin-top: 10px;
        }

        .rss-feed-form .form-table {
            margin-bottom: 20px;
        }

        .rss-feed-form .form-table th {
            width: 220px;
            padding: 10px;
            background-color: #f1f1f1;
            text-align: left;
        }

        .rss-feed-form .form-table td {
            padding: 10px;
        }

        .section-title {
            font-size: 24px;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 2px solid #0073aa;
            padding-bottom: 12px;
        }
		.wp-core-ui .button-primary {
    background: #2271b1;
    border-color: #2271b1;
    color: #fff;
    text-decoration: none;
    text-shadow: none;
    width: 70px;
    height: 40px;
    font-size: 17px;
}

        .rss-feed-list {
            list-style: none;
            padding: 0;
        }

        .rss-feed-list .rss-feed-item {
            background-color: #ffffff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .rss-feed-list .rss-feed-item a {
            color: #0073aa;
            text-decoration: none;
        }

        .rss-feed-list .rss-feed-item a:hover {
            text-decoration: underline;
        }

        .rss-list-pre {
            background-color: #ffffff;
            border: 1px solid #dcdcdc;
            border-radius: 5px;
            padding: 10px;
            margin-top: 20px;
            overflow: auto;
            font-size: 14px;
        }

        .rss-list-pre code {
            display: block;
        }
    </style>

    <script type="text/javascript">
    document.getElementById('add-feed-button').addEventListener('click', function() {
        var feedForm = `
            <h2>Add Newspaper Feed</h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Newspaper Name</th>
                    <td><input type="text" name="newspaper_name[]" class="regular-text" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">RSS Feed URL</th>
                    <td><input type="url" name="rss_feed_url[]" class="regular-text" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Number of News Items</th>
                    <td><input type="number" name="num_items[]" class="small-text" value="5" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Show Photos</th>
                    <td><input type="checkbox" name="show_photos[]" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Word Count for Summary</th>
                    <td><input type="number" name="word_count[]" class="small-text" value="20" /></td>
                </tr>
            </table>`;
        document.getElementById('rss-feed-forms').insertAdjacentHTML('beforeend', feedForm);
    });
    </script>
    <?php
}
?>
