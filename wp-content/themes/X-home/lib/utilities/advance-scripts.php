<?php
/**
 * Script nang cao
 * 21/8/19: bo sung cap nhat footer PC va footer Mobile rieng -> phuc vu cai script goal
 */

if( defined('CAIA_SETTINGS_FIELD') )
{
	add_action( 'caia_settings_metaboxes', 'caia_add_tag_manager_boxes' );
	add_action( 'genesis_before', 'caia_add_tag_manager_script' );
	add_action( 'wp_footer', 'caia_add_footer_script_advanced' );
	add_action( 'after_setup_theme', 'caia_update_ads_txt' );
	add_action( 'after_setup_theme', 'caia_update_robots_txt' );

	/* CAIA TAG MANAGER */
	function caia_add_tag_manager_boxes( $pagehook )
	{
		add_meta_box( 'caia-theme-tag-manager', __( 'Cài đặt script nâng cao', 'caia' ), 'caia_google_tag_manager', $pagehook, 'main' );
	}
	function caia_google_tag_manager()
	{
	?>
		
		<table class="form-table">
		<tbody>

			<tr valign="top">
				<th scope="row"><label>Ads.txt</label></th>
				<td>
					<p><textarea class="widefat" rows="5" name="<?php echo CAIA_SETTINGS_FIELD; ?>[ads_txt]"><?php caia_option( 'ads_txt' ); ?></textarea></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label>Robots.txt</label></th>
				<td>
					<p><textarea class="widefat" rows="5" name="<?php echo CAIA_SETTINGS_FIELD; ?>[robots_txt]"><?php caia_option( 'robots_txt' ); ?></textarea></p>
					<p><span class="description">User-agent: * <br>Disallow: /wp-admin/ <br>Disallow: *.php <br>Sitemap: <?php echo home_url(); ?>/sitemap_index.xml</span></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label>Scripts ngay sau thẻ body</label></th>
				<td>
					<p><textarea class="widefat" rows="5" name="<?php echo CAIA_SETTINGS_FIELD; ?>[google_tag_manager]"><?php caia_option( 'google_tag_manager' ); ?></textarea></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label>Scripts chân trang bản PC</label></th>
				<td>
					<p><textarea class="widefat" rows="5" name="<?php echo CAIA_SETTINGS_FIELD; ?>[script_footer_pc]"><?php caia_option( 'script_footer_pc' ); ?></textarea></p>
					<p><span class="description">Giữ nguyên thẻ mở/đóng &lt;script&gt;, chỉ xuất hiện ở bản PC</span></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><label>Scripts chân trang bản Mobile</label></th>
				<td>
					<p><textarea class="widefat" rows="5" name="<?php echo CAIA_SETTINGS_FIELD; ?>[script_footer_mobile]"><?php caia_option( 'script_footer_mobile' ); ?></textarea></p>
					<p><span class="description">Giữ nguyên thẻ mở/đóng &lt;script&gt;, chỉ xuất hiện ở bản Mobile</span></p>
				</td>
			</tr>

		</tbody>
		</table>

	<?php
	}

	function caia_update_ads_txt() {
		$ads_txt_content = caia_get_option('ads_txt');
		if (empty($ads_txt_content)) {
			$ads_txt_file = ABSPATH . 'ads.txt';
			if (file_exists($ads_txt_file)) {
				unlink($ads_txt_file);
			}
			return;
		}else{
			$ads_txt_file = ABSPATH . 'ads.txt';
			file_put_contents($ads_txt_file, $ads_txt_content);
		}
	}

	function caia_update_robots_txt() {
		$robots_txt_content = caia_get_option('robots_txt');
		if (empty($robots_txt_content)) {
			$robots_txt_file = ABSPATH . 'robots.txt';
			if (file_exists($robots_txt_file)) {
				unlink($robots_txt_file);
			}
			return;
		}else{
			$robots_txt_file = ABSPATH . 'robots.txt';
			file_put_contents($robots_txt_file, $robots_txt_content);
		}
	}

	function caia_add_tag_manager_script()
	{
		$tag = caia_get_option('google_tag_manager');
		echo $tag;
	}

	function caia_add_footer_script_advanced(){
		global $caia_detected_device;
		if (isset($caia_detected_device) && $caia_detected_device === 'Mobile'){
			$tag = caia_get_option('script_footer_mobile');
			echo $tag;
		}else{
			$tag = caia_get_option('script_footer_pc');
			echo $tag;
		}
	}

}else if (defined('GTID_SETTINGS_FIELD')) {
	add_action('admin_menu', 'gtid_theme_custom_settings_init');
	add_action( 'genesis_before', 'gtid_add_tag_manager_script' );

	/* GTID TAG MANAGER */
	function gtid_theme_custom_settings_init() {
		global $_gtid_theme_settings_pagehook;

		add_action('load-'.$_gtid_theme_settings_pagehook, 'gtid_theme_custom_settings_boxes');
	}
	function gtid_theme_custom_settings_boxes()
	{
		global $_gtid_theme_settings_pagehook;
		add_meta_box('gtid-theme-settings-tag-manager', __('Script ngay sau thẻ body', 'genesis'), 'gtid_google_tag_manager_box', $_gtid_theme_settings_pagehook, 'column2');
	}

	function gtid_google_tag_manager_box()
	{
	?>
		<label>
			<textarea name="<?php echo GTID_SETTINGS_FIELD; ?>[google_tag_manager]" cols="39" rows="5" style="width: 99%;"><?php gtid_option('google_tag_manager'); ?></textarea>
		</label>
	<?php
	}

	function gtid_add_tag_manager_script()
	{	
		$tag = gtid_get_option('google_tag_manager');
		echo $tag;
	}
}



