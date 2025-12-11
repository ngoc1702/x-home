<?php
/**
 * Create the CAIA Theme settings page
 *
 * @category CAIA
 * @package  Admin
 * @author   CAIA
 */

/**
 * Registers a new admin page, providing content and corresponding menu item
 * for the Theme Settings page.
 *
 * @category CAIA
 * @package  Admin
 *
 * @since    1.0
 */
class CAIA_Theme_Settings extends Genesis_Admin_Boxes
{
	/**
	 * Create an admin menu item and settings page.
	 *
	 * @since 1.0
	 *
	 * @uses  GENESIS_SETTINGS_FIELD settings field key
	 * @uses  genesis_get_default_layout() Get default layout
	 *
	 * @global string $_genesis_theme_settings_pagehook Theme Settings page hook,
	 *        kept for backwards compatibility, since this class now uses $this->pagehook.
	 */
	function __construct()
	{
		$page_id = 'caia-settings';

		$menu_ops = apply_filters(
			'caia_settings_menu_ops',
			array(
				'submenu' => array(
					'parent_slug' => 'themes.php',
					'page_title'  => __( 'CAIA Theme Settings', 'caia' ),
					'menu_title'  => __( 'CAIA Settings', 'caia' )
				)
			)
		);

		$page_ops = apply_filters(
			'caia_settings_page_ops',
			array(
				'screen_icon'       => 'options-general',
				'save_button_text'  => __( 'Save Settings', 'caia ' ),
				'reset_button_text' => __( 'Reset Settings', 'caia' ),
				'saved_notice_text' => __( 'Settings saved.', 'caia' ),
				'reset_notice_text' => __( 'Settings reset.', 'caia' ),
				'error_notice_text' => __( 'Error saving settings.', 'caia' ),
			)
		);

		$settings_field = CAIA_SETTINGS_FIELD;

		$default_settings = apply_filters(
			'caia_settings_defaults',
			array(
				'caia_version'          => CHILD_THEME_VERSION,
				'use_default_thumbnail' => 0,
				'default_thumbnail'     => '',
				'footer_text'           => '',
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
			'one_zero',
			$this->settings_field,
			array(
				'use_default_thumbnail',
			)
		);
	}

	// Tạo khung setting trong Caia Setting
	public function metaboxes(){
		//add_meta_box( 'caia-settings-general', __( 'General', 'caia' ), array( $this, 'general_box' ), $this->pagehook, 'main', 'high' );
		//add_meta_box( 'caia-settings-logo', __( 'Thay logo trang đăng nhập', 'caia' ), array( $this, 'logo_box' ), $this->pagehook, 'main' );
		add_meta_box( 'caia-settings-footer', __( 'Nội dung thông tin bản quyền dưới chân trang', 'caia' ), array( $this, 'footer_box' ), $this->pagehook, 'main' );

		do_action( 'caia_settings_metaboxes', $this->pagehook );
	}

	function general_box()
	{
		?>

		<p>
			<strong><?php _e( 'Version:', 'genesis' ); ?></strong> <?php echo CHILD_THEME_VERSION; ?>
		</p>

		<p>
			<label>
				<input type="checkbox" name="<?php echo $this->get_field_name( 'use_default_thumbnail' ); ?>" value="1" <?php checked( 1, $this->get_field_value( 'use_default_thumbnail' ) ); ?> />
				<?php _e( 'Use default thumbnail image.', 'caia' ); ?>
			</label>
		</p>

		<p class="default-thumbnail-uploader <?php echo $this->get_field_value( 'use_default_thumbnail' ) ? '' : 'hidden'; ?>">

		</p>

		<?php
	}
	
	/*Thay logo trang đăng nhập*/
	function logo_box(){
		?><p><label>Đường dẫn ảnh logo: </label><input type="text" name="<?php echo $this->get_field_name( 'logo_text' ); ?>" value="<?php echo $this->get_field_value( 'logo_text' ); ?>" size="70"/></p><?php
	}

	/*Thêm thông tin bản quyền website*/
	function footer_box(){
		$args =   array(
			'wpautop' => true,
			'media_buttons' => true,
			'textarea_name' => $this->get_field_name( 'footer_text' ),
			'tinymce'       => array(
				'toolbar1'      => 'formatselect,bold,italic,underline,bullist,numlist,link,unlink,forecolor,alignleft,aligncenter,alignright',
			),
			'editor_height' => 70
		);
		wp_editor( stripslashes($this->get_field_value( 'footer_text' )), 'footer_text', $args );
	}
}