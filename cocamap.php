<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://mika-carl.fr
 * @since             1.0.0
 * @package           cocamap
 *
 * @wordpress-plugin
 * Plugin Name:       Carte de contacts d'une catégorie Dolibarr
 * Plugin URI:        http://mika-carl.fr
 * Description:       Le plugin affiche une carte des contacts d'une catégorie Dolibarr.
 * Version:           1.0.0
 * Author:            Mikael Carlavan
 * Author URI:        http://mika-carl.fr
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cocamap
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'COCAMAP_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cocamap-activator.php
 */
function activate_cocamap() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cocamap-activator.php';
	Cocamap_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cocamap-deactivator.php
 */
function deactivate_cocamap() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cocamap-deactivator.php';
	Cocamap_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cocamap' );
register_deactivation_hook( __FILE__, 'deactivate_cocamap' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cocamap.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cocamap() {

	$plugin = new Cocamap();
	$plugin->run();

}

run_cocamap();
