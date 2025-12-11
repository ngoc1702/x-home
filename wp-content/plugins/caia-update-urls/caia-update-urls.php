<?php
/*
Plugin Name: Caia Update URLs
Description: This plugins update old url to new url, useful when change domain for website, upgrade by Caia from Velvet Update Url.
Author: Velvet, Caia
Version: 5.4
License: GPLv2 or later
*/



if ( !function_exists( 'add_action' ) ) {
	echo '<h3>Oops! This page cannot be accessed directly.</h3>';
	exit;
}

function CaiaUU_add_options_page(){
	add_options_page("Caia Update URLs", "Update URLs", "manage_options", basename(__FILE__), "CaiaUU_options_page");
}
function CaiaUU_load_textdomain(){
	load_plugin_textdomain( 'update-urls', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}


function CVB_update_widget($oldurl, $newurl){
	// support widget text and caia ads widget

	$count = 0;
	// widget_text
	$option_name = 'widget_text';
	$widget_arr = get_option( $option_name , '' );
	$before = $count;
	foreach ($widget_arr as $key => $widget) {		
		$cnt = substr_count($widget['text'], $oldurl);
		if ($cnt){
			$widget_arr[$key]['text'] = str_replace($oldurl, $newurl, $widget['text']);
			$count += $cnt;	
		}
		
	}

	if($count > $before){
		update_option( $option_name, $widget_arr );
	}
	
	

	// widget_ads_widget
	$option_name = 'widget_ads_widget';
	$widget_arr = get_option( $option_name , '' );
	$before = $count;
	foreach ($widget_arr as $key => $widget) {			
		$cnt = substr_count($widget['text'], $oldurl);
		if ($cnt){
			$widget_arr[$key]['text'] = str_replace($oldurl, $newurl, $widget['text']);
			$count += $cnt;	
		}
	}
	if($count > $before){
		update_option( $option_name, $widget_arr );
	}
	

	// bat dau cam replace voi noi dung ko phai url
	if  (( strpos($oldurl, 'http://') !== 0 && strpos($oldurl, 'https://') !== 0) ||
		( strpos($newurl, 'http://') !== 0 && strpos($newurl, 'https://') !== 0)){		
		return 0;
	}	

	// widget_image-upload-widget
	$option_name = 'widget_image-upload-widget';
	$widget_arr = get_option( $option_name , '' );
	$cnt = caia_replace_inside_var($widget_arr, $oldurl, $newurl);
	if ($cnt){
		update_option( $option_name, $widget_arr );
		$count += $cnt;
	}	
	
	
	return $count;
}

function CVB_update_caia_settings($oldurl, $newurl){
	if  (( strpos($oldurl, 'http://') !== 0 && strpos($oldurl, 'https://') !== 0) ||
		( strpos($newurl, 'http://') !== 0 && strpos($newurl, 'https://') !== 0)){		
		return 0;
	}	

	$count = 0;
	// caia-settings
	$option_name = 'caia-settings';
	$val = get_option( $option_name , '' );
	$cnt = caia_replace_inside_var($val, $oldurl, $newurl);
	if ($cnt){
		update_option($option_name, $val);
		$count += $cnt;
	}
	

	// caia-layout-settings
	$option_name = 'caia-layout-settings';
	$val = get_option( $option_name , '' );
	$cnt = caia_replace_inside_var($val, $oldurl, $newurl);
	if ($cnt){
		update_option($option_name, $val);
		$count += $cnt;
	}

	// caia-design-settings
	$option_name = 'caia-design-settings';
	$val = get_option( $option_name , '' );
	$cnt = caia_replace_inside_var($val, $oldurl, $newurl);
	if ($cnt){
		update_option($option_name, $val);
		$count += $cnt;
	}


	return $count;		
}

function CVB_update_caia_context($oldurl, $newurl){


	if  (( strpos($oldurl, 'http://') !== 0 && strpos($oldurl, 'https://') !== 0) ||
		( strpos($newurl, 'http://') !== 0 && strpos($newurl, 'https://') !== 0)){		
		return 0;
	}	

	$my_settings = get_option('caia_context_settings');
	
	if ( empty($my_settings) ) return 0;

	$count = caia_replace_inside_var($my_settings, $oldurl, $newurl, array('code'));

	if ($count){
		update_option('caia_context_settings', $my_settings);
	}
	
	return $count;
}

function caia_replace_meta_array($oldurl, $newurl){
	global $wpdb;
	$sql = "SELECT * FROM {$wpdb->postmeta} WHERE meta_key not like '_wp_%' and meta_key <> '_menu_item_classes' and meta_value LIKE 'a:%'";
	$rows = $wpdb->get_results($sql, ARRAY_A);
	$count = 0;	
	foreach ($rows as $row) {
		$post_id = $row['post_id'];
		$meta_key = $row['meta_key'];		
		$meta_value = unserialize($row['meta_value']);
		// print_r($meta_value);
		if (is_array($meta_value)){
			$count += caia_replace_inside_var($meta_value, $oldurl, $newurl);		
			if ($count){				
				$res = update_post_meta($post_id, $meta_key, $meta_value);
			}
		}
	}
	return $count;
}

function caia_replace_meta_term_array($oldurl, $newurl){
	global $wpdb;
	$sql = "SELECT * FROM {$wpdb->termmeta} WHERE meta_key not like '_wp_%' and meta_key <> '_menu_item_classes' and meta_value LIKE 'a:%'";
	$rows = $wpdb->get_results($sql, ARRAY_A);
	$count = 0;	
	foreach ($rows as $row) {
		$post_id = $row['post_id'];
		$meta_key = $row['meta_key'];		
		$meta_value = unserialize($row['meta_value']);
		// print_r($meta_value);
		if (is_array($meta_value)){
			$count += caia_replace_inside_var($meta_value, $oldurl, $newurl);		
			if ($count){				
				$res = update_term_meta($post_id, $meta_key, $meta_value);
			}
		}
	}
	return $count;
}

function caia_replace_inside_var( &$var, $oldurl, $newurl, $match_key_arr = array(), $my_key = '')
{
	if (is_string($var)){
		if ($my_key = '' || empty($match_key_arr) || in_array($my_key, $match_key_arr)){
			$cnt = substr_count($var, $oldurl);
			$var = str_replace($oldurl, $newurl, $var);
			return $cnt;
		}else{
			return 0;
		}		

	}else if (is_array($var)){
		$count = 0;
		foreach ($var as $key => $value) {
			$cnt = caia_replace_inside_var($value, $oldurl, $newurl, $match_key_arr, $key);
			$var[$key] = $value;
			$count += $cnt;
		}
		return $count;
	}else{
		// neu ko phai string hoac array thi ko lam gi
		return 0;
	}
}

function CaiaUU_options_page(){
	function VB_update_urls($options,$oldurl,$newurl){	
		global $wpdb;
		$results = array();
		$queries = array(
		'content' =>		array("UPDATE $wpdb->posts SET post_content = replace(post_content, %s, %s)",  __('Content Items (Posts, Pages, Custom Post Types, Revisions)','update-urls') ),
		'excerpts' =>		array("UPDATE $wpdb->posts SET post_excerpt = replace(post_excerpt, %s, %s)", __('Excerpts','update-urls') ),
		'attachments' =>	array("UPDATE $wpdb->posts SET guid = replace(guid, %s, %s) WHERE post_type = 'attachment'",  __('Attachments','update-urls') ),
		'links' =>			array("UPDATE $wpdb->links SET link_url = replace(link_url, %s, %s)", __('Links','update-urls') ),
		'custom' =>			array("UPDATE $wpdb->postmeta SET meta_value = replace(meta_value, %s, %s) WHERE meta_value NOT LIKE 'a:%' AND meta_value NOT LIKE 'O:%'",  __('Custom Fields','update-urls') ),
		'customterm' =>			array("UPDATE $wpdb->termmeta SET meta_value = replace(meta_value, %s, %s) WHERE meta_value NOT LIKE 'a:%' AND meta_value NOT LIKE 'O:%'",  __('Custom Fields Meta','update-urls') ),
		'guids' =>			array("UPDATE $wpdb->posts SET guid = replace(guid, %s, %s)",  __('GUIDs','update-urls') )
		);
		foreach($options as $option){
			if ($option == 'widgets'){
				$result = CVB_update_widget($oldurl, $newurl);
				$results[$option] = array($result, $option);
			
			}else if ($option == 'caia-theme-settings'){
				$result = CVB_update_caia_settings($oldurl, $newurl);
				$results[$option] = array($result, $option);
			
			}else if ($option == 'plugins'){
				$result = CVB_update_caia_context($oldurl, $newurl);
				$results[$option] = array($result, $option);

			}else if ($option == 'custom'){
				$count = caia_replace_meta_array($oldurl, $newurl);				
				$result = $wpdb->query( $wpdb->prepare( $queries[$option][0], $oldurl, $newurl) );
				$results[$option] = array($result + $count, $queries[$option][1]);
			}else if ($option == 'customterm'){
				$count = caia_replace_meta_term_array($oldurl, $newurl);				
				$result = $wpdb->query( $wpdb->prepare( $queries[$option][0], $oldurl, $newurl) );
				$results[$option] = array($result + $count, $queries[$option][1]);
			}else{
				$result = $wpdb->query( $wpdb->prepare( $queries[$option][0], $oldurl, $newurl) );
				$results[$option] = array($result, $queries[$option][1]);
			}
		}
		return $results;			
	}


	if ( isset( $_POST['VBUU_settings_submit'] ) && !check_admin_referer('VBUU_submit','VBUU_nonce')){
		if(isset($_POST['VBUU_oldurl']) && isset($_POST['VBUU_newurl'])){
			if(function_exists('esc_attr')){
				$vbuu_oldurl = esc_attr(trim($_POST['VBUU_oldurl']));
				$vbuu_newurl = esc_attr(trim($_POST['VBUU_newurl']));
			}else{
				$vbuu_oldurl = attribute_escape(trim($_POST['VBUU_oldurl']));
				$vbuu_newurl = attribute_escape(trim($_POST['VBUU_newurl']));
			}
		}
		echo '<div id="message" class="error fade"><p><strong>'.__('ERROR','update-urls').' - '.__('Please try again.','update-urls').'</strong></p></div>';
	}
	elseif( isset( $_POST['VBUU_settings_submit'] ) && !isset( $_POST['VBUU_update_links'] ) ){
		if(isset($_POST['VBUU_oldurl']) && isset($_POST['VBUU_newurl'])){
			if(function_exists('esc_attr')){
				$vbuu_oldurl = esc_attr(trim($_POST['VBUU_oldurl']));
				$vbuu_newurl = esc_attr(trim($_POST['VBUU_newurl']));
			}else{
				$vbuu_oldurl = attribute_escape(trim($_POST['VBUU_oldurl']));
				$vbuu_newurl = attribute_escape(trim($_POST['VBUU_newurl']));
			}
		}
		echo '<div id="message" class="error fade"><p><strong>'.__('ERROR','update-urls').' - '.__('Your URLs have not been updated.','update-urls').'</p></strong><p>'.__('Please select at least one checkbox.','update-urls').'</p></div>';
	}
	elseif( isset( $_POST['VBUU_settings_submit'] ) ){
		$vbuu_update_links = $_POST['VBUU_update_links'];
		if(isset($_POST['VBUU_oldurl']) && isset($_POST['VBUU_newurl'])){
			if(function_exists('esc_attr')){
				$vbuu_oldurl = esc_attr(trim($_POST['VBUU_oldurl']));
				$vbuu_newurl = esc_attr(trim($_POST['VBUU_newurl']));
			}else{
				$vbuu_oldurl = attribute_escape(trim($_POST['VBUU_oldurl']));
				$vbuu_newurl = attribute_escape(trim($_POST['VBUU_newurl']));
			}
		}
		if(($vbuu_oldurl && $vbuu_oldurl != 'http://www.oldurl.com' && trim($vbuu_oldurl) != '') && ($vbuu_newurl && $vbuu_newurl != 'http://www.newurl.com' && trim($vbuu_newurl) != '')){
			$results = VB_update_urls($vbuu_update_links,$vbuu_oldurl,$vbuu_newurl);
			$empty = true;
			$emptystring = '<strong>'.__('Why do the results show 0 URLs updated?','update-urls').'</strong><br/>'.__('This happens if a URL is incorrect OR if it is not found in the content. Check your URLs and try again.','update-urls').'<br/><br/><strong>'.__('Want us to do it for you?','update-urls').'</strong><br/>'.__('Contact us at','update-urls').' <a href="mailto:info@velvetblues.com?subject=Move%20My%20WP%20Site">info@velvetblues.com</a>. '.__('We will backup your website and move it for $65 OR simply update your URLs for only $29.','update-urls');

			$resultstring = '';
			foreach($results as $result){
				$empty = ($result[0] != 0 || $empty == false)? false : true;
				$resultstring .= '<br/><strong>'.$result[0].'</strong> '.$result[1];
			}
			
			if( $empty ):
			?>
			<div id="message" class="error fade"><table><tr><td><p><strong><?php _e('ERROR: Something may have gone wrong.','update-urls'); ?></strong><br/><?php _e('Your URLs have not been updated.','update-urls'); ?></p>		
			<?php
			else:
			?>
			<div id="message" class="updated fade"><table><tr><td><p><strong><?php _e('Success! Your URLs have been updated.','update-urls'); ?></strong></p>
			<?php
			endif;
			?>
			<p><u><?php _e('Results','update-urls'); ?></u><?php echo $resultstring; ?></p>
			<?php echo ($empty)? '<p>'.$emptystring.'</p>' : ''; ?>
			</td><td width="60"></td>
			<td align="center"><?php if( !$empty ): ?><p><?php //You can now uninstall this plugin.<br/> ?><?php printf(__('If you found our plugin useful, %s please consider donating','update-urls'),'<br/>'); ?>.</p><p><a style="outline:none;" href="http://www.velvetblues.com/go/updateurlsdonate/" target="_blank"><img src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" alt="PayPal -<?php _e('The safer, easier way to pay online!','update-urls'); ?>"></a></p><?php endif; ?></td></tr></table></div>
		<?php
		}
		else{
			echo '<div id="message" class="error fade"><p><strong>'.__('ERROR','update-urls').' - '.__('Your URLs have not been updated.','update-urls').'</p></strong><p>'.__('Please enter values for both the old url and the new url.','update-urls').'</p></div>';
		}
	}
?>
<div class="wrap">
<h2>Update URLs</h2>
<form method="post" action="options-general.php?page=<?php echo basename(__FILE__); ?>">
<?php wp_nonce_field('VBUU_submit','VBUU_nonce'); ?>
<p><?php printf(__("After moving a website, %s lets you fix old URLs in content, excerpts, links, and custom fields.",'update-urls'),'<strong>Update URLs</strong>'); ?></p><p><strong><?php _e('WE RECOMMEND THAT YOU BACKUP YOUR WEBSITE.','update-urls'); ?></strong><br/><?php _e('You may need to restore it if incorrect URLs are entered in the fields below.','update-urls'); ?></p>
<h3 style="margin-bottom:5px;"><?php _e('Step'); ?> 1: <?php _e('Enter your URLs in the fields below','update-urls'); ?></h3>
<table class="form-table"><tr valign="middle">
<th scope="row" width="140" style="width:140px"><strong><?php _e('Old URL','update-urls'); ?></strong><br/><span class="description"><?php _e('Old Site Address','update-urls'); ?></span></th>
<td><input name="VBUU_oldurl" type="text" id="VBUU_oldurl" value="<?php echo (isset($vbuu_oldurl) && trim($vbuu_oldurl) != '')? $vbuu_oldurl : 'http://www.oldurl.com'; ?>" style="width:300px;font-size:20px;" onfocus="if(this.value=='http://www.oldurl.com') this.value='';" onblur="if(this.value=='') this.value='http://www.oldurl.com';" /></td>
</tr>
<tr valign="middle">
<th scope="row" width="140" style="width:140px"><strong><?php _e('New URL','update-urls'); ?></strong><br/><span class="description"><?php _e('New Site Address','update-urls'); ?></span></th>
<td><input name="VBUU_newurl" type="text" id="VBUU_newurl" value="<?php echo (isset($vbuu_newurl) && trim($vbuu_newurl) != '')? $vbuu_newurl : 'http://www.newurl.com'; ?>" style="width:300px;font-size:20px;" onfocus="if(this.value=='http://www.newurl.com') this.value='';" onblur="if(this.value=='') this.value='http://www.newurl.com';" /></td>
</tr></table>
<br/>
<h3 style="margin-bottom:5px;"><?php _e('Step'); ?> 2: <?php _e('Choose which URLs should be updated','update-urls'); ?></h3>
<table class="form-table"><tr><td><p style="line-height:20px;">
<input name="VBUU_update_links[]" type="checkbox" id="VBUU_update_true" value="content" checked="checked" /> <label for="VBUU_update_true"><strong><?php _e('URLs in page content','update-urls'); ?></strong> (<?php _e('posts, pages, custom post types, revisions','update-urls'); ?>)</label><br/>
<input name="VBUU_update_links[]" type="checkbox" id="VBUU_update_true" value="excerpts" /> <label for="VBUU_update_true"><strong><?php _e('URLs in excerpts','update-urls'); ?></strong></label><br/>
<input name="VBUU_update_links[]" type="checkbox" id="VBUU_update_true" value="links" /> <label for="VBUU_update_true"><strong><?php _e('URLs in links','update-urls'); ?></strong></label><br/>
<input name="VBUU_update_links[]" type="checkbox" id="VBUU_update_true" value="attachments" /> <label for="VBUU_update_true"><strong><?php _e('URLs for attachments','update-urls'); ?></strong> (<?php _e('images, documents, general media','update-urls'); ?>)</label><br/>
<input name="VBUU_update_links[]" type="checkbox" id="VBUU_update_true" value="custom" /> <label for="VBUU_update_true"><strong><?php _e('URLs in custom fields and meta boxes','update-urls'); ?></strong></label><br/>
<input name="VBUU_update_links[]" type="checkbox" id="VBUU_update_true" value="customterm" /> <label for="VBUU_update_true"><strong><?php _e('URLs in custom fields and meta term boxes','update-urls'); ?></strong></label><br/>

<input name="VBUU_update_links[]" type="checkbox" id="VBUU_update_true" value="widgets" /> <label for="VBUU_update_true"><strong><?php _e('URLs in text/ads widget','update-urls'); ?></strong> <i>(only Text Widget, Caia Ads Widget and Caia Upload Image Widget)</i></label><br/>

<input name="VBUU_update_links[]" type="checkbox" id="VBUU_update_true" value="plugins" /> <label for="VBUU_update_true"><strong><?php _e('URLs in plugins settings','update-urls'); ?></strong> <i>(only Caia Context)</i></label><br/>

<input name="VBUU_update_links[]" type="checkbox" id="VBUU_update_true" value="caia-theme-settings" /> <label for="VBUU_update_true"><strong><?php _e('URLs in Caia child theme settings','update-urls'); ?></strong></label><br/>

<input name="VBUU_update_links[]" type="checkbox" id="VBUU_update_true" value="guids" /> <label for="VBUU_update_true"><strong><?php _e('Update ALL GUIDs','update-urls'); ?></strong> <span class="description" style="color:#f00;"><?php _e('GUIDs for posts should only be changed on development sites.','update-urls'); ?></span> <a href="http://www.velvetblues.com/go/guids/" target="_blank"><?php _e('Learn More.','update-urls'); ?></a></label><br/>
</p></td></tr></table>
<p><input class="button-primary" name="VBUU_settings_submit" value="<?php _e('Update URLs NOW','update-urls'); ?>" type="submit" /></p>
</form>
<?php
}
add_action('admin_menu', 'CaiaUU_add_options_page');
add_action('admin_init','CaiaUU_load_textdomain');
?>