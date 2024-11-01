<div id="wfst_bulk_multi_images_tab" class="panel woocommerce_options_panel hidden">
    
        <?php 

            $wfst_frequently_sold = get_post_meta( $post->ID, '_wfst_frequently_sold', true ); 
            $wfst_pramote_coupon  = get_post_meta( $post->ID, '_wfst_pramote_coupon', true ); 
            $promote_thankyou     = get_post_meta( $post->ID, '_promote_thankyou', true ); 
            $offer_coupon         = get_post_meta( $post->ID, '_offer_coupon', true ); 
            $couponargs           = array(
               'posts_per_page'           => -1,
               'post_type'                => 'shop_coupon',
            );  
            $coupons              = get_posts( $couponargs ); 


        ?>
        
            <div id="message" class="inline notice woocommerce-message">
                <div class="wfst-variations-defaults widefat">
                    <table>    
                        <tr class="checkout_field_products_tr" style="">
                        <td width="30%">
                            
                            <?php echo esc_html__('Chose frequently sold products','woomatrix-frequently-sold-together'); ?>
                        </td>
                        <td width="70%">
                            
                            
                            <select class="wfst_multiselect_variations" id="wfst_variations_multiselect" data-placeholder="<?php echo esc_html__('Search and Select','woomatrix-frequently-sold-together'); ?>" name="wfst_frequently_sold[]" multiple  style="width:600px">
                                <?php if (isset($wfst_frequently_sold) && (!empty($wfst_frequently_sold)) ) : ?>
                                <?php foreach ($wfst_frequently_sold as $key=>$value) : ?>
                                    <?php $_main_product           = wc_get_product($value); ?>
                                    <?php $product_price           = $_main_product->get_price();  ?>
                                    <?php $currency                = get_option('woocommerce_currency'); ?>
                                    <option value="<?php echo esc_html__($value); ?>" selected>
                                        #<?php echo esc_html__($value); ?>-<?php echo esc_html__(get_the_title( $value )); ?>- <?php echo $currency; ?>&emsp;<?php echo $product_price; ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <button class="button button-primary wfts_save_metabox_product">
                                <?php echo esc_html__('Apply Changes','woomatrix-frequently-sold-together'); ?>
                            </button>
                            
                        </td>
                        </tr>
                        <tr>
                            <td width="30%"><?php echo esc_html__('Promote on order confirmation','woomatrix-frequently-sold-together'); ?></td>
                            <td width="70%">
                                <input type="checkbox" prod-id="<?php echo esc_html__($product_id); ?>" class="wfst-meta-promote-thankyou-checkbox" value="yes" name="promote_thankyou" <?php if (isset($promote_thankyou) && ($promote_thankyou == "yes")) { echo 'checked';} ?>>
                            </td>
                        </tr>


                        <tr>
                            <td width="30%"><?php echo esc_html__('Offer Coupon after purchase','woomatrix-frequently-sold-together'); ?></td>
                            <td width="70%">
                                <input type="checkbox" prod-id="<?php echo esc_html__($product_id); ?>" class="wfst-meta-promote-coupon-checkbox" value="yes" name="offer_coupon" <?php if (isset($offer_coupon) && ($offer_coupon == "yes")) { echo 'checked';} ?>>
                            </td>
                        </tr>
                        <tr class="wfst-select-coupon-tr" style="<?php if (isset($offer_coupon) && ($offer_coupon == "yes")) { echo 'display:table-row;';} else { echo 'display:none;'; } ?>">
                            <td width="30%"><?php echo esc_html__('Chose coupon','woomatrix-frequently-sold-together'); ?></td>
                            <td width="70%">
                                 <select class="wfst_select_coupon" data-placeholder="<?php echo esc_html__('Search and Select','woomatrix-frequently-sold-together'); ?>" name="wfst_pramote_coupon"  style="width:600px">
                                <?php foreach ($coupons as $coupon) { ?>
                                   <option value="<?php echo $coupon->ID; ?>" <?php if ((isset($wfst_pramote_coupon)) && ($wfst_pramote_coupon == $coupon->ID )) { echo "selected"; } ?>>#<?php echo $coupon->ID; ?>- <?php echo $coupon->post_title; ?></option>
                                <?php } ?>
                                </select>
                            </td>
                        </tr>
                   </table>
                </div> 
            </div> 

            <div class="wfts-accordion">
                <?php if (isset($wfst_frequently_sold) && (!empty($wfst_frequently_sold)) ) : ?>
                <?php foreach ($wfst_frequently_sold as $key=>$value) : ?>
                    <?php $this->display_accordion_row($value); ?>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
</div>