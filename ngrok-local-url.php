<?php 
/**
 * Plugin Name: Ngrok Local URL
 * Plugin URI: 
 * Description: A plugin to handle local development with Ngrok tunnels
 * Version: 1.0.0
 * Author: Vitaliy Nosov
 * Text Domain: ngrok-local-url
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Ngrok_URL_Manager {

    // Store the WordPress site URL
    private $wordpress_url;
    
    // Store the Ngrok URL from settings
    private $ngrok_url;
    
    // Option name for storing the Ngrok URL
    private $option_name = 'ngrok_tunnel_url';
    
    /**
     * Initialize the plugin
     */
    public function __construct() {
        // Get the WordPress site URL
        $this->wordpress_url = site_url() . '/';
        
        // Get saved Ngrok URL from options
        $this->ngrok_url = get_option($this->option_name);
        
        // Check if WP_SITEURL and WP_HOME constants are already defined
        if (!defined('WP_SITEURL') && !defined('WP_HOME') && isset($_SERVER['HTTP_HOST'])) {
            define('WP_SITEURL', 'http://' . $_SERVER['HTTP_HOST']);
            define('WP_HOME', 'http://' . $_SERVER['HTTP_HOST']);
        } else {
            // If constants are already defined, we still want to continue for admin functionality
            // but won't modify URL behavior
        }
        
        // Register activation, deactivation and uninstall hooks
        register_activation_hook(__FILE__, array($this, 'plugin_activation'));
        register_deactivation_hook(__FILE__, array($this, 'plugin_deactivation'));
        register_uninstall_hook(__FILE__, array(__CLASS__, 'plugin_uninstall'));
        
        // Add the URL rewriting functionality
        add_action('template_redirect', array($this, 'rewrite_urls'));
        
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Add admin bar menu item
        add_action('admin_bar_menu', array($this, 'add_admin_bar_menu'), 999);
        
        // Register admin assets
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }
    
    /**
     * Handle URL rewriting similar to the original code
     */
    public function rewrite_urls() {
        if (!isset($_GET['ngrok_url_autoload'])) {
            $connection_protocol = is_ssl() ? 'https://' : 'http://';
            echo str_replace(
                $this->wordpress_url,
                wp_make_link_relative($this->wordpress_url),
                file_get_contents(add_query_arg('ngrok_url_autoload', 1, $connection_protocol . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']))
            );
            exit;
        }
    }
    
    /**
     * Add menu item to admin bar
     */
    public function add_admin_bar_menu($wp_admin_bar) {
        $wp_admin_bar->add_node(array(
            'id'    => 'ngrok-url-manager',
            'title' => 'Ngrok URL',
            'href'  => admin_url('admin.php?page=ngrok-local-url'),
            'meta'  => array(
                'title' => 'Manage Ngrok URL Settings',
            ),
        ));
    }
    
    /**
     * Add admin menu page
     */
    public function add_admin_menu() {
        add_menu_page(
            'Ngrok Local URL',
            'Ngrok URL',
            'manage_options',
            'ngrok-local-url',
            array($this, 'admin_page_content'),
            'dashicons-admin-links',
            2
        );
    }
    
    /**
     * Enqueue admin assets
     */
    
    public function enqueue_admin_assets($hook) {
        if ('toplevel_page_ngrok-local-url' !== $hook) {
            return;
        }
    
        // Enqueue Tailwind CSS from CDN
        wp_enqueue_style(
            'tailwind-css',
            'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css',
            array(),
            '2.2.19'
        );
    
        // Admin page inline styles
        wp_add_inline_style('tailwind-css', '
            .ngrok-container { 
                max-width: 1400px; 
                margin-top: 20px; 
            }
            .ngrok-card {
                box-shadow: 0 4px 6px rgba(0,0,0,.1);
                margin-bottom: 20px;
            }
        ');

        // Подключаем PHP-файл с функционалом
        require_once plugin_dir_path(__FILE__) . '/dark-mod.php';

        // Подключаем JS-файл
        wp_enqueue_script(
            'my-plugin-admin-js',
            plugin_dir_url(__FILE__) . 'assets/js/theme-switcher.js',
            array('jquery'),
            '1.0.0',
            true
        );
    }
       
    /**
     * Admin page content
     */
    
    public function admin_page_content() {
        // Process form submission
        if (isset($_POST['ngrok_url_submit']) && isset($_POST['ngrok_url'])) {
            // Verify nonce
            if (!isset($_POST['ngrok_url_nonce']) || !wp_verify_nonce($_POST['ngrok_url_nonce'], 'save_ngrok_url')) {
                wp_die('Security check failed');
            }
            
            $ngrok_url = esc_url_raw(trim($_POST['ngrok_url']));
            
            // Save to options
            update_option($this->option_name, $ngrok_url);
            
            // Update wp-config.php file
            $this->update_wp_config($ngrok_url);
            
            // Show success message
            echo '<div class="bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded relative mb-4" role="alert"><p>Ngrok URL saved successfully!</p></div>';
            
            // Update stored URL
            $this->ngrok_url = $ngrok_url;
        }
        
        ?>
        <div class="wrap dark">
            <div class="flex items-center justify-between mb-4">
                <h1 class="dark:text-white">Ngrok Local URL Settings</h1>
                <button id="theme-toggle" class="flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                    </svg>
                    Темная тема
                </button>
            </div>
            
            <div class="max-w-7xl mt-5">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-5">
                    <div class="bg-blue-600 dark:bg-blue-800 text-white px-4 py-3">
                        <h2 class="text-lg font-medium mb-0">Configure Ngrok Tunnel URL</h2>
                    </div>
                    <div class="p-6">
                        <p class="mb-4 dark:text-gray-300">
                            This plugin allows you to easily configure your WordPress site to work with Ngrok tunnels for local development.
                            Enter your Ngrok URL below (format: https://xxx-xxx-xxx-xxx.ngrok-free.app).
                        </p>
                        
                        <form method="post" action="">
                            <?php wp_nonce_field('save_ngrok_url', 'ngrok_url_nonce'); ?>
                            <div class="mb-4">
                                <label for="ngrok_url" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Ngrok URL</label>
                                <input type="url" class="shadow appearance-none border dark:border-gray-600 rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="ngrok_url" name="ngrok_url" 
                                       value="<?php echo esc_attr($this->ngrok_url); ?>" placeholder="https://your-subdomain.ngrok-free.app" required>
                            </div>
                            <button type="submit" name="ngrok_url_submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Save URL</button>
                        </form>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-5">
                    <div class="bg-blue-400 dark:bg-blue-700 text-white px-4 py-3">
                        <h2 class="text-lg font-medium mb-0">Current Configuration</h2>
                    </div>
                    <div class="p-6">
                        <p class="mb-2 dark:text-gray-300"><strong>WordPress Site URL:</strong> <?php echo site_url(); ?></p>
                        <p class="mb-2 dark:text-gray-300"><strong>Current Ngrok URL:</strong> <?php echo $this->ngrok_url ? esc_html($this->ngrok_url) : '<em>Not set</em>'; ?></p>
                        <p class="dark:text-gray-300"><strong>WP Config Status:</strong> 
                            <?php echo $this->check_wp_config_status() ? 'Ngrok constants found in wp-config.php' : 'No Ngrok constants in wp-config.php'; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    // script theme


    
    /**
     * Check if Ngrok constants are present in wp-config.php
     */
    private function check_wp_config_status() {
        $wp_config_path = $this->get_wp_config_path();
        if (!$wp_config_path) {
            return false;
        }
        
        $wp_config_content = file_get_contents($wp_config_path);
        return strpos($wp_config_content, '// ngrok settings url') !== false;
    }
    
    /**
     * Get the path to wp-config.php
     */
    private function get_wp_config_path() {
        // First check in the current directory
        if (file_exists(ABSPATH . 'wp-config.php')) {
            return ABSPATH . 'wp-config.php';
        }
        
        // Check one directory up
        if (file_exists(dirname(ABSPATH) . '/wp-config.php')) {
            return dirname(ABSPATH) . '/wp-config.php';
        }
        
        return false;
    }
    
    /**
     * Update wp-config.php with Ngrok URL constants
     */
    private function update_wp_config($ngrok_url) {
        $wp_config_path = $this->get_wp_config_path();
        if (!$wp_config_path) {
            return false;
        }
        
        $wp_config_content = file_get_contents($wp_config_path);
        
        // Remove existing Ngrok settings if present
        $pattern = '/\/\/ ngrok settings url\s+define\s*\(\s*[\'"]WP_HOME[\'"]\s*,\s*.*?\s*\)\s*;\s*define\s*\(\s*[\'"]WP_SITEURL[\'"]\s*,\s*.*?\s*\)\s*;/s';
        $wp_config_content = preg_replace($pattern, '', $wp_config_content);
        
        // Add new Ngrok settings before the "That's all" comment
        $ngrok_settings = "\n// ngrok settings url\ndefine('WP_HOME', '{$ngrok_url}');\ndefine('WP_SITEURL', '{$ngrok_url}');\n";
        $wp_config_content = preg_replace('/(\s*\/\*\s*That\'s all\s*,\s*stop editing!\s*\*\/)/', $ngrok_settings . '$1', $wp_config_content);
        
        // Save the modified content back to wp-config.php
        return file_put_contents($wp_config_path, $wp_config_content);
    }
    
    /**
     * Remove Ngrok URL constants from wp-config.php
     */
    private function remove_wp_config_settings() {
        $wp_config_path = $this->get_wp_config_path();
        if (!$wp_config_path) {
            return false;
        }
        
        $wp_config_content = file_get_contents($wp_config_path);
        
        // Remove existing Ngrok settings if present
        $pattern = '/\/\/ ngrok settings url\s+define\s*\(\s*[\'"]WP_HOME[\'"]\s*,\s*.*?\s*\)\s*;\s*define\s*\(\s*[\'"]WP_SITEURL[\'"]\s*,\s*.*?\s*\)\s*;/s';
        $wp_config_content = preg_replace($pattern, '', $wp_config_content);
        
        // Save the modified content back to wp-config.php
        return file_put_contents($wp_config_path, $wp_config_content);
    }
    
    /**
     * Activation hook callback
     */
    public function plugin_activation() {
        // Add Ngrok URL constants to wp-config.php if URL is already set
        if ($this->ngrok_url) {
            $this->update_wp_config($this->ngrok_url);
        }
    }
    
    /**
     * Deactivation hook callback
     */
    public function plugin_deactivation() {
        // Remove Ngrok URL constants from wp-config.php
        $this->remove_wp_config_settings();
    }
    
    /**
     * Uninstall hook callback - static because it's called statically
     */
    public static function plugin_uninstall() {
        // Clean up options
        delete_option('ngrok_tunnel_url');
        
        // Create instance to remove wp-config settings
        $instance = new self();
        $instance->remove_wp_config_settings();
    }
}

// Initialize the plugin
new Ngrok_URL_Manager();