=== Schema Scalpel ===
Contributors: kevingillispie
Donate link: https://schemascalpel.com/donate/
Tags: seo, schema, structured data, json, microdata, search engine
Requires at least: 3.0.1
Tested up to: 6.0
Stable tag: 1.2.5.4
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

### JSON-LD vs. Microdata: What's the Difference?

Both of these forms of structured data will provide to search engines and their respective bots the information they need to understand your website.

The primary difference is that microdata is written into and dispersed throughout the HTML of your page. This makes maintenance thereof very impractical. JavaScript Object Notation for Linked Data (JSON-LD), on the other hand, is contained wholly within a single set of `script` tags making it very easy to create, update, and improve. 

Most importantly, **[Google recommends that you use JSON-LD!](https://developers.google.com/search/docs/advanced/structured-data/intro-structured-data#format-placement)** Give the search engines what they ask for in the format that they prefer, and they'll rank your site higher.

== Changelog ==

= 1.2.5.4 =
THIS IS A NECESSARY UPDATE.
[FIX] The initial tab setting in the database has been updated to `homepage`. The schema-editing page will now load properly.

[FIX] The initial search query parameter is now fully defaulted to `s`. 

= 1.2.5.3 =
[FIX] If the absolute path to the plugin contained the word `home` in it, the `scsc-create-new-schema.php` file wouldn't properly switch between the schema-type tabs. Naming standard changed to `homepage`.

= 1.2.5.2 =
[FIX] `admin/vars` directory didn't get pushed with last update. >:(

= 1.2.5 =
[NEW] Added new example schema as highlighted by the Google Developer documentation, including `COVID Announcement` schema.

[UPDATE] Updated `NewsArticle` schema example to include `author` schema.

[UPDATE] Exmample schema are now called directly from an array rather than the database.

[FIX] Spaces after commas are now limited to one to maintain traditional punctuation practices. 

= 1.2.4.1 =
[FIX] The create and edit schema buttons were being generated with the incorrect schema type. Each tab will now display the appropriate schema (i.e. home, global, pages, posts).

= 1.2.4 =
[FIX] Should the `wp_head()` function be called more than once, some functions in the `/public/class-schema-scalpel-public.php` file would throw a fatal PHP error due to redundant declarations. 

= 1.2.3 =
[UPDATE] Schema Scalpel works with WordPress 6.0!

[FIX] A few CSS class names that weren't updated to Bootstrap 5.x naming standards.

[FIX] Page and post titles were not displaying properly due to sanitization function.

= 1.2.2 =
[UPDATE] The schema/JSON-LD format error checking has been greatly improved. 

= 1.2.1 =
[FIX] Version 1.2 was pushed without updating every instance of version number within the plugin.

= 1.2 =
[NEW] Multisite activation is now possible!

[UPDATE] Removed some comment clutter.

= 1.0.1 =
[FIX] Replaced use of `wp_print_scripts` hook with `wp_enqueue_scripts` as the former prevented the loading of schema with some themes.

[FIX] A bunch of stuff I have changed since publishing Schema Scalpel but have forgotten about because I didn't know how to use my Subversion client.

[UPDATE] Plugin init function `run_schema_scalpel` now called via `plugins_loaded` hook.

[UPDATE] Changed the default search key to `s` to conform to WordPress's default search functionality.

[UPDATE] Schema Scalpel logo now uses font paths.

= 1.0 =
The unleashing of the Schema Scalpel upon the world. You might say that the plugin is now “off the chain”.