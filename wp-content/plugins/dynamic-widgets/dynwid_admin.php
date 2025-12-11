<?php
/**
 * dynwid_admin.php - Startpage for admin
 *
 * @version $Id: dynwid_admin.php 1095126 2015-02-20 12:59:35Z qurl $
 * @copyright 2011 Jacco Drabbe
 */
 
	defined('ABSPATH') or die("No script kiddies please!");
?>



<div class="wrap">
<div class="icon32" id="icon-themes"><br></div>
<h2><div style="float:left;width:300px;">
	<?php _e('Tùy biến Widgets', DW_L10N_DOMAIN); ?>	
</div></h2>


<br style="clear:both" />
<?php
	if ( $DW->enabled ) {
		if ( dynwid_sql_mode() ) {
			echo '<div class="error" id="message"><p>';
			_e('<b>WARNING</b> STRICT sql mode in effect. Dynamic Widgets might not work correctly. Please disable STRICT sql mode.', DW_L10N_DOMAIN);
			echo '</p></div>';
		}

		// Actions
		if ( isset($_GET['action']) && $_GET['action'] == 'edit' ) {
			$dw_admin_script = '/dynwid_admin_edit.php';
			$DW->loadModules();
		} else {			
			$dw_admin_script = '/dynwid_admin_overview.php';

			// Do some housekeeping
			$lastrun = get_option('dynwid_housekeeping_lastrun');
			if ( time() - $lastrun > DW_TIME_LIMIT ) {
				$DW->housekeeping();
				update_option('dynwid_housekeeping_lastrun', time());
			}
		}
		require_once(dirname(__FILE__) . $dw_admin_script);
	} else {
		echo '<div class="error" id="message"><p>';
		_e('Oops! Something went horribly wrong. Please reinstall Dynamic Widgets.', DW_L10N_DOMAIN);
		echo '</p></div>';
	}
?>

<!-- Footer //-->
<div class="clear"><br /><br /></div>
<div><small>
  <a href="<?php echo DW_URL_AUTHOR; ?>/dynamic-widgets/" target="_blank">Dynamic Widgets</a> v<?php echo DW_VERSION; ?> (<?php echo ( DW_OLD_METHOD ) ? __('OLD', DW_L10N_DOMAIN)  : __('FILTER', DW_L10N_DOMAIN); ?>)
</small></div>

</div> <!-- /wrap //-->