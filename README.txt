=== Schema Scalpel ===
Contributors: kevingillispie
Donate link: https://schemascalpel.com/donate/
Tags: seo, schema, structured data, json-ld, markup, per-page, yoast, all-in-one-seo, microdata, search engine
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.6.2
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Boost your site’s SEO with Schema Scalpel, a user-friendly plugin for crafting custom schema markup on a per-page basis.

== Description ==

Elevate your website’s SEO with **Schema Scalpel**, the ultimate tool for creating custom JSON-LD schema markup tailored to each page or post. Structured data helps search engines understand your content, improving visibility and click-through rates. Whether you’re a beginner or an SEO pro, Schema Scalpel makes it easy to enhance your site’s structured data with precision and ease.

### Key Features
- Craft custom JSON-LD schema for any page, post, or site-wide.
- Use built-in, Google-recommended schema templates for quick setup.
- Seamlessly integrates with Yoast SEO and All in One SEO Pack.
- User-friendly interface for all skill levels.
- Control schema deletion with an uninstall setting for data safety.

Get started today and optimize your site’s search presence with Schema Scalpel!

== Installation ==

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

== Frequently Asked Questions ==

### What is schema markup?
Schema markup (structured data) is code that helps search engines understand your website’s content, such as articles, products, or FAQs. It enhances search results with rich snippets, improving visibility and clicks.

### Does Schema Scalpel work with Yoast or All in One SEO?
Yes! Schema Scalpel integrates seamlessly with Yoast SEO and All in One SEO Pack, allowing you to disable their schema output and use custom JSON-LD markup instead.

### What’s new in version 1.6?
Version 1.6 introduces database schema improvements for better performance, a “Settings” link on the Plugins page for quick access, and a `delete_on_uninstall` setting to control data removal during uninstallation.

### Why use JSON-LD instead of microdata?
JSON-LD is Google’s preferred format for structured data because it’s easier to manage within a single `<script>` tag, unlike microdata, which is embedded throughout HTML. Schema Scalpel uses JSON-LD for simplicity and compliance. [Learn more](https://developers.google.com/search/docs/advanced/structured-data/intro-structured-data#format-placement "Introduction to Structured Data" rel="nofollow").

== Changelog ==

= 1.6.2 =
* [SECURITY] Fixed Stored XSS vulnerability in JSON-LD output via post/page titles.
* Sanitized all title inputs using `sanitize_text_field()`.
* Secured JSON encoding with `wp_json_encode()` and `JSON_HEX_*` flags.
* Hardened breadcrumb and URL path handling.
* Updated all comments to comply with WordPress Coding Standards.

= 1.6.1 =
* [FIX] Made distinction between pages and posts explicit due to BlogPosting schema form conflict.

= 1.6 =
* [NEW] Added a "View All" and “Settings” link on the Plugins page for quick access.
* [UPDATE] Improved database schema: `id` column now `BIGINT UNSIGNED` and `custom_schema` now `MEDIUMBLOB`.
* [UPDATE] Added `delete_on_uninstall` setting to control data removal on deletion.
* [UPDATE] Enhanced code to meet WordPress Coding Standards.
* [FIX] Fixed uninstallation bug to respect the `delete_on_uninstall` setting.

= 1.5 =
* [UPDATE] Improved BlogPosting schema generator with more robust options.

= 1.4 =
* [NEW] Rebuilt codebase with custom HTML generator for performance.
* [UPDATE] Full compliance with WordPress Coding Standards.
* [UPDATE] Optimized admin JavaScript for schema editing.
* [FIX] Improved schema-editing functionality.

== Upgrade Notice ==

= 1.6.2 =
**Security Release** – Critical Stored XSS vulnerability patched. Update immediately to protect your site. No configuration needed.

= 1.6 =
Back up your database before upgrading — includes schema changes. New "Settings" link and uninstall control added.

== Screenshots ==

1. The Schema Scalpel admin panel for creating and editing custom schema.
2. Example of a JSON-LD schema template selection.
3. Settings page for configuring Yoast/AIOSEO integration and uninstall options.