<?php
function news_rss_feed_plugin_documentation_page() {
    ?>
    <div class="news-rss-documentation-wrapper">
        <h1><span class="dashicons dashicons-book-alt"></span> Bangla Press Pro Documentation</h1>
        <p>Welcome to the documentation page of Bangla Press Pro. This plugin allows you to display news from any newspapers or websites using RSS feeds on your site, offering various customization options for displaying headlines, summaries, and images.</p>

        <section class="doc-section">
            <h2>Adding a New Newspaper or Website RSS Feed</h2>
            <div class="section-content">
                <img src="<?php echo plugin_dir_url(__FILE__) . 'images/example.png'; ?>" alt="Bangla Press Pro Feed Settings" style="width: 500px; height:260px; float: right;">
                <p>To add a new RSS feed:</p>
                <ol>
                    <li>Navigate to the plugin's settings page.</li>
                    <li>Enter Newspaper Name.</li>
                    <li>Enter the Feed URL.</li>
                    <li>Specify the Number of News Items.</li>
                    <li>Choose to Show Photos: YES/NO.</li>
                    <li>Set the Word Count for Summary.</li>
                    <li>Save the settings. The new feed will be available in the widget dropdown menu.</li>
                </ol>
            </div>
        </section>

        <section class="doc-section">
            <h2>How to Use Shortcode</h2>
            <p>Bangla Press Pro allows you to display news feeds anywhere on your site using a shortcode. Place the shortcode inside any post or page to show RSS feeds.</p>

            <h3>News With Photos</h3>
            <pre><code>[rss_news_with_photos feed_url="https://www.nbcnews.com/feed" num_items="5" show_photos="true" word_count="20"]</code></pre>

            <h3>Shortcode Attributes</h3>
            <ul>
                <li><strong>feed_url</strong> - The URL of the RSS feed you want to display (e.g., "https://www.nbcnews.com/feed").</li>
                <li><strong>num_items</strong> - The number of news items to display (default is 5).</li>
                <li><strong>show_photos</strong> - Display thumbnail images (set to "true" or "false").</li>
                <li><strong>word_count</strong> - Set the word limit for each news item summary (default is 20).</li>
            </ul>

            <h3>Display Only News Title</h3>
            <p>To display 5 news items from "NDTV-India" with only titles, use the following shortcode:</p>
            <pre><code>[rss_titles feed_url="https://feeds.feedburner.com/NDTV-LatestNews" num_items="5"]</code></pre>

            <p>Customize the URL to display different news feeds or adjust the number of items as needed.</p>
        </section>

        <section class="doc-section">
            <h2>Display in Widget</h2>
            <p>To display news in widgets, you have two options:</p>
            <ul>
                <li><span style="font-weight: bold; background-color: yellow;">Title Only - Display News Title Only.</span></li>
                <li><span style="font-weight: bold; background-color: yellow;">News With Photos and Summary - Display News With Photo and Summary.</span></li>
            </ul>
        </section>

        <section class="doc-section">
            <h2>How to Get Any Newspapers/Website RSS Feed URL?</h2>
            <p>To generate an RSS feed URL, you can use the <a href="https://rssgenerator.mooo.com/rss-generator/" target="_blank">RSS Feed Generator</a>.</p>
        </section>

        <section class="doc-section">
            <h2>Support & Feedback</h2>
            <div class="support-feedback">
                <p>If you need help, please contact our support team via <a href="mailto:cxranabd@gmail.com">cxranabd@gmail.com</a></p>
                <p>Visit our website: <a href="https://cxrana.wordpress.com/2024/09/10/bangla-press-pro/" target="_blank">Plugins Home page</a></p>
                <img src="<?php echo plugin_dir_url(__FILE__) . 'images/bangla-press-logo.png'; ?>" alt="Logo" class="support-logo">
            </div>
        </section>

    </div>
    <style>
        .news-rss-documentation-wrapper {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }

        .news-rss-documentation-wrapper h1 {
            color: #2fafbc;
            font-size: 28px;
            border-bottom: 2px solid #2fafbc;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .news-rss-documentation-wrapper h2 {
            color: #23282d;
            font-size: 22px;
            margin-top: 30px;
            margin-bottom: 10px;
            padding-left: 30px;
            position: relative;
            background: #2fafbc;
            color: white;
            padding: 10px;
            border-radius: 8px;
        }

        .news-rss-documentation-wrapper p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .news-rss-documentation-wrapper ul, .news-rss-documentation-wrapper ol {
            margin-left: 40px;
            margin-bottom: 20px;
        }

        .news-rss-documentation-wrapper ul li, .news-rss-documentation-wrapper ol li {
            margin-bottom: 10px;
        }

        .doc-section {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        pre {
            background: #f1f1f1;
            padding: 10px;
            border-radius: 5px;
            font-family: "Courier New", Courier, monospace;
        }

        a {
            color: #0073aa;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .support-feedback {
            position: relative;
            padding: 5px;
        }

        .support-logo {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 150px; /* Adjust size as needed */
            height: auto;
        }
    </style>
    <?php
}
