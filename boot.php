<?php
	/**
	 * OnePress Updates
	 *
	 * @author Paul Kashtanoff <paul@byonepress.com>
	 * @copyright (c) 2013, OnePress Ltd
	 *
	 * @package core
	 * @since 1.0.0
	 */

	// Checks if the one is already loaded.
	// We prevent to load the same version of the module twice.
	if( defined('ONP_UPDATES_000_LOADED') ) {
		return;
	}
	define('ONP_UPDATES_000_LOADED', true);

	// Absolute path and URL to the files and resources of the module.
	define('ONP_UPDATES_000_DIR', dirname(__FILE__));

	load_plugin_textdomain('onepress_updates_000', false, dirname(plugin_basename(__FILE__)) . '/langs');

	#comp merge
	include(ONP_UPDATES_000_DIR . '/includes/transient.functions.php');
	include_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
	include(ONP_UPDATES_000_DIR . '/includes/class.upgrader.php');
	include(ONP_UPDATES_000_DIR . '/updates.php');
	#endcomp

