<?php
/*
Plugin Name: Content Optimizer Plugin
Description: Analyze your posts for SEO, readability, image optimization, and performance.
Version: 1.2
Author: Rijo Varghese
*/

function create_performance_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'optimizer_performance_history';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        url VARCHAR(255) NOT NULL,
        score INT NOT NULL,
        checked_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'create_performance_table');

require_once __DIR__ . '/vendor/autoload.php';
require_once plugin_dir_path(__FILE__) . 'ajax/performance_audit.php';
register_activation_hook(__FILE__, 'create_performance_table');

use Dotenv\Dotenv;
use Tinify\Tinify;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$tinify_api_key = $_ENV['TINIFY_API_KEY'] ?? null;
if ($tinify_api_key) {
    Tinify::setKey($tinify_api_key);
}

// Add admin menu
add_action('admin_menu', 'content_optimizer_add_admin_menu');
function content_optimizer_add_admin_menu()
{
    add_menu_page(
        'Content Optimizer',
        'Content Optimizer',
        'manage_options',
        'content-optimizer',
        'content_optimizer_admin_page',
        'dashicons-chart-line',
        6
    );
}

// Enqueue JS for plugin admin page
add_action('admin_enqueue_scripts', 'enqueue_performance_script');
function enqueue_performance_script($hook)
{
    if ($hook !== 'toplevel_page_content-optimizer') {
        return;
    }

    wp_enqueue_style(
        'optimizer-admin-style',
        plugin_dir_url(__FILE__) . 'css/admin_style.css',
        [],
        '1.0'
    );

    // Light (default) admin styles
    wp_enqueue_style(
        'optimizer-admin-style',
        plugin_dir_url(__FILE__) . 'css/admin_style.css',
        [],
        '1.0'
    );

    // 🌙 Dark‑mode overrides (must load AFTER the light file)
    wp_enqueue_style(
        'optimizer-admin-dark',
        plugin_dir_url(__FILE__) . 'css/admin_dark.css',
        ['optimizer-admin-style'],   // dependency
        '1.0'
    );

    // Dark‑mode toggle JS
    wp_enqueue_script(
        'optimizer-admin-dark-js',
        plugin_dir_url(__FILE__) . 'js/admin_darkmode.js',
        [],
        '1.0',
        true
    );

    wp_enqueue_script(
        'optimizer-mode-toggle',
        plugin_dir_url(__FILE__) . 'js/mode_toggle.js',
        [],
        '1.0',
        true
    );    

    wp_enqueue_script(
        'performance-audit',
        plugin_dir_url(__FILE__) . 'js/performance.js',
        [],
        '1.0',
        true
    );

    wp_localize_script('performance-audit', 'performanceAudit', [
        'ajax_url' => admin_url('admin-ajax.php'),
    ]);



}

// Admin Page Content
function content_optimizer_admin_page()
{
    ?>
    <div class="wrap">
        <!-- Dark‑/light‑mode toggle -->
        <button class="mode-toggle" id="modeToggle" aria-label="Toggle colour mode">🌙</button>
        <h1>Content Optimizer Dashboard</h1>




        <!-- SEO Form -->
        <form id="seo-form" method="post">
            <h2>SEO Analyzer</h2>

            <label for="seo_title">Post Title:</label><br>
            <input type="text" id="seo_title" name="seo_title" style="width:100%;" required><br><br>

            <label for="seo_meta_description">Meta Description:</label><br>
            <textarea id="seo_meta_description" name="seo_meta_description" rows="3" style="width:100%;"
                required></textarea><br><br>

            <label for="seo_content">Post Content:</label><br>
            <textarea id="seo_content" name="seo_content" rows="10" style="width:100%;" required></textarea><br><br>

            <label for="seo_keyword">Target Keyword:</label><br>
            <input type="text" id="seo_keyword" name="seo_keyword" style="width:100%;" required><br><br>

            <label for="seo_images">Image URLs (one per line):</label><br>
            <textarea id="seo_images" name="seo_images" rows="4" style="width:100%;"></textarea><br><br>

            <input type="submit" name="seo_analyze_submit" class="button button-primary" value="Analyze SEO">
        </form>

        <!-- Website Performance Section -->
        <form id="performance-form" onsubmit="return false;">
            <h2>Website Performance</h2>

            <label for="performance_url">Enter website URL:</label><br>
            <input type="text" id="performance_url" name="performance_url" placeholder="https://example.com"
                style="width:100%; padding: 8px; margin-top: 10px;" required><br><br>

            <button type="button" id="check-performance" class="button button-primary">Check Performance</button><br><br>

            <div id="performance-results" class="optimizer-message" style="display:none;"></div>
        </form>

        <style>
  body.dark-mode #wpwrap::after {
    content: none !important;
    display: none !important;
  }
</style>








        <?php
        // SEO Submission Handling
        if (isset($_POST['seo_analyze_submit'])) {
            $title = sanitize_text_field($_POST['seo_title']);
            $meta_description = sanitize_textarea_field($_POST['seo_meta_description']);
            $content = sanitize_textarea_field($_POST['seo_content']);
            $keyword = sanitize_text_field($_POST['seo_keyword']);
            $image_urls_raw = sanitize_textarea_field($_POST['seo_images']);

            echo '<h2>SEO Analysis Results</h2>';

            // Title Length
            $title_length = strlen($title);
            $title_class = ($title_length >= 50 && $title_length <= 60) ? 'optimizer-success' : 'optimizer-warning';
            $title_msg = ($title_length >= 50 && $title_length <= 60) ? "✅ Good." : "⚠️ Should be 50–60 characters.";
            echo "<div class='optimizer-message $title_class'><strong>Title Length:</strong> {$title_length} characters. $title_msg</div>";

            // Meta Description Length
            $meta_length = strlen($meta_description);
            $meta_class = ($meta_length >= 150 && $meta_length <= 160) ? 'optimizer-success' : 'optimizer-warning';
            $meta_msg = ($meta_length >= 150 && $meta_length <= 160) ? "✅ Good." : "⚠️ Should be 150–160 characters.";
            echo "<div class='optimizer-message $meta_class'><strong>Meta Description Length:</strong> {$meta_length} characters. $meta_msg</div>";

            // Keyword Density
            $word_count = str_word_count(strip_tags($content));
            $keyword_count = substr_count(strtolower($content), strtolower($keyword));
            $keyword_density = ($word_count > 0) ? round(($keyword_count / $word_count) * 100, 2) : 0;
            $density_class = ($keyword_density >= 1 && $keyword_density <= 2.5) ? 'optimizer-success' : 'optimizer-warning';
            $density_msg = ($keyword_density >= 1 && $keyword_density <= 2.5) ? "✅ Good." : "⚠️ Should be 1%–2.5%.";
            echo "<div class='optimizer-message $density_class'><strong>Keyword Density:</strong> {$keyword_density}% $density_msg</div>";


            // Image Optimization
            echo "<h2>Image Optimization</h2>";
            $image_urls = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $image_urls_raw)));

            if (empty($image_urls)) {
                echo "<div class='optimizer-message optimizer-warning'>⚠️ No image URLs provided.</div>";
            } else {
                foreach ($image_urls as $url) {
                    echo "<div class='optimizer-message'><strong>Image:</strong> <a href=\"$url\" target=\"_blank\">$url</a><br>";

                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_USERAGENT, 'ContentOptimizerPlugin/1.0');
                    $image_data = curl_exec($ch);
                    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);

                    if ($http_code !== 200 || !$image_data) {
                        echo "<span class='optimizer-warning'>⚠️ Failed to download image. HTTP Code: $http_code.</span></div>";
                        continue;
                    }

                    try {
                        $source = \Tinify\Source::fromBuffer($image_data);
                        $optimized_image = $source->toBuffer();

                        $original_kb = round(strlen($image_data) / 1024, 2);
                        $optimized_kb = round(strlen($optimized_image) / 1024, 2);

                        echo "Original Size: {$original_kb} KB<br>";
                        echo "Optimized Size: {$optimized_kb} KB<br>";

                        if ($optimized_kb < $original_kb) {
                            echo "<div class='optimizer-message optimizer-success'>✅ Image has been optimized.</div>";
                        } else {
                            echo "<div class='optimizer-message optimizer-warning'>⚠️ Image is already optimized.</div>";
                        }

                    } catch (Exception $e) {
                        echo "<div class='optimizer-message optimizer-error'>❌ Error: " . $e->getMessage() . "</div>";
                    }

                    echo "</div>";
                }
            }

        }
        ?>





    </div>
    <?php
}
