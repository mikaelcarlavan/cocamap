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
		$elements = array();

   	 	// normalize attribute keys, lowercase
    	$atts = array_change_key_case((array)$atts, CASE_LOWER);
 
	    // override default attributes with user attributes
	    $cocamap_atts = shortcode_atts([
	        'type' => 'customer',
	        'height' => '400',
	        'center' => '51.505, -0.09',
	        'zoom' => '13',
	        'cid' => '1',
	        'address'=> '1',
	        'phone'=> '0',
	        'email'=>'0',
	        'website'=>'1',
	        'goto'=>'0',
	        'scrollwheelzoom'=>'1'

	    ], $atts, $tag);

	    //var_dump($options);

	    $category_type = $cocamap_atts['type'];
	    $category_id = $cocamap_atts['cid'];
	    $address = $cocamap_atts['address'];
	    $phone = $cocamap_atts['phone'];
	    $mail = $cocamap_atts['email'];
	    $website = $cocamap_atts['website'];
	    $goto = $cocamap_atts['goto'];
	    $scrollwheelzoom = $cocamap_atts['scrollwheelzoom'];

        $url = $options['cocamap_dolibarr_url'];
        $key = $options['cocamap_dolibarr_key'];
        // remove trailing slash
        $url = rtrim($url, '/');
        
        if($category_type == 'customer'){
        	$url = $url .'/api/index.php/contactscategories/societe/'.$category_id;
        }elseif($category_type == 'contact'){
        	$url = $url .'/api/index.php/contactscategories/'.$category_id;
        }
                
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

			$elements = json_decode( $body, true );
						
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
		if ( sizeof( $elements )) {
			foreach ( $elements as $element) {

				if(!empty($element['center'])){		
					$todisplay='';
					$todisplay.=$element['lastname'].' '.$element['firstname'].'<br>';					 
					if($address==1){
							//$todisplay.=($element['address']).'<br>';
							$todisplay.=$element['zip'].' ';
							$todisplay.=$element['town'];
						} 
					    if($phone==1){
					    	$todisplay.='<br>'.$element['phone_perso'];
					    	$todisplay.='<br>'.$element['phone_mobile'];
					    }
					    if($mail==1){
					    	$todisplay.=$element['mail'];
					    }
					    if($website==1){
					    	$todisplay.=$element['website'];
					    }
					    if($goto ==1){
					    	$todisplay.=$element['goto'];
					    }

						$o .= "markers.addLayer(L.marker([".$element['center']."]).bindPopup('".str_replace ( "'", "\'", $todisplay)."'));"."\r\n";
						//$o .= "    		.openPopup();"."\r\n";				
				}
			}
		}

		$o .= "			map.addLayer(markers);"."\r\n";
		$o .= "		});"."\r\n";
	 	$o .= "})( jQuery );"."\r\n";
	 	$o .= "</script>"."\r\n";
	    // return output
	    return $o;

	}


    /**
	 * Register the stylesheets for the shortcodes-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function process_cocamaplist_shortcode($atts = [], $content = null, $tag = '') {

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

		$options = get_option('cocamap_options');
		$url = $options['cocamap_dolibarr_url'];
		$key = $options['cocamap_dolibarr_key'];
        $url = rtrim($url, '/');

		$elements = array();

   	 	// normalize attribute keys, lowercase
    	$atts = array_change_key_case((array)$atts, CASE_LOWER);
 
	    // override default attributes with user attributes
	    $cocamap_atts = shortcode_atts([
	        'type' => 'customer',
	        'cid' => '1',
	        'address'=> '1',
	        'phone'=> '1',
	        'email'=>'0',
	        'website'=>'1',
	        'goto'=>'1',
	    ], $atts, $tag);

	    $category_type = $cocamap_atts['type'];
	    $category_id = $cocamap_atts['cid'];
	    $address = $cocamap_atts['address'];
	    $phone = $cocamap_atts['phone'];
	    $mail = $cocamap_atts['email'];
	    $website = $cocamap_atts['website'];
	    $goto = $cocamap_atts['goto'];       
        
        if($category_type == 'customer'){
        	$url = $url .'/api/index.php/contactscategories/societe/'.$category_id;
        }elseif($category_type == 'contact'){
        	$url = $url .'/api/index.php/contactscategories/'.$category_id;
        }
                
        $args = array(
            'headers'     => array(
                'DOLAPIKEY' => $key,
            ),
        );

        $response = wp_remote_get( $url, $args ); 
		
		$http_code = wp_remote_retrieve_response_code( $response );

		if ($http_code == 200) {
			$body = wp_remote_retrieve_body( $response );

			$elements = json_decode( $body, true );
		}

		// start output
	    $o = "";
	    // start box
	    
	    $o .= '<div class="cocamap" id="cocamap-list">';
	    
	    foreach ( $elements as $element){
	    	
	    $todisplay = $element['description_name'];					
		if($address ==1){
			$todisplay.=$element['description_address'];
		} 
	    if($phone ==1){
	    	$todisplay.=$element['description_phone'];
	    }
	    if($mail ==1){
	    	$todisplay.=$element['description_mail'];
	    }
	    if($website ==1){
	    	$todisplay.=$element['description_website'];
	    }
	    if($goto ==1){
	    	$todisplay.=$element['description_goto'];
	    }
	     $o .= '<div class="cocamap_card">';
	     $o .= $todisplay;
	     $o .= '</div>';	
	    }

	    $o .= '</div>';	 
	 
	 	return $o;

	}
	

	/**
	 * Register the stylesheets for the shortcodes-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function process_cocamaplistdptfr_shortcode($atts = [], $content = null, $tag = '') {

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
		
		$departements = array(
    '01' => 'Ain',
    '02' => 'Aisne',
    '03' => 'Allier',
    '04' => 'Alpes-de-Haute-Provence',
    '05' => 'Hautes-Alpes',
    '06' => 'Alpes-Maritimes',
    '07' => 'Ardèche',
    '08' => 'Ardennes',
    '09' => 'Ariège',
    '10' => 'Aube',
    '11' => 'Aude',
    '12' => 'Aveyron',
    '13' => 'Bouches-du-Rhône',
    '14' => 'Calvados',
    '15' => 'Cantal',
    '16' => 'Charente',
    '17' => 'Charente-Maritime',
    '18' => 'Cher',
    '19' => 'Corrèze',
    '20' => 'Corse',
    '21' => 'Côte-d\'Or',
    '22' => 'Côtes-d\'Armor',
    '23' => 'Creuse',
    '24' => 'Dordogne',
    '25' => 'Doubs',
    '26' => 'Drôme',
    '27' => 'Eure',
    '28' => 'Eure-et-Loir',
    '29' => 'Finistère',
    '30' => 'Gard',
    '31' => 'Haute-Garonne',
    '32' => 'Gers',
    '33' => 'Gironde',
    '34' => 'Hérault',
    '35' => 'Ille-et-Vilaine',
    '36' => 'Indre',
    '37' => 'Indre-et-Loire',
    '38' => 'Isère',
    '39' => 'Jura',
    '40' => 'Landes',
    '41' => 'Loir-et-Cher',
    '42' => 'Loire',
    '43' => 'Haute-Loire',
    '44' => 'Loire-Atlantique',
    '45' => 'Loiret',
    '46' => 'Lot',
    '47' => 'Lot-et-Garonne',
    '48' => 'Lozère',
    '49' => 'Maine-et-Loire',
    '50' => 'Manche',
    '51' => 'Marne',
    '52' => 'Haute-Marne',
    '53' => 'Mayenne',
    '54' => 'Meurthe-et-Moselle',
    '55' => 'Meuse',
    '56' => 'Morbihan',
    '57' => 'Moselle',
    '58' => 'Nièvre',
    '59' => 'Nord',
    '60' => 'Oise',
    '61' => 'Orne',
    '62' => 'Pas-de-Calais',
    '63' => 'Puy-de-Dôme',
    '64' => 'Pyrénées-Atlantiques',
    '65' => 'Hautes-Pyrénées',
    '66' => 'Pyrénées-Orientales',
    '67' => 'Bas-Rhin',
    '68' => 'Haut-Rhin',
    '69' => 'Rhône',
    '70' => 'Haute-Saône',
    '71' => 'Saône-et-Loire',
    '72' => 'Sarthe',
    '73' => 'Savoie',
    '74' => 'Haute-Savoie',
    '75' => 'Paris',
    '76' => 'Seine-Maritime',
    '77' => 'Seine-et-Marne',
    '78' => 'Yvelines',
    '79' => 'Deux-Sèvres',
    '80' => 'Somme',
    '81' => 'Tarn',
    '82' => 'Tarn-et-Garonne',
    '83' => 'Var',
    '84' => 'Vaucluse',
    '85' => 'Vendée',
    '86' => 'Vienne',
    '87' => 'Haute-Vienne',
    '88' => 'Vosges',
    '89' => 'Yonne',
    '90' => 'Territoire de Belfort',
    '91' => 'Essonne',
    '92' => 'Hauts-de-Seine',
    '93' => 'Seine-Saint-Denis',
    '94' => 'Val-de-Marne',
    '95' => 'Val-d\'Oise'
	);

		$options = get_option('cocamap_options');
		$url = $options['cocamap_dolibarr_url'];
		$key = $options['cocamap_dolibarr_key'];
        $url = rtrim($url, '/');

		$elements = array();

   	 	// normalize attribute keys, lowercase
    	$atts = array_change_key_case((array)$atts, CASE_LOWER);
 
	    // override default attributes with user attributes
	    $cocamap_atts = shortcode_atts([
	        'type' => 'customer',
	        'cid' => '1',
	        'address'=> '1',
	        'phone'=> '0',
	        'email'=>'0',
	        'website'=>'1',
	        'goto'=>'1',
	    ], $atts, $tag);

	    $category_type = $cocamap_atts['type'];
	    $category_id = $cocamap_atts['cid'];
	    $address = $cocamap_atts['address'];
	    $phone = $cocamap_atts['phone'];
	    $mail = $cocamap_atts['email'];
	    $website = $cocamap_atts['website'];
	    $goto = $cocamap_atts['goto'];       
        

        if($category_type == 'customer'){
        	$url = $url .'/api/index.php/contactscategories/societe/'.$category_id;
        }elseif($category_type == 'contact'){
        	$url = $url .'/api/index.php/contactscategories/'.$category_id;
        }
                       
        $args = array(
            'headers'     => array(
                'DOLAPIKEY' => $key,
            ),
        );

        $response = wp_remote_get( $url, $args ); 
		$http_code = wp_remote_retrieve_response_code( $response );

		if ($http_code == 200) {
			$body = wp_remote_retrieve_body( $response );
			$elements = json_decode( $body, true );
		}

		//on trie par nom de ville
		uasort($elements, array($this,'tritown'));
		
		//on regroupe par département
		foreach ($elements as $element) {
			$dptkey=substr($element['zip'],0,2);
	    	$elements_by_zip[$dptkey][] = $element;
		}

		//on trie par département
		ksort($elements_by_zip);
			
		//on affiche la liste	
	    $o = "";
	    $o .= '<div class="cocamap" id="cocamap-list">';


	    foreach ($elements_by_zip as $dpt=>$items){
	    	//if($dpt>0){
				 $o.='<h3 class="cocamap_title_list">'.$dpt.' - '.$departements[$dpt].'</h3>';  	
				 
		    	foreach($items as $element){ 		
			    	
					    $todisplay = '';					
						$todisplay.=$element['lastname'].' '.$element['firstname'].'<br>';
						if($address==1){
							$todisplay.=nl2br($element['address']).'<br>';
							$todisplay.=$element['zip'].' ';
							$todisplay.=$element['town'];
						} 
					    if($phone==1){
					    	$todisplay.='<br>'.$element['phone_perso'];
					    	$todisplay.='<br>'.$element['phone_mobile'];
					    }
					    if($mail==1){
					    	$todisplay.=$element['mail'];
					    }
					    if($website==1){
					    	$todisplay.=$element['website'];
					    }
					    
					     $o .= '<div class="cocamap_card">';
					     $o .= $todisplay;
					     $o .= '</div>';
					
				}
			//}
				 
	    }
		$o .= '</div>';
	    
	 
	 	return $o;

	}

	function tritown($a, $b){
    return $a['town'] > $b['town'];
}

}
