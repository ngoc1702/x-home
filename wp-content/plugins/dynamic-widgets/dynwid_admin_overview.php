<?php
/**
 * dynwid_admin_overview.php - Overview page
 *
 * @version $Id: dynwid_admin_overview.php 1218814 2015-08-12 06:37:21Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	defined('ABSPATH') or die("No script kiddies please!");
 
	if ( isset($_GET['action']) ) {
		switch ( $_GET['action'] ) {
			case 'dynwid_set_method':
				if ( $_GET['oldmethod'] == 'on' ) {
					update_option('dynwid_old_method', TRUE);
				} else {
					update_option('dynwid_old_method', FALSE);
				}

				$text = __('Method set to', DW_L10N_DOMAIN) . ' ' . ( get_option('dynwid_old_method') ? '\''. __('OLD', DW_L10N_DOMAIN) .'\'' : '\'' . __('FILTER', DW_L10N_DOMAIN) . '\'' );
				DWMessageBox::create($text, '');
				break;

			case 'dynwid_set_page_limit':
				$limit = (int) $_GET['page_limit'];
				if ( $limit > 0 ) {
					update_option('dynwid_page_limit', $limit);
					$text = __('Page limit set to', DW_L10N_DOMAIN) . ' ' . $limit . '.';
					DWMessageBox::create($text, '');
				} else {
					$text = __('ERROR', DW_L10N_DOMAIN) . ': ' . strip_tags($_GET['page_limit']) . ' ' . __('is not a valid limit.', DW_L10N_DOMAIN);
					DWMessageBox::setTypeMsg('error');
					DWMessageBox::create($text, '');
				}
				break;

			case 'reset':
				check_admin_referer('plugin-name-action_reset_' . $_GET['id']);
				$DW->resetOptions($_GET['id']);
				DWMessageBox::create(__('Widget options have been reset to default.', DW_L10N_DOMAIN), '');
				break;
		} // switch
	}

	if ( isset($_GET['dynwid_save']) && $_GET['dynwid_save'] == 'yes' ) {
		$lead = __('Widget options saved.', DW_L10N_DOMAIN);
		$msg = '';
		DWMessageBox::create($lead, $msg);
	}

  // print_r($DW->sidebars);

  foreach ( $DW->sidebars as $sidebar_id => $widgets ) {
    if ( count($widgets) > 0 ) {

      if ( $sidebar_id == 'wp_inactive_widgets' ) {
        // Caia
        continue;
        $name = __('Inactive Widgets');        

      } else if(substr($sidebar_id, 0, strlen('orphaned_widgets_')) == 'orphaned_widgets_' ){
        // Caia
        continue;
      } else {
        $name = $DW->getName($sidebar_id, 'S');
      }
?>

<div class="postbox-container" style="width:48%;margin-top:10px;margin-right:10px;">
<table cellspacing="0" class="widefat fixed">
	<thead>
	<tr  style="background-color: #ddd">
	  <th class="managage-column" scope="col"><b><?php echo $name; ?></b></th>
	  <th style="width:70px">&nbsp;</th>
  </tr>
  </thead>

  <tbody class="list:link-cat" id="<?php echo str_replace('-', '_', $sidebar_id); ?>">
  <?php foreach ( $widgets as $widget_id ) {
          $name = $DW->getName($widget_id);
          // When $name is empty, we have a widget which not belongs here
          if (! empty($name) ) {
  ?>
  <tr>
    <td class="name">
      <p class="row-title"><a title="<?php _e('Edit this widget options', DW_L10N_DOMAIN); ?>" href="themes.php?page=dynwid-config&amp;action=edit&amp;id=<?php echo $widget_id; ?>"><?php echo $name; ?></a></p>
      <div class="row-actions">
       <span class="edit">
          <a title="<?php _e('Edit this widget options', DW_L10N_DOMAIN); ?>" href="themes.php?page=dynwid-config&amp;action=edit&amp;id=<?php echo $widget_id; ?>"><?php _e('Edit'); ?></a>
        </span>
        <?php if ( $DW->hasOptions($widget_id) ) { ?>
        <span class="delete">
        <?php $href = wp_nonce_url('themes.php?page=dynwid-config&amp;action=reset&amp;id=' . $widget_id, 'plugin-name-action_reset_' . $widget_id); ?>
          | <a class="submitdelete" title="<?php _e('Reset widget to Static', DW_L10N_DOMAIN); ?>" onclick="if ( confirm('You are about to reset this widget \'<?php echo strip_tags($DW->getName($widget_id)); ?>\'\n \'Cancel\' to stop, \'OK\' to reset.') ) { return true;}return false;" href="<?php echo $href; ?>"><?php _e('Reset', DW_L10N_DOMAIN); ?></a>
        </span>
        <?php } ?>
      </div>
    </td>
    <td>
      <?php echo ( $DW->hasOptions($widget_id) ) ? __('Dynamic', DW_L10N_DOMAIN) : __('Static', DW_L10N_DOMAIN); ?>
    </td>
  </tr>
  <?php   } // END if (! empty($name) ) ?>
  <?php } // END foreach ( $widgets as $widget_id ) ?>
  </tbody>
 </table>
 </div>
<?php
    } // END if ( count($widgets) > 0 )
  } // END foreach ( $DW->sidebars as $sidebar_id => $widgets )
?>

<div class="clear"><br /><br /></div>
