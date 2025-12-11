<?php

// phục vụ ajax cho những CAIA Block khi thay đổi Taxonomy
add_action( 'admin_footer-appearance_page_caia-design', 'caia_ajax_tax_change_javascript' );

add_action( 'admin_footer-widgets.php', 'caia_ajax_tax_change_javascript' );



function caia_ajax_tax_change_javascript() {

	// global $hook_suffix;
	// echo 'Hook: ' . $hook_suffix;
	?>
	<script type="text/javascript" >
	jQuery.noConflict();
    jQuery(document).ready(function($) {
        $(document).on('change', ".caia-tax-ajax-select", function(){
            
            var myclass = $(this).attr('class');            
            var myarr = myclass.split(' ');
            for(key in myarr){
            	if(myarr[key].indexOf('_ajax_', 0) == 0){
            		var papa_id = myarr[key];
            		break;
            	}
            }

            var mytax = $(this).find('option:selected').val(); //text()
           
            var data = {
                action : 'caia_tax_change_action',
                tax_name: mytax                             
            };
            

            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: data,
                dataType: 'html',
                beforeSend : function(){
                    // currentItem.addClass("gf-loading");
                    $('.caia-loading-ajax_' + papa_id).html( ' loading...');
                },
                success: function(data) {
                  	$('.caia-term-ajax_' + papa_id).html(data);  
                  	$('.caia-loading-ajax_' + papa_id).html( '&nbsp;');
                }
            });

            // de ko scroll browser len top
            return false; 
            
        });
    });
	</script>
	<?php
}


add_action( 'wp_ajax_caia_tax_change_action', 'caia_tax_change_action' );

function caia_tax_change_action() {

	$tax = $_POST['tax_name'];

	$terms = get_terms( $tax, array('orderby' => 'id') );
    	
    $res = '<option value="0">' . __( 'All Items', 'caia' ) . '</option>"';
	foreach ($terms as $value) { 	
		$res .= '<option value="' . $value->term_id . '">' . $value->name . '</option>"';		
	}					

	echo $res;
    // echo $tax . ' is loaded!';

	die(); // this is required to return a proper result
}