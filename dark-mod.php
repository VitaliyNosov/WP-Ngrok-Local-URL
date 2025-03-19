<?php
/**
 * Add this code to the main file of your plugin
 */

/**
 * Enqueue Tailwind CSS assets with dark mode support
 */

function ngrok_enqueue_tailwind_theme_assets($hook) {
    if ('toplevel_page_ngrok-local-url' !== $hook) {
        return;
    }
        
    // Enqueue JavaScript for theme switching
    wp_enqueue_script(
        'theme-switcher',
        plugins_url('assets/js/theme-switcher.js', __FILE__),
        array(),
        '1.0.0',
        true
    );
}
add_action('admin_enqueue_scripts', 'ngrok_enqueue_tailwind_theme_assets');

/**
 * Add an attribute to the <html> tag for dark mode support in Tailwind
 */
function ngrok_add_dark_mode_support() {
    $screen = get_current_screen();
    if ($screen && $screen->id === 'toplevel_page_ngrok-local-url') {
        // Add a script in the head to prevent flickering on load
        echo '<script>
            if (localStorage.theme === "dark" || (!("theme" in localStorage) && window.matchMedia("(prefers-color-scheme: dark)").matches)) {
                document.documentElement.classList.add("dark");
            } else {
                document.documentElement.classList.remove("dark");
            }
        </script>';
    }
}
add_action('admin_head', 'ngrok_add_dark_mode_support');


echo '<script>console.log("php test");</script>';
