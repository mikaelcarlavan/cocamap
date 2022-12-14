<?php

/**
 * The shortcodes-facing functionality of the plugin.
 *
 * @link              http://mika-carl.fr
 * @since             1.0.0
 * @package           cocamap
 * @subpackage cocamap/shortcodes
 */

/**
 * The shortcodes-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the shortcodes-facing stylesheet and JavaScript.
 *
 * @package    cocamap
 * @subpackage cocamap/shortcodes
 * @author     		Mikael Carlavan <contact@mika-carl.fr>
 */
class Cocamap_Shortcodes {

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
	 * Register the stylesheets for the shortcodes-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function process_cocamap_shortcode($atts = [], $content = null, $tag = '') {

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

		$options = get_option( 'cocamap_options' );
		$contacts = array();

   	 	// normalize attribute keys, lowercase
    	$atts = array_change_key_case((array)$atts, CASE_LOWER);
 
	    // override default attributes with user attributes
	    $cocamap_atts = shortcode_atts([
	        'height' => '400',
	        'center' => '51.505, -0.09',
	        'zoom' => '13',
	        'cid' => '1'
	    ], $atts, $tag);

	    $category_id = $cocamap_atts['cid'];

        $url = $options['cocamap_dolibarr_url'];
        $key = $options['cocamap_dolibarr_key'];
        // remove trailing slash
        $url = rtrim($url, '/');
        $url = $url .'/api/index.php/contactscategories/'.$category_id;
        // add key
        // $url = $url .'?DOLAPIKEY='.$key;

        $args = array(
            'headers'     => array(
                'DOLAPIKEY' => $key,
            ),
        );

        $response = wp_remote_get( $url, $args );
		
		$http_code = wp_remote_retrieve_response_code( $response );

		if ($http_code == 200) {
			$body = wp_remote_retrieve_body( $response );

			$contacts = json_decode( $body, true );
		}


		$map_id = uniqid();

		// start output
	    $o = "";
	    // start box
	    $o .= "<div class=\"cocamap\" style=\"height:".$cocamap_atts['height']."px\" id=\"cocamap-".$map_id."\"></div>"."\r\n";	 
	 
	 	$o .= "<script type=\"text/javascript\">"."\r\n";

	 	$o .= "(function( $ ) {"."\r\n";
		$o .= "'use strict';"."\r\n";
		$o .= "		$( document ).ready(function() {"."\r\n";
		$o .= "			var map = L.map('cocamap-".$map_id."').setView([".$cocamap_atts['center']."], ".$cocamap_atts['zoom'].");"."\r\n";

		$o .= "			L.tileLayer('http://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {"."\r\n";
		$o .= "    			attribution: '&copy; <a href=\"http://osm.org/copyright\">OpenStreetMap</a> contributors'"."\r\n";
		$o .= "			}).addTo(map);"."\r\n";

		$o .= "			var markers = L.markerClusterGroup();"."\r\n";
		if ( sizeof( $contacts )) {
			foreach ( $contacts as $contact) {
				$o .= "			markers.addLayer(L.marker([".$contact['center']."]).bindPopup('".str_replace ( "'", "\'", $contact['description'])."'));"."\r\n";
				//$o .= "    		.openPopup();"."\r\n";				
			}
		}

		$o .= "			map.addLayer(markers);"."\r\n";
		$o .= "		});"."\r\n";
	 	$o .= "})( jQuery );"."\r\n";
	 	$o .= "</script>"."\r\n";
	    // return output
	    return $o;

	}

}
