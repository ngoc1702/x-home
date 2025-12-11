<?php
/**
	* Caia - Image Box
	* @category CAIA
	* @package  Widgets
	* @author   HungTH
*/

// Tối ưu hiển thị ảnh đầy đủ title, alt, width, height
// Đổi tiêu đề H4 sang p
// Lấy tiêu đề ảnh theo ID bỏ hàm get_image_title truy xuất vào CSDL theo URL

define('CAIA_IMAGE_WIDGET_VER', 2.0);

global $wp_version;
if ( version_compare( $wp_version, '4.9', '<' ) ) {	
	if (basename($_SERVER['SCRIPT_FILENAME']) == 'widgets.php'){
		add_action('admin_enqueue_scripts', 'caia_load_wp_media_files');	
		function caia_load_wp_media_files() {
		    wp_enqueue_media();    
		}	
	}	
}

class Images_upload_Widget extends WP_Widget {
	function __construct() {
    parent::__construct(
        'images_upload_widget', // ID riêng của widget, không nên đổi
        __('Image Box', 'text_domain'), // Tên hiển thị trong giao diện admin
        array(
            'classname'   => 'image-upload', // class CSS sẽ áp dụng ngoài frontend
            'description' => __('Thêm khung ảnh có nội dung', 'text_domain') // mô tả ngắn
        )
    );
}


	function widget($args, $instance) {
		extract($args);
		echo $before_widget;

		$title            = $instance['title'] ?? '';
		$text             = $instance['text'] ?? '';
		$check            = $instance['check'] ?? '';
		$readmore         = $instance['readmore'] ?? '';
		$image_url        = $instance['image_uri'] ?? '';
		$image_alignment  = $instance['image_alignment'] ?? 'alignnone';
		$title_alignment  = $instance['title_alignment'] ?? 'before';
		$noidung          = $instance['noidung'] ?? '';	

		// Lấy thông tin id và tiêu đề ảnh
		$attachment_id = attachment_url_to_postid( $image_url );
		$attachment_title = get_the_title( $attachment_id );	

		

		echo '<div class="mainposts">';

		if(!empty($title)){
			if($title_alignment == 'before'){
				if(!empty($text)){
					echo '<div class="box">';
					echo '<p class="title"><a href="'.$text.'"/>'.$title.'</a></p>';
				}else{
					echo '<p class="title">';
						echo  do_shortcode($title);
					echo '</p>';
					echo '<div class="box">';
				}
			}
		}

		if( !empty($image_url)){

			if(!empty($text)){
				if($image_alignment == 'alignleft'){
					if(!empty($check)){
						echo '<a href="'.$text.'" target="_blank" class="alignleft">';
							echo wp_get_attachment_image($attachment_id, 'full', false, array('alt' => $attachment_title));
						echo '</a>';
					}else{
						echo '<a href="'.$text.'" class="alignleft">';
							echo wp_get_attachment_image($attachment_id, 'full', false, array('alt' => $attachment_title));
						echo '</a>';
					}
				}
				else if($image_alignment == 'alignright'){
					if(!empty($check)){
						echo '<a href="'.$text.'" target="_blank" class="alignright">';
							echo wp_get_attachment_image($attachment_id, 'full', false, array('alt' => $attachment_title));
						echo '</a>';
					}else{
						echo '<a href="'.$text.'" class="alignright">';
							echo wp_get_attachment_image($attachment_id, 'full', false, array('alt' => $attachment_title));
						echo '</a>';
					}
				}else{
					if(!empty($check)){
						echo '<a href="'.$text.'" target="_blank" class="alignnone">';
							echo wp_get_attachment_image($attachment_id, 'full', false, array('alt' => $attachment_title));
						echo '</a>';
					}else{
						echo '<a href="'.$text.'" class="alignnone">';
							echo wp_get_attachment_image($attachment_id, 'full', false, array('alt' => $attachment_title));
						echo '</a>';
					}
				}
			}else{
				if($image_alignment == 'alignleft'){
					echo wp_get_attachment_image($attachment_id, 'full', false, array('alt' => $attachment_title, 'class' => 'alignleft'));
				}
				else if($image_alignment == 'alignright'){
					echo wp_get_attachment_image($attachment_id, 'full', false, array('alt' => $attachment_title, 'class' => 'alignright'));
				}else{
					echo wp_get_attachment_image($attachment_id, 'full', false, array('alt' => $attachment_title, 'class' => 'alignnone'));
				}
			}
		}

		if(!empty($title)){
			if($title_alignment == 'after'){
				if(!empty($text)){
					echo '<div class="box">';
					echo '<p class="title"><a href="'.$text.'"/>'.$title.'</a></p>';
				}else{
					echo '<div class="box">';
					echo '<p class="title">'.$title.'</p>';
				}
			}
		}

		if(!empty($noidung)){
			echo '<div class="noidung">';
				echo wpautop( do_shortcode($noidung) );
			echo '</div>';
		}

		if(!empty($text)){
			if(!empty($readmore)){
				if(!empty($check)){
					echo '<a href="'.$text.'" class="readmore" target="_blank">'.$readmore.'</a>';
				}else{
					echo '<a href="'.$text.'" class="readmore">'.$readmore.'</a>';
				}
			}
		}
		if(!empty($title)){
			echo '</div>';
		}
		echo '</div>';

	    echo $after_widget;
	}

	function update($new_instance, $old_instance) {
	    $instance = $old_instance;

		$instance['title']           = isset($new_instance['title'])           ? stripslashes(wp_filter_post_kses($new_instance['title'])) : '';
		$instance['text']            = isset($new_instance['text'])            ? strip_tags($new_instance['text']) : '';
		$instance['check']           = isset($new_instance['check'])           ? 1 : 0;
		$instance['readmore']        = isset($new_instance['readmore'])        ? strip_tags($new_instance['readmore']) : '';
		$instance['image_uri']       = isset($new_instance['image_uri'])       ? esc_url_raw($new_instance['image_uri']) : '';
		$instance['image_alignment'] = isset($new_instance['image_alignment']) ? strip_tags($new_instance['image_alignment']) : 'alignnone';
		$instance['title_alignment'] = isset($new_instance['title_alignment']) ? strip_tags($new_instance['title_alignment']) : 'before';
		$instance['noidung']         = isset($new_instance['noidung'])         ? stripslashes(wp_filter_post_kses($new_instance['noidung'])) : '';

		

	    return $instance;
	}

	function form($instance) {

		$title            = $instance['title'] ?? '';
		$text             = $instance['text'] ?? '';
		$check            = $instance['check'] ?? '';
		$readmore         = $instance['readmore'] ?? '';
		$image_uri        = $instance['image_uri'] ?? '';
		$image_alignment  = $instance['image_alignment'] ?? 'alignnone';
		$title_alignment  = $instance['title_alignment'] ?? 'before';
		$noidung          = $instance['noidung'] ?? '';
	?>
	    <p>
	        <label for="<?php echo $this->get_field_id('title'); ?>">Tiêu đề:</label><br />
	        <input type="text" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
	    </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'title_alignment' ); ?>"><?php _e( 'Vị trí tiêu đề', 'caia' ); ?>:</label>
            <select id="<?php echo $this->get_field_id( 'title_alignment' ); ?>" name="<?php echo $this->get_field_name( 'title_alignment' ); ?>">
                <option value="before" <?php selected( 'before', $instance['title_alignment'] ); ?>><?php _e( 'Phía trên', 'caia' ); ?></option>
                <option value="after" <?php selected( 'after', $instance['title_alignment'] ); ?>><?php _e( 'Phía dưới', 'caia' ); ?></option>
            </select>
        </p>
	    <p>
	        <label for="<?php echo $this->get_field_id('image_uri'); ?>">Ảnh:</label><br />

	        <?php
	            if ( $instance['image_uri'] != '' ) :
	                echo '<img class="custom_media_image" src="' . $instance['image_uri'] . '" style="margin:0;padding:0;max-width:100px;float:left;display:inline-block" /><br />';
	            endif;
	        ?>

	        <input type="text" class="widefat custom_media_url" name="<?php echo $this->get_field_name('image_uri'); ?>" id="<?php echo $this->get_field_id('image_uri'); ?>" value="<?php echo $instance['image_uri']; ?>" style="margin-top:5px;">

	        <input type="button" class="button button-primary custom_media_button" id="custom_media_button" name="<?php echo $this->get_field_name('image_uri'); ?>" value="Thêm ảnh" style="margin-top:5px;" />
	    </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'image_alignment' ); ?>"><?php _e( 'Vị trí Ảnh', 'caia' ); ?>:</label>
            <select id="<?php echo $this->get_field_id( 'image_alignment' ); ?>" name="<?php echo $this->get_field_name( 'image_alignment' ); ?>">
                <option value="alignnone">- <?php _e( 'Không căn', 'caia' ); ?> -</option>
                <option value="alignleft" <?php selected( 'alignleft', $instance['image_alignment'] ); ?>><?php _e( 'Trái', 'caia' ); ?></option>
                <option value="alignright" <?php selected( 'alignright', $instance['image_alignment'] ); ?>><?php _e( 'Phải', 'caia' ); ?></option>
            </select>
        </p>
	    <p>
       		<textarea class="custom-widget-wp-editor" id="<?php echo $this->get_field_id( 'noidung' ); ?>" name="<?php echo $this->get_field_name( 'noidung' ); ?>" style="width: 100%;height: 60px;"><?php echo esc_textarea( $instance['noidung'] ); ?></textarea>
	    </p>
	    <p>
	        <label for="<?php echo $this->get_field_id('readmore'); ?>">Xem thêm: </label><br/>
	        <input type="text" name="<?php echo $this->get_field_name('readmore'); ?>" id="<?php echo $this->get_field_id('readmore'); ?>" value="<?php echo $instance['readmore']; ?>" class="widefat" />
	    </p>
	    <p>
	        <label for="<?php echo $this->get_field_id('text'); ?>">Đường dẫn:</label><br />
	        <input type="text" name="<?php echo $this->get_field_name('text'); ?>" id="<?php echo $this->get_field_id('text'); ?>" value="<?php echo $instance['text']; ?>" class="widefat" />
	    </p>
	    <p>
	        <label for="<?php echo $this->get_field_id('check'); ?>">Mở Tab mới:   </label>
		    <input type="checkbox" id="<?php echo $this->get_field_id('check'); ?>" name="<?php echo $this->get_field_name('check'); ?>" value="1" <?php checked(1, $instance['check']); ?> class="widefat"/> 
	    </p>
		
		<script>
			jQuery(document).ready( function($) {
			    function media_upload(button_class) {
			        var _custom_media = true,
			        _orig_send_attachment = wp.media.editor.send.attachment;

			        $('body').on('click', button_class, function(e) {
			            var button_id ='#'+$(this).attr('id');
			            var self = $(button_id);
			            var send_attachment_bkp = wp.media.editor.send.attachment;
			            var button = $(button_id);
			            var id = button.attr('id').replace('_button', '');
			            _custom_media = true;
			            wp.media.editor.send.attachment = function(props, attachment){
			                if ( _custom_media  ) {
			                    $('.custom_media_id').val(attachment.id);
			                    $('.custom_media_url').val(attachment.url);
			                    $('.custom_media_image').attr('src',attachment.url).css('display','block');
			                } else {
			                    return _orig_send_attachment.apply( button_id, [props, attachment] );
			                }
			            }
			            wp.media.editor.open(button);
			                return false;
			        });
			    }
			    media_upload('.custom_media_button.button');
			});	
		</script>
	<?php
	}
}