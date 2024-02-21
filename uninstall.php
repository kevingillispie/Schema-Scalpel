<?php

namespace SchemaScalpel;

/**
 * @link       https://schemascalpel.com
 *
 * @package    Schema_Scalpel
 */

if (!defined('WP_UNINSTALL_PLUGIN')) exit();

global $wpdb;
$table = [
	$wpdb->prefix . "scsc_settings",
	$wpdb->prefix . "scsc_custom_schemas"
];

foreach ($table as $key => $value) {
	$sql = "DROP TABLE IF EXISTS $value";
	$wpdb->query($sql);
}

flush_rewrite_rules();
