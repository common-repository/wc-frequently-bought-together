<?php
class wfst_metabox_add_images_class {
	
	  /**
	   * Construct
	   * since version 1.0.0
	   */
        public function __construct() {
	   
         add_action('admin_enqueue_scripts', array(&$this, 'wfst_register_scripts'));
	     
	     
		 add_action('woocommerce_process_product_meta', array($this,'wfst_update_variation_meta'), 10, 2 );
		 add_action('woocommerce_product_write_panel_tabs', array($this, 'wfst_add_bulk_multi_images_tab'));
		 add_action('woocommerce_product_data_panels', array($this, 'wfst_bulk_multi_tab_options'));
		 add_action('wp_ajax_wfstgetajaxproductslist', array( &$this, 'wfst_get_posts_ajax_callback' ) );
		 add_action('wp_ajax_wfst_ajax_new_values', array( &$this, 'wfst_ajax_new_values' ) );
		 
	    }




	    /**
		 * get newly added product details.
		 * 
		 * @since 1.1.0
		 */
        
		
		public function wfst_ajax_new_values() {
			global $post;
			
			$newproducts = $this->recursive_sanitize_text_field($_POST['newproducts']);
			
            foreach ($newproducts as $newproduct) {
            	$this->display_accordion_row($newproduct);
            }
            
	        
	        die;
		}







	    /**
		 * get newly added product details.
		 * 
		 * @since 1.1.0
		 */
        
		
		public function wfst_get_posts_ajax_callback($post) {
			global $post;
			$return = array();
			
            $post_type_array = array('product','product_variation');
	        // you can use WP_Query, query_posts() or get_posts() here - it doesn't matter
	        $search_results = new WP_Query( array( 
		      's'                   => sanitize_text_field($_GET['q']), // the search query
		      'post_status'         => 'publish', // if you don't want drafts to be returned
		      'ignore_sticky_posts' => 1,
		      'post_type'           => $post_type_array,
		      'posts_per_page'      => 50 // how much to show at once
	        ) );
	  
	        $current_title = get_the_title( $post_id );
	        if( $search_results->have_posts() ) :
		      while( $search_results->have_posts() ) : $search_results->the_post();	
			// shorten the title a little
			  $title = ( mb_strlen( $search_results->post->post_title ) > 50 ) ? mb_substr( $search_results->post->post_title, 0, 49 ) . '...' : $search_results->post->post_title;
			      $parent_id = $search_results->post->post_parent;
			      $product_type            = WC_Product_Factory::get_product_type($search_results->post->ID);
			      
			      $_main_product           = wc_get_product($search_results->post->ID);
		          $product_price           = $_main_product->get_price();
		          $currency                = get_option('woocommerce_currency');
				
				  if (isset($product_price) && $product_price != "") {
                     $finaltitle='#'. $search_results->post->ID.'- '.$title.'- '.$currency.' '.$product_price.'';
                     $return[] = array( $search_results->post->ID, $finaltitle );
				  }
				  
			    
			  
		   
		   endwhile;
	        endif;
	        echo json_encode( $return );
	        die;
		}
		
		

		
		


	    /**
		 * saving variation images.
		 * 
		 * @since 1.1.0
		 */
		public function wfst_add_bulk_multi_images_tab(){
			
			?>
           <a href="#wfst_bulk_multi_images_tab"><li class="wfst_bulk_multi_tab" >&nbsp;&nbsp;<?php _e('Frequently Sold Together', 'woomatrix-frequently-sold-together'); ?></a></li>
	       <?php
			
			
		}


	   /**
	    * Adds metabox tab content
	    * since version 1.0.1
	    */
	    public function wfst_bulk_multi_tab_options() { 
	    	global $post,$woocommerce,$product;

	    	include('forms/multi_tab_content.php'); 
            
	    }


	   /**
        * Recursive sanitation for an array
        * 
        * @param $array
        *
        * @return mixed
        */
        public function recursive_sanitize_text_field($array) {
            foreach ( $array as $key => $value ) {
                
                    $value = sanitize_text_field( $value );
               
            }

          return $array;
        }





		
	    /**
		 * saving variation images.
		 * 
		 * @since 1.0.0
		 */
		public function wfst_update_variation_meta($post_id) {
			
			
			if (isset($_POST['wfst_frequently_sold']) && !empty($_POST['wfst_frequently_sold']) && (is_array( $_POST['wfst_frequently_sold'] ))) {
                $wfst_frequently_sold = $this->recursive_sanitize_text_field($_POST['wfst_frequently_sold']);
            } else {
                $wfst_frequently_sold = array();
            }

            if (isset($_POST['wfst_values']) && !empty($_POST['wfst_values']) && (is_array( $_POST['wfst_values'] ))) {
                $wfst_values = $this->recursive_sanitize_text_field($_POST['wfst_values']);
            } else {
                $wfst_values = array();
            }

            if (isset($_POST['wfst_pramote_coupon']) && $_POST['wfst_pramote_coupon']) {
                $wfst_pramote_coupon = intval($_POST['wfst_pramote_coupon']);
            } else {
                $wfst_pramote_coupon = '';
            }

            if (isset($_POST['promote_thankyou'])) {
                $promote_thankyou = "yes";
            } else {
                $promote_thankyou = 'no';
            }

            if (isset($_POST['promote_email'])) {
                $promote_email = "yes";
            } else {
                $promote_email = 'no';
            }

            if (isset($_POST['offer_coupon'])) {
                $offer_coupon = "yes";
            } else {
                $offer_coupon = 'no';
            }

            
           
            
	        update_post_meta( $post_id, '_wfst_values', $wfst_values );
	        update_post_meta( $post_id, '_wfst_frequently_sold', $wfst_frequently_sold );
	        update_post_meta( $post_id, '_wfst_pramote_coupon', $wfst_pramote_coupon );
	        update_post_meta( $post_id, '_promote_thankyou', $promote_thankyou );
	        update_post_meta( $post_id, '_offer_coupon', $offer_coupon );
			
	
			
			
		}
      
	   /**
	    * Adds required js/css assets
	    * since version 1.0.0
	    */
	   public function wfst_register_scripts() {
		global $wp_query, $post;
		$screen         = get_current_screen();
        $screen_id      = $screen ? $screen->id : '';
		
		
		
		if ( in_array( $screen_id, array( 'product', 'edit-product' ) ) ) {
		
		
		  wp_enqueue_script('jquery-ui-accordion');
		  wp_enqueue_style( 'select2', ''.wfst_PLUGIN_URL.'assets/css/select2.css' );
		  wp_enqueue_script( 'select2', ''.wfst_PLUGIN_URL.'assets/js/select2.js' ,array('jquery') );
		  
		  wp_register_script( 'wfst-meta', ''.wfst_PLUGIN_URL.'assets/js/meta.js' );
		 
		  wp_register_style( 'wfst-meta', ''.wfst_PLUGIN_URL.'assets/css/meta.css' );
		  
		  $translation_array = array( 
		      
			    
		   );
          wp_localize_script( 'wfst-meta', 'wfstmeta', $translation_array );
	      
	      wp_enqueue_script('wfst-meta');
		 
		  wp_enqueue_style('wfst-meta');
		  
		}
	     
	    }
        

        /**
         * package @woomatrix
         * display accordion row from product id
         * $product_id - product id
         */

	    public function display_accordion_row($product_id) {
	    	global $post;

	    	$wfst_values          = get_post_meta( $post->ID, '_wfst_values', true ); 
            $_product             = wc_get_product($product_id);
            $product_price        = $_product->get_price();
		    $currency             = get_option('woocommerce_currency');
		    $couponargs           = array(
	           'posts_per_page'           => -1,
	           'post_type'                => 'shop_coupon',
	        );	
	        $coupons              = get_posts( $couponargs ); 

            
            if (isset($wfst_values[$product_id])) {
            	$wfst_values          = $wfst_values[$product_id];
            }


            
            ?>
	    	<h3 rownum ="<?php echo esc_html__($product_id); ?>" class="wfst-<?php echo esc_html__($product_id); ?> wfst-tab">
	    		#<?php echo esc_html__($product_id); ?>-<?php echo esc_html__(get_the_title( $product_id )); ?> - <?php echo $currency; ?> <?php echo $product_price; ?>
            </h3>
            <div prod-id="<?php echo esc_html__($product_id); ?>" class ="wfst-div-<?php echo esc_html__($product_id); ?> wfst-div">
            	<span class="wfst-view-product-link">
	    			<a target="_blank" title="<?php echo esc_html__('View on frontend','wfst'); ?>" href="<?php echo get_permalink($product_id); ?>"><span class="dashicons dashicons-visibility"></span></a>
	    			<a target="_blank" title="<?php echo esc_html__('Edit product','wfst'); ?>" href="<?php echo get_edit_post_link($product_id); ?>"><span class="dashicons dashicons-edit"></span></a>
	    		</span>
                <table class="table widefat wfst-table-li">
                    <tr class="wfst-meta-second-tr-<?php echo esc_html__($product_id); ?>" style="">

                	    <td width="30%"><?php echo esc_html__('Checkout page discounted price','woomatrix-frequently-sold-together'); ?></td>
                	    <td width="70%"> 
                	    	<?php

                	    	if (isset($wfst_values['promote_price'])) {
                	    			$promote_price  = esc_html__($wfst_values['promote_price']); 
                	    	} else {
                                    $promote_price  = $product_price;
                	        } 

                	    	?>
                	    	<input type="number" max="<?php echo $product_price; ?>" class="wfst_special_price_number_input" name="wfst_values[<?php echo esc_html__($product_id); ?>][promote_price]" value="<?php echo $promote_price; ?>" >
                	    </td>
                    </tr>

                	<tr>
                	    <td width="30%"><?php echo esc_html__('Promote on checkout page','woomatrix-frequently-sold-together'); ?></td>
                	    <td width="70%">
                	    	<input type="checkbox" prod-id="<?php echo esc_html__($product_id); ?>" class="wfst-meta-checkbox" value="yes" name="wfst_values[<?php echo esc_html__($product_id); ?>][checkout_promote]" <?php if (isset($wfst_values['checkout_promote']) && ($wfst_values['checkout_promote'] == "yes")) { echo 'checked';} ?>>
                	    </td>
                    </tr>
                    <?php
                        if (isset($wfst_values['checkout_promote']) && ($wfst_values['checkout_promote'] == "yes")) { 
                        	 $desc_tr_display= "display:table-row;";
                        } else {
                             $desc_tr_display= "display:none;";
                        }

                        if (isset($wfst_values['promote_text'])) {
                	    			$pramote_text  = esc_html__($wfst_values['promote_text']); 
                	    		} else {
                                    $pramote_text  = apply_filters( 'woocommerce_short_description', esc_html__($_product->get_short_description()) );
                                    $pramote_text  = preg_replace('#\<(.+?)\>#', ' ', $pramote_text);
                	    } 
                    ?>
                    <tr class="wfst-meta-tr-<?php echo esc_html__($product_id); ?>" style="<?php echo $desc_tr_display; ?>">
                	    <td width="30%"><?php echo esc_html__('Promote text on checkout page','woomatrix-frequently-sold-together'); ?></td>
                	    <td width="70%">
                	    	<textarea rows="3" cols="60" name="wfst_values[<?php echo esc_html__($product_id); ?>][promote_text]"><?php echo $pramote_text; ?></textarea> 
                	    </td>
                    </tr>




                    
                </table>
            </div>
            <?php
	    }
	}



         
new wfst_metabox_add_images_class();



?>