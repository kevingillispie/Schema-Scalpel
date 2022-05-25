=== Schema Scalpel ===
Contributors: kevingillispie
Donate link: https://schemascalpel.com/donate/
Tags: seo, schema, structured data, json, microdata, search engine
Requires at least: 3.0.1
Tested up to: 5.9.3
Stable tag: 1.2.2
Requires PHP: 7.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.txt

Schema Scalpel helps you create rich text snippets for search engines in the format that they prefer.

== Description ==

The **secret** to great schema is to _actually_ put it on your website! Many sites don't use schema, or use it incorrectly, thereby missing out on a powerful SEO and branding tool.

Schema Scalpel solves this problem by giving you complete control over your website’s structured data. It allows you to tell search engines exactly what you want them to know about who you are and what you do.

### Features
- Create completely customized schema on any page… or _every_ page!
- Copy and paste from a collection of built-in schema templates recommended by Google
- Works with the SEO plugins Yoast and All-in-One

== Installation ==

### From your WordPress dashboard:

- Visit `Plugins > Add New`
- Search for `Schema Scalpel`
- Click `Install`
- Activate Schema Scalpel from your Plugins page.

### From WordPress.org:

- Download Schema Scalpel.
- Upload the `schema-scalpel` directory to your `/wp-content/plugins/` directory (using one of the following: FTP, SFTP, SCP, et al.)
- Activate Schema Scalpel from your Plugins page.

You will then find a Schema Scalpel menu item in your WordPress admin dashboard.

== Frequently Asked Questions ==

### What is schema?

Schema, also known as structured data, is a form of microdata. I know that clarifies nothing, but stay with me here. Schema allows a website to give search engines precise information about the nature and content of the website.

Organizations, businesses, and individuals all have different types of information necessary for describing who and what they are and what they do. Schema is how that information is conveyed to search engines so that the websites associated with those entities show up in the most relevant search results.

== Changelog ==

= 1.2.2 =
[UDPATED] The schema/JSON-LD format error checking has been greatly improved. 

= 1.2.1 =
[FIXED] Version 1.2 was pushed without updating every instance of version number within the plugin.

= 1.2 =
[NEW] Multisite activation is now possible!

[UPDATED] Removed some comment clutter.

= 1.0.1 =
[FIXED] Replaced use of `wp_print_scripts` hook with `wp_enqueue_scripts` as the former prevented the loading of schema with some themes.

[FIXED] A bunch of stuff I have changed since publishing Schema Scalpel but have forgotten about because I didn't know how to use my Subversion client.

[UPDATED] Plugin init function `run_schema_scalpel` now called via `plugins_loaded` hook.

[UPDATED] Changed the default search key to `s` to conform to WordPress's default search functionality.

[UPDATED] Schema Scalpel logo now uses font paths.

= 1.0 =
The unleashing of the Schema Scalpel upon the world. You might say that the plugin is now “off the chain”.

== Upgrade Notice ==