# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a production WordPress 6.9.1 site running PHP 8.4 and MySQL 8.0. There are no build tools, package managers, or test suites — no `npm`, `composer`, or `Makefile`. All configuration is stored in the WordPress database; file edits primarily affect plugin/theme PHP, CSS, and JS.

## WP-CLI

WP-CLI is installed at `/usr/bin/wp`. Run it as `www-data` to avoid permission issues and to have the mysqli extension available:

```bash
sudo -u www-data wp <command>
```

Common commands:
```bash
sudo -u www-data wp plugin list
sudo -u www-data wp theme list
sudo -u www-data wp option get siteurl
sudo -u www-data wp cache flush
sudo -u www-data wp db query "SELECT option_value FROM wp_options WHERE option_name='siteurl';"
sudo -u www-data wp eval 'echo get_bloginfo("version");'
sudo -u www-data wp eval-file script.php
```

## Enabling Debug Mode

To enable PHP/WordPress error output during development, set in `wp-config.php`:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );   // logs to /wp-content/debug.log
define( 'WP_DEBUG_DISPLAY', false );
```

## Architecture

### Theme Layer
- **Active theme:** Hello Elementor (`/wp-content/themes/hello-elementor/`) — a minimal shell theme designed to delegate all layout to Elementor. It provides `header.php`, `footer.php`, and `functions.php` hooks but contains little custom logic.
- **Alternate theme installed:** GeneratePress (not active).

### Page Builder
- **Elementor Free + Pro** are the primary design tools. Page layouts are stored as post meta in the database (not as template files), so visual structure cannot be edited by modifying PHP/HTML files directly — use the Elementor editor or manipulate the database.

### Key Plugin Groups

| Function | Plugins |
|---|---|
| Page building | Elementor, Elementor Pro |
| Security | Wordfence, Really Simple SSL, WP fail2ban, Akismet |
| SEO | Yoast SEO, Meta Tag Manager, Google Site Kit |
| Forms | WPForms (Lite + paid) |
| Email | WP Mail SMTP |
| Backup | All-in-One WP Migration, Backup Migration |
| Performance | WP-Optimize, Nginx Helper, Image Optimization |
| GDPR | Complianz |

Plugin code lives in `/wp-content/plugins/<plugin-slug>/`.

### Must-Use Plugins
`/wp-content/mu-plugins/` contains the Elementor safe-mode loader. Files here load automatically before regular plugins and cannot be disabled from the admin.

### Database
- **Host:** localhost, **Database:** `wordpress`, **Prefix:** `wp_`
- WordPress stores all settings, page content, Elementor layouts, plugin options, and user data in MySQL. When debugging behavior, check `wp_options` and `wp_postmeta` alongside the file system.

### URL Rewriting
Apache rewrite rules in `.htaccess` handle WordPress pretty permalinks and pass `HTTP_AUTHORIZATION` through to PHP.

## Important File Locations

| File | Purpose |
|---|---|
| `wp-config.php` | DB credentials, salts, `WP_DEBUG` flag |
| `.htaccess` | Apache rewrite rules |
| `wp-content/uploads/` | All user-uploaded media (~856 MB) |
| `wp-content/debug.log` | WordPress error log (when `WP_DEBUG_LOG` is on) |
| `/var/log/` | Server-level logs (Apache/Nginx, PHP-FPM) |
