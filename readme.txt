=== Ngrok Local URL ===
Contributors: vitaliynosov
Tags: development, ngrok, local development, tunnel, development environment
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Simplify local WordPress development with Ngrok tunnels by handling URL configurations automatically.

== Description ==

Ngrok Local URL allows WordPress developers to easily configure their local WordPress installations to work with Ngrok tunnels. The plugin handles URL rewriting and configuration to ensure your WordPress site functions properly when accessed through an Ngrok tunnel.

**Key Features:**

* Easy management of Ngrok tunnel URLs from the WordPress admin
* Automatic update of WordPress configuration
* Admin toolbar quick access
* Dark/light theme for settings page
* Full support for Ngrok Free and Ngrok Pro

**How It Works:**

1. Start an Ngrok tunnel on your local machine
2. Copy the tunnel URL (e.g., https://your-subdomain.ngrok-free.app)
3. Paste the URL in the plugin settings
4. The plugin automatically configures WordPress to work correctly with this URL

**For Developers:**

The plugin modifies the wp-config.php file, adding WP_HOME and WP_SITEURL constants, which allows WordPress to properly handle requests coming through the Ngrok tunnel.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/ngrok-local-url` directory, or install the plugin through the WordPress plugins screen.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the 'Ngrok URL' menu item to configure your Ngrok tunnel URL.

== Frequently Asked Questions ==

= What is Ngrok? =

Ngrok is a service that creates secure tunnels to expose your local web server to the internet.

= Do I need an Ngrok account to use this plugin? =

Yes, you need to have an Ngrok account and have Ngrok set up on your local machine. This plugin only manages the WordPress side of the Ngrok integration.

= Is it safe to use this plugin on a production site? =

The plugin is designed for use in development environments. It is not recommended to use it on production sites as it changes core WordPress URL settings.

= Is the plugin compatible with WordPress Multisite? =

Yes, the plugin is compatible with WordPress Multisite installations, but requires additional configuration. Please refer to the documentation for detailed information.

== Screenshots ==

1. Settings page for Ngrok URL configuration
2. Dark theme settings interface
3. Quick access via admin toolbar

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial version of Ngrok Local URL plugin.

== Privacy Policy ==

The Ngrok Local URL plugin does not collect any personal data.