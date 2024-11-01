var $vas = jQuery.noConflict();
(function( $vas ) {
    'use strict';

    $vas(function() {
        
       $vas('.wfst-li-checkbox').on("change",function() {

       	    var price       = $vas(this).attr("price");
       	    var price       = parseInt(price);
       	   	var pid         = $vas(this).attr("prod-id");
       	   	var sttype      = $vas(this).attr("st-type");
       	   	var totalprice  = $vas('.wfst-total-price').text();
       	   	var totalprice  = parseInt(totalprice);

       	    if ($vas(this).is(':checked')) {
                
       	   	    var newprice = totalprice + price;

       	   	    $vas('.wfst-main-li-'+pid+'').show();
       	   	    $vas('.wfst-total-price').text(newprice);

       	   	        $vas('.wfst_total_li').show();

       	   	    if (sttype == "main") {
                	$vas('.wfst-main-li-'+pid+'').next('.wfst_plus_li').show();
                }
                

       	    } else {

       	    	
                var newprice = totalprice - price;

                $vas('.wfst-main-li-'+pid+'').hide();
                $vas('.wfst-total-price').text(newprice);

                if (sttype == "main") {
                    
                    $vas('.wfst-main-li-'+pid+'').next('.wfst_plus_li').hide();
                }
                
       	    }
            
            var chosen_pid = "";

       	    $vas('.wfst-li-checkbox').each(function() {
       	    	var cpid         = $vas(this).attr("prod-id");

       	    	if ($vas(this).is(':checked')) {
                   chosen_pid += ''+cpid+',';
                }
       	    });

       	    chosen_pid = chosen_pid.replace(/,\s*$/, "");

       	    $vas('.wfst-add-to_cart').val(chosen_pid);

       	    var checkedcount = $vas('.wfst-li-checkbox').filter(':checked').length;

            if (checkedcount == 0) {
            	$vas('.wfst_total_li').hide();
            }

            



       	    
           
       });

    });


})( jQuery );


