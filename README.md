# Schema Scalpel

[![WordPress plugin](https://img.shields.io/wordpress/plugin/v/schema-scalpel?label=WP.org)](https://wordpress.org/plugins/schema-scalpel/)
[![License](https://img.shields.io/badge/license-GPLv2%20or%20later-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

Schema Scalpel gives you surgical control over structured data — add custom JSON-LD schema markup precisely where it matters, now with a powerful metabox editor right in the post/page screen.

## Contributors

- kevingillispie

## Donate

Support ongoing development at [https://schemascalpel.com/donate/](https://schemascalpel.com/donate/)

## Tags

- seo
- schema
- structured data
- json-ld
- markup
- per-page
- yoast
- rank-math
- all-in-one-seo
- rich snippets
- search engine

## Requirements

- **Requires at least**: WordPress 5.0
- **Tested up to**: WordPress 6.7 (or latest; update on release)
- **Stable tag**: 2.0
- **Requires PHP**: 7.4
- **License**: GPLv2 or later
- **License URI**: [https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html)

## Description

Many sites miss out on rich snippets, better CTR, and stronger entity signals because schema is missing, duplicated, or wrong. **Schema Scalpel** fixes that with precise, per-page (or global) JSON-LD control — no bloat, no conflicts.

**Version 2.0** introduces a game-changing **metabox editor** directly in the Gutenberg or Classic post/page editor:

- Create, edit, and delete custom schemas **without leaving your content screen**.
- Real-time AJAX saving for instant updates.
- Syntax-highlighted JSON editor with clean, modern UI.
- **Examples tab** — one-click copy of Google-recommended templates (FAQPage, Article, HowTo, Recipe, Product, Organization, BreadcrumbList, and more).

### Why Schema Scalpel Stands Out

- **Per-page & global precision** — Tailor schema to individual posts/pages or set site-wide defaults.
- **New metabox workflow** — Faster than dashboard-only tools; ideal for content creators and SEO pros.
- **Improved security** — Full sanitization (titles, URLs, breadcrumbs), secure JSON encoding, strict typing — hardened post-XSS-fix in 1.6.2.
- **Seamless compatibility** — Works alongside (and can override) Yoast SEO, Rank Math, All in One SEO — disable their schema to avoid duplicates.
- **Lightweight performance** — Pure JSON-LD output, optimized DB storage (MEDIUMBLOB), no frontend overhead.
- **Data safety** — Optional `delete_on_uninstall` setting cleans up on deletion.
- **Modern codebase** — Strict types, full WordPress Coding Standards, PHP 7.4+ ready.

Perfect for blogs, agencies, eCommerce, local SEO — unlock rich results (stars, FAQs, carousels) and better search visibility today.

## Installation

### From WordPress Dashboard

1. Go to **Plugins → Add New**.
2. Search “Schema Scalpel”.
3. Install and activate.
4. Use the new **Schema Scalpel metabox** in any post/page editor, or visit the **Schema Scalpel** admin menu for global/homepage schemas.

### Manual

1. Download from [WordPress.org](https://wordpress.org/plugins/schema-scalpel/) or GitHub.
2. Upload `schema-scalpel` folder to `/wp-content/plugins/`.
3. Activate via Plugins screen.
4. Start adding schemas in the metabox or dashboard.

## Frequently Asked Questions

### Where do I manage schemas in v2.0?

Primarily in the new **metabox** inside the post/page editor (fastest for per-page work). Global and homepage schemas stay in the **Schema Scalpel** admin menu.

### Will my existing schemas carry over?

Yes — 100% preserved. All prior global/post/page schemas appear automatically in the metabox and dashboard. No migration needed.

### Does it conflict with other SEO plugins?

No — excellent integration. Disable schema output in Yoast, Rank Math, or AIOSEO to let Schema Scalpel handle it cleanly.

### Why JSON-LD?

Google’s preferred format: clean, script-based, no HTML pollution. Easier to maintain and supports advanced types.

### Is it secure?

Yes — post-1.6.2, all dynamic content (titles, paths) is sanitized, JSON is securely encoded, inputs validated strictly.

## Changelog

### 2.0.0 (January 2026)

- **Major**: New **Schema Scalpel metabox** in post/page editor.
  - Create/edit/delete per-post/page JSON-LD schemas on the spot.
  - Real-time AJAX saves, syntax highlighting, smooth UI.
  - Built-in "Examples" tab with copyable Google templates.
- **Added**: Rank Math compatibility support.
- **Improved**: UI animations, visual distinction between local/global schemas.
- **Note**: Dashboard remains for global/advanced management; all old data auto-migrates to metabox view.
- Huge usability upgrade — most users will now edit schema right where they write content.

### 1.6.4

- [FIX] Removed `readonly` property modifiers for PHP 7.4 compatibility.

### 1.6.3

- [UPDATE] Strict typing (`declare(strict_types=1);`), void returns, enhanced PHPDoc.

### 1.6.2

- [SECURITY] Fixed Stored XSS via post titles in JSON-LD output.
- Sanitized titles, hardened `wp_json_encode()`, secured URLs/breadcrumbs.

(Older entries omitted for brevity; keep full history in repo if desired.)

## Upgrade Notice

**To 2.0**: Major feature release — enjoy the new metabox! All existing schemas remain intact. Clear cache if needed after update. Highly recommended.

**To 1.6.2+**: Security-critical update — install immediately if on older versions.

## Screenshots

1. The new Schema Scalpel metabox in the post editor – create and edit JSON-LD right where you write content.
2. Global & homepage schema management in the dashboard.
3. Examples tab with one-click Google-recommended schema templates.
4. User Settings dashboard where you can fine tune which pages get schema.
5. Export tool for transferring your schema from one site to another via SQL.
6. Generate customized schema for posts automatically!
