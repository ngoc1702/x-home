<?php
/**
 * Create the CAIA Layout settings page
 *
 * @category CAIA
 * @package  Admin
 * @author   CAIA
 */
class CAIA_Layout extends Genesis_Admin_Boxes
{
	/**
	 * Create layout settings page.
	 *
	 * @since 1.0
	 */
	function __construct()
	{
		$page_id = 'caia-layout';

		$menu_ops = apply_filters(
			'caia_layout_menu_ops',
			array(
				'submenu' => array(
					'parent_slug' => 'themes.php',
					'page_title'  => __( 'CAIA Layout Settings', 'caia' ),
					'menu_title'  => __( 'CAIA Layout', 'caia' )
				)
			)
		);

		$page_ops = apply_filters(
			'caia_layout_page_ops',
			array(
				'screen_icon'       => 'options-general',
				'save_button_text'  => __( 'Save Settings', 'caia ' ),
				'reset_button_text' => __( 'Reset Settings', 'caia' ),
				'saved_notice_text' => __( 'Settings saved.', 'caia' ),
				'reset_notice_text' => __( 'Settings reset.', 'caia' ),
				'error_notice_text' => __( 'Error saving settings.', 'caia' ),
			)
		);

		$settings_field = CAIA_LAYOUT_SETTINGS_FIELD;

		$default_settings = apply_filters(
			'caia_layout_settings_defaults',
			array(
				'404_layout'        => '',
				'home_layout'       => '',
				'category_layout'   => '',
				'tag_layout'        => '',
				'taxonomy_layout'   => '',
				'author_layout'     => '',
				'date_layout'       => '',
				'date_year_layout'  => '',
				'date_month_layout' => '',
				'date_day_layout'   => '',
				'post_layout'       => '',
				'page_layout'       => '',
				'search_layout'     => '',
			)
		);

		$this->create( $page_id, $menu_ops, $page_ops, $settings_field, $default_settings );

		add_action( 'genesis_layout_settings_sanitizer_init', array( $this, 'sanitizer_filters' ) );
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
				'404_layout',
				'home_layout',
				'category_layout',
				'tag_layout',
				'taxonomy_layout',
				'author_layout',
				'date_layout',
				'date_year_layout',
				'date_month_layout',
				'date_day_layout',
				'post_layout',
				'page_layout',
				'search_layout',
			)
		);
	}

	/**
	 * Register the metaboxes.
	 *
	 * Must be overridden in a subclass, or it obviously won't work.
	 *
	 * @since 1.0
	 */
	public function metaboxes()
	{
		add_meta_box( 'caia-layout-home', __( 'Homepage Layout', 'caia' ), array( $this, 'home_layout_box' ), $this->pagehook, 'main', 'high' );
		add_meta_box( 'caia-layout-singular', __( 'Singular Layout', 'caia' ), array( $this, 'singular_layout_box' ), $this->pagehook, 'main' );
		add_meta_box( 'caia-layout-taxonomy', __( 'Taxonomy Layout', 'caia' ), array( $this, 'taxonomy_layout_box' ), $this->pagehook, 'main' );
		add_meta_box( 'caia-layout-search', __( 'Search Layout', 'caia' ), array( $this, 'search_layout_box' ), $this->pagehook, 'main' );
		add_meta_box( 'caia-layout-404', __( '404 Layout', 'caia' ), array( $this, 'notfound_layout_box' ), $this->pagehook, 'main' );
		add_meta_box( 'caia-layout-date', __( 'Date Layout', 'caia' ), array( $this, 'date_layout_box' ), $this->pagehook, 'main' );

		do_action( 'caia_layout_metaboxes', $this->pagehook );
	}

	/**
	 * Callback for Homepage Layout meta box.
	 *
	 * @since 1.0
	 */
	public function home_layout_box()
	{
		$this->layout_selector( 'home_layout' );
	}

	/**
	 * Callback for Singular Layout meta box.
	 *
	 * @since 1.0
	 */
	public function singular_layout_box()
	{
		$post_types = get_post_types(
			array(
				'public' => true,
				'show_ui' => true
			),
			'objects'
		);

		foreach( $post_types as $post_type => $post_type_obj ) {
			$layout_opt = $post_type . '_layout';

			echo '<p><strong>' . sprintf( __( 'Single %s Layout', 'caia' ), $post_type_obj->labels->singular_name ) . '</strong></p>';
			$this->layout_selector( $layout_opt );

		}
	}

	/**
	 * Callback for Taxonomy Layout meta box.
	 *
	 * @since 1.0
	 */
	public function taxonomy_layout_box()
	{
		$taxonomies = get_taxonomies(
			array(
				'public'   => true,
				'show_ui'  => true,
				'_builtin' => false
			),
			'objects'
		);

		echo '<p><strong>' . __( 'Category Layout', 'caia' ) . '</strong></p>';
		$this->layout_selector( 'category_layout' );

		echo '<p><strong>' . __( 'Tag Layout', 'caia' ) . '</strong></p>';
		$this->layout_selector( 'tag_layout' );

		echo '<p><strong>' . __( 'Taxonomy Layout', 'caia' ) . '</strong></p>';
		$this->layout_selector( 'taxonomy_layout' );

		foreach( $taxonomies as $taxonomy => $taxonomy_obj )
		{
			$layout_opt = $taxonomy . '_layout';
			echo '<p><strong>' . sprintf( __( '%s Layout', 'caia' ), $taxonomy_obj->labels->singular_name ) . '</strong></p>';
			$this->layout_selector( $layout_opt );
		}

	}

	/**
	 * Callback for Search Layout meta box.
	 *
	 * @since 1.0
	 */
	public function search_layout_box()
	{
		$this->layout_selector( 'search_layout' );
	}

	/**
	 * Callback for 404 Layout meta box.
	 *
	 * @since 1.0
	 */
	public function notfound_layout_box()
	{
		$this->layout_selector( '404_layout' );
	}

	/**
	 * Callback for date Layout meta box.
	 *
	 * @since 1.0
	 */
	public function date_layout_box()
	{
		$this->layout_selector( 'date_layout' );
	}

	/**
	 * Create layout selector.
	 * Add an option: default to the default genesis layout selector
	 *
	 * @param $field_name
	 */
	public function layout_selector( $field_name )
	{
		?>

		<div class="genesis-layout-selector">
			<p>
				<label>
					<input type="radio" id="default-layout" name="<?php echo $this->get_field_name( $field_name ); ?>" value="" <?php checked( '', $this->get_field_value( $field_name ) ); ?> />
					<?php _e( 'Default Layout set in ', 'caia' ); ?>
					<a href="?page=genesis"><?php _e( 'Theme Settings', 'caia' ); ?></a>
				</label>
			</p>

			<p>
				<?php genesis_layout_selector( array( 'name' => $this->get_field_name( $field_name ), 'selected' => $this->get_field_value( $field_name ), 'type' => 'site' ) ); ?>
			</p>
			<br class="clear" />
		</div>

		<?php
	}
}
