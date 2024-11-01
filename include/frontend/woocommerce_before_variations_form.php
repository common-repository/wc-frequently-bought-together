<?php 

class wfst_add_frontend_class {
    
    public function __construct() {
	
	
	  add_action( 'wp_enqueue_scripts', array( $this, 'wfst_load_assets' ) );
	  add_action( 'woocommerce_after_main_content', array($this, 'wfst_woocommerce_before_variations_form'));
	  add_action( 'wp_loaded', array($this, 'woocommerce_maybe_add_multiple_products_to_cart'), 15 );
      add_action( 'woocommerce_checkout_order_review', array($this, 'wfst_woocommerce_checkout_order_review'),01);
      add_action( 'wp_footer',  array($this, 'wfst_checkout_custom_jquery_script') );
      add_action( 'wp_ajax_add_a_product', array($this, 'wfst_checkout_ajax_add_a_product') );
      add_action( 'wp_ajax_nopriv_add_a_product', array($this, 'wfst_checkout_ajax_add_a_product') );
      add_action( 'wp_ajax_remove_a_product', array($this, 'wfst_checkout_ajax_remove_a_product') );
      add_action( 'wp_ajax_nopriv_remove_a_product', array($this, 'wfst_checkout_ajax_remove_a_product') );
      add_action( 'woocommerce_before_calculate_totals', array($this, 'wfst_update_dicounted_price') );
      add_filter( 'woocommerce_thankyou', array($this, 'wfst_after_thank_you_page'), 75);


	}


    /**
     * Loads assets required for this plugin on single product page
     * package @woomatrix
     * since 1.0.0
     */


	public function wfst_load_assets() {

		
   
        wp_enqueue_script( 'wfstfrontend', ''.wfst_PLUGIN_URL.'assets/js/frontend.js',array( 'jquery'), false, true);
   
		wp_enqueue_style( 'wfst-frontend', ''.wfst_PLUGIN_URL.'assets/css/frontend.css' );
		


        $wfst_locals = array(
			    //'enable_tooltip'  => $enable_tooltip
		);

		wp_localize_script( 'wfstfrontend', 'wfstfrontend', $wfst_locals );
	}

    /**
     * Addes frequently purchased products to checkout page with one click
     * package @woomatrix
     * since 1.0.0
     */

    public function wfst_woocommerce_checkout_order_review() {

        global $woocommerce;

        $items = $woocommerce->cart->get_cart();

        $cart_items_array = array();

        foreach($items as $skey => $sval ) {

            $_newproduct_id = $sval['data']->get_id();

            array_push($cart_items_array, $_newproduct_id);

        }

        $itemnum = 0;

        ?>
        <ul class="wfst-promote-ul">
        <?php

        foreach($items as $item => $values) { 

            
            $_product_id = $values['data']->get_id();
            
            $wfst_frequently_sold = get_post_meta($_product_id, '_wfst_frequently_sold', true );

            if (isset($wfst_frequently_sold) && !empty($wfst_frequently_sold)) {

                $wfst_values          = get_post_meta( $_product_id, '_wfst_values', true ); 

                if (isset($wfst_values) && !empty($wfst_values)) {

                    foreach ($wfst_values as $key=>$value) {

                        if (isset($value['checkout_promote']) && ($value['checkout_promote'] == "yes")) {

                            
  
                                if (!in_array($key,$cart_items_array)) { 

                                    $itemnum++;
                                    
                                    $this->valid_product_html($key,$_product_id);
                                    
                                }
                                
                            
                        } 
                    }

                }
            }

        } 

        ?>
        </ul>
        <?php
        
        if ($itemnum == 0) { ?>
            <style>
                .wfst-promote-ul {
                    display:none;
                }
            </style>
        <?php }

      
    }

    
    /**
     * Individual view of frequently purchased product on checkout page
     * package @woomatrix
     * since 1.0.0
     * $parent_product - parent product 
     */

    public function valid_product_html($product_id,$parent_product) { 

            $checkout_product        = wc_get_product($product_id);
            $price                   = $checkout_product->get_price();
            $oldprice                = $checkout_product->get_price();
            $post_thumb              = get_post_meta($product_id, '_thumbnail_id', true );
            $wfst_values             = get_post_meta($parent_product, '_wfst_values', true ); 

            

            if (isset($wfst_values[$product_id])) {
                $wfst_values          = $wfst_values[$product_id];
            }

            if (isset($post_thumb)  && ($post_thumb != "")) { 
                $post_thumbanail = get_the_post_thumbnail_url($product_id);
            } else {
                $post_thumbanail = wfst_placeholder_img_src();
            } 

            if (isset($wfst_values['promote_text'])) {

                $pramote_text  = esc_html__($wfst_values['promote_text']); 
            
            } else {
                $pramote_text  = apply_filters( 'woocommerce_short_description', esc_html__($checkout_product->get_short_description()) );
                $pramote_text  = preg_replace('#\<(.+?)\>#', ' ', $pramote_text);
            }

            

            $price = $wfst_values['promote_price'] ? $wfst_values['promote_price'] : $price;
                

        ?>
        <li class="wfst-uppper-li-div-checkout-page">
            <div class="wfst-first-li-image-div">
                <img alt="<?php echo get_the_title($product_id); ?>" src="<?php echo  $post_thumbanail; ?>" class="">
            </div>
            <div class="wfst-first-li-text-div">
                 <span class="wfst_promote_product_text"><?php echo $pramote_text; ?></span>
            </div>
        </li>
        <li>
            <input type="checkbox" prod-id="<?php echo $product_id; ?>" class="wfst_checkout_li_<?php echo $product_id; ?> wfst_checkout_li" />
                <span class="wfst-checkbox-text-checkout-page">
                    <span class="wfst-add-remove-text-<?php echo $product_id; ?>">
                        <?php echo esc_html__("Add","woomatrix-frequently-sold-together"); ?> 
                    </span>

                    <span class="wfst-strong wfst_checkout_product_title_<?php echo $product_id; ?>">
                        <?php echo get_the_title($product_id); ?> 
                    </span>
                    <span class="wfst-checkout-price-wrapper-<?php echo $product_id; ?>">
                        <?php echo esc_html__("For","woomatrix-frequently-sold-together") ;?> 
                        <span class="wfst-strong">
                            <?php $this->wfst_discounted_price($oldprice,$price);  ?>
                        </span>
                    </span>
                </span>
        </li>
       
        

    <?php }



    /**
     * display frequently add together section
     * package @woomatrix
     * Since 1.0.0
     */


	public function wfst_woocommerce_before_variations_form() {

		global $post;

		$wfst_frequently_sold = get_post_meta($post->ID, '_wfst_frequently_sold', true );
        $wfst_values          = get_post_meta($post->ID, '_wfst_values', true );
		$post_thumb           = get_post_meta($post->ID, '_thumbnail_id', true );
		$_main_product        = wc_get_product($post->ID);
		$main_price           = $_main_product->get_price();
		$first_price          = $_main_product->get_price();
		$product_ids          = ''.$post->ID.'';

		if (isset($wfst_frequently_sold) && (!empty($wfst_frequently_sold))) :
	    

	    ?>  
	        <form class="cart" action="<?php echo wc_get_cart_url(); ?>" method="post" enctype="multipart/form-data">
		

	
		
            <section class="wfst_frequently_sold">

		        <h2><?php echo esc_html__("Frequently sold together","woomatrix-frequently-sold-together") ?></h2>

		        <?php 
                    if (isset($post_thumb)  && ($post_thumb != "")) { 
                        $post_thumbanail = get_the_post_thumbnail_url($post->ID);
                    } else {
                        $post_thumbanail = wfst_placeholder_img_src();
                    } 
                ?>

		        <ul class="">
                    <li class="wfst-main-li-<?php echo $post->ID; ?>">
                    	<span class="">

                    		<div class="">
                    			<img alt="<?php echo get_the_title($post->ID); ?>" src="<?php echo $post_thumbanail; ?>" class="">
                    		</div>
                    	</span>
                    </li>
                    
                    
                    <?php $loopsize= sizeof($wfst_frequently_sold); ?>
                    <?php foreach ($wfst_frequently_sold as $key=>$value) : ?>

                        <?php 
                            $product_ids          .= ','.$value.'';
                            $li_thumb = get_post_meta($value, '_thumbnail_id', true );
                            $_li_product           = wc_get_product($value);
		                    $main_price           += $wfst_values[$value]['promote_price'] ? $wfst_values[$value]['promote_price'] : $_li_product->get_price();
                            
                            if (isset($li_thumb) && ($li_thumb != "")) { 
                               $thumbanail = get_the_post_thumbnail_url($value);
                            } else {
                               $thumbanail = wfst_placeholder_img_src();
                            }



                            

                        ?>
                    
                    <li class="wfst_plus_li wfst-main-li-<?php echo $value; ?>">
                    	    <span class="wfst_plus">+</span>
                    </li>
                    

                    <li class="wfst-main-li-<?php echo $value; ?>">
                    	<span class="">
                    		<a class="" href="<?php echo get_permalink($value); ?>">
                    			<div class="">
                    				<img alt="<?php echo get_the_title($value); ?>" src="<?php echo  $thumbanail; ?>" class="">
                    			</div>
                    		</a>
                    	</span>
                    </li>
                    
                    <?php endforeach; ?>

                    <li class="wfst_total_li">
                    	<div class="wfst-price-wrapper">
                    	    <span class="wfst_total"><?php echo esc_html__("Total price","woomatrix-frequently-sold-together") ?>:</span>
                    	    <span class="wfst_price"><?php $this->wfst_price($main_price); ?></span>
                    	</div>
                    	<?php
                    	$add_to_cart_url = esc_url_raw( add_query_arg( 'add-to-cart', $product_ids, wc_get_cart_url() ) );
                    	?>
                    	
                    	<button type="submit" name="add-to-cart" value="<?php echo $product_ids; ?>" class="single_add_to_cart_button wfst-add-to_cart button alt"><?php echo esc_html__("Add all to cart","woomatrix-frequently-sold-together") ?></button>
                    </li>

                </ul>

                <ul class="wfst-checkbox-ul">
                	
                	<li>
                		
                		<input type="checkbox" class="wfst-li-checkbox" st-type="main" price="<?php echo $first_price; ?>" prod-id="<?php echo $post->ID; ?>" checked="" disabled="disabled" style="display: inline-block;">
                						
                						
                	    <span class="wfts-strong"><?php echo esc_html__("This item","woomatrix-frequently-sold-together") ?>:</span>
                		<span><?php echo get_the_title($post->ID); ?></span> 
                		<span class="">
                			<?php echo wc_price($first_price); ?>
                		</span> 
                		
                    </li>
                	<?php $linum = 0; ?>
                    <?php $licount = count($wfst_frequently_sold); ?>
                	<?php foreach ($wfst_frequently_sold as $key=>$value) : ?>
                	<li>
                		<?php $li_product               = wc_get_product($value); ?>
		                <?php $li_price                 = $li_product->get_price(); ?>
                        <?php $li_old_price             = $li_product->get_price(); ?>
		                <?php   

                        if ($linum == $licount - 1) { 
                            $sttype= "last";
                        } else {
                            $sttype= "normal";
                        }

                        $li_price = $wfst_values[$value]['promote_price'] ? $wfst_values[$value]['promote_price'] : $li_price;


		                ?>

                		<input type="checkbox" class="wfst-li-checkbox" st-type="<?php echo $sttype; ?>" price="<?php echo $li_price; ?>" prod-id="<?php echo $value; ?>" checked="" style="display: inline-block;">
                						        
                				
                		<span><?php echo get_the_title($value); ?></span> 
                		<span class="">
                			<?php $this->wfst_discounted_price($li_old_price,$li_price); ?>
                		</span> 
                	</li>
                	<?php $linum++; endforeach; ?>
                </ul>
                
            </section>


			</form>
	    <?php 

	    endif;
	}

	/**
	 * display price template
	 * package @woomatrix
	 * $price - price to display in formatted way
	 */
    public function wfst_price($price) {
        ?>
        <span class="wfst-total-price-template">
            <span class="wfst-total-currency"><?php echo get_woocommerce_currency_symbol(); ?></span>
            <span class="wfst-total-price"><?php echo $price; ?></span>
        </span>
        <?php
    }


    /**
     * display price template
     * package @woomatrix
     * $price - price to display in formatted way
     */
    public function wfst_discounted_price($oldprice,$newprice) {
        if (($newprice < $oldprice) && ($newprice != $oldprice)) {
        
        ?>
            <del>
                <span class="woocommerce-Price-amount amount">
                    <span class="woocommerce-Price-currencySymbol"><?php echo get_woocommerce_currency_symbol(); ?></span><?php echo $oldprice; ?></span>
            </del> 
            <ins>
                <span class="woocommerce-Price-amount amount">
                <span class="woocommerce-Price-currencySymbol"><?php echo get_woocommerce_currency_symbol(); ?></span><?php echo $newprice; ?></span>
            </ins>
        <?php

        } else { ?>
            
            <ins>
                <span class="woocommerce-Price-amount amount">
                <span class="woocommerce-Price-currencySymbol"><?php echo get_woocommerce_currency_symbol(); ?></span><?php echo $oldprice; ?></span>
            </ins>
        
        <?php
        }
    }


    

    /*
     * Adds multiple products in cart
     * package @woomatrix
     * since 1.0.0
     * params REQUEST['add-to-cart'] contains comma separated id string like 27,48,25
     */
    
    public function woocommerce_maybe_add_multiple_products_to_cart() {
    // Make sure WC is installed, and add-to-cart qauery arg exists, and contains at least one comma.
        if ( ! class_exists( 'WC_Form_Handler' ) || empty( $_REQUEST['add-to-cart'] ) || false === strpos( $_REQUEST['add-to-cart'], ',' ) ) {
           return;
        }

        // Remove WooCommerce's hook, as it's useless (doesn't handle multiple products).
        remove_action( 'wp_loaded', array( 'WC_Form_Handler', 'add_to_cart_action' ), 20 );

        $product_ids = explode( ',', $_REQUEST['add-to-cart'] );
        $count       = count( $product_ids );
        $number      = 0;

    


        foreach ( $product_ids as $product_id ) {
    	
        

            $product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $product_id ) );
            $was_added_to_cart = false;

            $adding_to_cart    = wc_get_product( $product_id );

            if ( ! $adding_to_cart ) {
                continue;
            }

      
            // quantity applies to all products atm
            $quantity          = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( $_REQUEST['quantity'] );
            $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

            if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity ) ) {
                

            }
        }

        add_action( 'woocommerce_before_cart_contents', array($this, 'wfts_add_cart_notice') );
    }


    /**
     * Add notice to cart
     * package @woomatrix
     * since 1.0.0
     */


    public function wfts_add_cart_notice() {

	    $product_ids        = explode( ',', $_REQUEST['add-to-cart'] );
	    $products_text      = '';

	    foreach ($product_ids as $product_id) {
            $quantity          = empty( $_REQUEST['quantity'] ) ? 0 : wc_stock_amount( $_REQUEST['quantity'] );
            $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
            $title             = get_the_title( $product_id );

            if ( $passed_validation  ) {
                $products_text  .=''.$title.',';
            }
	    }

        $and_text      = esc_html__("and","woomatrix-frequently-sold-together"); 

	    $products_text = substr_replace($products_text, "", -1);
	    $products_text = preg_replace('/,([^,]*)$/', ' '.$and_text.' \1', $products_text);

        $extra_text    = esc_html__("has been added to your cart","woomatrix-frequently-sold-together"); 


	    $message = ''.$products_text.' '.$extra_text.' ';

	    echo '<div class="woocommerce-message" role="alert">'.$message.' </div>';
    }



    /**
     * Loads custom script on checkout page to handle add/remove of products based on checkbox behavior
     * Package @WooMatrix
     * Since 1.0.0
     * attr("prod-id") is a product id of concerned checkbox
     */



    public function  wfst_checkout_custom_jquery_script() {
            // Only checkout page
        if( is_checkout() && ! is_wc_endpoint_url() ):

           // Remove "ship_different" custom WC session on load
            if( WC()->session->get('add_a_product') ){
                WC()->session->__unset('add_a_product');
            }
        
            if( WC()->session->get('product_added_key') ){
                WC()->session->__unset('product_added_key');
            }
          // jQuery Ajax code
        ?>
        <script type="text/javascript">
            jQuery( function($){

                if (typeof wc_checkout_params === 'undefined')
                   return false;

                var remove_text = "<?php echo esc_html__("Remove","woomatrix-frequently-sold-together"); ?>";
                var add_text    = "<?php echo esc_html__("Add","woomatrix-frequently-sold-together"); ?>";

                $('form.checkout').on( 'change', '.wfst_checkout_li', function(){
                    var value = $(this).attr("prod-id");
           
                    if ($(this).is(':checked')) {
                        
                        $.ajax({
                            type: 'POST',
                            url: wc_checkout_params.ajax_url,
                            data: {
                                'action': 'add_a_product',
                                'add_a_product': value,
                            },
                            success: function (result) {

                                $('body').trigger('update_checkout');
                   
                                $('.wfst-add-remove-text-'+value+'').text(remove_text);
                        
                                $('.wfst-checkout-price-wrapper-'+value+'').hide();
                            }
                        });

                    } else {

                        $.ajax({
                            type: 'POST',
                            url: wc_checkout_params.ajax_url,
                            data: {
                                'action': 'remove_a_product',
                                'remove_a_product': value,
                            },
                            success: function (result) {

                                $('body').trigger('update_checkout');
                    
                                $('.wfst-add-remove-text-'+value+'').text(add_text);

                                $('.wfst-checkout-price-wrapper-'+value+'').show();
                    
                            }
                        });
                    }
                });
            });
        </script>
        <?php
    endif;
    }

    
    /**
     * Adds product via ajax on checkout page
     * Package @WooMatrix
     * Since 1.0.0
     * $_POST['add_a_product'] is checkbox product id
     */

    public function wfst_checkout_ajax_add_a_product() {
        if ( isset($_POST['add_a_product']) ){

            WC()->session->set('add_a_product', esc_attr($_POST['add_a_product']));
        
            WC()->cart->add_to_cart( intval($_POST['add_a_product']) );
        }

        die();
    }


    /**
     * Removes product via ajax on checkout page
     * Package @WooMatrix
     * Since 1.0.0
     * $_POST['remove_a_product'] is checkbox product id
     */


    public function wfst_checkout_ajax_remove_a_product() {

        if ( isset($_POST['remove_a_product']) ){

            WC()->session->set('remove_a_product', esc_attr($_POST['remove_a_product']));

            $cartId       = WC()->cart->generate_cart_id(intval($_POST['remove_a_product']));

            $cartItemKey  = WC()->cart->find_product_in_cart( $cartId );
        
            WC()->cart->remove_cart_item( $cartItemKey );
        }
    
        die();
    }


    /**
     * replace discounted price with main price on checkout page
     * Package @WooMatrix
     * Since 1.0.0
     * $cart_object contains current cart items
     */



    public function wfst_update_dicounted_price( $cart_object ) {
       
        global $woocommerce;


       
        foreach ( $cart_object->cart_contents as $key => $value ) {
            
            $_product_id          = $value['data']->get_id();
            
            
            $wfst_frequently_sold = get_post_meta($_product_id, '_wfst_frequently_sold', true );

            if (isset($wfst_frequently_sold) && !empty($wfst_frequently_sold)) {

                $wfst_values          = get_post_meta( $_product_id, '_wfst_values', true ); 

                if (isset($wfst_values) && !empty($wfst_values)) {

                    foreach ($wfst_values as $fkey=>$fvalue) {

                        if (isset($fvalue['checkout_promote']) && ($fvalue['checkout_promote'] == "yes")) {

                            
                                
                            foreach ( $cart_object->cart_contents as $nkey => $nvalue ) {

                                $n_product_id     = $nvalue['data']->get_id();
                                $n_product        = wc_get_product($n_product_id);
                                $n_product_price  = $n_product->get_price();

                                $new_price    = $fvalue['promote_price'] ? $fvalue['promote_price'] : $n_product_price;

                                if ($n_product_id == $fkey) {

                                    $nvalue['data']->set_price($new_price);
                                }
                            }
                        }
                    }
                }
            }
        }
    }






    /**
     * Displays frequently baught together section after thank you page
     * Also optionaly displays coupon code
     * Package @WooMatrix
     * Since 1.0.0
     * $orderid - id of submitted order
     */


    public function wfst_after_thank_you_page($orderid) {

        $order = wc_get_order( $orderid );
        $items = $order->get_items();

        foreach ( $items as $item ) {
            $product_name         = $item->get_name();
            $product_id           = $item->get_product_id();
            
            $wfst_frequently_sold = get_post_meta($product_id, '_wfst_frequently_sold', true );
            $promote_thankyou     = get_post_meta($product_id, '_promote_thankyou', true );
            

            if (isset($promote_thankyou) && ($promote_thankyou == "yes") && isset($wfst_frequently_sold) && !empty($wfst_frequently_sold)) {

                $wfst_values          = get_post_meta( $_product_id, '_wfst_values', true ); 

                $this->wfst_display_freuqently_purchased_thankyou($wfst_frequently_sold,$wfst_values,$product_name);
            }

            $offer_coupon                = get_post_meta($product_id, '_offer_coupon', true );
            $wfst_pramote_coupon         = get_post_meta($product_id, '_wfst_pramote_coupon', true );

            if (isset($offer_coupon) && ($offer_coupon == "yes") && isset($wfst_pramote_coupon) && ($wfst_pramote_coupon != '')) {

                $this->wfst_display_coupon_thankyou($wfst_pramote_coupon);

            }


        }
    }


    /**
     * Displays frequently baught together section if it is enabled and products are set
     * display chosen product in loop
     * Package @WooMatrix
     * Since 1.0.0
     * $wfst_frequently_sold - array id of frquently purchased
     * $wfst_values - meta value of each item
     */


    public function wfst_display_freuqently_purchased_thankyou($wfst_frequently_sold,$wfst_values,$product_name) {
        

        
        if ( $wfst_frequently_sold ) : ?>

        <div class="cross-sells">

        <h3>
            <?php echo esc_html__( 'Customers who baught', 'woomatrix-frequently-sold-together' ); ?> 
            <span class="wfst-strong"><?php echo $product_name; ?></span> <?php echo esc_html__( 'also baught', 'woomatrix-frequently-sold-together' ); ?>
            
        </h3>

        <?php woocommerce_product_loop_start(); ?>


            <?php foreach ( $wfst_frequently_sold as $key=>$value ) : ?>

                <?php
                    $post_object = get_post( $value );

                    setup_postdata( $GLOBALS['post'] =& $post_object );

                    wc_get_template_part( 'content', 'product' ); ?>

            <?php endforeach; ?>

        <?php woocommerce_product_loop_end(); ?>

         </div>

       <?php endif;
    }


    /**
     * Displays promote coupon view on thank you page
     * Package @WooMatrix
     * Since 1.0.0
     * $coupon_id - id of chosen copon
     */


    public function wfst_display_coupon_thankyou($coupon_id) { 

        ?>
      
        <div class="wfst-coupon">
            <div class="wfst-cpoupon-container wfst-coupon-container-head">
                <?php do_action('wfst_before_coupon_heading'); ?>
                <span class="wfst-coupon-heading-text"><?php echo esc_html__( 'Special coupon for you', 'woomatrix-frequently-sold-together' ); ?></span>
                    
                <?php do_action('wfst_before_coupon_description'); ?>
                <p class="wfst_coupon_description"><?php echo get_the_excerpt($coupon_id); ?></p>
            </div>
            <div class="wfst-cpoupon-container">
                <p><?php echo esc_html__( 'Use Coupon Code', 'woomatrix-frequently-sold-together' ); ?>: <span class="wfst-promo"><?php echo get_the_title($coupon_id); ?></span></p>
            </div>
        </div>
        <?php do_action('wfst_after_coupon_content'); ?>
    <?php 
    }

}






new wfst_add_frontend_class();

?>