<?php

define('CAIA_SOCIAL_VERSION', '4.7');

/*
- 01/10/19: bổ sung hỗ trợ chuyển like share khi đổi sang https, dùng CAIA_GET_LIKE_FROM_NON_HTTPS
- 19/08/20: tối ưu fb chat ở mobile, chỉ hiện thị icon
- 10/06/21: style các nút đều nhau
- 09/08/21: bỏ fb sdk nếu trang đó ko sử dụng các module liên quan FB.
- 24/11/21: nâng cấp fb version từ 3.3 lên 12.0
- 09/12/21: bổ sung filter caia_social_fb_chat_mobile và caia_social_fb_chat_pc
- 31/03/2022: Thêm nút share twitter
- 01/10/2022: Bỏ nút like, nâng cấp nút chia sẻ Facebook và thêm số lượt chia sẻ tuỳ chỉnh
- 12/10/2022: Thêm chức năng khi chuyển đổi chia sẻ Facebook sẽ lấy số lượt share hiện tại và update metabox
- 01/11/2022: tinh chỉnh chút ở hàm lấy số share hiện có
*/

if (!class_exists('Caia_Social_Share')){	

	
	class Caia_Social{
		public $is_run = false;
		function __construct(){

			add_action( 'init', array($this, 'init') );
			
		}

		function init(){
			$share_pri = apply_filters( 'caia_social_share_bottom', 20 );
			if ($share_pri){
				add_filter('the_content', array($this, 'add_native_share_button_at_bottom'), $share_pri);	
			}
			

			$old_script_pri = has_action('wp_footer', 'vi_fbmlsetting');
			if ( $old_script_pri !== false ){
				remove_action( 'wp_footer', 'vi_fbmlsetting', $old_script_pri );
			}

			add_action('wp_footer', array($this, 'add_fb_chat'));

			add_action('wp_footer', array($this, 'add_scripts'));

			// theme setting
			add_action( 'caia_settings_metaboxes', array($this, 'add_theme_settings_boxes') );
			
			add_shortcode( 'caia_social_share',  array($this, 'shortcode_social_share') );

			$share_facebook = caia_get_option( 'share_facebook' );
			if ($share_facebook){
				add_filter( 'rwmb_meta_boxes', array($this, 'caia_register_meta_boxes_social') );
				add_action('wp_footer', array($this, 'add_fb_share'));
				add_action( 'wp_ajax_nopriv_caia_update_facebook_share', array($this, 'caia_update_facebook_share') );
				add_action( 'wp_ajax_caia_update_facebook_share', array($this, 'caia_update_facebook_share') );
				
			}
		}

		function caia_register_meta_boxes_social( $meta_boxes ){
			$prefix = '';
			$meta_boxes[] = array(
				'title'      => esc_html__( 'Chia sẻ Facebook', 'caia' ),
				'post_types' => array( 'post', 'page', 'fitwp_question' ),
				'context'    => 'normal',
				'priority'   => 'high',
				'autosave'   => true,
				'fields'     => array(
					array(				
						'name'  => esc_html__( 'Lượt chia sẻ', 'caia' ),				
						'id'    => "{$prefix}countsharefb",								
						'type'  => 'text',
						'size' => 5,
					),
				),
			);
			return $meta_boxes;			
		}

		function add_theme_settings_boxes( $pagehook ){
			add_meta_box( 'caia-social', __( 'Tích hợp Mạng Xã Hội', 'caia' ), array($this, 'options_admin'), $pagehook, 'main' );
		}

		function options_admin(){
			$cs_field = CAIA_SETTINGS_FIELD;

			$share_facebook = caia_get_option( 'share_facebook' );
			$share_twitter = caia_get_option( 'share_twitter' );
			$share_linkedin = caia_get_option( 'share_linkedin' );
			$share_pinterest = caia_get_option( 'share_pinterest' );
			$enable_fb_chat = caia_get_option( 'enable_fb_chat' );
			$fanpage_id = caia_get_option( 'fanpage_id' );
			$fb_chat_color = caia_get_option( 'fb_chat_color' );
			$fb_chat_greeting = caia_get_option( 'fb_chat_greeting' );			

			echo '<table class="form-table">';
			
			echo '<tr>';
			echo '<th>Hỗ trợ nút chia sẻ ở MXH</th>';
			echo '<td>';	
			echo "<input type='checkbox' name='{$cs_field}[share_facebook]' value='1' " . checked('1', $share_facebook, false) . '> Facebook Share &nbsp;&nbsp;&nbsp;&nbsp;';	
			echo "<input type='checkbox' name='{$cs_field}[share_twitter]' value='1' " . checked('1', $share_twitter, false) . '> Twitter &nbsp;&nbsp;&nbsp;&nbsp;';			
			echo "<input type='checkbox' name='{$cs_field}[share_linkedin]' value='1' " . checked('1', $share_linkedin, false) . '> Linkedin &nbsp;&nbsp;&nbsp;&nbsp;';
			echo "<input type='checkbox' name='{$cs_field}[share_pinterest]' value='1' " . checked('1', $share_pinterest, false) . '> Pinterest';	
			echo '<p class="description">Riêng Like&Share của FB là ngầm định (Chọn Facebook Share hiển thị giao diện mới).</p>';				
			echo '</td>';
			echo '</tr>';

			echo '<tr>';
			echo '<th>Hỗ trợ Chat FB</th>';
			echo '<td>';						
			echo "<input type='checkbox' name='{$cs_field}[enable_fb_chat]' value='1' " . checked('1', $enable_fb_chat, false) . '> bật chat FB <br>';			
			echo '</td>';
			echo '</tr>';
			
			echo '<tr>';
			echo '<th> - Chat FB > Page ID</th>';
			echo '<td>';									
			echo "<input name='{$cs_field}[fanpage_id]' type='text' value='{$fanpage_id}' size='60'>";
			echo '<p class="description">ex: 466761795321, truy cập Fanpage > About > Page ID (ở cuối trang).</p>';			
			echo '</td>';
			echo '</tr>';

			echo '<tr>';
			echo '<th> - Chat FB > Tông màu</th>';
			echo '<td>';									
			echo "<input name='{$cs_field}[fb_chat_color]' type='text' value='{$fb_chat_color}' size='60'>";
			echo '<p class="description">ex: #365899, nên chọn theo tông màu chủ đạo Website.</p>';			
			echo '</td>';
			echo '</tr>';

			echo '<tr>';
			echo '<th> - Chat FB > Lời chào</th>';
			echo '<td>';									
			echo "<input name='{$cs_field}[fb_chat_greeting]' type='text' value='{$fb_chat_greeting}' size='60'>";
			echo '<p class="description"> ex: Hi, liệu Caia.vn có thể giúp gì cho bạn?</p>';			
			echo '</td>';
			echo '</tr>';		
			

			echo '</table>';
		}

		function add_native_share_button_at_bottom($content){
			global $caia_social;
			$caia_social->is_run = true;		
			// ngam dinh add like, share va pinterest
			$social_share = $this->get_native_share_button();

			return $content . $social_share;
			
		}

		function shortcode_social_share($args, $content) {
			global $caia_detected_device, $caia_social;
			$caia_social->is_run = true;

			if ( isset($caia_detected_device) && ( $caia_detected_device == 'Mobile') ){
				//return $this->get_native_share_button_mobile();
				return $this->get_native_share_button();
			}else{
				return $this->get_native_share_button();
			}
		}

		function get_api_share_facebook( $url ){
			$access_token = '793773438562036|0392694a96f88dc41ddf6830c0b2e7ae';
			$response = wp_remote_get( add_query_arg( 
							array( 
							  'id' => urlencode( $url ),
							  'access_token' => $access_token,
							  'fields' => 'engagement'
							),  
							'https://graph.facebook.com/v10.0/' ) );

			if( $response instanceof WP_Error ){
				return 0;
			}else{
				$body = json_decode( $response['body'] );
				$count = intval( $body->engagement->share_count );
				if ($count > 1000){
				  $count = 945;
				} else if ($count < 100) 
				  $count = 2*$count;     
			return $count; 
			}
		}

		function get_native_share_button(){

			$share_facebook = caia_get_option( 'share_facebook' );
			$share_twitter = caia_get_option( 'share_twitter' );
			$share_linkedin = caia_get_option( 'share_linkedin' );
			$share_pinterest = caia_get_option( 'share_pinterest' );

			if ($share_facebook){
				
				global $post;
				$countsharefb = get_post_meta( $post->ID, 'countsharefb', true );

				if( $countsharefb === '' ){				
					$url_post = get_the_permalink( $post->ID );
					$countsharefb = $this->get_api_share_facebook( $url_post );
					update_post_meta( $post->ID, 'countsharefb', $countsharefb );	
					caia_log( 'facebook_api', 'api', $post->ID . ' > ' .  $countsharefb );
				}

				$social_share .= '<div class="caia-social-share">
					<div class="caia_social_button_bound"><a data-id="'.$post->ID.'" class="caia_social_button facebook" onClick="share_facebook();"><img style="filter: brightness(0) invert(1); width: 15px; height: 15px;border-radius: 2px;top: 3px; margin-left: 5px;position: absolute;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABmJLR0QA/wD/AP+gvaeTAAABhUlEQVRIid2VTStFURiF1+viTxBlbKAk4Sf4SJf4CYqZXMaKkrgTQxNC3RHFX5AyR/KZJAMDnwNF9zG492p3zj62w4g12r1n7bXWfvc+e0v/GkANMAAUgGPgFXgAToE1YBio+al4L3BCGGdANo2wAbNA8RviLhaAqu8YzKQUdjEXEu8JCDwCOaATaAI6gJsIpz9JvJZSP5NQBLo887YjvFOg1meQDaTfjYQZAZaAdw930GdQCBgsO9zJAHe9wnV3veXLDZJunXFzgNvqM2gITCo640yAWxerAE+epZ4BTUkqQCNw75n37FvBnUdjz8wukgzM7ErSi+fTZzurneK+pGjaQeBA0qWkIzM7LCdvV6mlbZLqPQb7sQowFDgZ0w53I8AdqnDdFm1JuvY3IxXOJW3GDMzsTdLYL8WRNG5m7zGDssmOpPwvDPJmtu0WfNfrhKTFcpo0mJeUixZjBmaGmU1I6pOUeEQdXErqNrMpM0sXCshQejJXgFGnPgesUrogQ3/1H8cHn++TUO71JIUAAAAASUVORK5CYII="> <span>Chia sẻ</span><strong class="count_share">'.$countsharefb.'</strong></a></div>';
			}else{

				if (defined('CAIA_GET_LIKE_FROM_NON_HTTPS') && CAIA_GET_LIKE_FROM_NON_HTTPS){
					$myurl = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; 
					$social_share = '<div class="caia-social-share">
						<div class="fb-like" data-width="" data-layout="button" ' . "data-href='{$myurl}'" . ' data-action="like" data-size="small" data-show-faces="false" data-share="false"></div>
						<div class="fb-share-button" data-layout="button_count" ' . "data-href='{$myurl}'" . ' data-size="small"><a target="_blank" class="fb-xfbml-parse-ignore">Chia sẻ</a></div>';
				}else{
					$social_share = '<div class="caia-social-share">
						<div class="fb-like" data-width="" data-layout="button" data-action="like" data-size="small" data-show-faces="false" data-share="false"></div>
						<div class="fb-share-button" data-layout="button_count" data-size="small"><a target="_blank" class="fb-xfbml-parse-ignore">Chia sẻ</a></div>';	
				}
			}
			
			if ($share_twitter){
				global $wp;
				$myurl = home_url( $wp->request );
				$social_share .= '<div class="caia_social_button_bound"><a class="caia_social_button twitter" onClick="share_twitter();"><img style="filter: brightness(0) invert(1); width: 16px; height: 15px;border-radius: 2px;top: 3px; margin-left: 5px;position: absolute;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAABmJLR0QA/wD/AP+gvaeTAAADz0lEQVRoge2XTWgdVRiGn+/MpGnv3LRoqC4UhCz8I8RFRClSXdgqWkuV4CRpi1gqglZTuhFXkiDdCVIKBfEHo1Bzr0JRtGAqWhTBQhsaFX9ADbZisbXaJnPb5t6Z+VyotU0yc89MYlGYZzcz73fO+84ZzjcHCgoKCgoK/sfIJZ2tOnmdq+ZxYLXCNUCowvdG9b3QNbvo8Y7NLFm0O+iqry9/njRk0wDOSO2e6JvS+wxKnNv4oBr3hmBIkacBN8FKgLIt6vNe4u0Tbc65xXch8gSxfBn1eU/mC1A9Xna09JPAcNhb3prXvzMy9QIij1rKDwFdQAtwLJKoE3/Zb0likzqxercDyxQGnJHaLqrqWLv+e4xqsD6DeYBu/jR/0gj30lB13wjuTBKnBlCJO85fiD7maG0P1dOXW1tRFWJ91lr/DyHI/ljZ4bjud8bheJIwNQA66/laR51xtxqstnHRUjlzMyIdzZWzcEF7gFsQ2VT3y18kCVMDCGZijttXqzLqVKb28Nbk9Wn1Mdpp53dOfhXiVZHvvZMmSg0QLT6zHySY+6nc70TmK6cSvOtUamvZq60zFSralsXxDAbC3qWfNhMlbGl/sW75lFSCVxQGEhQCrAFd40zVJqnU9in6mREdC018VCJJ3D2aojptI2veyF5Tz22tjSlcm9tMDkS5O+wrjzbTpW+jI8EGZ1GtR2LpUxhfOHvNMYZZXXkuUj8hFW0XZEcsisDZhbFmhdbPeT/YCNNXQOTjCy6XzMtSBgS+5iGp2WhTAzT88mGEAwtjyx5FP7HVpjcywCBbgMa8HGXGpO79FymbCRq+dwjVDVy6ECcjKX1gK24aACDqa3vTqNyGcDC/LztEeRFf6rZ6qwAAsXAVyCDKq4BVk8nB2VBlZ5aC9E58MatQ3fJvnuEEdtLv/ZylxnoFIomHgF8yu7LnSNiY3p61yDoAftsJE5t1wO9ZJ7EgFuERNrZPZi20DwA0+ksHIie6Ffgw60RpqMhQ6Jf35anN/UW3VGvdkXKfiK5ESTzyWTAc+d4mRDRPcaYVuJDGg6UxgaMo3XnHAIYj8TbnNQ95VmCvtjpBrV9hmyhdOedVRZ6J/dL2+ZiHlAAtI8FNGHFVY0+FK1SlQ+AOYCUwn5PWhKCbw962j+YxxnkS+0CjtfGjW295SpGtKKUF2P5PK/JcPF163vZP04bmvnYHVxqHjcDDApkP6QrjqL4ct4av88Blp/KYTCPbi61O3ehgVhDrCgydxLockXZgKXAKZRLDBKrfouZgZMwo/pIjC226oKCgoKCg4L/CH/2pQ2x506tNAAAAAElFTkSuQmCC"> <span>Tweet</span></a></div>';
			}	

			if ($share_linkedin){
				$social_share .= '<div class="caia_social_button_bound"><a class="caia_social_button" onClick="share_linkedin();"><img style=" width: 13px; height: 13px;background-color: #fff;border-radius: 2px;top: 3px; margin-left: 5px;position: absolute;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEwAACxMBAJqcGAAAAVVJREFUSIntlD9LgmEUxc95FKJCJKda3WpurCFoiMCaaijaW82i2SUQrKWoKSjoA4hO0ReIoEBojaB/NFakRsY9DaGvr0JJLxKEZ7vnPtzfhcN9gK5+EJsNt5pfgJAUeab+ShLp+fcggLCvWj+Jolo+hHNhAqN87bkwYD8IwPmqclWAM6/rFGR4K2B7+gW0JQCngnasr3IUFNBx+UNO5Uao0Ea96XBs2cSeWyksCpir+XLKhMgBM02BGgJ4pRB2kZm5aQb4Q5aL0WG2XgqPACBimPR8QuMSYyTrO7KqZUsVxrCZuGwc6c+gbblYq8Uoia0W+3cAAKZnANeNFmGTWMtFAgME5S0SGbRsIi7YccM44iMUDwxw4AHSE29fNN77u9YbGACTd4Dkt8f4+wzaVBfw9wD/VwErASzWKoJ3AkDoAdB5/Rndk/dGt4CK3oxwqXPr/kt9Akx/aitLlhc/AAAAAElFTkSuQmCC"> <span>Chia sẻ</span></a></div>';
			}			
			
			if ($share_pinterest){
				$social_share .= '<div class="caia_social_button_bound"><a data-pin-do="buttonBookmark" data-pin-tall="false" data-pin-lang="vi" href="https://www.pinterest.com/pin/create/button/" ></a></div>';
			}
					

			$social_share .= '</div><div style="clear:both;"></div>';

			if (!has_action('wp_footer', array($this, 'add_css_share'), 1)){
			 	// chi them css 1 lan, va o page nao co social button thoi
			 	add_action('wp_footer', array($this, 'add_css_share'), 1);	
			}

			return apply_filters('caia_social_native_button', $social_share);
		}
		
		function get_native_share_button_mobile(){

			$share_facebook = caia_get_option( 'share_facebook' );
			$share_twitter = caia_get_option( 'share_twitter' );
			$share_linkedin = caia_get_option( 'share_linkedin' );
			$share_pinterest = caia_get_option( 'share_pinterest' );

			if ($share_facebook){
				global $post;
				$countsharefb = get_post_meta( $post->ID, 'countsharefb', true );
				
				if( $countsharefb === ''  ){				
					$url_post = get_the_permalink( $post->ID );
					$countsharefb = $this->get_api_share_facebook( $url_post );
					update_post_meta( $post->ID, 'countsharefb', $countsharefb );	
					//caia_log( 'facebook_api', 'api', $countsharefb );
				}

				$social_share .= '<div class="caia-social-share">
					<div class="caia_social_button_bound"><a data-id="'.$post->ID.'" class="caia_social_button facebook" onClick="share_facebook();"><img style="filter: brightness(0) invert(1); width: 15px; height: 15px;border-radius: 2px;top: 3px; margin-left: 5px;position: absolute;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABmJLR0QA/wD/AP+gvaeTAAABhUlEQVRIid2VTStFURiF1+viTxBlbKAk4Sf4SJf4CYqZXMaKkrgTQxNC3RHFX5AyR/KZJAMDnwNF9zG492p3zj62w4g12r1n7bXWfvc+e0v/GkANMAAUgGPgFXgAToE1YBio+al4L3BCGGdANo2wAbNA8RviLhaAqu8YzKQUdjEXEu8JCDwCOaATaAI6gJsIpz9JvJZSP5NQBLo887YjvFOg1meQDaTfjYQZAZaAdw930GdQCBgsO9zJAHe9wnV3veXLDZJunXFzgNvqM2gITCo640yAWxerAE+epZ4BTUkqQCNw75n37FvBnUdjz8wukgzM7ErSi+fTZzurneK+pGjaQeBA0qWkIzM7LCdvV6mlbZLqPQb7sQowFDgZ0w53I8AdqnDdFm1JuvY3IxXOJW3GDMzsTdLYL8WRNG5m7zGDssmOpPwvDPJmtu0WfNfrhKTFcpo0mJeUixZjBmaGmU1I6pOUeEQdXErqNrMpM0sXCshQejJXgFGnPgesUrogQ3/1H8cHn++TUO71JIUAAAAASUVORK5CYII="> <span>Chia sẻ</span><strong class="count_share">'.$countsharefb.'</strong></a></div>';
			}else{
				$social_share = '<div class="caia-social-share">
				<div class="fb-share-button one" data-layout="button_count" data-size="small"><a target="_blank" class="fb-xfbml-parse-ignore">Chia sẻ</a></div>
				<div class="open-social"></div><div class="hide"><div class="fb-like" data-width="" data-layout="button" data-action="like" data-size="small" data-show-faces="false" data-share="false"></div>';
			}

			if ($share_twitter){
				global $wp;
				$myurl = home_url( $wp->request );
				$social_share .= '<div class="caia_social_button_bound"><a class="caia_social_button twitter" onClick="share_twitter();"><img style="filter: brightness(0) invert(1); width: 16px; height: 15px;border-radius: 2px;top: 3px; margin-left: 5px;position: absolute;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAABmJLR0QA/wD/AP+gvaeTAAADz0lEQVRoge2XTWgdVRiGn+/MpGnv3LRoqC4UhCz8I8RFRClSXdgqWkuV4CRpi1gqglZTuhFXkiDdCVIKBfEHo1Bzr0JRtGAqWhTBQhsaFX9ADbZisbXaJnPb5t6Z+VyotU0yc89MYlGYZzcz73fO+84ZzjcHCgoKCgoK/sfIJZ2tOnmdq+ZxYLXCNUCowvdG9b3QNbvo8Y7NLFm0O+iqry9/njRk0wDOSO2e6JvS+wxKnNv4oBr3hmBIkacBN8FKgLIt6vNe4u0Tbc65xXch8gSxfBn1eU/mC1A9Xna09JPAcNhb3prXvzMy9QIij1rKDwFdQAtwLJKoE3/Zb0likzqxercDyxQGnJHaLqrqWLv+e4xqsD6DeYBu/jR/0gj30lB13wjuTBKnBlCJO85fiD7maG0P1dOXW1tRFWJ91lr/DyHI/ljZ4bjud8bheJIwNQA66/laR51xtxqstnHRUjlzMyIdzZWzcEF7gFsQ2VT3y18kCVMDCGZijttXqzLqVKb28Nbk9Wn1Mdpp53dOfhXiVZHvvZMmSg0QLT6zHySY+6nc70TmK6cSvOtUamvZq60zFSralsXxDAbC3qWfNhMlbGl/sW75lFSCVxQGEhQCrAFd40zVJqnU9in6mREdC018VCJJ3D2aojptI2veyF5Tz22tjSlcm9tMDkS5O+wrjzbTpW+jI8EGZ1GtR2LpUxhfOHvNMYZZXXkuUj8hFW0XZEcsisDZhbFmhdbPeT/YCNNXQOTjCy6XzMtSBgS+5iGp2WhTAzT88mGEAwtjyx5FP7HVpjcywCBbgMa8HGXGpO79FymbCRq+dwjVDVy6ECcjKX1gK24aACDqa3vTqNyGcDC/LztEeRFf6rZ6qwAAsXAVyCDKq4BVk8nB2VBlZ5aC9E58MatQ3fJvnuEEdtLv/ZylxnoFIomHgF8yu7LnSNiY3p61yDoAftsJE5t1wO9ZJ7EgFuERNrZPZi20DwA0+ksHIie6Ffgw60RpqMhQ6Jf35anN/UW3VGvdkXKfiK5ESTzyWTAc+d4mRDRPcaYVuJDGg6UxgaMo3XnHAIYj8TbnNQ95VmCvtjpBrV9hmyhdOedVRZ6J/dL2+ZiHlAAtI8FNGHFVY0+FK1SlQ+AOYCUwn5PWhKCbw962j+YxxnkS+0CjtfGjW295SpGtKKUF2P5PK/JcPF163vZP04bmvnYHVxqHjcDDApkP6QrjqL4ct4av88Blp/KYTCPbi61O3ehgVhDrCgydxLockXZgKXAKZRLDBKrfouZgZMwo/pIjC226oKCgoKCg4L/CH/2pQ2x506tNAAAAAElFTkSuQmCC"> <span>Tweet</span></a></div>';
			}

			if ($share_linkedin){
				$social_share .= '<div class="caia_social_button_bound"><a class="caia_social_button" onClick="share_linkedin();"><img style=" width: 13px; height: 13px;background-color: #fff;border-radius: 2px;top: 3px; margin-left: 5px;position: absolute;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEwAACxMBAJqcGAAAAVVJREFUSIntlD9LgmEUxc95FKJCJKda3WpurCFoiMCaaijaW82i2SUQrKWoKSjoA4hO0ReIoEBojaB/NFakRsY9DaGvr0JJLxKEZ7vnPtzfhcN9gK5+EJsNt5pfgJAUeab+ShLp+fcggLCvWj+Jolo+hHNhAqN87bkwYD8IwPmqclWAM6/rFGR4K2B7+gW0JQCngnasr3IUFNBx+UNO5Uao0Ea96XBs2cSeWyksCpir+XLKhMgBM02BGgJ4pRB2kZm5aQb4Q5aL0WG2XgqPACBimPR8QuMSYyTrO7KqZUsVxrCZuGwc6c+gbblYq8Uoia0W+3cAAKZnANeNFmGTWMtFAgME5S0SGbRsIi7YccM44iMUDwxw4AHSE29fNN77u9YbGACTd4Dkt8f4+wzaVBfw9wD/VwErASzWKoJ3AkDoAdB5/Rndk/dGt4CK3oxwqXPr/kt9Akx/aitLlhc/AAAAAElFTkSuQmCC"> <span>Chia sẻ</span></a></div>';
			}			
			
			if ($share_pinterest){
				$social_share .= '<div class="caia_social_button_bound"><a data-pin-do="buttonBookmark" data-pin-tall="false" data-pin-lang="vi" href="https://www.pinterest.com/pin/create/button/" ></a></div>';
			}
					

			$social_share .= '</div></div><div style="clear:both;"></div>';

			if (!has_action('wp_footer', array($this, 'add_css_share'), 1)){
			 	// chi them css 1 lan, va o page nao co social button thoi
			 	add_action('wp_footer', array($this, 'add_css_share'), 1);	
			}

			return apply_filters('caia_social_native_button', $social_share);
		}

		function add_fb_chat(){
			$enable_fb_chat = caia_get_option('enable_fb_chat');
			$fanpage_id = caia_get_option('fanpage_id');			
			

			if ($enable_fb_chat && $fanpage_id){
				global $caia_social;
				$caia_social->is_run = true;

				$fb_chat_color = caia_get_option( 'fb_chat_color' );
				$fb_chat_greeting = caia_get_option( 'fb_chat_greeting' );	
				$theme_color = $fb_chat_color ? "theme_color='{$fb_chat_color}'" : '';
				global $caia_detected_device;
				if ( isset($caia_detected_device) && ( $caia_detected_device == 'Mobile') ){
					$chat_html = "<div id='fb-customer-chat' class='fb-customerchat-mobile' attribution='biz_inbox' page_id='{$fanpage_id}' style='position: fixed;right: 2%;bottom: 10px;z-index: 40000;'><a class='icon-fb' href='https://m.me/{$fanpage_id}' style='background: url(/wp-content/themes/caia/lib/utilities/images/fb.png) no-repeat center center;height: 60px;width: 60px;display: table;text-indent: -9999px;background-size: 100%;'>Messegers</a></div>";					
					$chat_html = apply_filters( 'caia_social_fb_chat_mobile', $chat_html );
					echo $chat_html;					
				}else{
					$chat_html = "<div id='fb-customer-chat' class='fb-customerchat' attribution='biz_inbox' page_id='{$fanpage_id}' {$theme_color} logged_in_greeting='{$fb_chat_greeting}' logged_out_greeting='{$fb_chat_greeting}'></div>";
					$chat_html = apply_filters( 'caia_social_fb_chat_pc', $chat_html );
					echo $chat_html;					
				}
			}
		}

		function add_fb_share(){
			if( is_singular( array( 'post', 'page', 'fitwp_question' ) ) ){
			?>
			<script>
				jQuery(document).ready( function($){
					var url = {"ajax_url":"<?php echo admin_url( 'admin-ajax.php' ); ?>"};
					$('a.caia_social_button.facebook').click(function(){
						var post_id = $(this).attr("data-id");  
						$.ajax({
							url : url.ajax_url,
							type : 'post',
							data : {
								action : 'caia_update_facebook_share',
								post_id : post_id         
							},
							success : function( response ) {
								//$('.count_share').html( response );
							}
						});
						//return false;
					});
				});
			</script>
			<?php
			}
		}

		function caia_update_facebook_share() {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) { 
				$countsharefb = get_post_meta( $_POST['post_id'], 'countsharefb', true );
				$countsharefb++;
				update_post_meta( $_POST['post_id'], 'countsharefb', $countsharefb );
				//echo $countsharefb;    
			}
			die();
		}
		
		function add_scripts(){
			global $fboptn, $caia_detected_device;

			if (!$this->is_run) return;


			$fb_version = 'v12.0';


			if (isset($fboptn['fbml']) && $fboptn['fbml'] == 'on' && isset($fboptn['appID'])) {
				$app_id = $fboptn['appID'];
			}else{
				$app_id = '';
			}

			$enable_fb_chat = caia_get_option('enable_fb_chat');
			$fanpage_id = caia_get_option('fanpage_id');

			if ($enable_fb_chat && $fanpage_id && ( !isset($caia_detected_device) || $caia_detected_device !== 'Mobile') ){
				$src = "https://connect.facebook.net/vi_VN/sdk/xfbml.customerchat.js";
			}else{
				if ($app_id){
					$src = "//connect.facebook.net/vi_VN/sdk.js#xfbml=1&appId={$app_id}&version={$fb_version}&autoLogAppEvents=1";	
				}else{
					$src = "//connect.facebook.net/vi_VN/sdk.js#xfbml=1&version={$fb_version}";	
				}		
			}			

			$share_facebook = caia_get_option( 'share_facebook' );
			$share_twitter = caia_get_option( 'share_twitter' );
			$share_pinterest = caia_get_option( 'share_pinterest' );
			$share_linkedin = caia_get_option( 'share_linkedin' );
			?>
			<script>  			  	  
			window.addEventListener('load', function(){
				<?php
				if ($enable_fb_chat && $fanpage_id){
					?>
					window.fbAsyncInit = function() { FB.init({ xfbml: true, version : '<?php echo $fb_version;?>'}); }; (function(d, s, id) {var js, fjs = d.getElementsByTagName(s)[0];if (d.getElementById(id)) return; js = d.createElement(s); js.id = id; js.src = '<?php echo $src;?>'; fjs.parentNode.insertBefore(js, fjs); }(document, 'script', 'facebook-jssdk'));
					<?php
				}else{
					?>
					(function(d, s, id) {var js, fjs = d.getElementsByTagName(s)[0];if (d.getElementById(id)) return; js = d.createElement(s); js.id = id; js.src = '<?php echo $src;?>'; fjs.parentNode.insertBefore(js, fjs); }(document, 'script', 'facebook-jssdk'));
					<?php
				}
				if ($share_pinterest){
					?>
					(function() { var pi = document.createElement('script'); pi.type = 'text/javascript'; pi.async = true; pi.src = '//assets.pinterest.com/js/pinit.js'; var s = document.getElementsByTagName('body')[0]; s.prepend(pi); })();
					<?php					
				}
				?>			    
			});
			<?php 						
			if ($share_linkedin){?>
				function share_linkedin(){
					window.open('https://www.linkedin.com/cws/share?url=' + window.location, 'linkedin-share-dialog', 'width=626,height=436'); 
				}
			<?php }			
			if ($share_twitter){?>
				function share_twitter(){
					window.open('https://twitter.com/intent/tweet?url=' + window.location, 'twitter-share-dialog', 'width=626,height=436'); 
				}
			<?php }
			if ($share_facebook){?>
				function share_facebook(){
					window.open('https://www.facebook.com/sharer/sharer.php?u=' + window.location, 'facebook-share-dialog', 'width=626,height=436'); 
				}
			<?php }

			global $caia_detected_device;
			if ( false && isset($caia_detected_device) && ( $caia_detected_device == 'Mobile') ){ ?>
				jQuery(document).ready( function($){
					$(".caia-social-share .open-social").click(function(){
						$(this).toggleClass("close");
						$(".caia-social-share .hide").toggle();
					});				
				});
			<?php } ?>		
			</script>
			<?php	
			
		}

		function add_css_share(){
			?>
			<style>
			div.caia-social-share{
				float:right;
				position: relative;
			}
			.caia-social-share .hide {
				display: none;
				position: absolute;
				top: 35px;
				position: absolute;
				top: 35px;
				left: -115px;
				background: #d6d6d6;
				padding: 5px 10px;
				width: 187px;
				border-radius: 10px;
				z-index: 300;
			}
			.caia-social-share .hide:after{
				content: '';
				display: block;
				position: absolute;
				top: -16px;
				right: 31px;
				bottom: 100%;
				width: 0;
				height: 0;
				border-color: transparent transparent #d6d6d6 transparent;
				border-style: solid;
				border-width: 8px;			
			}
			.caia-social-share .fb-share-button.one{
				float: left;
			}
			.caia-social-share .open-social {
				float: right;
				display: inline-block;
			}
			.caia-social-share .open-social:after {
				content: "+";
				float: right;
				font-size: 26px;
				margin-left: 5px;
				color: gray;
				line-height: 26px;
			}
			.caia-social-share .open-social.close:after{
				content: "-";
				float: right;
				font-size: 50px;
				margin-left: 5px;
				color: gray;
				line-height: 23px;
			}
			a.caia_social_button {
				background-color:#007ea8;
				-moz-border-radius:3px;
				-webkit-border-radius:3px;
				border-radius:3px;
				/*border:1px solid #124d77;*/
				display:inline-block;
				cursor:pointer;
				color:#ffffff;
				font-family:Arial;
				font-size:14px;
				font-weight:bold;
				/*padding: 4px 12px 4px 4px;*/
				text-decoration:none;
				height: 20px;
				margin-top: -1px;
				position: relative;
			    width: 74px;
			}
			a.caia_social_button.twitter {
			    background-color: #1d9bf0;
			    width: 70px;
			}
			a.caia_social_button.facebook {
				background-color: #1877f2;
				width: 94px;
			}
			a.caia_social_button > span{
			    position: absolute;
			    top: 2px;
			    left: 25px;
			    font-size: 11px;
			}
			a.caia_social_button > strong{
			    position: absolute;
			    top: 2px;
			    left: 70px;
			    font-size: 11px;
			}
			a.caia_social_button:hover {
				background-color:#0061a7;
			}
			a.caia_social_button:active {
				position:relative;
				top:1px;
			}
			.caia_social_button_bound{
				display: inline-flex;
    			vertical-align: top;
    			margin-left: 4px;
			}
			/*.logged-in .caia_social_button_bound{
				vertical-align: baseline;
			}*/
			.logged-in .caia_social_button_bound.pinterest{
				vertical-align: top;
			}
			body span.PIN_1560416120510_button_pin.PIN_1560416120510_save {
			    display: inline-flex !important;
			    vertical-align: bottom !important;
			    border-radius: 3px !important;
			}
			.fb-share-button{
				margin-left: 5px;
				vertical-align: top;
			}	

			.fb-share-button.fb_iframe_widget span {
			    vertical-align: inherit !important;
			}
			
			</style>
			<?php
		}
	}

	$caia_social = new Caia_Social();

}