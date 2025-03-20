<?php
/**
 * Uninstall Ngrok Local URL
 *
 * @package     Ngrok_Local_URL
 * @author      Vitaliy Nosov
 * @copyright   2025 Vitaliy Nosov
 * @license     GPL-2.0-or-later
 */

// Exit if accessed directly.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete the Ngrok URL option.
delete_option( 'ngrok_tunnel_url' );

// Remove Ngrok URL constants from wp-config.php if possible.
remove_ngrok_config_settings();

/**
 * Remove Ngrok URL constants from wp-config.php
 *
 * @return bool True on success, false on failure.
 */
function remove_ngrok_config_settings() {
    // Get wp-config.php path.
    $wp_config_path = get_wp_config_path();
    if ( ! $wp_config_path ) {
        return false;
    }
    
    // Get wp-config.php content.
    $wp_config_content = file_get_contents( $wp_config_path );
    
    // Remove existing Ngrok settings if present.
    $pattern = '/\/\/ ngrok settings url\s+define\s*\(\s*[\'"]WP_HOME[\'"]\s*,\s*.*?\s*\)\s*;\s*define\s*\(\s*[\'"]WP_SITEURL[\'"]\s*,\s*.*?\s*\)\s*;/s';
    $wp_config_content = preg_replace( $pattern, '', $wp_config_content );
    
    // Save the modified content back to wp-config.php.
    return file_put_contents( $wp_config_path, $wp_config_content );
}

/**
 * Get the path to wp-config.php
 *
 * @return string|false Path to wp-config.php or false if not found.
 */
function get_wp_config_path() {
    // First check in the current directory.
    if ( file_exists( ABSPATH . 'wp-config.php' ) ) {
        return ABSPATH . 'wp-config.php';
    }
    
    // Check one directory up.
    if ( file_exists( dirname( ABSPATH ) . '/wp-config.php' ) ) {
        return dirname( ABSPATH ) . '/wp-config.php';
    }
    
    return false;
}