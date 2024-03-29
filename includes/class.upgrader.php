<?php

	/**
	 * Plugins upgrader
	 * @author Alex Kovalev <alex.kovalevv@gmail.com>
	 * @copyright Alex Kovalev 05.04.2017
	 * @version 1.0
	 */
	class Onp_Plugin_Upgrader extends Plugin_Upgrader {

		/**
		 * Upgrade a plugin.
		 *
		 * @since 2.8.0
		 * @since 3.7.0 The `$args` parameter was added, making clearing the plugin update cache optional.
		 * @access public
		 *
		 * @param string $plugin The basename path to the main plugin file.
		 * @param array $args {
		 *     Optional. Other arguments for upgrading a plugin package. Default empty array.
		 *
		 * @type bool $clear_update_cache Whether to clear the plugin updates cache if successful.
		 *                                    Default true.
		 * }
		 * @return bool|WP_Error True if the upgrade was successful, false or a WP_Error object otherwise.
		 */
		public function upgrade($plugin, $args = array())
		{

			$defaults = array(
				'package' => array(),
				'clear_update_cache' => true,
			);
			$parsed_args = wp_parse_args($args, $defaults);

			$this->init();
			$this->upgrade_strings();

			add_filter('upgrader_pre_install', array($this, 'deactivate_plugin_before_upgrade'), 10, 2);
			add_filter('upgrader_clear_destination', array($this, 'delete_old_plugin'), 10, 4);

			//'source_selection' => array($this, 'source_selection'), //there's a trac ticket to move up the directory for zip's which are made a bit differently, useful for non-.org plugins.
			if( $parsed_args['clear_update_cache'] ) {
				// Clear cache so wp_update_plugins() knows about the new plugin.
				add_action('upgrader_process_complete', 'wp_clean_plugins_cache', 9, 0);
			}

			$package = $parsed_args['package'];

			$this->run(array(
				'package' => $package,
				'destination' => WP_PLUGIN_DIR,
				'clear_destination' => true,
				'clear_working' => true,
				'hook_extra' => array(
					'plugin' => $plugin,
					'type' => 'plugin',
					'action' => 'update',
				),
			));

			// Cleanup our hooks, in case something else does a upgrade on this connection.
			remove_action('upgrader_process_complete', 'wp_clean_plugins_cache', 9);
			remove_filter('upgrader_pre_install', array($this, 'deactivate_plugin_before_upgrade'));
			remove_filter('upgrader_clear_destination', array($this, 'delete_old_plugin'));

			if( !$this->result || is_wp_error($this->result) ) {
				return $this->result;
			}

			// Force refresh of plugin update information
			wp_clean_plugins_cache($parsed_args['clear_update_cache']);

			return true;
		}
	}