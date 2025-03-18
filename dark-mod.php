<?php
/**
 * Добавьте этот код в основной файл вашего плагина
 */

/**
 * Подключение ресурсов Tailwind CSS с поддержкой темной темы
 */
function ngrok_enqueue_tailwind_theme_assets($hook) {
    if ('toplevel_page_ngrok-local-url' !== $hook) {
        return;
    }
    
    // Подключаем Tailwind CSS 3 с поддержкой темной темы
    // wp_enqueue_style(
    //     'tailwind-css',
    //     'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css',
    //     array(),
    //     '2.2.19'
    // );
    
    
    // Подключаем JavaScript для переключения темы
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
 * Добавляем атрибут к тегу <html> для поддержки темной темы Tailwind
 */
function ngrok_add_dark_mode_support() {
    $screen = get_current_screen();
    if ($screen && $screen->id === 'toplevel_page_ngrok-local-url') {
        // Добавляем script в head для предотвращения мигания при загрузке
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

