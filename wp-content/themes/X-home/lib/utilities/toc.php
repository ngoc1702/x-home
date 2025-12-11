<?php

define( 'CAIA_TOC_VERSION', '3.0' );

/*
* 13/9: fix khung toc tren mobile bi rong qua, va tu dong center o mobile
* fix ẩn hiện toc : dòng 212
* fix đổi tiêu đề mục lục : dòng 871
*/

if ( !class_exists( 'toc' ) ) :

	define( 'TOC_VERSION', '1509' );
	define( 'TOC_POSITION_BEFORE_FIRST_HEADING', 1 );
	define( 'TOC_POSITION_TOP', 2 );
	define( 'TOC_POSITION_BOTTOM', 3 );
	define( 'TOC_POSITION_AFTER_FIRST_HEADING', 4 );
	define( 'TOC_MIN_START', 2 );
	define( 'TOC_MAX_START', 10 );
	define( 'TOC_SMOOTH_SCROLL_OFFSET', 30 );
	define( 'TOC_WRAPPING_NONE', 0 );
	define( 'TOC_WRAPPING_LEFT', 1 );
	define( 'TOC_WRAPPING_RIGHT', 2 );
	define( 'TOC_WRAPPING_CENTER', 3 );
	define( 'TOC_THEME_GREY', 1 );
	define( 'TOC_THEME_LIGHT_BLUE', 2 );
	define( 'TOC_THEME_WHITE', 3 );
	define( 'TOC_THEME_BLACK', 4 );
	define( 'TOC_THEME_TRANSPARENT', 99 );
	define( 'TOC_THEME_CUSTOM', 100 );
	define( 'TOC_DEFAULT_BACKGROUND_COLOUR', '#f9f9f9' );
	define( 'TOC_DEFAULT_BORDER_COLOUR', '#aaaaaa' );
	define( 'TOC_DEFAULT_TITLE_COLOUR', '#' );
	define( 'TOC_DEFAULT_LINKS_COLOUR', '#' );
	define( 'TOC_DEFAULT_LINKS_HOVER_COLOUR', '#' );
	define( 'TOC_DEFAULT_LINKS_VISITED_COLOUR', '#' );	

	class toc {
		
		// private $path;		// eg /wp-content/plugins/toc
		private $options;
		private $show_toc;	// allows to override the display (eg through [no_toc] shortcode)
		private $exclude_post_types;
		private $collision_collector;	// keeps a track of used anchors for collision detecting	
		public $toc_showed = false;	
		function __construct()
		{
			// $this->path = plugins_url( '', __FILE__ );
			$this->show_toc = true;
			$this->exclude_post_types = array( 'attachment', 'revision', 'nav_menu_item', 'safecss' );
			$this->collision_collector = array();

			// get options
			$defaults = array(		// default options
				'fragment_prefix' => 'i',
				'position' => TOC_POSITION_BEFORE_FIRST_HEADING,
				'start' => 4,
				'show_heading_text' => true,
				'heading_text' => 'Mục lục',
				'auto_insert_post_types' => array(),
				'show_heirarchy' => true,
				'ordered_list' => false,
				'smooth_scroll' => true,
				'smooth_scroll_offset' => TOC_SMOOTH_SCROLL_OFFSET,
				'visibility' => true,
				'visibility_show' => 'Hiện',
				'visibility_hide' => 'Ẩn',
				'visibility_hide_by_default' => false,
				'width' => '97%',
				'width_custom' => '275',
				'width_custom_units' => 'px',
				'wrapping' => TOC_WRAPPING_NONE,
				'font_size' => '95',
				'font_size_units' => '%',
				'theme' => TOC_THEME_GREY,
				'custom_background_colour' => TOC_DEFAULT_BACKGROUND_COLOUR,
				'custom_border_colour' => TOC_DEFAULT_BORDER_COLOUR,
				'custom_title_colour' => TOC_DEFAULT_TITLE_COLOUR,
				'custom_links_colour' => TOC_DEFAULT_LINKS_COLOUR,
				'custom_links_hover_colour' => TOC_DEFAULT_LINKS_HOVER_COLOUR,
				'custom_links_visited_colour' => TOC_DEFAULT_LINKS_VISITED_COLOUR,
				'lowercase' => true,
				'hyphenate' => false,
				'bullet_spacing' => false,
				'include_homepage' => false,
				'exclude_css' => false,
				'exclude' => '',
				'heading_levels' => array('2', '3'),
				'restrict_path' => '',
				'css_container_class' => '',
				'sitemap_show_page_listing' => true,
				'sitemap_show_category_listing' => true,
				'sitemap_heading_type' => 3,
				'sitemap_pages' => 'Pages',
				'sitemap_categories' => 'Categories',
				'show_toc_in_widget_only' => false,
				'show_toc_in_widget_only_post_types' => array()
			);
			// $options = get_option( 'toc-options', $defaults );
			// $this->options = wp_parse_args( $options, $defaults );

			$this->options = $defaults;
			
			// add_action( 'toc_enqueue_scripts', array(&$this, 'toc_enqueue_scripts') );
			add_action( 'wp_head', array(&$this, 'wp_head') );			
			
			add_filter( 'the_content', array(&$this, 'the_content'), 12 );	// run after shortcodes are interpretted (level 10)			
			
			add_shortcode( 'toc', array(&$this, 'shortcode_toc') );
			
			if (is_admin()){
				add_action( 'caia_settings_metaboxes',  array( $this, 'add_theme_settings_toc' ) );
			}
		}
		

		function add_theme_settings_toc( $pagehook ){
			add_meta_box( 'caia-toc-settings', __( 'Thiết lập Mục lục', 'caia' ), array($this, 'caia_toc_setting'), $pagehook, 'main' );
		}

		function caia_toc_setting(){
			?>
			<table class="form-table">
			<tbody>									
				<tr valign="top">
					<th scope="row">Nhảy tới trước điểm neo một khoảng (px) trên PC</th>
					<td>				
						<input type="text" name="<?php echo CAIA_SETTINGS_FIELD; ?>[pc_toc_offset]" value="<?php caia_option( 'pc_toc_offset' ); ?>" placeholder="30" size="3"/> px <i>(ex: 15, ngầm định: 30)</i>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Nhảy tới trước điểm neo một khoảng (px) trên Mobile</th>
					<td>				
						<input type="text" name="<?php echo CAIA_SETTINGS_FIELD; ?>[mobile_toc_offset]" value="<?php caia_option( 'mobile_toc_offset' ); ?>" placeholder="60" size="3"/> px <i>(ex: 15, ngầm định: 60)</i>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Tiêu đề mục lục</th>
					<td>				
						<input type="text" name="<?php echo CAIA_SETTINGS_FIELD; ?>[title_toc]" value="<?php caia_option( 'title_toc' ); ?>" placeholder="Mục lục" size="60"/>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Ẩn/ hiện mục lục</th>
					<td>				
						<input type="checkbox" name="<?php echo CAIA_SETTINGS_FIELD; ?>[show_hide_toc]" value="1" <?php checked( 1, caia_get_option( 'show_hide_toc' ) ); ?> /><label for="backtotop">Tắt hiện [TOC] mặc định</label>
					</td>
				</tr>
			</tbody>
			</table>
			<?php
		}
		
		function __destruct()
		{
		}
		


		public function set_option($array)
		{
			$this->options = array_merge($this->options, $array);
		}


		/**
		 * Allows the developer to disable TOC execution
		 */
		public function disable()
		{
			$this->show_toc = false;
		}


		/**
		 * Allows the developer to enable TOC execution
		 */
		public function enable()
		{
			$this->show_toc = true;
		}
		
		


		public function get_exclude_post_types()
		{
			return $this->exclude_post_types;
		}
			
				
		function shortcode_toc( $atts )
		{
			extract( shortcode_atts( array(
				'label' => $this->options['heading_text'],
				'label_show' => $this->options['visibility_show'],
				'label_hide' => $this->options['visibility_hide'],
				'no_label' => false,
				'class' => false,
				'pos' => $this->options['wrapping'],
				'ol'	=> false,
				'heading_levels' => $this->options['heading_levels'],
				'exclude' => $this->options['exclude'],
				'collapse' => false,				
				), $atts )
			);

			// caia_log('toc', 'shortcode_toc', $_SERVER['REQUEST_URI']);

			$re_enqueue_scripts = true;

			// fix ẩn hiện toc 
			if (!empty( caia_get_option( 'show_hide_toc' ) )) {
				$this->options['visibility_hide_by_default'] = true;
			}else{
				$this->options['visibility_hide_by_default'] = false;
			}

			if ( $no_label ) $this->options['show_heading_text'] = false;
			if ( $label ) $this->options['heading_text'] = html_entity_decode( $label );
			if ( $label_show ) {
				$this->options['visibility_show'] = html_entity_decode( $label_show );
				$re_enqueue_scripts = true;
			}
			if ( $label_hide ) {
				$this->options['visibility_hide'] = html_entity_decode( $label_hide );
				$re_enqueue_scripts = true;
			}
			if ( $class ) $this->options['css_container_class'] = $class;
			if ( $pos ) {
				switch ( strtolower(trim($pos)) ) {
					case 'left':
						$this->options['wrapping'] = TOC_WRAPPING_LEFT;
						break;
						
					case 'right':
						$this->options['wrapping'] = TOC_WRAPPING_RIGHT;
						break;
					
					case 'center':
						$this->options['wrapping'] = TOC_WRAPPING_CENTER;
						break;	
					default:
						// do nothing
				}
			}

			if ($ol){
				$this->options['ordered_list'] = true;
			}

			if ( $exclude ) $this->options['exclude'] = $exclude;
			if ( $collapse ) {
				$this->options['visibility_hide_by_default'] = true;
				$re_enqueue_scripts = true;
			}

			if ( $re_enqueue_scripts ) $this->toc_enqueue_scripts();

			// if $heading_levels is an array, then it came from the global options
			// and wasn't provided by per instance
			if ( $heading_levels && !is_array($heading_levels) ) {
				// make sure they are numbers between 1 and 6 and put into 
				// the $clean_heading_levels array if not already
				$clean_heading_levels = array();
				foreach (explode(',', $heading_levels) as $heading_level) {
					if ( is_numeric($heading_level) ) {
						if ( 1 <= $heading_level && $heading_level <= 6 ) {
							if ( !in_array($heading_level, $clean_heading_levels) ) {
								$clean_heading_levels[] = $heading_level;
							}
						}
					}
				}
				
				if ( count($clean_heading_levels) > 0 )
					$this->options['heading_levels'] = $clean_heading_levels;
			}
		
			if ( !is_search() && !is_archive() && !is_feed() )
				return '<!--TOC-->';
			else
				return;
		}
		
		
		
		
		/**
		 * Register and load CSS and javascript files for frontend.
		 */
		function toc_enqueue_scripts()
		{	
			// enqueue them!
			if ( !$this->options['exclude_css'] ){								
				add_action('wp_footer', array(&$this, 'toc_add_css'), 0);	
			} 
						
			add_action('wp_footer', array(&$this, 'toc_add_js'));			
		}
		
		function toc_add_css(){
			$toc_css = '<style>#toc_container li,#toc_container ul{margin:0;padding:0}#toc_container.no_bullets li,#toc_container.no_bullets ul,#toc_container.no_bullets ul li,.toc_widget_list.no_bullets,.toc_widget_list.no_bullets li{background:0 0;list-style-type:none;list-style:none}#toc_container.have_bullets li{padding-left:12px}#toc_container ul ul{margin-left:1.5em}#toc_container{background:#f9f9f9;border:1px solid #aaa;padding:10px;margin-bottom:1em;width:auto;display:table;font-size:95%}#toc_container.toc_light_blue{background:#edf6ff}#toc_container.toc_white{background:#fff}#toc_container.toc_black{background:#000}#toc_container.toc_transparent{background:none transparent}#toc_container p.toc_title{text-align:center;font-weight:700;margin:0;padding:0}#toc_container.toc_black p.toc_title{color:#aaa}#toc_container span.toc_toggle{font-weight:400;font-size:90%}#toc_container p.toc_title+ul.toc_list{margin-top:1em}.toc_wrap_left{float:left;margin-right:10px}.toc_wrap_right{float:right;margin-left:10px}.toc_wrap_center{margin:auto;}#toc_container a{text-decoration:none;text-shadow:none}#toc_container a:hover{text-decoration:underline}.toc_sitemap_posts_letter{font-size:1.5em;font-style:italic}</style>';
			echo $toc_css;
		}
		function toc_add_js(){
			global $caia_detected_device;

			$js_vars = array();
			if ( $this->options['smooth_scroll'] ) $js_vars['smooth_scroll'] = true;
			wp_enqueue_script( 'toc-front' );

			if ( $this->options['show_heading_text'] && $this->options['visibility'] ) {
				$width = ( $this->options['width'] != 'User defined' ) ? $this->options['width'] : $this->options['width_custom'] . $this->options['width_custom_units'];			
				$js_vars['visibility_show'] = esc_js($this->options['visibility_show']);
				$js_vars['visibility_hide'] = esc_js($this->options['visibility_hide']);
				if ( $this->options['visibility_hide_by_default'] ) $js_vars['visibility_hide_by_default'] = true;
				$js_vars['width'] = esc_js($width);
			}
			if ( $this->options['smooth_scroll_offset'] != TOC_SMOOTH_SCROLL_OFFSET )
				$js_vars['smooth_scroll_offset'] = esc_js($this->options['smooth_scroll_offset']);

			if (isset($caia_detected_device) && $caia_detected_device === 'Mobile' ){
				$offset = caia_get_option( 'mobile_toc_offset' ) ;
				if (!$offset) $offset = 60;
				$js_vars['smooth_scroll_offset'] = $offset;
			}else{
				$offset = caia_get_option( 'pc_toc_offset' ) ;
				if (!$offset) $offset = 30;
				$js_vars['smooth_scroll_offset'] = $offset;
			}

			
			
			if ( count($js_vars) > 0 ) {				
				wp_localize_script(
					'toc-front',
					'tocplus',
					$js_vars
				);
			}		
		echo '<script type="text/javascript">/* <![CDATA[ */';
		$js_array = json_encode($js_vars);
		echo "var tocplus = ". $js_array . ";";
		echo '/* ]]> */</script>';
?>
<script type="text/javascript">!function(t){"function"==typeof define&&define.amd?define(["jquery"],t):t("object"==typeof module&&module.exports?require("jquery"):jQuery)}(function(t){function e(t){return t.replace(/(:|\.|\/)/g,"\\$1")}var o="1.6.0",i={},l={exclude:[],excludeWithin:[],offset:0,direction:"top",delegateSelector:null,scrollElement:null,scrollTarget:null,beforeScroll:function(){},afterScroll:function(){},easing:"swing",speed:400,autoCoefficient:2,preventDefault:!0},s=function(e){var o=[],i=!1,l=e.dir&&"left"===e.dir?"scrollLeft":"scrollTop";return this.each(function(){var e=t(this);return this!==document&&this!==window?!document.scrollingElement||this!==document.documentElement&&this!==document.body?void(e[l]()>0?o.push(this):(e[l](1),i=e[l]()>0,i&&o.push(this),e[l](0))):(o.push(document.scrollingElement),!1):void 0}),o.length||this.each(function(){"BODY"===this.nodeName&&(o=[this])}),"first"===e.el&&o.length>1&&(o=[o[0]]),o};t.fn.extend({scrollable:function(t){var e=s.call(this,{dir:t});return this.pushStack(e)},firstScrollable:function(t){var e=s.call(this,{el:"first",dir:t});return this.pushStack(e)},smoothScroll:function(o,i){if(o=o||{},"options"===o)return i?this.each(function(){var e=t(this),o=t.extend(e.data("ssOpts")||{},i);t(this).data("ssOpts",o)}):this.first().data("ssOpts");var l=t.extend({},t.fn.smoothScroll.defaults,o),s=function(o){var i=this,s=t(this),n=t.extend({},l,s.data("ssOpts")||{}),c=l.exclude,a=n.excludeWithin,r=0,h=0,u=!0,d={},p=t.smoothScroll.filterPath(location.pathname),f=t.smoothScroll.filterPath(i.pathname),m=location.hostname===i.hostname||!i.hostname,g=n.scrollTarget||f===p,v=e(i.hash);if(n.scrollTarget||m&&g&&v){for(;u&&r<c.length;)s.is(e(c[r++]))&&(u=!1);for(;u&&h<a.length;)s.closest(a[h++]).length&&(u=!1)}else u=!1;u&&(n.preventDefault&&o.preventDefault(),t.extend(d,n,{scrollTarget:n.scrollTarget||v,link:i}),t.smoothScroll(d))};return null!==o.delegateSelector?this.undelegate(o.delegateSelector,"click.smoothscroll").delegate(o.delegateSelector,"click.smoothscroll",s):this.unbind("click.smoothscroll").bind("click.smoothscroll",s),this}}),t.smoothScroll=function(e,o){if("options"===e&&"object"==typeof o)return t.extend(i,o);var l,s,n,c,a,r=0,h="offset",u="scrollTop",d={},p={};"number"==typeof e?(l=t.extend({link:null},t.fn.smoothScroll.defaults,i),n=e):(l=t.extend({link:null},t.fn.smoothScroll.defaults,e||{},i),l.scrollElement&&(h="position","static"===l.scrollElement.css("position")&&l.scrollElement.css("position","relative"))),u="left"===l.direction?"scrollLeft":u,l.scrollElement?(s=l.scrollElement,/^(?:HTML|BODY)$/.test(s[0].nodeName)||(r=s[u]())):s=t("html, body").firstScrollable(l.direction),l.beforeScroll.call(s,l),n="number"==typeof e?e:o||t(l.scrollTarget)[h]()&&t(l.scrollTarget)[h]()[l.direction]||0,d[u]=n+r+l.offset,c=l.speed,"auto"===c&&(a=Math.abs(d[u]-s[u]()),c=a/l.autoCoefficient),p={duration:c,easing:l.easing,complete:function(){l.afterScroll.call(l.link,l)}},l.step&&(p.step=l.step),s.length?s.stop().animate(d,p):l.afterScroll.call(l.link,l)},t.smoothScroll.version=o,t.smoothScroll.filterPath=function(t){return t=t||"",t.replace(/^\//,"").replace(/(?:index|default).[a-zA-Z]{3,4}$/,"").replace(/\/$/,"")},t.fn.smoothScroll.defaults=l}),jQuery.cookie=function(t,e,o){if(arguments.length>1&&"[object Object]"!==String(e)){if(o=jQuery.extend({},o),(null===e||void 0===e)&&(o.expires=-1),"number"==typeof o.expires){var i=o.expires,l=o.expires=new Date;l.setDate(l.getDate()+i)}return e=String(e),document.cookie=[encodeURIComponent(t),"=",o.raw?e:encodeURIComponent(e),o.expires?"; expires="+o.expires.toUTCString():"",o.path?"; path="+o.path:"",o.domain?"; domain="+o.domain:"",o.secure?"; secure":""].join("")}o=e||{};var s,n=o.raw?function(t){return t}:decodeURIComponent;return(s=new RegExp("(?:^|; )"+encodeURIComponent(t)+"=([^;]*)").exec(document.cookie))?n(s[1]):null},jQuery(document).ready(function(t){if("undefined"!=typeof tocplus){if(t.fn.shrinkTOCWidth=function(){t(this).css({width:"auto",display:"table"}),/MSIE 7\./.test(navigator.userAgent)&&t(this).css("width","")},1==tocplus.smooth_scroll){var e=hostname=pathname=qs=hash=null;t("body a").click(function(){if(hostname=t(this).prop("hostname"),pathname=t(this).prop("pathname"),qs=t(this).prop("search"),hash=t(this).prop("hash"),pathname.length>0&&"/"!=pathname.charAt(0)&&(pathname="/"+pathname),window.location.hostname==hostname&&window.location.pathname==pathname&&window.location.search==qs&&""!==hash){var o=hash.replace(/([ !"$%&'()*+,.\/:;<=>?@[\]^`{|}~])/g,"\\$1");t(o).length>0?e=hash:(anchor=hash,anchor=anchor.replace("#",""),e='a[name="'+anchor+'"]',0==t(e).length&&(e="")),offset="undefined"!=typeof tocplus.smooth_scroll_offset?-1*tocplus.smooth_scroll_offset:t("#wpadminbar").length>0&&t("#wpadminbar").is(":visible")?-30:0,e&&t.smoothScroll({scrollTarget:e,offset:offset})}})}if("undefined"!=typeof tocplus.visibility_show){var o="undefined"!=typeof tocplus.visibility_hide_by_default?!0:!1;if(t.cookie)var i=t.cookie("tocplus_hidetoc")?tocplus.visibility_show:tocplus.visibility_hide;else var i=tocplus.visibility_hide;o&&(i=i==tocplus.visibility_hide?tocplus.visibility_show:tocplus.visibility_hide),t("#toc_container p.toc_title").append(' <span class="toc_toggle">[<a href="#">'+i+"</a>]</span>"),i==tocplus.visibility_show&&(t("ul.toc_list").hide(),t("#toc_container").addClass("contracted").shrinkTOCWidth()),t("span.toc_toggle a").click(function(e){switch(e.preventDefault(),t(this).html()){case t("<div/>").html(tocplus.visibility_hide).text():t(this).html(tocplus.visibility_show),t.cookie&&(o?t.cookie("tocplus_hidetoc",null,{path:"/"}):t.cookie("tocplus_hidetoc","1",{expires:30,path:"/"})),t("ul.toc_list").hide("fast"),t("#toc_container").addClass("contracted").shrinkTOCWidth();break;case t("<div/>").html(tocplus.visibility_show).text():default:t(this).html(tocplus.visibility_hide),t.cookie&&(o?t.cookie("tocplus_hidetoc","1",{expires:30,path:"/"}):t.cookie("tocplus_hidetoc",null,{path:"/"})),t("#toc_container").css("width",tocplus.width).removeClass("contracted"),t("ul.toc_list").show("fast")}})}}});</script>
			<?php
		}
		
			
		
		
		/**
		 * Tries to convert $string into a valid hex colour.
		 * Returns $default if $string is not a hex value, otherwise returns verified hex.
		 */
		private function hex_value( $string = '', $default = '#' )
		{
			$return = $default;
			
			if ( $string ) {
				// strip out non hex chars
				$return = preg_replace( '/[^a-fA-F0-9]*/', '', $string );
				
				switch ( strlen($return) ) {
					case 3:	// do next
					case 6:
						$return = '#' . $return;
						break;
					
					default:
						if ( strlen($return) > 6 )
							$return = '#' . substr($return, 0, 6);	// if > 6 chars, then take the first 6
						elseif ( strlen($return) > 3 && strlen($return) < 6 )
							$return = '#' . substr($return, 0, 3);	// if between 3 and 6, then take first 3
						else
							$return = $default;						// not valid, return $default
				}
			}
			
			return $return;
		}
		
		
		
		
		
							
		function wp_head(){
			global $caia_detected_device;

			$css = '';
						

			if ( !$this->options['exclude_css'] ) {
				if ( $this->options['theme'] == TOC_THEME_CUSTOM || $this->options['width'] != 'Auto' ) {
					$css .= 'div#toc_container {';
					if ( $this->options['theme'] == TOC_THEME_CUSTOM )
						$css .= 'background: ' . $this->options['custom_background_colour'] . ';border: 1px solid ' . $this->options['custom_border_colour'] . ';';
					if ( $this->options['width'] != 'Auto' ) {
						$css .= 'width: ';
						if (isset($caia_detected_device) && $caia_detected_device === 'Mobile'){
							$css .= '92%';
							$this->options['wrapping'] = TOC_WRAPPING_CENTER;
						}else{
							if ( $this->options['width'] != 'User defined' )
								$css .= $this->options['width'];
							else
								$css .= $this->options['width_custom'] . $this->options['width_custom_units'];	
						}
						
						$css .= ';';
					}
					if (isset($caia_detected_device) && $caia_detected_device === 'Mobile'){
						$css .= 'margin:auto';
					}
					$css .= '}';
				}
				
				if ( '95%' != $this->options['font_size'] . $this->options['font_size_units'] )
					$css .= 'div#toc_container ul li {font-size: ' . $this->options['font_size'] . $this->options['font_size_units'] . ';}';
	
				if ( $this->options['theme'] == TOC_THEME_CUSTOM ) {
					if ( $this->options['custom_title_colour'] != TOC_DEFAULT_TITLE_COLOUR )
						$css .= 'div#toc_container p.toc_title {color: ' . $this->options['custom_title_colour'] . ';}';
					if ( $this->options['custom_links_colour'] != TOC_DEFAULT_LINKS_COLOUR )
						$css .= 'div#toc_container p.toc_title a,div#toc_container ul.toc_list a {color: ' . $this->options['custom_links_colour'] . ';}';
					if ( $this->options['custom_links_hover_colour'] != TOC_DEFAULT_LINKS_HOVER_COLOUR )
						$css .= 'div#toc_container p.toc_title a:hover,div#toc_container ul.toc_list a:hover {color: ' . $this->options['custom_links_hover_colour'] . ';}';
					if ( $this->options['custom_links_hover_colour'] != TOC_DEFAULT_LINKS_HOVER_COLOUR )
						$css .= 'div#toc_container p.toc_title a:hover,div#toc_container ul.toc_list a:hover {color: ' . $this->options['custom_links_hover_colour'] . ';}';
					if ( $this->options['custom_links_visited_colour'] != TOC_DEFAULT_LINKS_VISITED_COLOUR )
						$css .= 'div#toc_container p.toc_title a:visited,div#toc_container ul.toc_list a:visited {color: ' . $this->options['custom_links_visited_colour'] . ';}';
				}
			}
			
			if ( $css )
				echo '<style type="text/css">' . $css . '</style>';
		}
		
		
		/**
		 * Returns a clean url to be used as the destination anchor target
		 */
		private function url_anchor_target( $title )
		{
			$return = false;
			
			if ( $title ) {
				
				$return = strtolower( trim( strip_tags($title) ) );

				// convert accented characters to ASCII 
				$return = remove_accents( $return );				
								
				// replace newlines with spaces (eg when headings are split over multiple lines)
				$return = str_replace( array("\r", "\n", "\n\r", "\r\n"), ' ', $return );
				$return = trim($return);
				
				// remove &amp;
				$return = str_replace( '&amp;', '', $return );
				
				// remove non alphanumeric chars
				$return = preg_replace( '/[^a-zA-Z0-9 \-_]*/', '', $return );

				// lay key trong title neu co
				$keys = array('tai sao', 'nguyen nhan', 'trieu chung', 'dau hieu', 'dac diem', 'quy trinh', 'giai phap', 'phuong phap', 'cach thuc', 'cach tri', 'cach chua', 
					'loi ich', 'chi phi', 'vi sao');
				
				$found = false;
				foreach ($keys as $key) {					
					if (strpos($return, $key) !== false){
						$found = $key;
						break;
					}
				}

				if ($found){
					$return = $found;
				}else{
					$pos_space = strpos($return, ' ');
					if ($pos_space){
						$return = substr($return, 0, $pos_space);	
					}
					
				}

				// echo $return . '-';
				
				// convert spaces to _
				$return = str_replace(
					array('  ', ' '),
					'-',
					$return
				);
				
				// remove trailing - and _
				$return = rtrim( $return, '-_' );
				
				
				// if blank, then prepend with the fragment prefix
				// blank anchors normally appear on sites that don't use the latin charset
				if ( !$return ) {
					$return = ( $this->options['fragment_prefix'] ) ? $this->options['fragment_prefix'] : '_';
				}
				
				// hyphenate?
				if ( $this->options['hyphenate'] ) {
					$return = str_replace('_', '-', $return);
					$return = str_replace('--', '-', $return);
				}
			}
			
			if ( array_key_exists($return, $this->collision_collector) ) {
				$this->collision_collector[$return]++;
				$return .= '-' . $this->collision_collector[$return];
			}
			else
				$this->collision_collector[$return] = 1;
			
			return apply_filters( 'toc_url_anchor_target', $return );			
		}
		

		private function build_hierarchy( &$matches )
		{
			$current_depth = 100;	// headings can't be larger than h6 but 100 as a default to be sure
			$html = '';
			$numbered_items = array();
			$numbered_items_min = null;
			
			// reset the internal collision collection
			$this->collision_collector = array();
			
			// find the minimum heading to establish our baseline
			for ($i = 0; $i < count($matches); $i++) {
				if ( $current_depth > $matches[$i][2] )
					$current_depth = (int)$matches[$i][2];
			}
			
			$numbered_items[$current_depth] = 0;
			$numbered_items_min = $current_depth;

			for ($i = 0; $i < count($matches); $i++) {

				if ( $current_depth == (int)$matches[$i][2] )
					$html .= '<li>';
			
				// start lists
				if ( $current_depth != (int)$matches[$i][2] ) {
					for ($current_depth; $current_depth < (int)$matches[$i][2]; $current_depth++) {
						$numbered_items[$current_depth + 1] = 0;
						$html .= '<ul><li>';
					}
				}
				
				// list item
				if ( in_array($matches[$i][2], $this->options['heading_levels']) ) {
					$html .= '<a href="#' . $this->url_anchor_target( $matches[$i][0]) . '">';
					if ( $this->options['ordered_list'] ) {
						// attach leading numbers when lower in hierarchy
						$html .= '<span class="toc_number toc_depth_' . ($current_depth - $numbered_items_min + 1) . '">';
						for ($j = $numbered_items_min; $j < $current_depth; $j++) {
							$number = ($numbered_items[$j]) ? $numbered_items[$j] : 0;
							$html .= $number . '.';
						}
						
						$html .= ($numbered_items[$current_depth] + 1) . '.</span> ';
						$numbered_items[$current_depth]++;
					}
					$html .= strip_tags($matches[$i][0]) . '</a>';
				}
				
				
				// end lists
				if ( $i != count($matches) - 1 ) {
					if ( $current_depth > (int)$matches[$i + 1][2] ) {
						for ($current_depth; $current_depth > (int)$matches[$i + 1][2]; $current_depth--) {
							$html .= '</li></ul>';
							$numbered_items[$current_depth] = 0;
						}
					}
					
					if ( $current_depth == (int)@$matches[$i + 1][2] )
						$html .= '</li>';
				}
				else {
					// this is the last item, make sure we close off all tags
					for ($current_depth; $current_depth >= $numbered_items_min; $current_depth--) {
						$html .= '</li>';
						if ( $current_depth != $numbered_items_min ) $html .= '</ul>';
					}
				}
			}

			return $html;
		}
		
		
		/**
		 * Returns a string with all items from the $find array replaced with their matching
		 * items in the $replace array.  This does a one to one replacement (rather than
		 * globally).
		 *
		 * This function is multibyte safe.
		 *
		 * $find and $replace are arrays, $string is the haystack.  All variables are
		 * passed by reference.
		 */
		private function mb_find_replace( &$find = false, &$replace = false, &$string = '' )
		{
			if ( is_array($find) && is_array($replace) && $string ) {
				// check if multibyte strings are supported
				if ( function_exists( 'mb_strpos' ) ) {
					for ($i = 0; $i < count($find); $i++) {
						$string = 
							mb_substr( $string, 0, mb_strpos($string, $find[$i]) ) .	// everything befor $find
							$replace[$i] .												// its replacement
							mb_substr( $string, mb_strpos($string, $find[$i]) + mb_strlen($find[$i]) )	// everything after $find
						;
					}
				}
				else {
					for ($i = 0; $i < count($find); $i++) {
						$string = substr_replace(
							$string,
							$replace[$i],
							strpos($string, $find[$i]),
							strlen($find[$i])
						);
					}
				}
			}
			
			return $string;
		}
		
		
		/**
		 * This function extracts headings from the html formatted $content.  It will pull out
		 * only the required headings as specified in the options.  For all qualifying headings,
		 * this function populates the $find and $replace arrays (both passed by reference)
		 * with what to search and replace with.
		 * 
		 * Returns a html formatted string of list items for each qualifying heading.  This 
		 * is everything between and NOT including <ul> and </ul>
		 */
		public function extract_headings( &$find, &$replace, $content = '' )
		{
			$matches = array();
			$anchor = '';
			$items = false;

			// reset the internal collision collection as the_content may have been triggered elsewhere
			// eg by themes or other plugins that need to read in content such as metadata fields in
			// the head html tag, or to provide descriptions to twitter/facebook
			$this->collision_collector = array();
			
			if ( is_array($find) && is_array($replace) && $content ) {
				// get all headings
				// the html spec allows for a maximum of 6 heading depths
				$heading_str = implode('', $this->options['heading_levels']);
				if ( preg_match_all('/(<h([' . $heading_str . ']{1})[^>]*>).*<\/h\2>/msuU', $content, $matches, PREG_SET_ORDER) ) {

					// remove undesired headings (if any) as defined by heading_levels
					if ( count($this->options['heading_levels']) != 6 ) {
						$new_matches = array();
						for ($i = 0; $i < count($matches); $i++) {
							if ( in_array($matches[$i][2], $this->options['heading_levels']) )
								$new_matches[] = $matches[$i];
						}
						$matches = $new_matches;
					}

					// remove specific headings if provided via the 'exclude' property
					if ( $this->options['exclude'] ) {
						$excluded_headings = explode('|', $this->options['exclude']);
						if ( count($excluded_headings) > 0 ) {
							for ($j = 0; $j < count($excluded_headings); $j++) {
								// escape some regular expression characters
								// others: http://www.php.net/manual/en/regexp.reference.meta.php
								$excluded_headings[$j] = str_replace(
									array('*'), 
									array('.*'), 
									trim($excluded_headings[$j])
								);
							}
	
							$new_matches = array();
							for ($i = 0; $i < count($matches); $i++) {
								$found = false;
								for ($j = 0; $j < count($excluded_headings); $j++) {
									if ( @preg_match('/^' . $excluded_headings[$j] . '$/imU', strip_tags($matches[$i][0])) ) {
										$found = true;
										break;
									}
								}
								if (!$found) $new_matches[] = $matches[$i];
							}
							if ( count($matches) != count($new_matches) )
								$matches = $new_matches;
						}
					}

					// remove empty headings
					$new_matches = array();
					for ($i = 0; $i < count($matches); $i++) {
						if ( trim( strip_tags($matches[$i][0]) ) != false )
							$new_matches[] = $matches[$i];
					}
					if ( count($matches) != count($new_matches) )
						$matches = $new_matches;

					// check minimum number of headings
					if ( count($matches) >= $this->options['start'] ) {						
						for ($i = 0; $i < count($matches); $i++) {
							// get anchor and add to find and replace arrays
							$anchor = $this->url_anchor_target( $matches[$i][0] );							
							$find[] = $matches[$i][0];
							$replace[] = str_replace(
								array(
									$matches[$i][1],				// start of heading
									'</h' . $matches[$i][2] . '>'	// end of heading
								),
								array(
									$matches[$i][1] . '<span id="' . $anchor . '">',
									'</span></h' . $matches[$i][2] . '>'
								),
								$matches[$i][0]
							);

							// assemble flat list
							if ( !$this->options['show_heirarchy'] ) {
								$items .= '<li><a href="#' . $anchor . '">';
								if ( $this->options['ordered_list'] ) $items .= count($replace) . ' ';
								$items .= strip_tags($matches[$i][0]) . '</a></li>';
							}
						}

						// build a hierarchical toc?
						// we could have tested for $items but that var can be quite large in some cases
						if ( $this->options['show_heirarchy'] ) $items = $this->build_hierarchy( $matches );
						
					}
				}
			}
			
			return $items;
		}
		
		
		/**
		 * Returns true if the table of contents is eligible to be printed, false otherwise.
		 */
		public function is_eligible( $shortcode_used = false )
		{
			global $post;

			return $shortcode_used;

			// do not trigger the TOC when displaying an XML/RSS feed
			if ( is_feed() ) return false;
			
			// if the shortcode was used, this bypasses many of the global options
			if ( $shortcode_used !== false ) {
				// shortcode is used, make sure it adheres to the exclude from 
				// homepage option if we're on the homepage
				if ( !$this->options['include_homepage'] && is_front_page() )
					return false;
				else
					return true;
			}
			else {
				if (
					( in_array(get_post_type($post), $this->options['auto_insert_post_types']) && $this->show_toc && !is_search() && !is_archive() && !is_front_page() ) || 
					( $this->options['include_homepage'] && is_front_page() )
				) {
					if ( $this->options['restrict_path'] ) {
						if ( strpos($_SERVER['REQUEST_URI'], $this->options['restrict_path']) === 0 )
							return true;
						else
							return false;
					}
					else
						return true;
				}
				else
					return false;
			}
		}
		
		
		function the_content( $content )
		{
			global $post;
			
			// caia_log('toc', 'the_content', substr($content, 0, 30));

			$items = $css_classes = $anchor = '';
			$custom_toc_position = strpos($content, '<!--TOC-->');
			$find = $replace = array();

			// if ( $this->is_eligible($custom_toc_position) !== false ) {
			 if ( $custom_toc_position !== false ) {
				
				$items = $this->extract_headings($find, $replace, $content);

				if ( $items ) {
					// do we display the toc within the content or has the user opted
					// to only show it in the widget?  if so, then we still need to 
					// make the find/replace call to insert the anchors
					// wrapping css classes
					switch( $this->options['wrapping'] ) {
						case TOC_WRAPPING_LEFT:
							$css_classes .= ' toc_wrap_left';
							break;
							
						case TOC_WRAPPING_RIGHT:
							$css_classes .= ' toc_wrap_right';
							break;

						case TOC_WRAPPING_CENTER:
							$css_classes .= ' toc_wrap_center';
							break;

						case TOC_WRAPPING_NONE:
						default:
							// do nothing
					}
					
					// colour themes
					switch ( $this->options['theme'] ) {
						case TOC_THEME_LIGHT_BLUE:
							$css_classes .= ' toc_light_blue';
							break;
						
						case TOC_THEME_WHITE:
							$css_classes .= ' toc_white';
							break;
							
						case TOC_THEME_BLACK:
							$css_classes .= ' toc_black';
							break;
						
						case TOC_THEME_TRANSPARENT:
							$css_classes .= ' toc_transparent';
							break;
					
						case TOC_THEME_GREY:
						default:
							// do nothing
					}
					
					// bullets?
					if ( $this->options['bullet_spacing'] )
						$css_classes .= ' have_bullets';
					else
						$css_classes .= ' no_bullets';
					
					if ( $this->options['css_container_class'] ) $css_classes .= ' ' . $this->options['css_container_class'];

					$css_classes = trim($css_classes);
					
					// an empty class="" is invalid markup!
					if ( !$css_classes ) $css_classes = ' ';
					
					// add container, toc title and list items
					$html = '<div id="toc_container" class="' . $css_classes . '">';
					if ( $this->options['show_heading_text'] ) {
						// fix đổi tiêu đề mục lục
						if (!empty(caia_get_option( 'title_toc' ))) {
							$toc_title =  caia_get_option( 'title_toc' );
						}else{
							$toc_title = $this->options['heading_text'];
						}

						//$toc_title =  caia_get_option( 'title_toc' );
						
						if ( strpos($toc_title, '%PAGE_TITLE%') !== false ) $toc_title = str_replace( '%PAGE_TITLE%', get_the_title(), $toc_title );
						if ( strpos($toc_title, '%PAGE_NAME%') !== false ) $toc_title = str_replace( '%PAGE_NAME%', get_the_title(), $toc_title );
						$html .= '<p class="toc_title">' . htmlentities( $toc_title, ENT_COMPAT, 'UTF-8' ) . '</p>';
					}
					$html .= '<ul class="toc_list">' . $items . '</ul></div>' . "\n";
					
					if ( $custom_toc_position !== false ) {
						$find[] = '<!--TOC-->';
						$replace[] = $html;
						$content = $this->mb_find_replace($find, $replace, $content);
						$this->toc_showed = true;
					}									
				}
			}					
			return $content;
		}
		
	} // end class Toc --------


	// do the magic
	$toc = new toc();
endif;





