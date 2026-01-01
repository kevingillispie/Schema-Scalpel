# Schema Scalpel

[![WordPress](https://img.shields.io/wordpress/plugin/v/schema-scalpel?label=WP.org)](https://wordpress.org/plugins/schema-scalpel/)

Schema Scalpel helps you create rich text snippets for search engines in the format they prefer.

## Contributors

- kevingillispie

## Donate

Support Schema Scalpel at [https://schemascalpel.com/donate/](https://schemascalpel.com/donate/)

## Tags

- seo
- schema
- structured data
- json-ld
- markup
- per-page
- yoast
- all-in-one-seo
- microdata
- search engine

## Requirements

- **Requires at least**: WordPress 5.0
- **Tested up to**: WordPress 6.9
- **Stable tag**: 1.6.3
- **Requires PHP**: 7.4
- **License**: GPLv2 or later
- **License URI**: [https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html)

## Description

The **secret** to great schema is to _actually_ put it on your website! Many sites don't use schema or use it incorrectly, missing out on a powerful SEO and branding tool.

Schema Scalpel solves this problem by giving you complete control over your website’s structured data. It allows you to tell search engines exactly what you want them to know about who you are and what you do. Elevate your website’s SEO with **Schema Scalpel**, the ultimate tool for creating custom JSON-LD schema markup tailored to each page or post. Structured data helps search engines understand your content, improving visibility and click-through rates. Whether you’re a beginner or an SEO pro, Schema Scalpel makes it easy to enhance your site’s structured data with precision and ease.

### Key Features

- Craft custom JSON-LD schema for any page, post, or site-wide.
- Use built-in, Google-recommended schema templates for quick setup.
- Seamlessly integrates with Yoast SEO and All in One SEO Pack.
- User-friendly interface for all skill levels.
- Control schema deletion with an uninstall setting for data safety.

Get started today and optimize your site’s search presence with Schema Scalpel!

## Installation

### From your WordPress dashboard:

1. Visit **Plugins > Add New**.
2. Search for **Schema Scalpel**.
3. Click **Install Now**.
4. Click **Activate**.
5. Access the **Schema Scalpel** menu in your WordPress admin to start customizing schema.

### Manual Installation:

1. Download Schema Scalpel from WordPress.org.
2. Upload the `schema-scalpel` directory to `/wp-content/plugins/` via FTP or similar.
3. Go to **Plugins** in your WordPress admin and activate **Schema Scalpel**.
4. Find the **Schema Scalpel** menu in your dashboard to configure schema settings.

## Frequently Asked Questions

### What is schema markup?

Schema markup (structured data) is code that helps search engines understand your website’s content, such as articles, products, or FAQs. It enhances search results with rich snippets, improving visibility and clicks.

### Does Schema Scalpel work with Yoast or All in One SEO?

Yes! Schema Scalpel integrates seamlessly with Yoast SEO and All in One SEO Pack, allowing you to disable their schema output and use custom JSON-LD markup instead.

### What’s new in version 1.6?

Version 1.6 introduces database schema improvements for better performance, a “Settings” link on the Plugins page for quick access, and a `delete_on_uninstall` setting to control data removal during uninstallation.

### Why use JSON-LD instead of microdata?

JSON-LD is Google’s preferred format for structured data because it’s easier to manage within a single `<script>` tag, unlike microdata, which is embedded throughout HTML. Schema Scalpel uses JSON-LD for simplicity and compliance. [Learn more](https://developers.google.com/search/docs/advanced/structured-data/intro-structured-data#format-placement "Introduction to Structured Data").

## Changelog

#### 1.6.4

- [FIX] Removed `readonly` property modifiers from class declarations to restore full compatibility with PHP 7.4.
- Ensures the plugin loads correctly on older PHP versions still within WordPress's minimum supported range (PHP 7.4+).
- No functional changes to schema output or features — this is a compatibility maintenance release.

#### 1.6.3

- [UPDATE] Added `declare(strict_types=1);` to the main plugin file for enhanced type safety (PHP 7.4+).
- [UPDATE] Introduced void return type declarations (`: void`) on loader methods to improve code clarity and static analysis.
- [UPDATE] Enhanced inline documentation and PHPDoc blocks throughout the codebase for full WordPress Coding Standards compliance.

#### 1.6.2

- [SECURITY] Fixed Stored XSS vulnerability in JSON-LD output via post/page titles.
- Sanitized all title inputs using `sanitize_text_field()`.
- Secured JSON encoding with `wp_json_encode()` and `JSON_HEX_*` flags.
- Hardened breadcrumb and URL path handling.
- Updated all comments to comply with WordPress Coding Standards.

#### 1.6.1

- [FIX] Made distinction between pages and posts explicit due to BlogPosting schema form conflict.

#### 1.6

- [NEW] Added a "View All" and “Settings” link on the Plugins page for quick access to the admin panel.
- [UPDATE] Improved database schema: `id` column now `BIGINT UNSIGNED` and `custom_schema` column now `MEDIUMBLOB` for better scalability.
- [UPDATE] Added `delete_on_uninstall` setting to control whether tables are dropped during plugin deletion.
- [UPDATE] Enhanced code to meet WordPress Coding Standards, removing short ternaries and ensuring inline comments end with periods.
- [FIX] Resolved uninstallation bug to respect the `delete_on_uninstall` setting.
- [FIX] Schema export SQL syntax.

## Upgrade Notice

### 1.6

Back up your database before upgrading to 1.6, as it includes schema changes to improve performance. A new "View All" and “Settings” link and uninstallation options enhance usability.

## Screenshots

1. The Schema Scalpel admin panel for creating and editing custom schema.
2. Example of a JSON-LD schema template selection.
3. Settings page for configuring Yoast/AIOSEO integration and uninstall options.
