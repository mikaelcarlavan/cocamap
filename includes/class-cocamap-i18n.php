<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link              http://mika-carl.fr
 * @since             1.0.0
 * @package           cocamap
 * @subpackage 		  cocamap/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since             1.0.0
 * @package           cocamap
 * @subpackage 		  cocamap/includes
 * @author     		Mikael Carlavan <contact@mika-carl.fr>
 */
class Cocamap_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'cocamap',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
