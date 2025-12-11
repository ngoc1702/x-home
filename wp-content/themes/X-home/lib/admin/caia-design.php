<?php
/**
 * Create the CAIA Design settings page
 *
 * @category CAIA
 * @package  Admin
 * @author   CAIA
 */

/**
 * Registers a new admin page, providing content and corresponding menu item
 * for the Design Settings page.
 *
 * @category CAIA
 * @package  Admin
 *
 * @since    1.0
 */
class CAIA_Design extends Genesis_Admin_Boxes
{
	/**
	 * Create an admin menu item and settings page.
	 *
	 * @since 1.0
	 *
	 * @uses  CAIA_DESIGN_SETTINGS_FIELD settings field key
	 * @uses  genesis_get_default_layout() Get default layout
	 *
	 * @global string $_genesis_theme_settings_pagehook Theme Settings page hook,
	 *        kept for backwards compatibility, since this class now uses $this->pagehook.
	 */
	function __construct()
	{
		$page_id = 'caia-design';

		$menu_ops = apply_filters(
			'caia_design_settings_menu_ops',
			array(
				'submenu' => array(
					'parent_slug' => 'themes.php',
					'page_title'  => __( 'Khung nội dung trang chủ', 'caia' ),
					'menu_title'  => __( 'CAIA Homepage', 'caia' )
				)
			)
		);

		$page_ops = apply_filters(
			'caia_design_settings_page_ops',
			array(
				'screen_icon'       => 'options-general',
				'save_button_text'  => __( 'Lưu lại', 'caia ' ),
				'reset_button_text' => __( 'Nhập lại', 'caia' ),
				'saved_notice_text' => __( 'Settings saved.', 'caia' ),
				'reset_notice_text' => __( 'Settings reset.', 'caia' ),
				'error_notice_text' => __( 'Error saving settings.', 'caia' ),
			)
		);

		$settings_field = CAIA_DESIGN_SETTINGS_FIELD;

		$default_settings = apply_filters(
			'caia_design_settings_defaults',
			array(
				'custom_css' => '',
			)
		);

		$this->create( $page_id, $menu_ops, $page_ops, $settings_field, $default_settings );

		add_action( 'genesis_settings_sanitizer_init', array( $this, 'sanitizer_filters' ) );
	}

	/**
	 * Registers each of the settings with a sanitization filter type.
	 *
	 * @since 1.0
	 *
	 * @uses  genesis_add_option_filter() Assign filter to array of settings
	 *
	 * @see   Genesis_Settings_Sanitizer::add_filter()
	 */
	public function sanitizer_filters()
	{
		genesis_add_option_filter(
			'no_html',
			$this->settings_field,
			array(
				'custom_css',
			)
		);
	}

	/**
	 * Override function.
	 * Include the necessary sortable metabox scripts.
	 *
	 * @since 1.0
	 */
	public function scripts()
	{
		//wp_enqueue_script( 'common' );
		//wp_enqueue_script( 'wp-lists' );
		//wp_enqueue_script( 'postbox' );
		//wp_enqueue_script( 'jquery-ui-draggable' );
		//wp_enqueue_script( 'caia-admin-script', CHILD_URL . '/lib/js/admin.js', array( 'jquery' ), CHILD_THEME_VERSION, true );
		//wp_enqueue_style( 'caia-admin-style', CHILD_URL . '/lib/css/admin.css' );
	}

	/**
	 * Register the metaboxes.
	 *
	 * @since 1.0
	 */
	public function metaboxes()
	{
		//add_meta_box( 'caia-design-home', __( 'Nội dung trang chủ', 'caia' ), array( $this, 'home_settings_box' ), $this->pagehook, 'main', 'high' );
		add_meta_box( 'caia-design-custom-css', __( 'CSS bổ sung', 'caia' ), array( $this, 'custom_css_box' ), $this->pagehook, 'main' );

		//do_action( 'caia_design_settings_metaboxes', $this->pagehook );
	}

	/**
	 * Callback for Homepage design settings meta box.
	 *
	 * @since 1.0
	 */
	public function home_settings_box()
	{
		$registed_blocks = caia_get_registed_blocks();
		$home_blocks = $this->get_field_value( 'homepage' );
		$home_blocks = ! empty( $home_blocks ) ? $home_blocks : array();
		unset($home_blocks['__i__']  );
		?>

		<div id="home-block-holder" class="block-holder">
			<div class="block-list">

				<?php foreach( $registed_blocks as $key => $block ) : ?>

	                <div class="block deactived">
		                <div class="block-title-action">
			                <a href="#" class="block-delete block-action" title="<?php _e( 'Delete', 'caia' ); ?>"><?php _e( 'Delete', 'caia' ); ?></a>
			                <a href="#" class="block-toggle block-action" title="<?php _e( 'Toggle', 'caia' ); ?>"><?php _e( 'Toggle', 'caia' ); ?></a>
		                </div>
	                    <h4><p><?php echo $block->name; ?></p></h4>
		                <div class="block-inside hidden">
			                <div class="block-settings">
				                <?php
				                    $block->settings_field = $this->settings_field;
					                $block->options_group = 'homepage';
				                    $block->form();
				                ?>
			                </div>
			                <div class="block-info">
                                <input type="hidden" name="<?php echo $block->get_field_name( '__class_name' ); ?>" value="<?php echo $key; ?>" />
			                </div>
		                </div>
	                </div><!-- end .block -->

				<?php endforeach; ?>

			</div>
		</div>

		<div id="home-design-settings" class="sortable-placeholder sortable">
			<p class="description"><?php _e( 'Kéo thả các khung vào đây', 'caia' ); ?></p>
			<div class="actived-blocks-settings actived-blocks">
				<?php foreach( $home_blocks as $num => $home_block ) : ?>
					<?php 
					if(!class_exists($home_block['__class_name'])) 
						continue;
					$block = new $home_block['__class_name'];
					$block->number = $num;
					$block->options_group = 'homepage'; ?>

	                <div class="block closed">
	                    <div class="block-title-action">
	                        <a href="#" class="block-delete block-action" title="<?php _e( 'Delete', 'caia' ); ?>"><?php _e( 'Delete', 'caia' ); ?></a>
	                        <a href="#" class="block-toggle block-action" title="<?php _e( 'Toggle', 'caia' ); ?>"><?php _e( 'Toggle', 'caia' ); ?></a>
	                    </div>
	                    <h4><p><?php echo $block->name; ?><?php echo isset( $home_block['title'] ) ? ': <span>' . $home_block['title'] . '</span>' : ''; ?></p></h4>
	                    <div class="block-inside hidden">
	                        <div class="block-settings">
								<?php
								$block->settings_field = $this->settings_field;
								$block->options_group = 'homepage';
								$block->form();
								?>
	                        </div>
	                        <div class="block-info">
	                            <input type="hidden" name="<?php echo $block->get_field_name( '__class_name' ); ?>" value="<?php echo get_class( $block ); ?>" />
	                        </div>
	                    </div>
	                </div><!-- end .block -->

				<?php endforeach; ?>
			</div>
		</div>

		<br class="clear" />
		<?php
	}
	
	// Bổ sung khung CSS tùy chỉnh thêm
	function custom_css_box()
	{
		?>

		<textarea class="widefat" rows="7" id="<?php echo $this->get_field_id( 'custom_css' ); ?>" name="<?php echo $this->get_field_name( 'custom_css' ); ?>"><?php echo $this->get_field_value( 'custom_css' ); ?></textarea>

		<?php
	}

}