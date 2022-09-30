<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link              http://mika-carl.fr
 * @since             1.0.0
 * @package           cocamap
 * @subpackage cocamap/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    cocamap
 * @subpackage cocamap/public
 * @author     		Mikael Carlavan <contact@mika-carl.fr>
 */
class Cocamap_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cocamap_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cocamap_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( 'leaflet', 'https://unpkg.com/leaflet@1.2.0/dist/leaflet.css', array(), null, 'all');

		wp_enqueue_style( 'leaflet.markercluster', 'https://unpkg.com/leaflet.markercluster@1.2.0/dist/MarkerCluster.css', array(), null, 'all');
		wp_enqueue_style( 'leaflet.markercluster.default', 'https://unpkg.com/leaflet.markercluster@1.2.0/dist/MarkerCluster.Default.css', array(), null, 'all');
		
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cocamap-public.css?ver='.uniqid(), array(), null, 'all' );



	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cocamap_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cocamap_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( 'leaflet', 'https://unpkg.com/leaflet@1.2.0/dist/leaflet.js', array( 'jquery' ), null, false);
		wp_enqueue_script( 'leaflet.markercluster', 'https://unpkg.com/leaflet.markercluster@1.2.0/dist/leaflet.markercluster.js', array( 'jquery' ), null, false);
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cocamap-public.js?ver='.uniqid(), array( 'jquery' ), null, false );

	}

}
