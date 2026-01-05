=== Schema Scalpel ===
Contributors: kevingillispie
Donate link: https://schemascalpel.com/donate/
Tags: seo, schema, structured data, json-ld, markup, per-page, yoast, all-in-one-seo, microdata, search engine
Requires at least: 5.0
Tested up to: 6.9
Stable tag: 2.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Custom JSON-LD schema markup on every post/page – now with a powerful editor metabox for easy per-page structured data.

== Description ==

**Schema Scalpel 2.0** revolutionizes structured data in WordPress with a brand-new **post/page editor metabox** – add, edit, and manage custom JSON-LD schema markup directly alongside your content!

No more jumping to a separate dashboard. Create rich, Google-friendly structured data exactly where you need it, with real-time AJAX saving and a beautiful, intuitive interface.

### Why Schema Scalpel?
- **Per-page/post precision** – Tailor JSON-LD schema for individual content (FAQ, Article, Recipe, Product, and more).
- **New in 2.0: Full metabox editor** – Create/edit/delete schemas in the Gutenberg or Classic editor.
- **Built-in examples tab** – Copy ready-to-use Google-recommended templates with one click.
- **Global & homepage support** – Site-wide schemas remain available via the dashboard.
- **Lightweight & fast** – Pure JSON-LD output, no bloat.
- **Seamless compatibility** – Works alongside Yoast SEO, Rank Math, and All in One SEO (disable their schema if desired).
- **Safe data handling** – Optional uninstall cleanup setting.

Perfect for bloggers, agencies, eCommerce sites, and anyone serious about rich snippets, better SEO visibility, and higher click-through rates.

Get started in minutes – elevate your search presence today!

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

### Where do I manage schema now?
In version 2.0, most users will use the new **Schema Scalpel metabox** directly in the post/page editor – it's faster and more intuitive. Global and homepage schemas are still managed via the Schema Scalpel dashboard menu.

### Does the new metabox replace the old dashboard?
No! The original dashboard remains for advanced/global use. The metabox brings per-page editing front-and-center.

### Will my existing schemas disappear? 
Absolutely not – all existing global, post, and page schemas are preserved and now appear in the new metabox as well as the original dashboard.

### Can I still use schema templates? 
Yes! The new "Examples" tab in the metabox includes copyable templates for FAQPage, Article, Recipe, Product, and many more.

### Is Schema Scalpel still compatible with Yoast/Rank Math/AIOSEO? 
Yes – it integrates perfectly. You can safely disable their built-in schema output and use Schema Scalpel's custom JSON-LD instead.

### What is schema markup and why JSON-LD? 
Schema markup (structured data) helps search engines understand your content, enabling rich snippets (stars, FAQs, etc.) in results. JSON-LD is Google's preferred format – clean, easy, and non-intrusive.

== Screenshots ==

1. The new Schema Scalpel metabox – edit custom JSON-LD right in the post/page editor.
2. Built-in Examples tab with one-click copyable schema templates.
3. Creating or editing a schema with real-time saving.
4. Global schemas still accessible via the dashboard.
5. Rich snippet example in Google search results.

== Changelog ==

= 2.0.0 =

*(2026-01-05)*

**Added**

- **New Post/Page Editor Metabox** – Full-featured Schema Scalpel editor now directly available in the WordPress post and page editing screens.
  - Create, edit, and delete custom JSON-LD schemas right where you write your content.
  - Real-time saving via AJAX – no need to visit a separate settings page.
  - Support for both post-specific and global schemas in the same interface.
  - Built-in "Examples" tab with ready-to-use schema templates (FAQ, Article, Recipe, etc.) that can be copied with one click.
  - Clean, modern interface with syntax-highlighted JSON editing and clear visual separation between post and global schemas.
- Improved workflow – most users will now manage schema directly alongside their content, making structured data easier and faster to implement.

**Changed**

- Major version bump to reflect the significant improvement in usability and core functionality.
- The original dashboard-based schema management remains available for advanced/global use cases.

**Note for Upgraders**

- All existing custom schemas (global, post, and page) are fully preserved and will now appear in the new metabox.
- No data migration needed – everything works seamlessly.

A huge leap forward in making structured data accessible and intuitive – thank you for using Schema Scalpel!

= 1.6.4 =
* [FIX] Removed `readonly` property modifiers from class declarations to restore full compatibility with PHP 7.4.
* Ensures the plugin loads correctly on older PHP versions still within WordPress's minimum supported range (PHP 7.4+).
* No functional changes to schema output or features — this is a compatibility maintenance release.

= 1.6.3 =
* [UPDATE] Added `declare(strict_types=1);` to the main plugin file for enhanced type safety (PHP 7.4+).
* [UPDATE] Introduced void return type declarations (`: void`) on loader methods to improve code clarity and static analysis.
* [UPDATE] Enhanced inline documentation and PHPDoc blocks throughout the codebase for full WordPress Coding Standards compliance.

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

== Upgrade Notice ==

= 1.6.2 =
**Security Release** – Critical Stored XSS vulnerability patched. Update immediately to protect your site. No configuration needed.

= 1.6 =
Back up your database before upgrading — includes schema changes. New "Settings" link and uninstall control added.

== Screenshots ==

1. The Schema Scalpel admin panel for creating and editing custom schema.
2. Example of a JSON-LD schema template selection.
3. Settings page for configuring Yoast/AIOSEO integration and uninstall options.