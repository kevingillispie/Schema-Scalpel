# Schema Scalpel

[![WordPress plugin](https://img.shields.io/wordpress/plugin/v/schema-scalpel?label=WP.org)](https://wordpress.org/plugins/schema-scalpel/)
[![License](https://img.shields.io/badge/license-GPLv3%20or%20later-blue.svg)](https://www.gnu.org/licenses/gpl-3.0.html)

Schema Scalpel gives you surgical control over structured data — add custom JSON-LD schema markup precisely where it matters, now with a powerful metabox editor right in the post/page screen.

## Contributors

- kevingillispie

## Donate

Support ongoing development at [https://schemascalpel.com/donate/](https://schemascalpel.com/donate/)

## Tags

- **schema**
- **seo**
- **json-ld**
- **structured data**
- **rich snippets**

## Requirements

- **Requires at least**: WordPress 5.0
- **Tested up to**: WordPress 6.9
- **Stable tag**: 2.0.3
- **Requires PHP**: 7.4 (Fully compatible through **PHP 8.4**)
- **License**: GPLv3 or later

## Description

Many sites miss out on rich snippets and stronger entity signals because schema is missing or duplicated. **Schema Scalpel** provides precise, per-page JSON-LD control with zero bloat.

**Schema Scalpel v2.0.3** reinforces the plugin's reputation for stability and data integrity. We have overhauled the storage engine to support high-density structured data:

- **Expanded Storage Capacity**: Upgraded the schema engine to use `MEDIUMBLOB`, allowing up to **16MB** of JSON-LD per post. This eliminates the "64KB truncation" bug found in many other plugins, ensuring massive FAQ and Product schemas remain valid.
- **SQL Migration Hardening**: Refactored the database upgrade logic to resolve syntax errors on modern MariaDB environments while strictly adhering to WordPress identifier escaping standards.
- **Scalable Architecture**: Updated the primary keys to `BIGINT UNSIGNED`, future-proofing the plugin for enterprise-level sites with millions of records.

**Version 2.0+** features a game-changing **metabox editor** directly in the Gutenberg or Classic editor:

- **Edit In-Situ**: Create and manage schemas without leaving your content screen.
- **Real-time AJAX**: Instant saves and syntax-highlighted editing.
- **Examples Library**: One-click copy for FAQ, Article, Recipe, Product, and more.

### Why Schema Scalpel?

- **Enterprise Reliability**: v2.0.3 ensures your data is never truncated and your migrations are error-free.
- **Hardened Security**: Implements modern `%i` identifier placeholders and `JSON_HEX_TAG` encoding to neutralize XSS threats.
- **High Performance**: Optimized batch processing reduces database round-trips by up to 98%.
- **Deep Compatibility**: Works flawlessly alongside Yoast, Rank Math, and AIOSEO.

## Changelog

### 2.0.3 (2026)

- **FIX**: Resolved "SQL Syntax Error" in database migration script affecting specific MariaDB/MySQL configurations.
- **DATABASE**: Upgraded `custom_schema` storage from `TEXT` to `MEDIUMBLOB` to prevent data truncation for large JSON-LD payloads.
- **SCALABILITY**: Transitioned `id` columns to `BIGINT UNSIGNED` to support high-volume content databases.
- **REFACTOR**: Satisfied WP-Coding Standards by using `sprintf` patterns for dynamic table identifiers in migration queries.

### 2.0.2 (2026)

- **SECURITY**: Implemented modern `%i` identifier placeholders for all custom database queries.
- **HARDENING**: Enhanced JSON encoding with bitwise flags to neutralize advanced XSS payloads.
- **PERFORMANCE**: Refactored bulk generation to use efficient batch logic.
- **COMPATIBILITY**: Full audit for **PHP 8.4** support.

### 2.0.1

- **Performance**: Third-party schema disabling now uses a single efficient database query.
- Updated query to use modern identifier placeholders.

### 2.0.0 (2026)

- **Major**: New **Schema Scalpel metabox** in post/page editor.
- **Added**: Rank Math compatibility support.
- **Improved**: UI animations and real-time AJAX saving.

## Upgrade Notice

**To 2.0.3**: **Critical Maintenance Update.** Fixes database migration errors and prevents schema data truncation for large files. Highly recommended for all users.

**To 2.0.2**: Implements critical database hardening and performance optimizations.
