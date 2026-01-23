=== Schema Scalpel ===
Contributors: kevingillispie
Donate link: https://schemascalpel.com/donate/
Tags: seo, schema, structured data, json-ld, markup, per-page, yoast, rank math, all-in-one-seo, microdata, search engine, rich snippets
Requires at least: 5.0
Tested up to: 6.7
Stable tag: 2.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Add custom JSON-LD schema markup per post or page with a powerful new editor metabox – precise, fast, and SEO-boosting.

== Description ==

**Schema Scalpel 2.0** delivers surgical precision to your WordPress structured data. The headline feature: a **brand-new metabox** right inside the post/page editor (Gutenberg and Classic), letting you create, edit, and manage custom JSON-LD schemas without ever leaving the content screen.

No bloated page builders or separate dashboards for everyday use — just fast, intuitive schema editing where you already work, with real-time AJAX saves and a modern, clean interface.

### Core Benefits & Features
- **New in 2.0: Powerful Editor Metabox** — Add/edit/delete per-post/page schemas directly in the editor. Real-time saving, syntax-highlighted JSON editor, visual separation of local vs global schemas.
- **Examples Library** — One-click copy of Google-recommended templates (FAQPage, Article, HowTo, Recipe, Product, Organization, Breadcrumb, and more).
- **Per-Page Precision** — Override or supplement global schemas for ultimate control — ideal for blogs, eCommerce, local businesses, and agencies.
- **Global & Homepage Schemas** — Still fully supported via the dedicated Schema Scalpel dashboard.
- **Improved Security** — Hardened against XSS (titles, URLs, breadcrumbs fully sanitized), secure JSON encoding, strict input validation — building on previous critical fixes.
- **Compatibility** — Works flawlessly alongside Yoast SEO, Rank Math, and All in One SEO. Easily disable their schema output to avoid duplication.
- **Lightweight & Performant** — Pure JSON-LD injection, no frontend bloat, optimized database storage.
- **Safe Uninstall** — Optional setting to clean up data on deletion — respect privacy and keep your site secure.
- **Modern Codebase** — Strict typing, full WordPress Coding Standards compliance, PHP 7.4+ compatibility.

Whether you're chasing rich results (stars, carousels, FAQs), improving entity understanding, or just giving search engines cleaner data — Schema Scalpel makes it effortless and reliable.

Install today and start enhancing click-through rates and visibility!

== Installation ==

### From WordPress Dashboard
1. Go to **Plugins → Add New**.
2. Search for **Schema Scalpel**.
3. Install and **Activate**.
4. Start adding schemas via the new metabox in any post/page editor, or visit **Schema Scalpel** in the admin menu for global settings.

### Manual Upload
1. Download the plugin ZIP from WordPress.org or GitHub.
2. Upload the `schema-scalpel` folder to `/wp-content/plugins/` via FTP.
3. Activate the plugin from **Plugins** screen.
4. Customize schemas in the editor metabox or dashboard.

== Frequently Asked Questions ==

= Where do I add/edit schema in v2.0? =
Use the new **Schema Scalpel metabox** that appears in the post/page editor sidebar — it's now the fastest way for per-page work. Global and homepage schemas remain in the main **Schema Scalpel** menu.

= Will my old schemas still work? =
Yes — all existing global, post, and page schemas are automatically available in both the metabox and dashboard. Zero data loss or migration required.

= Does it conflict with Yoast, Rank Math, or AIOSEO? =
No — it plays nicely. Disable schema generation in those plugins if you want Schema Scalpel to take full control.

= Why choose JSON-LD over microdata/RDFa? =
Google strongly recommends JSON-LD — it's cleaner, easier to maintain, doesn't mix with HTML, and supports the most advanced schema types.

= Is the plugin secure? =
Yes — recent updates include full sanitization of dynamic content (post titles, URLs), secure JSON encoding, strict typing, and hardened output. Previous XSS issues (pre-1.6.2) are long resolved.

== Screenshots ==

1. The new Schema Scalpel metabox in the post editor – create and edit JSON-LD right where you write content.
2. Global & homepage schema management in the dashboard.
3. Examples tab with one-click Google-recommended schema templates.
4. User Settings dashboard where you can fine tune which pages get schema.
5. Export tool for transferring your schema from one site to another via SQL.
6. Generate customized schema for posts automatically!

== Changelog ==

= 2.0.0 =
*Date: 2026-01-XX* (adjust to your release date)

**Major Feature**
- Introduced full-featured **Schema Scalpel metabox** in the post/page editor.
  - Create, edit, delete per-post/page JSON-LD schemas directly.
  - Real-time AJAX saving.
  - Modern UI with syntax highlighting and smooth animations.
  - "Examples" tab with copyable Google-recommended templates.

**Enhancements**
- Added compatibility support for Rank Math SEO.
- Improved UI/UX with enhanced animations and visual clarity.
- Preserved and integrated all existing schema data into the new metabox.

**Other**
- Major version increment to highlight the usability leap.
- Dashboard schema management remains for global/advanced use.

= 1.6.4 =
* Compatibility fix: Removed `readonly` property modifiers for full PHP 7.4 support.

= 1.6.3 =
* Added strict typing and void return declarations.
* Improved PHPDoc and WordPress Coding Standards compliance.

= 1.6.2 =
* **Security**: Fixed Stored XSS vulnerability via post titles in JSON-LD output.
* Sanitized titles, hardened JSON encoding, secured URL/breadcrumb handling.

(Older entries can be truncated or kept as-is if space allows)

== Upgrade Notice ==

= 2.0 =
Big usability upgrade! Enjoy the new metabox for faster per-page schema editing. All existing data is preserved — no migration needed. Highly recommended for all users.

= 1.6.2 =
**Security update** — patches a Stored XSS vulnerability. Update immediately.