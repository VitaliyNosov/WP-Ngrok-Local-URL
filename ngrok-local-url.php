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

        wp_enqueue_style(
            'my-plugin-style',
            plugin_dir_url( __FILE__ ) . 'assets/css/style.css', // Путь к вашему локальному файлу стилей
            array('tailwind-css'), // Указываем, что ваш стиль зависит от Tailwind
            '1.0.0' // Версия вашего стиля
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
        // wp_enqueue_script(
        //     'my-plugin-admin-js',
        //     plugin_dir_url(__FILE__) . 'assets/js/tailwindcss.js',
        //     array('jquery'),
        //     '1.0.0',
        //     true
        // );

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
            echo '<div class="alert bg-green-100 dark:bg-green-800 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded relative mb-4" role="alert"><p>Ngrok URL saved successfully!</p></div>';
            
            // Update stored URL
            $this->ngrok_url = $ngrok_url;
        }
        
        ?>
         <div class="header-block">
            <h1 class="dark:text-white">Welcome</h1>
            <p>ngrok is your app’s front door—a globally distributed reverse proxy that secures, protects and accelerates your applications and network services, no matter where you run them.</p>
            <div class="logo-block">
                <svg class="high-contrast:text-blue-100/50 !h-auto w-full object-cover text-blue-500/5" width="1174" height="269" viewBox="0 0 1174 269" fill="currentColor"><path d="M404.855 152.332C388.249 133.737 367.709 124.375 343.299 124.375C328.253 124.375 314.394 127.304 301.658 133.227C288.922 139.15 277.934 147.174 268.632 157.427C259.392 167.744 252.088 179.717 246.719 193.6C241.35 207.419 238.666 222.385 238.666 238.561C238.666 254.418 241.163 268.875 246.095 281.93C251.089 294.922 258.019 306.066 267.009 315.364C275.999 324.662 286.674 331.922 299.036 337.144C311.397 342.366 324.944 344.978 339.678 344.978C346.358 344.978 352.539 344.468 358.157 343.513C363.776 342.558 369.145 340.965 374.264 338.8C379.384 336.571 384.378 333.769 389.372 330.394C394.304 326.955 399.486 322.624 404.855 317.466V372.68H404.793V378.03H336.619L285.363 436.874V447H477.712V423.118V130.17H404.855V152.332ZM404.668 253.527C402.108 259.386 398.737 264.544 394.617 268.938C390.434 273.333 385.502 276.708 379.758 279.192C374.015 281.675 367.896 282.885 361.466 282.885C354.724 282.885 348.481 281.675 342.737 279.192C336.993 276.708 331.999 273.333 327.816 268.938C323.633 264.544 320.387 259.386 317.952 253.527C315.517 247.668 314.331 241.236 314.331 234.422C314.331 227.862 315.58 221.685 318.139 215.953C320.699 210.221 324.07 205.254 328.44 200.987C332.748 196.72 337.68 193.345 343.299 190.734C348.918 188.123 354.973 186.849 361.404 186.849C367.584 186.849 373.515 188.059 379.321 190.543C385.065 192.963 390.122 196.402 394.429 200.796C398.737 205.19 402.108 210.221 404.73 215.953C407.29 221.685 408.539 227.989 408.539 234.867C408.476 241.427 407.228 247.668 404.668 253.527Z"></path><path d="M201.585 155.772C197.714 151.123 193.344 147.111 188.661 143.544C184.479 140.424 180.046 137.686 175.239 135.457C172.929 134.374 170.494 133.482 167.935 132.654C164.251 131.444 160.256 130.617 156.135 129.916H104.193L69.8563 169.846V165.197V130.744H-3V341.412H69.8563V251.044V199.268H79.97H100.26H137.406H138.28L143.961 199.141V341.348H216.818V209.585C216.818 198.377 215.756 188.378 213.634 179.59C211.511 170.865 207.515 162.968 201.585 155.772Z"></path><path d="M691.971 130.17H637.968C637.968 130.17 618.677 130.17 612.497 130.17L580.907 166.471V130.17H507.988V340.838H581.032L581.094 199.841H605.067H634.098L691.971 133.1V130.17Z"></path><path d="M1085.66 227.48L1185.3 133.737V130.171H1089.29L1012.87 206.146V0H940.015V340.775H1012.87V254.992L1092.97 340.775H1191V336.763L1085.66 227.48Z"></path><path d="M883.073 154.179C871.961 144.181 858.788 136.411 843.617 130.807C828.447 125.203 812.027 122.401 794.297 122.401C776.317 122.401 759.711 125.267 744.602 130.998C729.432 136.73 716.384 144.627 705.334 154.561C694.346 164.56 685.73 176.342 679.55 189.906C673.369 203.471 670.31 218.055 670.31 233.657C670.31 250.916 673.369 266.582 679.55 280.656C685.73 294.731 694.221 306.831 705.146 316.957C716.009 327.082 728.932 334.915 743.978 340.392C759.024 345.869 775.381 348.608 793.111 348.608C811.091 348.608 827.76 345.869 843.243 340.392C858.663 334.915 871.898 327.21 882.886 317.148C893.874 307.149 902.552 295.24 908.857 281.421C915.163 267.601 918.347 252.381 918.347 235.632C918.347 218.946 915.225 203.662 909.107 189.843C902.864 176.087 894.186 164.178 883.073 154.179ZM837.562 253.909C835.002 259.768 831.631 264.926 827.51 269.321C823.327 273.715 818.395 277.09 812.652 279.574C806.846 282.058 800.79 283.268 794.36 283.268C787.929 283.268 781.811 282.058 776.005 279.574C770.261 277.09 765.267 273.715 761.147 269.321C756.964 264.926 753.655 259.768 751.095 253.909C748.536 248.05 747.287 241.554 747.287 234.422C747.287 227.862 748.536 221.685 751.095 215.953C753.655 210.222 756.964 205.191 761.147 200.796C765.329 196.402 770.261 192.963 776.005 190.543C781.811 188.059 787.867 186.849 794.36 186.849C800.79 186.849 806.908 188.059 812.652 190.543C818.395 192.963 823.39 196.402 827.51 200.796C831.693 205.191 835.002 210.349 837.562 216.208C840.121 222.067 841.37 228.308 841.37 234.867C841.37 241.682 840.121 248.05 837.562 253.909Z"></path></svg>
            </div>
         </div>
        <div class="wrap">
            <div class="flex items-center justify-between mb-4">
                <h1 class="dark:text-white">Ngrok Local URL Settings</h1>
                <button id="theme-toggle" class="flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-white rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                    </svg>
                    Светлая тема1 
                </button>
            </div>
            
            <div class="max-w-7xl mt-5">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-5">
                    <div class="u-userColor dark:bg-blue-800 text-white px-4 py-3">
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
                            <button type="submit" name="ngrok_url_submit" class="u-userColor hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Save URL</button>
                        </form>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-5">
                    <div class="u-userColor dark:bg-blue-700 text-white px-4 py-3">
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