<?php

/*
* 18/10: nâng cấp sử dụng bản tối ưu art decoration
*/

ADS_Shortcodes::instance();


class ADS_Shortcodes {
    private static $instance;
    // Khai báo thuộc tính admin_settings
    private $admin_settings;

    public function __construct() {
        $this->init();
    }

    public function init() {
        if ( is_admin() ) {
            // Gán đối tượng cho admin_settings
            $this->admin_settings = new ADS_Shortcodes_Admin();
        }

        if ( ! is_admin() ) {
            // Gán đối tượng cho admin_settings
            $this->admin_settings = new ADS_Shortcodes_Frontend();
        }
    }

    // Hàm singleton instance
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }

    // Getter cho admin_settings nếu cần
    public function get_admin_settings() {
        return $this->admin_settings;
    }
}



class ADS_Shortcodes_Admin {
	public function __construct() {
		add_filter( 'mce_external_plugins', array( $this, 'add_shortcode_tinymce_plugin' ) );
		add_filter( 'mce_buttons', array( $this, 'register_shortcode_button' ) );
		add_filter( 'mce_external_languages', array( $this, 'add_tinymce_locales' ) );
		add_filter( 'tiny_mce_version', array( $this, 'refresh_mce' ) );
		add_action( 'admin_footer', array( $this, 'admin_scripts' ) );
	}

	public function add_tinymce_locales( $locales ) {
		$locales['button_ads'] =  __DIR__ . '/includes/translations.php';
		return $locales;
	}

	public function register_shortcode_button( $buttons ) {
		array_push( $buttons, 'button_ads' );
		return $buttons;
	}

	public function add_shortcode_tinymce_plugin( $plugins ) {
		$plugins['button_ads'] = home_url() . '/wp-content/themes/caia/lib/utilities/js/editor-button.js';
		return $plugins;
	}

	public function refresh_mce( $version ) {
		$version += 3;
		return $version;
	}

	public function admin_scripts() {
		echo '<style>i.mce-i-dashicons{line-height:20px;font-size:20px;font-family:dashicons;margin-right:4px}.mce-menubtn i.mce-i-dashicons{color:#ff8f00}.mce-menu-item i.dashicons-red{color:#e40101;margin-right:4px}.mce-menu-item i.dashicons-blue{color:#335bf5;margin-right:4px}.mce-menu-item i.dashicons-green{color:#22b100;margin-right:4px}.mce-menu-item i.dashicons-yellow{color:#e4b80e;margin-right:4px}.mce-menu-item.mce-selected i.mce-i-dashicons,.mce-menu-item:focus i.mce-i-dashicons,.mce-menu-item:hover i.mce-i-dashicons{color:#fff}</style>';
	}
} // end of class Shortcode Admin

class ADS_Shortcodes_Frontend {
	
	public $is_runned = false;

	public function __construct() {
		add_shortcode( 'tds_warning', array( $this, 'warning' ) );
		add_shortcode( 'tds_council', array( $this, 'council' ) );
		add_shortcode( 'tds_note', array( $this, 'note' ) );
		add_shortcode( 'tds_info', array( $this, 'info' ) );
		add_shortcode( 'ads_custom_box', array( $this, 'custom_box' ) );
		add_shortcode( 'ads_color_box', array( $this, 'color_box' ) );
		add_action('wp_footer', array($this, 'add_assets'), 0);
	}

	public function add_assets(){
		if ($this->is_runned){
			echo '<style>.tds-message-box{margin:15px 0;padding:20px 15px 20px 70px;position:relative}.box-warning{background:#fbe9e7;color:#bf360c}.box-council{background:#e3f2fd;color:#0d47a1}.box-note{background:#e8f5e9;color:#1b5e20}.box-info{background:#fff8e1;color:#de6000}.box-warning:before{content:"\f339"}.box-council:before{content:"\f130"}.box-note:before{content:"\f109"}.box-info:before{content:"\f348"}.box-council:before,.box-info:before,.box-note:before,.box-warning:before{font-family:dashicons,monospace;font-size:40px;display:inline-block;opacity:.8;vertical-align:top;float:left;font-style:normal;line-height:40px;margin-right:7px;position:absolute;left:15px;top:25%}.ads-custom-box{padding:0 15px 30px;margin:30px 0 15px}.custom-box{border-color:#e87e04;border-style:solid;border-width:1px}.ads-custom-box-title{position:relative;display:inline-block;top:-15px;left:0;background:#fff;padding:0 15px;font-size:1.4rem;color:#e87e04}.ads-color-box{padding:30px 15px;border-radius:3px}</style>';
		}
	}

	/* Warning box */
	public function warning( $atts, $content = null ) {
		
		$this->is_runned = true;

		$atts = shortcode_atts(
			array(
				'class' => '',
			),
			$atts
		);
		$class = $atts['class'] ? $atts['class'] : '';
		$output = '<div class="tds-message-box box-warning ' . $class . '">';
		$output .= do_shortcode( $content );
		$output .= '</div>';
		wp_enqueue_style( 'dashicons' );
		return apply_filters( 'tds_warning_filter_html', $output );
	}

	/* Council box */
	public function council( $atts, $content = null ) {

		$this->is_runned = true;

		$atts = shortcode_atts(
			array(
				'class' => '',
			),
			$atts
		);
		$class = $atts['class'] ? $atts['class'] : '';
		$output = '<div class="tds-message-box box-council ' . $class . '">';
		$output .= do_shortcode( $content );
		$output .= '</div>';
		wp_enqueue_style( 'dashicons' );
		return apply_filters( 'tds_council_filter_html', $output );
	}

	/* Note box */
	public function note( $atts, $content = null ) {

		$this->is_runned = true;

		$atts = shortcode_atts(
			array(
				'class' => '',
			),
			$atts
		);
		$class = $atts['class'] ? $atts['class'] : '';
		$output = '<div class="tds-message-box box-note ' . $class . '">';
		$output .= do_shortcode( $content );
		$output .= '</div>';
		wp_enqueue_style( 'dashicons' );
		return apply_filters( 'tds_note_filter_html', $output );
	}

	/* Info box */
	public function info( $atts, $content = null ) {

		$this->is_runned = true;


		$atts = shortcode_atts(
			array(
				'class' => '',
			),
			$atts
		);
		$class = $atts['class'] ? $atts['class'] : '';
		$output = '<div class="tds-message-box box-info ' . $class . '">';
		$output .= do_shortcode( $content );
		$output .= '</div>';
		wp_enqueue_style( 'dashicons' );
		return apply_filters( 'tds_info_filter_html', $output );
	}

	/* Custom box */
	public function custom_box( $atts, $content = null ) {

		$this->is_runned = true;


		$atts = shortcode_atts(
			array(
				'title'        => '',
				'color_border' => '',
				'class'        => '',
			),
			$atts
		);
		$class         = $atts['class'] ? $atts['class'] : '';
		$color_border  = '#e87e04' !== $atts['color_border'] ? 'border-color:' . $atts['color_border'] . ';' : '';
		$color_title   = '#e87e04' !== $atts['color_border'] ? 'style="color:' . $atts['color_border'] . ';"' : '';
		$title         = $atts['title'] ? '<div class="ads-custom-box-title" ' . $color_title . '>' . $atts['title'] . '</div>' : '';
		$title_padding = ! $title ? 'padding: 2.2rem 2.2rem;' : '';
		$output = '<div class="ads-custom-box custom-box ' . $class . '" style="' . $color_border . $title_padding . '">';
		$output .= apply_filters( 'ads_custom_box_title_filter_html', $title );
		$output .= '<div class="ads-custom-box-content">' . do_shortcode( $content ) . '</div>';
		$output .= '</div>';
		return apply_filters( 'ads_custom_box_filter_html', $output );
	}

	/* Color box */
	public function color_box( $atts, $content = null ) {

		$this->is_runned = true;

		$atts   = shortcode_atts(
			array(
				'color_background' => '#eee',
				'color_text'       => '#444',
			),
			$atts
		);
		$output = '<div class="ads-color-box" style="color:' . $atts['color_text'] . ';background:' . $atts['color_background'] . ';">' . do_shortcode( $content ) . '</div>';
		return $output;
	}

} // end of class Shortcode Frontend