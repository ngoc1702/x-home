<?php

define( 'CAIA_BACKTOTOP_VERSION', '3.0' );

// 27/08/21: fix lỗi xung đột disable-copy
// 04/11/2022: Thêm chức năng backtoptop hiển thị toàn bộ hoặc bài viết

if (is_admin()){
	// Thêm setting bật tính năng lên đầu trang
	add_action( 'caia_settings_metaboxes', 'caiatn_add_theme_settings_backtotop' );
	function caiatn_add_theme_settings_backtotop( $pagehook ){
		add_meta_box( 'caia-settings-backtotop', __( 'Thiết lập lên đầu trang', 'caia' ), 'caia_add_button_backtotop', $pagehook, 'main' );
	}

	function caia_add_button_backtotop(){
		?>
		<table class="form-table">
		<tbody>				
			<tr valign="top">
				<th scope="row">Tắt chức năng</th>
				<td>
					<input type="checkbox" name="<?php echo CAIA_SETTINGS_FIELD; ?>[backtotop_disable]" value="1" <?php checked( 1, caia_get_option( 'backtotop_disable' ) ); ?> /><label for="backtotop">Tắt nút lên đầu trang</label>
				</td>
			</tr>	
			<tr valign="top">
				<th scope="row">Tuỳ chọn hiển thị</th>
				<td>
					<?php
					$show_backtotop = caia_get_option( 'show_backtotop' );
					if( $show_backtotop === 'all' ){
						?>
						<input type="radio" name="<?php echo CAIA_SETTINGS_FIELD; ?>[show_backtotop]" value="all" <?php checked( 'all', caia_get_option( 'show_backtotop' ) ); ?> checked="checked"/><label for="backtotop">Toàn bộ</label>&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="radio" name="<?php echo CAIA_SETTINGS_FIELD; ?>[show_backtotop]" value="post" <?php checked( 'post', caia_get_option( 'show_backtotop' ) ); ?>/><label for="backtotop">Bài viết/Trang</label>&nbsp;&nbsp;&nbsp;&nbsp;
						<?php
					}else{
						?>
						<input type="radio" name="<?php echo CAIA_SETTINGS_FIELD; ?>[show_backtotop]" value="all" <?php checked( 'all', caia_get_option( 'show_backtotop' ) ); ?> /><label for="backtotop">Toàn bộ</label>&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="radio" name="<?php echo CAIA_SETTINGS_FIELD; ?>[show_backtotop]" value="post" <?php checked( 'post', caia_get_option( 'show_backtotop' ) ); ?> checked="checked"/><label for="backtotop">Bài viết/Trang</label>&nbsp;&nbsp;&nbsp;&nbsp;
						<?php
					}
					?>
				</td>
			</tr>					
			<tr valign="top">
				<th scope="row">Đường dẫn ảnh riêng</th>
				<td>				
					<input type="text" name="<?php echo CAIA_SETTINGS_FIELD; ?>[backtotop_image]" value="<?php caia_option( 'backtotop_image' ); ?>" placeholder="http://demo.vn/url/to/custom_image.png" size="80"/>
				</td>
			</tr>
		</tbody>
		</table>
		<?php
	}
} else {
	// Tạo nút lên đầu trang
	if ( ! caia_get_option( 'backtotop_disable' ) ) {

		add_action ('wp_head', 'caia_before_show_back_to_top');
		function caia_before_show_back_to_top(){

			/*if ( genesis_html5() ){			
				add_action('genesis_entry_footer', 'caia_show_back_to_top');	
			}else{			
				add_action('genesis_after_post_content', 'caia_show_back_to_top');		
			}*/
			add_action('genesis_after_footer', 'caia_show_back_to_top');	
		}
		
		
		function caia_show_back_to_top(){

			$show_backtotop = caia_get_option( 'show_backtotop' );
			if( $show_backtotop === 'all' ){
				add_action('wp_footer','caia_add_js_back_to_top', 99);
				add_action('wp_footer','caia_add_content_backtotop', 10);				
			}else{
				global $toc;			
				if (isset($toc) && isset($toc->toc_showed) && $toc->toc_showed){
					add_action('wp_footer','caia_add_js_back_to_top', 99);
					add_action('wp_footer','caia_add_content_backtotop', 10);
				}				
			}

		}
		
		function caia_add_js_back_to_top(){
			global $caia_detected_device;
			$offset = isset($caia_detected_device) && $caia_detected_device === 'Mobile' ? 60 : 30;
			// if (isset($caia_detected_device) && $caia_detected_device === 'Mobile' ){
			// 	$offset = caia_get_option( 'mobile_toc_offset' ) ;
			// 	if (!$offset) $offset = 60;				
			// }else{
			// 	$offset = caia_get_option( 'pc_toc_offset' ) ;
			// 	if (!$offset) $offset = 30;				
			// }


			?><script>jQuery(document).ready(function($){$("#backtotop").click(function(){var offset_top = jQuery("h1").offset(); var top_it = (typeof offset_top === "undefined") ? 0 : offset_top.top; top_it = top_it > 500 ? 0 : top_it; top_it = top_it > <?php echo $offset; ?> ? top_it - <?php echo $offset; ?> : top_it; $('body,html').animate({scrollTop: top_it}, 'slow');}); var last_st = 0; $(window).scroll(function () { var st = $(this).scrollTop(); if (st < last_st){ if (st > 500){ $('#backtotop').fadeIn();}else{$('#backtotop').fadeOut(); } }else{ $('#backtotop').hide(); } last_st = st; });});</script>
			<?php
		}
		
		function caia_add_content_backtotop(){
			if ( !empty( caia_get_option( 'backtotop_image' ) ) ){
				echo '<img src="'. caia_get_option( 'backtotop_image' ) . '" title="Lên đầu trang" id="backtotop" />';
			}else{
				echo '<span id="backtotop" title="Lên đầu trang">↑</span>'; 
			}	
			echo '<style type="text/css">#backtotop{position:fixed;bottom:15%;left:2%;border-radius:100%;background-color:rgba(85,85,85,0.5);width:50px;height:50px;text-align:center;font-size:24px;color:#fff;font-weight:bold;line-height:50px;display:none;}</style>';		
		}
	}
}
	

