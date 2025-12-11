<?php

define( 'CAIA_DISABLE_COPY_VERSION', '2.0' );

// 27/08/21: fix lỗi xung đột backtotop
// 02/11/21: cho phép copy với user của Caia đang login

if (is_admin()){
	// Thêm setting bật tính năng lên đầu trang
	add_action( 'caia_settings_metaboxes', 'caiatn_add_theme_settings_disable_copy' );
	function caiatn_add_theme_settings_disable_copy( $pagehook ){
		add_meta_box( 'caia-settings-disable-copy', __( 'Bật tắt cho phép copy', 'caia' ), 'caia_add_option_disable_copy', $pagehook, 'main' );

	}


	function caia_add_option_disable_copy(){		
		?>
		<table class="form-table">
		<tbody>				
			<tr valign="top">
				<th scope="row">Cho phép copy</th>
				<td>
					<input id='allow_copy' type="checkbox" name="<?php echo CAIA_SETTINGS_FIELD; ?>[allow_copy]" value="1" <?php checked( 1, caia_get_option( 'allow_copy' ) ); ?> /><label for="allow_copy"><i>click để cho phép copy</i></label>
				</td>
			</tr>					
		</tbody>
		</table>
		<?php
	}
} else {
	// bật tính năng chống copy
	// $caia_remote_role
	
	if ( ! caia_get_option( 'allow_copy' ) ) {
		
		

		if ( is_user_logged_in() ) {
		    $copiable_remote_roles = array('admin', 'seo_lead', 'seo', 'seo_senior');
		    $copiable_roles = array('administrator', 'caia_sub_admin');
		    
		    $cur_user_role = caia_get_user_role();
		    $caia_remote_role = get_user_meta(get_current_user_id(), 'caia_remote_role', true); // hoặc gán giá trị mặc định: ''

		    if ( !in_array($caia_remote_role, $copiable_remote_roles) && !in_array($cur_user_role, $copiable_roles) ) {
		        add_action('wp_footer', 'caia_disable_copy');	
		    }
		} else {
		    add_action('wp_footer', 'caia_disable_copy');
		}

		
		function caia_disable_copy(){
			?>
<script>
	jQuery(document).ready(function () {
	    jQuery('body').bind('cut copy paste', function (e) { e.preventDefault(); });	    
	    jQuery("body").on("contextmenu",function(e){ return false; });
	});
</script>
			<?php	
		}
		
				
	}
}
	

