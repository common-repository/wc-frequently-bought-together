var $ext = jQuery.noConflict();
(function( $ext ) {
    'use strict';

    $ext(function() {
        
        $ext(".wfst_multiselect_variations").select2({
  		   ajax: {
    			url: ajaxurl, // AJAX URL is predefined in WordPress admin
    			dataType: 'json',
    			delay: 250, // delay in ms while typing when to perform a AJAX search
    			data: function (params) {
      				return {
        				q: params.term, // search query
        				action: 'wfstgetajaxproductslist'
      				};
    			},
    			processResults: function( data ) {
				var options = [];
				if ( data ) {
 
					// data is the array of arrays, and each of them contains ID and the Label of the option
					$ext.each( data, function( index, text ) { // do not forget that "index" is just auto incremented value
						options.push( { id: text[0], text: text[1]  } );
					});
 
				}
				return {
					results: options
				};
			},
			cache: true
		   },
		     minimumInputLength: 3 ,
			 width: "480px"// the minimum of symbols to input before perform a search
	    });

        
      

        $ext('.wfst_select_coupon').select2({
              width: "480px"// the minimum of symbols to input before perform a search
        });


        $ext('.wfst-meta-promote-coupon-checkbox').on("change",function() {
            
            if ($ext(this).is(':checked')) {

                $ext('.wfst-select-coupon-tr').show();
                
                
            } else {
                $ext('.wfst-select-coupon-tr').hide();
                
            }

        });
        


	    $ext(".wfts-accordion").accordion();

        $ext(".wfst-meta-checkbox").on("change",function() {

            var pid = $ext(this).attr("prod-id");

            if ($ext(this).is(':checked')) {

                $ext('.wfst-meta-tr-'+pid+'').show();
                
                
            } else {
                $ext('.wfst-meta-tr-'+pid+'').hide();
                
            }

        });

	    $ext(".wfts_save_metabox_product").on("click",function(event){

	    	event.preventDefault();

	    	var multiselectvalues = $ext('.wfst_multiselect_variations').val();


	    	$ext( ".wfst-tab" ).each(function() {
                var rowno = $ext(this).attr("rownum");
                
                if ($ext.inArray(rowno, multiselectvalues) === -1) {
                	
                	$ext('.wfst-'+rowno+'').remove();
                	$ext('.wfst-div-'+rowno+'').remove();
                }
            });
            
            var newmultiarray     = [];

            $ext.each(multiselectvalues, function( index, value ) {
            	var rowleangth = $ext('.wfst-'+value+'').length;
                
                if (rowleangth == 0) {
                	newmultiarray.push(value);
                }
            });


            

            if (newmultiarray.length > 0) {

            	$ext('.product_attributes').ajaxStart($ext.blockUI({message: null,overlayCSS: {
				    background: '#fff',
				    opacity: 0.6
			    }})).ajaxStop($ext.unblockUI);

                $ext.ajax({
                    data: { 
                        action: "wfst_ajax_new_values", 
			            newproducts : newmultiarray    
                    },
                    type: 'POST',
                    url: ajaxurl,
                    dataType: 'html',
                    success: function( response ) { 
			            
			            $ext('.wfts-accordion').append(response);
			            $ext(".wfts-accordion").accordion("refresh");

                        $ext(".wfst-meta-checkbox").on("change",function() {

                            var pid = $ext(this).attr("prod-id");

                            if ($ext(this).is(':checked')) {

                                $ext('.wfst-meta-tr-'+pid+'').show();
                
                            } else {
                    
                                $ext('.wfst-meta-tr-'+pid+'').hide();
                
                            }

                        });
				    }
                });

                
            }

        	
	    });
           
        
    });
    

 
})( jQuery );