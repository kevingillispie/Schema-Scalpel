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
- **Tested up to**: WordPress 6.8
- **Stable tag**: 1.6.2
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

#### 1.6

- [NEW] Added a "View All" and “Settings” link on the Plugins page for quick access to the admin panel.
- [UPDATE] Improved database schema: `id` column now `BIGINT UNSIGNED` and `custom_schema` column now `MEDIUMBLOB` for better scalability.
- [UPDATE] Added `delete_on_uninstall` setting to control whether tables are dropped during plugin deletion.
- [UPDATE] Enhanced code to meet WordPress Coding Standards, removing short ternaries and ensuring inline comments end with periods.
- [FIX] Resolved uninstallation bug to respect the `delete_on_uninstall` setting.
- [FIX] Schema export SQL syntax.

#### 1.5

- [UPDATE] Improved BlogPosting schema generator with more robust options for updating existing schema.

#### 1.4.7

- [FIX] Fixed display issue with schema wrapped in square brackets on the admin page.

#### 1.4.6

- [UPDATE] Applied corrections from version 1.4.5.

#### 1.4.5

- [FIX] Corrected saving of BreadcrumbList schema.
- [FIX] Fixed typo in documentation.

#### 1.4.4

- [NEW] Added title-based filtering for sites with large page/post counts.
- [UPDATE] Performed maintenance updates to the codebase.

#### 1.4.3

- [UPDATE] Minor housekeeping updates.
- [UPDATE] Confirmed compatibility with WordPress 6.7.

#### 1.4.2

- [UPDATE] Confirmed compatibility with WordPress 6.6.
- [FIX] Resolved a longstanding bug, thanks to contributor [dantefff](https://github.com/dantefff "dantefff's GitHub Profile").

#### 1.4.1

- [FIX] Fixed a bug preventing admin pages from loading.

#### 1.4

- [NEW] Rebuilt codebase with a custom HTML generator for improved performance.
- [UPDATE] Ensured full compliance with WordPress Coding Standards.
- [UPDATE] Optimized admin JavaScript for better schema editing.
- [FIX] Improved schema-editing JavaScript functionality.

#### 1.3.2

- [UPDATE] Enhanced compliance with WordPress Coding Standards.

#### 1.3.1

- [UPDATE] Clarified the BlogPosting schema generator process.

#### 1.3

- [NEW] Added one-click schema generation for all blog posts.
- [UPDATE] Upgraded CSS framework to Bootstrap v5.3.2.
- [UPDATE] Improved UI elements.
- [UPDATE] Updated PHP code throughout the plugin.
- [FIX] Fixed menu item logo sizing.

#### 1.2.7.1

- [NEW] Added click-to-edit feature for global, pages, and posts tabs.

#### 1.2.7

- [UPDATE] Enabled immediate schema editing via popup textbox.
- [FIX] Addressed minor bugs.

#### 1.2.6.2

- [UPDATE] Replaced menu icon and updated menu names.
- [UPDATE] Prepared codebase for a major overhaul.

#### 1.2.5.5

- [FIX] Standardized `schema_type` for homepage schema across all files. Deactivate and reactivate to apply.

#### 1.2.5.4

- [FIX] Set initial tab to `homepage` for proper schema-editing page loading.
- [FIX] Defaulted search query parameter to `s`.

#### 1.2.5.3

- [FIX] Fixed tab-switching issue when the plugin path contained “home”.

#### 1.2.5.2

- [FIX] Included missing `admin/vars` directory.

#### 1.2.5

- [NEW] Added Google-recommended example schemas, including `COVID Announcement`.
- [UPDATE] Enhanced `NewsArticle` schema with `author` field.
- [UPDATE] Sourced example schemas from an array instead of the database.
- [FIX] Standardized spacing after commas.

#### 1.2.4.1

- [FIX] Corrected schema type generation for create/edit buttons.

#### 1.2.4

- [FIX] Prevented fatal PHP errors from redundant function declarations in `class-schema-scalpel-public.php`.

#### 1.2.3

- [UPDATE] Confirmed compatibility with WordPress 6.0.
- [FIX] Updated CSS class names to Bootstrap 5.x standards.
- [FIX] Fixed page/post title display due to sanitization issues.

#### 1.2.2

- [UPDATE] Improved schema/JSON-LD format error checking.

#### 1.2.1

- [FIX] Updated version numbers consistently across the plugin.

#### 1.2

- [NEW] Enabled multisite activation.
- [UPDATE] Removed unnecessary comments.

#### 1.0.1

- [FIX] Replaced `wp_print_scripts` with `wp_enqueue_scripts` for better theme compatibility.
- [FIX] Addressed multiple undocumented fixes.
- [UPDATE] Moved plugin initialization to `plugins_loaded` hook.
- [UPDATE] Set default search key to `s`.
- [UPDATE] Updated logo to use font paths.

#### 1.0

- [NEW] Initial release of Schema Scalpel.

## Upgrade Notice

### 1.6

Back up your database before upgrading to 1.6, as it includes schema changes to improve performance. A new "View All" and “Settings” link and uninstallation options enhance usability.

## Screenshots

1. The Schema Scalpel admin panel for creating and editing custom schema.
2. Example of a JSON-LD schema template selection.
3. Settings page for configuring Yoast/AIOSEO integration and uninstall options.
