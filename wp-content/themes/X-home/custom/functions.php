<?php

// Bật HTML5
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

// Thêm metabox
include('metabox.php');

// Thêm caiajs
include('js/caiajs.php');

// Thêm jquery
add_action('wp_enqueue_scripts', 'caia_add_scripts_homes');
function caia_add_scripts_homes(){
	wp_enqueue_script('caia-slick', CHILD_URL.'/custom/js/slick.js', array('jquery'));
}

function add_swiper_webcomponent_script() {
    echo '<script type="module">
      import "https://cdn.jsdelivr.net/npm/swiper@11/swiper-element-bundle.min.js";
    </script>';
}
add_action('wp_footer', 'add_swiper_webcomponent_script');


add_action('wp_enqueue_scripts', 'custom_override_style', 100);
function custom_override_style() {
    // Đảm bảo đúng với theme con
    $style_path = get_stylesheet_directory() . '/style.css';
    $version = file_exists($style_path) ? filemtime($style_path) : time();

    // Handle thực tế (cập nhật lại nếu bạn tìm thấy tên khác sau bước kiểm tra)
    $handle = 'caia';

    // Gỡ bỏ và thêm lại
    wp_dequeue_style($handle);
    wp_deregister_style($handle);
    wp_enqueue_style($handle, get_stylesheet_uri(), array(), $version);
}
//Cho phép upload ảnh định dạng Svg
add_filter('upload_mimes', 'caia_mime_types', 1, 1);
function caia_mime_types($mime_types){  
	$mime_types['svg'] = 'image/svg+xml';
	$mime_types['webp'] = 'image/webp';
	return $mime_types;
}

add_filter( 'wp_check_filetype_and_ext', 'caia_disable_real_mime_check', 10, 4 );
function caia_disable_real_mime_check( $data, $file, $filename, $mimes ) {
	$wp_filetype = wp_check_filetype( $filename, $mimes );
	$ext = $wp_filetype['ext'];
	$type = $wp_filetype['type'];
	$proper_filename = $data['proper_filename'];
	return compact( 'ext', 'type', 'proper_filename' );
}

// Xóa các kích thước mặc định trong Wordpress
add_filter( 'intermediate_image_sizes_advanced', 'prefix_remove_default_images' );
function prefix_remove_default_images( $sizes ){
	unset( $sizes['medium']);
	unset( $sizes['large']);
	unset( $sizes['medium_large']);
	unset( $sizes['1536x1536']);
	unset( $sizes['2048x2048']);
	return $sizes;
}

// Ẩn hiển thị các kích thước ảnh mặc định
add_filter( 'intermediate_image_sizes', function( $sizes ){
    return array_filter( $sizes, function( $val ){
        return 'medium' !== $val && 'medium_large' !== $val && 'large' !== $val && '1536x1536' !== $val && '2048x2048' !== $val;
    });
});

// Đặt kích thước mặc định cho website
update_option( 'thumbnail_size_w', 750 );
update_option( 'thumbnail_size_h', 395 );

// Thêm kích thước ảnh sản phẩm
add_image_size('product-image',640,640,true);
add_image_size('product-avatar',300,300,true);

add_filter( 'image_size_names_choose', 'caia_custom_sizes' );
function caia_custom_sizes( $sizes ) {
    return array_merge( $sizes, array(
		'product-image' => __( 'Kích thước 640x640' ),
    ));
}

// Bỏ toàn bộ thẻ H4 khỏi tiêu đề widget
add_filter( 'genesis_register_widget_area_defaults', 'caia_change_all_widget_titles' );
function caia_change_all_widget_titles( $defaults ) { 
	$defaults['before_title'] = '<div class="widget-title widgettitle">';
	$defaults['after_title'] = "</div>";
	return $defaults;
}

// Thêm thẻ đóng mở cho tiêu đề của widget
add_filter( 'widget_title', 'caia_add_html_widget_title' );
function caia_add_html_widget_title( $title ) {	
	$title = str_replace( '[span]', '<span>', $title );
	$title = str_replace( '[/span]', '</span>', $title );	
	return $title;
}

// Thêm font
add_action('wp_head','caia_add_font_website');
function caia_add_font_website(){
	?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bai+Jamjuree:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;1,200;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
	<?php
}



genesis_register_sidebar(
	array(
		'id'			=> 'nhantuvan',
		'name'			=> 'Toàn bộ - Nhận tư vấn',
	)
);

genesis_register_sidebar( 
	array(
		'id'			=> 'content-banner',
		'name'			=> 'Trang chủ - Banner',
	)
);


genesis_register_sidebar( 
	array(
		'id'			=> 'content-vechungtoi',
		'name'			=> 'Trang chủ - Về chúng tôi',
	)
);



genesis_register_sidebar( 
	array(
		'id'			=> 'content-dichvu',
		'name'			=> 'Trang chủ - Dịch vụ',
	)
);

genesis_register_sidebar( 
	array(
		'id'			=> 'content-congtrinhthucte',
		'name'			=> 'Trang chủ - Công trình thực tế',
	)
);

genesis_register_sidebar( 
	array(
		'id'			=> 'content-taisao',
		'name'			=> 'Trang chủ - Tại sao nên chọn chúng tôi',
	)
);



genesis_register_sidebar( 
	array(
		'id'			=> 'content-news',
		'name'			=> 'Trang chủ - Câu chuyện kiến trúc',
	)
);

genesis_register_sidebar( 
	array(
		'id'			=> 'content-dangky',
		'name'			=> 'Trang chủ - Đăng ký tư vấn',
	)
);

genesis_register_sidebar( 
	array(
		'id'			=> 'content-tieudefeedback',
		'name'			=> 'Trang chủ - Tiêu đề Feedback',
	)
);

genesis_register_sidebar( 
	array(
		'id'			=> 'content-feedback',
		'name'			=> 'Trang chủ - Feedback của khách hàng',
	)
);

genesis_register_sidebar( 
	array(
		'id'			=> 'content-posts',
		'name'			=> 'Tin tức - Bài viết nổi bật',
	)
);

genesis_register_sidebar( 
	array(
		'id'			=> 'content-tuvan',
		'name'			=> 'Sản phẩm - Hỗ trợ tư vấn',
	)
);

genesis_register_sidebar( 
	array(
		'id'			=> 'content-muahang',
		'name'			=> 'Sản phẩm - Hỗ trợ mua hàng',
	)
);



genesis_register_sidebar( 
	array(
		'id'			=> 'content-bfooter',
		'name'			=> 'Toàn bộ - Nội dung trước chân trang',
	)
);

genesis_register_sidebar( 
	array(
		'id'			=> 'content-footer',
		'name'			=> 'Toàn bộ - Nội dung chân trang',
	)
);

genesis_register_sidebar( 
	array(
		'id'			=> 'content-fix',
		'name'			=> 'Toàn bộ - Nội dung cố định',
	)
);

add_action('genesis_before_header','caia_add_contactus');
function caia_add_contactus(){
	if( is_active_sidebar( 'nhantuvan' ) ){
		echo '<div class="nhantuvan section"><div class="wrap">';
			dynamic_sidebar( 'Toàn bộ - Nhận tư vấn' );
		echo '</div></div>';
	}
}


remove_action('genesis_footer','genesis_do_footer');
add_action('genesis_footer','caia_add_content_footer');
function caia_add_content_footer(){
	if( is_active_sidebar( 'content-footer' ) ){
		dynamic_sidebar( 'Toàn bộ - Nội dung chân trang' );		
	}
}

add_action('genesis_before_footer','caia_add_content_after_footer',8);
function caia_add_content_after_footer(){
	echo '<div data-aos="fade-up" class="content-contact section"><div class="wrap">';
		dynamic_sidebar( 'Toàn bộ - Liên hệ tư vấn' );
	echo '</div></div>';
}

add_action('genesis_before_footer','caia_add_content_after_footer2');
function caia_add_content_after_footer2(){
	echo '<div class="before_footer section"><div class="wrap"><div class="wrap-section">';

		dynamic_sidebar( 'Toàn bộ - Nội dung trước chân trang' );
	echo '</div></div></div>';
}

add_action('genesis_after_footer','caia_add_content_fix');
function caia_add_content_fix(){
	if( is_active_sidebar( 'content-fix' ) ){
		echo '<div class="content-fix">';
			dynamic_sidebar( 'Toàn bộ - Nội dung cố định' );		
		echo '</div>';
	}
}


add_action( 'genesis_before', function() {
	// Xóa sidebar mặc định của Genesis trước khi render
	remove_action( 'genesis_sidebar', 'genesis_do_sidebar' );
});

add_action( 'genesis_sidebar', function() {

	if ( is_singular( 'post' ) ) {
		genesis_widget_area(
			'sidebar',
			[ 'before' => '<aside class="sidebar primary-sidebar widget-area">', 'after' => '</aside>' ]
		);

	// Các trang khác, trừ sản phẩm
	} elseif ( ! is_singular( 'product' ) ) {
		genesis_widget_area(
			'sidebar',
			[ 'before' => '<aside class="sidebar primary-sidebar widget-area">', 'after' => '</aside>' ]
		);
	}
});

// Chỉnh hiển thị nút Next Page và Previous Page trong phân trang
add_filter ( 'genesis_next_link_text' , 'caia_next_page_link' );
function caia_next_page_link( $text ) {
    return '&#x000BB;';
}

add_filter ( 'genesis_prev_link_text' , 'caia_previous_page_link' );
function caia_previous_page_link( $text ) {
    return '&#x000AB;';
}

// Thay đổi vị trí breadcrumbs
remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );
// add_action( 'genesis_after_header', 'genesis_do_breadcrumbs',9);

// Tùy biến breadcrumbs trong Genesis
// ✅ Shortcode hiển thị breadcrumb tùy chỉnh
function my_custom_breadcrumb_shortcode() {
    // Thiết lập tham số breadcrumb
    $args = array(
        'home' => '<span class="home">Trang chủ</span>',
        'sep'  => '<span aria-label="breadcrumb separator" class="label"> » </span>',
        'list_sep' => ', ',
        'prefix' => '<div class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"><div class="wrap"><div class="thanhdieuhuong">',
        'suffix' => '</div></div></div>',
        'heirarchial_attachments' => true,
        'heirarchial_categories'  => true,
        'labels' => array(
            'prefix' => '',
            'author' => '',
            'category' => '',
            'tag' => '',
            'date' => '',
            'search' => '',
            'tax' => '',
            'post_type' => '',
            '404' => '',
        ),
    );

    // Nếu bạn dùng Genesis
    if ( function_exists( 'genesis_breadcrumb' ) ) {
        ob_start();
        genesis_breadcrumb( $args );
        return ob_get_clean();
    }

    // Nếu không có Genesis, bạn có thể thay bằng breadcrumb tĩnh hoặc plugin khác
    return '<div class="breadcrumb"><a href="/">Trang chủ</a> » ...</div>';
}
add_shortcode( 'breadcrumb', 'my_custom_breadcrumb_shortcode' );



// Thiết kế lại form comment
add_filter( 'comment_form_defaults', 'rayno_comment_form_args' );
function rayno_comment_form_args($defaults) {
	global $user_identity, $id;
	$commenter = wp_get_current_commenter();
	$req       = get_option( 'require_name_email' );
	$aria_req  = ( $req ? ' aria-required="true"' : '' );
	$author = '<div class="popup-comment"><div class="box-comment"><span class="close-popup-comment">✕</span><p>Bạn vui lòng điền thêm thông tin!</p><p class="comment-form-author">' .
	          '<input id="author" name="author" type="text" class="author" placeholder="Họ và tên" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" tabindex="1"' . $aria_req . '/>' .
	          '</p>';
	$email = '<p class="comment-form-email">' .
	         '<input id="email" name="email" type="text" class="email" placeholder="Email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" tabindex="2"' . $aria_req . ' />' .
	         '</p>';
	$comment_field = '<p class="comment-form-comment">' .
	                 '<textarea id="comment" name="comment" cols="45" rows="8" class="form" tabindex="4" aria-required="true" placeholder="Nội dung bình luận"></textarea>' .
	                 '</p>';
	$args = array(
		'fields' => array(
		'author' => $author,
		'email'  => $email,
		),
		'comment_field'        => $comment_field,
		'title_reply'          => __( 'Bình luận của bạn', 'genesis' ),
		'comment_notes_before' => '',
		'comment_notes_after'  => '',
	);
	$args = wp_parse_args( $args, $defaults );
	return apply_filters( 'raynoblog_comment_form_args', $args, $user_identity, $id, $commenter, $req, $aria_req );
}

// Sửa nút comment
add_filter( 'comment_form_defaults', 'caia_change_submit_comment' );
function caia_change_submit_comment( $defaults ){
    $defaults['label_submit'] = 'Gửi đi';
    return $defaults;
}

// Sửa chữ comment
add_filter( 'genesis_title_comments', 'caia_title_comments' );
function caia_title_comments() {
	echo '';
}

// Thay đổi chữ says
add_filter('comment_author_says_text', 'caia_change_says');
function caia_change_says($args){
	$args = 'đã bình luận';
	return $args;
}

// Sửa thẻ h4 ý kiến của bạn
add_filter( 'comment_form_defaults', 'caia_custom_reply_title' );
function caia_custom_reply_title( $defaults ){
	$defaults['title_reply_before'] = '<p id="reply-title" class="comment-reply-title">';
	$defaults['title_reply_after'] = '</p>';
	return $defaults;
}

// Thêm nút comment
add_action( 'comment_form_logged_in_after', 'additional_fields',1 );
add_action( 'comment_form_after_fields', 'additional_fields',1 );
function additional_fields (){
	if(!is_user_logged_in()){
		echo '<p class="comment-form-phone"><input id="author" name="phone" type="text" size="30" tabindex="4" placeholder="Số điện thoại"/></p>
		<p><input name="actionsubmit" type="hidden" value="1" /><input id="submit-commnent" name="submit-commnent" type="submit" value="Hoàn tất" /></p></div></div>';
	}
}

// Lưu nội dung comment 
add_action( 'comment_post', 'save_comment_meta_data' );
function save_comment_meta_data( $comment_id ){
	if ( ( isset( $_POST['phone'] ) ) && ( $_POST['phone'] != '') )
	$phone = wp_filter_nohtml_kses($_POST['phone']);
	add_comment_meta( $comment_id, 'phone', $phone );
}

// Add the filter to check if the comment meta data has been filled or not
add_filter( 'preprocess_comment', 'verify_comment_meta_data', 1, 1 );
function verify_comment_meta_data( $commentdata ){
	$commentdata['phone'] = ( ! empty ( $_POST['phone'] ) ) ? sanitize_text_field( $_POST['phone'] ) : false;
	if ( ! $commentdata['phone'] && ! is_admin() ){
		wp_die( __( '<p>Lỗi: Vui lòng điền số điện thoại</p><a href="javascript:history.back()">« Quay lại</a>' ) );
	}	
    return $commentdata;
}

// Thêm nút trong trang quản trị 
add_action( 'add_meta_boxes_comment', 'extend_comment_add_meta_box' );
function extend_comment_add_meta_box() {
    add_meta_box( 'title', __( 'Thông tin số điện thoại khách hàng' ), 'extend_comment_meta_box', 'comment', 'normal', 'high' );
}
 
function extend_comment_meta_box ( $comment ){
    $phone = get_comment_meta( $comment->comment_ID, 'phone', true );
    wp_nonce_field( 'extend_comment_update', 'extend_comment_update', false );
    ?><p><label for="phone"><?php _e( 'Số điện thoại' ); ?></label><input type="text" name="phone" value="<?php echo esc_attr( $phone ); ?>" class="widefat" /></p><?php
}

// Cập nhật khi thay đổi 
add_action( 'edit_comment', 'extend_comment_edit_metafields' );
function extend_comment_edit_metafields( $comment_id ){
    if( ! isset( $_POST['extend_comment_update'] ) || ! wp_verify_nonce( $_POST['extend_comment_update'], 'extend_comment_update' ) ) return;
	if ( ( isset( $_POST['phone'] ) ) && ( $_POST['phone'] != '') ) : 
	$phone = wp_filter_nohtml_kses($_POST['phone']);
	update_comment_meta( $comment_id, 'phone', $phone );
	else :
	delete_comment_meta( $comment_id, 'phone');
	endif;
}

//Thêm cột số điện thoại trong admin
add_filter( 'manage_edit-comments_columns', 'myplugin_comment_columns' );
function myplugin_comment_columns( $columns ){
	return array_merge( $columns, array(
		'phone' => __( 'Số điện thoại' ),
	) );
}

add_filter( 'manage_comments_custom_column', 'myplugin_comment_column', 10, 2 );
function myplugin_comment_column( $column, $comment_ID ){
	switch ( $column ) {
		case 'phone':
			if ( $meta = get_comment_meta( $comment_ID, $column , true ) ) {
				echo $meta;
			} else {
				echo '-';
			}
		break;
	}
}

add_action('admin_head', 'my_column_width');
function my_column_width() {
    echo '<style type="text/css">';
    echo 'th#phone {width: 15%;}';
    echo '</style>';
}

function wp_youtube_video($atts) {
         extract(shortcode_atts(array(

              'id'    => '',
              'width'   => '',
              'height'  => ''

         ), $atts));

        return '<div class="embed-video"><iframe id="videoIframe" width="'.$atts['width'].'" height="'.$atts['height'].'" src="https://www.youtube.com/embed/'.$atts['id'].'" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>';
    }
add_shortcode('youtube', 'wp_youtube_video');





// Mobile
if (wp_is_mobile() ){
	remove_action( 'genesis_meta', 'genesis_load_stylesheet' );
	wp_enqueue_style( 'style-mobile', CHILD_URL.'/style-mobile.css' );

}


function add_fontawesome_to_theme() {
    wp_enqueue_style(
        'font-awesome', 
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css', 
        array(), 
        '6.5.0'
    );
}
add_action('wp_enqueue_scripts', 'add_fontawesome_to_theme');


add_action('init', 'caia_create_category_project');
function caia_create_category_project() {
	$labels = [
		'name' => 'Chuyên mục Công trình thực tế',
		'singular_name' => 'Chuyên mục Công trình thực tế',
		'menu_name' => 'Chuyên mục Công trình thực tế',
	];
	$args = [
		'labels' => $labels,
		'hierarchical' => true,
        'public' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'rewrite' => ['slug' => 'danh-muc-du-an'],
	];
	register_taxonomy('project_cat', ['project'], $args);
}

add_action('init', 'caia_create_tag_project');
function caia_create_tag_project() {
	$labels = [
		'name' => 'Thẻ Công trình thực tế',
		'singular_name' => 'Thẻ Công trình thực tế',
		'menu_name' => 'Thẻ Công trình thực tế',
	];
	$args = [
		'labels' => $labels,
		'hierarchical' => false,
        'public' => true,
		'show_ui' => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'rewrite' => ['slug' => 'the-cong-trinh'],
	];
	register_taxonomy('project_tag', ['project'], $args);
}


add_action('init', 'caia_custom_post_type');
function caia_custom_post_type() {
    $labels = [
        'name'               => __('Công trình thực tế'),
        'singular_name'      => __('Công trình thực tế'),
        'menu_name'          => __('Công trình thực tế'),
        'name_admin_bar'     => __('Công trình thực tế'),
        'add_new'            => __('Thêm Công trình thực tế'),
        'add_new_item'       => __('Thêm Công trình thực tế'),
        'edit_item'          => __('Sửa Công trình thực tế'),
        'new_item'           => __('Công trình thực tế mới'),
        'view_item'          => __('Xem Công trình thực tế'),
        'search_items'       => __('Tìm kiếm Công trình thực tế'),
        'not_found'          => __('Không có Công trình thực tế nào'),
        'not_found_in_trash' => __('Không có Công trình thực tế trong thùng rác'),
        'all_items'          => __('Tất cả Công trình thực tế'),
    ];

    $args = [
        'label'               => __('Công trình thực tế'),
        'description'         => __('Công trình thực tế'),
        'labels'              => $labels,
        'supports'            => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions'],
        'taxonomies'          => ['project_cat', 'project_tag'], 
        'public'              => true,
        'menu_icon'           => 'dashicons-portfolio',
        'show_ui'             => true,
        'show_in_menu'        => true,
        'has_archive'         => true,
        'rewrite'             => ['slug' => 'cong-trinh'],
        'publicly_queryable'  => true,
    ];
    register_post_type('project', $args);
}




// Shortcode [cart_icon] để chèn vào header
function adsdigi_header_cart_icon() {
    if ( ! function_exists( 'WC' ) ) return '';

    ob_start(); ?>
    <div class="header-cart">
        <a class="header-cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>"
           aria-label="<?php esc_attr_e('Xem giỏ hàng','woocommerce'); ?>">
            <i class="fas fa-shopping-cart cart-icon" aria-hidden="true"></i>
            <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
        </a>

        <div class="mini-cart-dropdown" aria-hidden="true">
            <?php woocommerce_mini_cart(); ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'cart_icon', 'adsdigi_header_cart_icon' );

// Cập nhật số lượng & mini cart bằng AJAX sau khi thêm vào giỏ
add_filter( 'woocommerce_add_to_cart_fragments', function( $fragments ) {
    if ( ! function_exists( 'WC' ) ) return $fragments;

    // Cập nhật số lượng
    $fragments['.cart-count'] =
        '<span class="cart-count">' . WC()->cart->get_cart_contents_count() . '</span>';

    // Cập nhật nội dung mini cart
    ob_start();
    woocommerce_mini_cart();
    $mini = ob_get_clean();
    $fragments['.mini-cart-dropdown'] = '<div class="mini-cart-dropdown">'.$mini.'</div>';

    return $fragments;
});




// Shortcode: [woo_login_button]
add_shortcode('woo_login_button', function($atts){
    if ( ! function_exists('wc_get_page_permalink') ) return '';

    $a = shortcode_atts([
        // 'text_login'   => 'Đăng nhập',
        // 'text_account' => 'Tài khoản',
        'icon' => '<i class="fa-solid fa-user"></i>', 
    ], $atts);

    $url = wc_get_page_permalink('myaccount');

    if ( is_user_logged_in() ) {
        // Nếu đã login → hiện nút "Tài khoản"
        return sprintf(
            '<a class="woo-login-btn" href="%1$s">%3$s %2$s</a>',
            esc_url($url),
            esc_html($a['text_account']),
            $a['icon'] 
        );
    } else {
        // Nếu chưa login → hiện nút "Đăng nhập"
        return sprintf(
            '<a class="woo-login-btn" href="%1$s">%3$s %2$s</a>',
            esc_url($url),
            esc_html($a['text_login']),
            $a['icon']
        );
    }
});



// Tắt breadcrumb của WooCommerce
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );


// 2. (Tùy chọn) Tắt sidebar mặc định của Woo (vì ta sẽ tự có sidebar riêng)
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

// Bật hỗ trợ WooCommerce
add_action('after_setup_theme', function () {
  add_theme_support('woocommerce');
});
// Đăng ký sidebar cho bộ lọc (chuẩn WordPress, chạy trên widgets_init)
add_action('widgets_init', function () {
    register_sidebar([
        'id'            => 'content-filter',
        'name'          => 'Shop Filter',
        'description'   => 'Widget lọc theo giá/thuộc tính cho trang shop & category.',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
});



// --- Xóa các hook mặc định ---
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
add_filter('loop_shop_columns', function(){ return 4; });          
add_filter('loop_shop_per_page', function($n){ return 16; }, 20);   

// --- Thêm hình ảnh có link riêng ---
add_action( 'woocommerce_before_shop_loop_item_title', 'custom_product_image_link', 9 );
function custom_product_image_link() {
    global $product;
    echo '<a href="' . get_permalink( $product->get_id() ) . '" class="custom-product-image-link">';
    echo woocommerce_get_product_thumbnail();
    echo '</a>';
}



// Đăng ký sidebar riêng cho trang cửa hàng WooCommerce
function register_shop_sidebar() {
    register_sidebar( array(
        'name'          => __( 'Banner Trang Cửa Hàng', 'your-theme' ),
        'id'            => 'shop-sidebar',
        'description'   => __( 'Kéo thả widget vào đây để hiển thị ở trang cửa hàng WooCommerce.', 'your-theme' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'register_shop_sidebar' );

// Ẩn tiêu đề trang cửa hàng
add_filter( 'woocommerce_show_page_title', '__return_false' );


