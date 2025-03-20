# Ngrok Local URL

![WordPress Plugin](https://img.shields.io/wordpress/plugin/v/ngrok-local-url)
![PHP Version](https://img.shields.io/badge/PHP-%3E=7.2-blue)
![License](https://img.shields.io/badge/License-GPLv2%2B-green)

## Description

**Ngrok Local URL** simplifies local WordPress development by automatically handling URL configurations for Ngrok tunnels.

### Features

- Easy management of Ngrok tunnel URLs from the WordPress admin
- Automatic update of WordPress configuration
- Quick access via the admin toolbar
- Dark/light theme for settings page
- Full support for Ngrok Free and Ngrok Pro

## How It Works

1. Start an Ngrok tunnel on your local machine
2. Copy the tunnel URL (e.g., `https://your-subdomain.ngrok-free.app`)
3. Paste the URL in the plugin settings
4. The plugin automatically configures WordPress to work correctly with this URL

### For Developers

The plugin modifies the `wp-config.php` file, adding `WP_HOME` and `WP_SITEURL` constants to ensure WordPress handles requests correctly via the Ngrok tunnel.

## Installation

1. Upload the plugin files to the `/wp-content/plugins/ngrok-local-url` directory, or install the plugin via the WordPress plugin screen.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Use the 'Ngrok URL' menu item to configure your Ngrok tunnel URL.

## Frequently Asked Questions

### What is Ngrok?

Ngrok is a service that creates secure tunnels to expose your local web server to the internet.

### Do I need an Ngrok account to use this plugin?

Yes, you need to have an Ngrok account and have Ngrok set up on your local machine. This plugin only manages the WordPress-side configuration.

### Is it safe to use this plugin on a production site?

The plugin is designed for development environments. It is not recommended for production use as it changes core WordPress URL settings.

### Is the plugin compatible with WordPress Multisite?

Yes, but additional configuration is required. Please refer to the documentation for details.

## Screenshots

1. Settings page for Ngrok URL configuration
2. Dark theme settings interface
3. Quick access via the admin toolbar

## Changelog

### 1.0.0
- Initial release

## Upgrade Notice

### 1.0.0
Initial version of Ngrok Local URL plugin.

## License

This plugin is licensed under the GPLv2 or later. See the [GPL-2.0 License](https://www.gnu.org/licenses/gpl-2.0.html) for details.

## Privacy Policy

The Ngrok Local URL plugin does not collect any personal data.
