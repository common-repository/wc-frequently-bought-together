<?php
/*
	Plugin Name: Frequently Bought Together for WooCommerce
	Description: Encourage your site users to buy more by suggesting products that are sold together.
    Version: 1.0.1
	Author: woomatrix
	Author URI: http://woomatrix.com
	

	Domain Path: /languages
	Requires at least: 3.3
    Tested up to: 5.7
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


 if( !defined( 'wfst_PLUGIN_URL' ) )
    define( 'wfst_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

 if( !defined( 'wfst_plugin_text' ) )
 	define( 'wfst_plugin_text', esc_html__( 'Frequently Baught Together' ,'woomatrix-frequently-sold-together') );





    load_plugin_textdomain( 'woomatrix-frequently-sold-together', false, basename( dirname(__FILE__) ).'/languages' );





    include dirname( __FILE__ ) . '/include/admin/metabox/metabox_tab.php';
    include dirname( __FILE__ ) . '/include/frontend/woocommerce_before_variations_form.php';





	/*
	 * Get woocommerce version 
	 */
	function wfst_get_woo_version_number() {
       
	   if ( ! function_exists( 'get_plugins' ) )
		 require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	
       
	   $plugin_folder = get_plugins( '/' . 'woocommerce' );
	   $plugin_file = 'woocommerce.php';
	
	
	   if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
		  return $plugin_folder[$plugin_file]['Version'];

	   } else {
	
		return NULL;
	   }
    }

    /**
	 * Returns placeholder images
	 */

    function wfst_placeholder_img_src() {
       return ''.wfst_PLUGIN_URL.'assets/images/placeholder.png';
    }


?>