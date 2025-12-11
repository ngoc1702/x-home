<?php
// modified: 20/04/20
// 11/08/21: fix hiển thị link tới caiadolink cho cả seosenior
// 22/07/20: cam xmlrpc in code for wo vps
// 20/04/20: chi cho phep user co quyen publish_posts moi co the thay doi tac gia bai viet
// 1/02/20: cho phep cong tac vien upload anh
// 22/8/19: remove link khoi comment author cua genesis
// 21/8/19: bo sung lua chon Author khi soan thao
// 10/7/19: toi uu soan thao
// 04/7/19: fix unicode to hop
// 18/6/19: fix switch qua lai theme ko gay loi widget
// 29/09/21: cho phép ctv dc phép create page và gửi duyệt
// 30/09/21: tối ưu link của phân trang comment ở comment-page-1
// 06/10/21: ngầm định tắt wp rest api
// 29/04/22: ẩn các thông báo nâng cấp ko cần thiết trong quản trị


// Tắt Gunteburg và Css của Gunteburg
if ( ! has_filter( 'use_block_editor_for_post', '__return_false', 10 ) ){
    add_filter('use_block_editor_for_post', '__return_false', 10); 
    add_filter('use_block_editor_for_post_type', '__return_false', 10);

    add_action( 'wp_enqueue_scripts', function() {
        wp_dequeue_style( 'wp-block-library' );
    });
}

// Chuyển giao diện widget
if ( ! has_filter( 'use_widgets_block_editor', '__return_false' ) ){
    add_filter( 'use_widgets_block_editor', '__return_false' );
}

// Xóa logo WP khỏi trang Login
add_action( 'login_enqueue_scripts', 'login_logo' );
function login_logo() { 
    ?><style type="text/css">#login h1 a, .login h1 a {display: none;}</style><?php 
}

// Xóa logo WP
add_action( 'wp_before_admin_bar_render', 'example_admin_bar_remove_logo', 0 );
function example_admin_bar_remove_logo() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu( 'wp-logo' );
}

// Xóa version WP
add_filter('the_generator', '__return_empty_string');


// Xóa chân trang WP
add_filter( 'admin_footer_text', '__return_false' );
add_action( 'admin_menu', 'caia_remove_version' );
function caia_remove_version() {
    remove_filter( 'update_footer', 'core_update_footer' ); 
}

// Tắt xác nhận email quản trị
add_filter( 'admin_email_check_interval', '__return_false' );

// Bỏ các trang không dùng trong quản trị
add_action( 'admin_menu', function () {
    remove_menu_page( 'index.php' );
    remove_menu_page( 'meta-box' );
    remove_submenu_page( 'options-general.php', 'options-privacy.php' );
    // remove_menu_page( 'tools.php' );    
}, 999);

// Xóa các box ở trang bảng tin
remove_action('welcome_panel', 'wp_welcome_panel');


// toi uu link cua comment-page-1
add_filter( 'paginate_links', 'caia_optimize_paginate_links', 10, 1 );
function caia_optimize_paginate_links($link){
    $link = str_replace('/comment-page-1/', '/', $link);
    $link = str_replace('/comment-page-1#', '#', $link);
    return $link;
}

// add_filter( 'rest_authentication_errors', function( $result ) {         
//     return new WP_Error( 'REST API is disabled', 'API is disabled', array( 'status' => 401 ) );
// });



// fully disable xmlrpc
add_filter( 'xmlrpc_enabled', '__return_false');
add_filter( 'xmlrpc_methods', 'disable_full_xmlrpc', 1000 );
function disable_full_xmlrpc( $methods ) { 
    return array();
}


// cho phep cong tac vien upload anh
if ( current_user_can('contributor') && !current_user_can('upload_files') ){
    add_action('admin_init', 'allow_contributor_uploads');
    function allow_contributor_uploads() {
		$contributor = get_role('contributor');
	    $contributor->add_cap('upload_files');
	}
}
// cho phep cong tac vien dc phep edit_pages
if ( current_user_can('contributor') && !current_user_can('edit_pages') ){
    add_action('admin_init', 'allow_contributor_edit_pages');
    function allow_contributor_edit_pages() {
        $contributor = get_role('contributor');
        $contributor->add_cap('edit_pages');
    }
}

//--- begin remove comment author link

add_filter( 'genesis_comment_list_args', 'caia_change_genesis_comment_callback' );
function caia_change_genesis_comment_callback($args){
    if (genesis_html5()){
        $args['callback'] = 'caia_html5_comment_callback';
    }else{
        $args['callback'] = 'caia_comment_callback';
    }
    return $args;
}

function caia_html5_comment_callback( $comment, array $args, $depth ) {
    ?>

    <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
    <article <?php echo genesis_attr( 'comment' ); ?>>

        <?php        
        do_action( 'genesis_before_comment' );
        ?>

        <header <?php echo genesis_attr( 'comment-header' ); ?>>
            <p <?php echo genesis_attr( 'comment-author' ); ?>>
                <?php
                if ( 0 !== $args['avatar_size'] ) {
                    echo get_avatar( $comment, $args['avatar_size'] );
                }
                $author = get_comment_author();
                // $url    = get_comment_author_url();

                // if ( ! empty( $url ) && 'http://' !== $url ) {
                //     $author = sprintf( '<a href="%s" %s>%s</a>', esc_url( $url ), genesis_attr( 'comment-author-link' ), $author );
                // }

                
                $comment_author_says_text = apply_filters( 'comment_author_says_text', __( 'says', 'genesis' ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

                if ( ! empty( $comment_author_says_text ) ) {
                    $comment_author_says_text = '<span class="says">' . $comment_author_says_text . '</span>';
                }

                printf( '<span itemprop="name">%s</span> %s', $author, $comment_author_says_text );
                ?>
            </p>

            <?php
            
            $comment_date = apply_filters( 'genesis_show_comment_date', true, get_post_type() );

            if ( $comment_date ) {
                printf( '<p %s>', genesis_attr( 'comment-meta' ) );
                printf( '<time %s>', genesis_attr( 'comment-time' ) );
                printf( '<a href="%s" %s>', esc_url( get_comment_link( $comment->comment_ID ) ), genesis_attr( 'comment-time-link' ) );
                echo esc_html( get_comment_date() ) . ' ' . esc_html__( 'at', 'genesis' ) . ' ' . esc_html( get_comment_time() );
                echo '</a></time></p>';
            }

            edit_comment_link( __( '(Edit)', 'genesis' ), ' ' );
            ?>
        </header>

        <div <?php echo genesis_attr( 'comment-content' ); ?>>
            <?php if ( ! $comment->comment_approved ) : ?>
                <?php
                
                $comment_awaiting_moderation_text = apply_filters( 'genesis_comment_awaiting_moderation', __( 'Your comment is awaiting moderation.', 'genesis' ) );
                ?>
                <p class="alert"><?php echo $comment_awaiting_moderation_text; ?></p>
            <?php endif; ?>

            <?php comment_text(); ?>
        </div>

        <?php
        comment_reply_link(
            array_merge(
                $args,
                array(
                    'depth'  => $depth,
                    'before' => sprintf( '<div %s>', genesis_attr( 'comment-reply' ) ),
                    'after'  => '</div>',
                )
            )
        );
        ?>

        <?php
        
        do_action( 'genesis_after_comment' );
        ?>

    </article>
    <?php
    // No ending </li> tag because of comment threading.
}

function caia_comment_callback( $comment, array $args, $depth ) {
    ?>

    <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">

        <?php
        /** This action is documented in lib/structure/comments.php */
        do_action( 'genesis_before_comment' );
        ?>

        <div class="comment-header">
            <div class="comment-author vcard">
                <?php echo get_avatar( $comment, $args['avatar_size'] ); ?>
                <cite class="fn"><?php echo get_comment_author(); ?></cite>
                <span class="says">
                <?php
                    echo apply_filters( 'comment_author_says_text', __( 'says', 'genesis' ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
                ?>
                </span>
            </div>

            <div class="comment-meta commentmetadata">
                <a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><?php /* translators: 1: Comment date, 2: Comment time. */ printf( esc_html__( '%1$s at %2$s', 'genesis' ), esc_html( get_comment_date() ), esc_html( get_comment_time() ) ); ?></a>
                <?php edit_comment_link( esc_html__( '(Edit)', 'genesis' ), '' ); ?>
            </div>
        </div>

        <div class="comment-content">
            <?php if ( ! $comment->comment_approved ) : ?>
                <p class="alert"><?php echo apply_filters( 'genesis_comment_awaiting_moderation', __( 'Your comment is awaiting moderation.', 'genesis' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
            <?php endif; ?>

            <?php comment_text(); ?>
        </div>

        <div class="reply">
            <?php
            comment_reply_link(
                array_merge(
                    $args,
                    array(
                        'depth'     => $depth,
                        'max_depth' => $args['max_depth'],
                    )
                )
            );
            ?>
        </div>

        <?php
        /** This action is documented in lib/structure/comments.php */
        do_action( 'genesis_after_comment' );

        // No ending </li> tag because of comment threading.
}


//---- end remove comment author link

// Thêm các chức năng soạn thảo cho WP 4.9 trở lên
if ( ! function_exists( 'caia_ilc_mce_buttons' ) ){
    add_filter("mce_buttons_2", "caia_ilc_mce_buttons");
    function caia_ilc_mce_buttons($buttons){
        array_push($buttons,
            "unlink",
            "subscript",
            "superscript",
            "backcolor",
            "fontsizeselect",
            "alignjustify",
            "media"
        );
        return $buttons;
    }
}

// Thêm các kích thước chữ
if ( ! function_exists( 'caia_mce_text_sizes' ) ) {
    add_filter( 'tiny_mce_before_init', 'caia_mce_text_sizes' );
    function caia_mce_text_sizes( $initArray ){
        $initArray['fontsize_formats'] = "5px 6px 7px 8px 9px 10px 11px 12px 13px 14px 15px 16px 17px 18px 19px 20px 21px 24px 28px 32px 36px 40px 46px 48px 60px 72px";
        return $initArray;
    }
}




// remove emoji cua wp
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );


// fix register spam wp
add_filter( 'register', '__return_false' );
add_action( 'init', 'caia_redirect_registration_page' );
function caia_redirect_registration_page() {
    if ( isset( $_GET['action'] ) && $_GET['action'] == 'register' ) {
        ob_start();
        wp_redirect( wp_login_url() );
        ob_clean();
    }
}


// tắt bớt option ko bao giờ dùng tới
add_action( 'after_setup_theme', 'caia_change_default_value' );
function caia_change_default_value(){
    add_filter( 'pre_option_default_ping_status', '__return_zero' );
    add_filter( 'pre_option_default_pingback_flag', '__return_zero' );
    // add_filter( 'pre_option_ping_sites', function (){ return 'http://demo.vn'; } );

    add_filter( 'enable_post_by_email_configuration', '__return_false' );
    add_filter( 'enable_update_services_configuration', '__return_false' );

    remove_action( 'do_pings', 'do_all_pings', 10);
}

// --------------------------------
// toi uu Admin panel
if(is_admin()){	

	// remove bớt metabox dư thừa ở daskboard đi => áp dụng với mọi user
    add_action( 'wp_dashboard_setup', 'caia_remove_dashboard_widgets' );

    // Faster Admin Post Lists
    // add_filter( 'posts_fields', 'caia_limit_post_fields_on_post_list', 0, 2 );

    
    // remove bot metabox ko can thiet o trang edit post.
    add_action('admin_menu', 'remove_edit_metabox');

    //set metabox ngầm định
    add_action('admin_init', 'set_user_metaboxes');

    // remove comment count from ds post/page/post type
    add_filter('manage_posts_columns', 'caia_custom_post_columns');

    // Disable change link structure        
    add_action('admin_head-options-permalink.php', 'caia_hide_permalink', 5);     

    // Remove update nag
    add_action('admin_menu','caia_remove_update_nag');

    
    // neu ko phai ban genesis sua rieng cho CAIA thi optimize mot so diem
    if(!defined('GENESIS_EDIT_BY_CAIA')){ 
        caia_genesis_optimize();        
    }    

    // toi uu admin cua SEO by Yoast
    if(defined('WPSEO_VERSION')){
        if(defined('WPSEO_EDIT_BY_CAIA')){ // la ban chinh rieng cho CAIA
            
            // disable Page Analysis and calcul seo score
            add_filter('wpseo_use_page_analysis', '__return_false');
            // remove column of SEO by Yoast from list page of post/page/post type
            add_filter('caia_wpseo_add_meta_to_post_list', '__return_false');
                       
        }else{
                 
            // disable Page Analysis and calcul seo score
            add_filter('wpseo_use_page_analysis', '__return_false');            

            // remove metabox of wp seo from daskboard admin            
            // remove_action( 'wp_dashboard_setup', array( $GLOBALS['wpseo_admin']->dashboard_widget, 'add_dashboard_widget' ) );
            
            // remove section profile of Yoast
            remove_object_hook( 'show_user_profile', 'WPSEO_Admin_User_Profile', 'user_profile');
            remove_object_hook( 'edit_user_profile', 'WPSEO_Admin_User_Profile', 'user_profile');
            remove_object_hook( 'personal_options_update', 'WPSEO_Admin_User_Profile', 'process_user_option_update' );
            remove_object_hook( 'edit_user_profile_update', 'WPSEO_Admin_User_Profile', 'process_user_option_update' );    
        }
        
    }

} // end of is_admin()



// remove update nag
function caia_remove_update_nag() {
    remove_action( 'admin_notices', 'update_nag', 3 );    
    remove_action( 'admin_notices', 'maintenance_nag' );
}

function caia_genesis_optimize(){

    if(version_compare(PARENT_THEME_VERSION, '2.1') >=0 ){
        // remove taxonomy archive option
        remove_action( 'admin_init', 'genesis_add_taxonomy_archive_options' );


        // remove section from profile.php
        remove_filter( 'user_contactmethods', 'genesis_user_contactmethods' );
        remove_action( 'show_user_profile', 'genesis_user_options_fields' );
        remove_action( 'edit_user_profile', 'genesis_user_options_fields' );
        remove_action( 'show_user_profile', 'genesis_user_archive_fields' );
        remove_action( 'edit_user_profile', 'genesis_user_archive_fields' );
        remove_action( 'show_user_profile', 'genesis_user_seo_fields' );
        remove_action( 'edit_user_profile', 'genesis_user_seo_fields' );
        remove_action( 'show_user_profile', 'genesis_user_layout_fields' );
        remove_action( 'edit_user_profile', 'genesis_user_layout_fields' );
        remove_action( 'personal_options_update',  'genesis_user_meta_save' );
        remove_action( 'edit_user_profile_update', 'genesis_user_meta_save' );
        remove_filter( 'get_the_author_genesis_author_box_single', 'genesis_author_box_single_default_on', 10, 2 );

        // Disable chuc nang update va check update cua genesis        
        remove_action( 'admin_init', 'genesis_upgrade', 20 );    
        remove_action( 'admin_notices', 'genesis_upgraded_notice' );
        remove_action( 'admin_notices', 'genesis_update_nag' );
        remove_action( 'init', 'genesis_update_email' );
        remove_filter( 'transient_update_themes', 'genesis_update_push' );
        remove_action( 'load-update-core.php', 'genesis_clear_update_transient' );
        remove_action( 'load-themes.php', 'genesis_clear_update_transient' );

        // Remove unuseful genesis metabox
        add_action( 'genesis_theme_settings_metaboxes', 'caia_remove_genesis_metaboxes' );  
        add_action('admin_head-toplevel_page_genesis', 'hide_unuse_genesis_setting');

    }else {
        // remove taxonomy archive option
        remove_action( 'admin_init', 'genesis_add_taxonomy_archive_options' );

        // remove user profile.php
        remove_filter( 'user_contactmethods', 'genesis_user_contactmethods' );
        remove_action( 'admin_init', 'genesis_add_user_profile_fields' );
        remove_action( 'personal_options_update',  'genesis_user_meta_save' );
        remove_action( 'edit_user_profile_update', 'genesis_user_meta_save' );
        remove_filter( 'get_the_author_genesis_author_box_single', 'genesis_author_box_single_default_on', 10, 2 );

        // Disable chuc nang update va check update cua genesis        
        remove_action( 'admin_init', 'genesis_upgrade', 20 );    
        remove_action( 'admin_notices', 'genesis_upgraded_notice' );
        remove_action( 'admin_notices', 'genesis_update_nag' );
        remove_action( 'init', 'genesis_update_email' );
        remove_filter( 'transient_update_themes', 'genesis_update_push' );
        remove_action( 'load-update-core.php', 'genesis_clear_update_transient' );
        remove_action( 'load-themes.php', 'genesis_clear_update_transient' );

        // Remove unuseful genesis metabox
        add_action( 'genesis_theme_settings_metaboxes', 'caia_remove_genesis_metaboxes' ); 
        add_action('admin_head-toplevel_page_genesis', 'hide_unuse_genesis_setting'); 

    }
}

function remove_some_widgets(){
    unregister_sidebar( 'sidebar-alt' );
}
add_action( 'widgets_init', 'remove_some_widgets', 11 );

// hàm này sẽ ẩn form cập nhật permalink đi
function caia_hide_permalink(){
    echo '<style>form>table.form-table, form>h3.title {display: none;}</style>';
}

// remove comment count collum from post list
function caia_custom_post_columns($defaults) {
    unset($defaults['comments']);
    
    return $defaults;
}

// set metabox default cho trang edit post
function set_user_metaboxes($user_id=NULL) {

    // These are the metakeys we will need to update
    $meta_key['order'] = 'meta-box-order_post';
    $meta_key['hidden'] = 'metaboxhidden_post';

    // So this can be used without hooking into user_register
    if ( ! $user_id)
        $user_id = get_current_user_id(); 

    // Set the default order if it has not been set yet
    if ( ! get_user_meta( $user_id, $meta_key['order'], true) ) {
        $meta_value = array(
            'side' => 'submitdiv,formatdiv,categorydiv,tagsdiv-post_tag,postimagediv',
            'normal' => 'wpseo_meta,postexcerpt,postcustom,commentstatusdiv,commentsdiv,trackbacksdiv,authordiv,slugdiv,revisionsdiv',
            'advanced' => '',
        );
        update_user_meta( $user_id, $meta_key['order'], $meta_value );
    }

    // Set the default hiddens if it has not been set yet
    if ( ! get_user_meta( $user_id, $meta_key['hidden'], true) ) {
        $meta_value = array('yarpp_relatedposts','genesis_inpost_layout_box','genesis_inpost_scripts_box','postcustom','trackbacksdiv','commentstatusdiv','commentsdiv','slugdiv','authordiv','revisionsdiv');
        update_user_meta( $user_id, $meta_key['hidden'], $meta_value );
    }
}

// remove bớt metabox khi add new post
function remove_edit_metabox()
{
    remove_meta_box('authordiv', 'post', 'normal');    
    remove_meta_box('trackbacksdiv', 'post', 'normal');
    remove_meta_box('postcustom', 'post', 'normal');
}

if ( current_user_can('publish_posts') ){
    add_action( 'post_submitbox_misc_actions', 'caia_author_in_publish', 1000 );    
}

function caia_author_in_publish() {

    global $post_ID;

    $post = get_post( $post_ID );
    echo '<div class="misc-pub-section">Tác giả: ';
    post_author_meta_box( $post );
    echo '</div>';
}

function caia_remove_genesis_metaboxes( $_genesis_theme_settings_pagehook ) {
	remove_meta_box( 'genesis-theme-settings-feeds', $_genesis_theme_settings_pagehook, 'main' );	
	remove_meta_box( 'genesis-theme-settings-blogpage', $_genesis_theme_settings_pagehook, 'main' );	
    remove_meta_box( 'genesis-theme-settings-adsense', $_genesis_theme_settings_pagehook, 'main' );    
}

function hide_unuse_genesis_setting()
{
    echo '<style>div#genesis-theme-settings-header, div#genesis-theme-settings-nav {display: none;}</style>';
}

// bỏ bớt những metabox dư thừa đi
function caia_remove_dashboard_widgets() {   
    
    // thuc chat la thao tac vao bien global $wp_meta_boxes
    remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );    
    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );  

    remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');
    remove_meta_box( 'dashboard_php_nag', 'dashboard', 'normal'); 

}

add_action( 'admin_menu', 'remove_site_health_menu' );  
function remove_site_health_menu(){
  remove_submenu_page( 'tools.php','site-health.php' ); 
  remove_submenu_page( 'tools.php','export-personal-data.php' ); 
  remove_submenu_page( 'tools.php','erase-personal-data.php' ); 
}

// tang toc post list http://unserkaiser.com
function caia_limit_post_fields_on_post_list( $fields, $query )
{
  if (
        ! is_admin()
        OR ! $query->is_main_query()
        OR ( defined( 'DOING_AJAX' ) AND DOING_AJAX )
        OR ( defined( 'DOING_CRON' ) AND DOING_CRON )
    )
        return $fields;

    $p = $GLOBALS['wpdb']->posts;
    
    // thực chất là thay vì lấy all field của wp_post ($p), mà chỉ lấy các field cần thiết thôi.
    return implode( ",", array(
        "{$p}.ID",
        "{$p}.post_date",
        "{$p}.post_date_gmt",
        "{$p}.post_name",
        "{$p}.post_title",
        "{$p}.ping_status",
        "{$p}.post_author",
        "{$p}.post_password",
        "{$p}.post_status",
        "{$p}.comment_status",
        "{$p}.comment_count",
    ) );
}

//--------------------------------
// Optimize RSS for SEO by CAIA;
// Remove chuc nang rss cua genesis va wordpress
if(!defined('GENESIS_EDIT_BY_CAIA')){
    remove_filter( 'feed_link', 'genesis_feed_links_filter', 10, 2 );
    remove_action( 'template_redirect', 'genesis_feed_redirect' );
}
remove_action( 'wp_head', 'feed_links', 2 );
add_action( 'wp_head', 'caia_feed_links', 2 );
add_action( 'wp_head', 'remove_single_feed', 1);



function caia_feed_links( $args = array() ) {

  $defaults = array(
    /* translators: Separator between blog name and feed type in feed links */
    'separator' => _x('&raquo;', 'feed link'),
    /* translators: 1: blog title, 2: separator (raquo) */
    'feedtitle' => __('%1$s %2$s Feed'),    
  );

  $args = wp_parse_args( $args, $defaults );

  echo '<link rel="alternate" type="' . feed_content_type() . '" title="' . esc_attr( sprintf( $args['feedtitle'], get_bloginfo('name'), $args['separator'] ) ) . '" href="' . esc_url( get_feed_link() ) . "\" />\n";
}

// Remove single feed, is_single don't work in functions.php
function remove_single_feed()
{
    if( is_single()){       
        remove_action( 'wp_head', 'feed_links_extra', 3 );
    }
}


// ----------------------------------------------
// rename file upload, remove sigal
// Auto rename upload file
add_filter('sanitize_file_name', 'caia_rename_upload_files', 10);

function caia_rename_upload_files($filename) {
    $info = pathinfo($filename);
    $ext  = empty($info['extension']) ? '' : '.' . $info['extension'];
    $name = basename($filename, $ext);
    $name = sanitize_title($name);
    return $name . $ext;
}


// -----------------------------------------------
// toi uu switch theme ko gay ra loi data widget
add_action('switch_theme', 'caia_switch_theme');

function caia_switch_theme () {
    caia_log('theme', 'switch', 1);
    // luu lai hien trang widget truoc khi switch
    $sidebars_widgets = get_option('sidebars_widgets');
    $data['sidebars_widgets'] = $sidebars_widgets;
    

    $wig_array = array();
    foreach ($sidebars_widgets as $key => $sidebar) {
        # code...
        if($key === 'array_version') continue;

        if(!is_array($sidebar)) continue;

        foreach ($sidebar as $wname) {      
            # code...
            // go bo phan id cuoi name di
            $arr = explode('-', $wname);
            if(count($arr) > 0)
            {
                unset($arr[count($arr) - 1]);
            }
            $nname = implode('-', $arr);
            if(!in_array($nname, $wig_array)){
                $wig_array[] = $nname;
            }
        }   
    }   

    foreach ($wig_array as $wname) {
        # code...
        $option_name = 'widget_' . $wname;
        $val = get_option( $option_name , '' );
        $data[$option_name] = $val;
    }

    
    $data['caia_switch_theme_time'] = time();

    update_option('caia_switch_theme_backup', $data);
}


add_action('after_switch_theme', 'caia_after_switch_theme');

function caia_after_switch_theme () {
    caia_log('theme', 'after-switch', 1);

    $data = get_option( 'caia_switch_theme_backup', false );
    if (isset($data['caia_switch_theme_time'])){
        $backup_time = $data['caia_switch_theme_time'];
        // neu dat chua qua 1 ngay thi chap nhan restore
        $now = time();
        if ($now - $backup_time < 24*60*60){ // kem hon 1 ngay
            // tien hanh restore
            foreach ($data as $key => $value) {
                if ($key === 'caia_switch_theme_time') continue;
                update_option( $key, $value );
            }
        }
    }


}

// end switch theme

// ---------------------------------
// fix unicode to hop
function caia_to_unicode_dung_san($str) {
    $maps = array(  'á' => 'á',
                    'à' => 'à',
                    'ả' => 'ả',
                    'ã' => 'ã',
                    'ạ' => 'ạ',
                    'ă' => 'ă',
                    'ắ' => 'ắ',
                    'ằ' => 'ằ',
                    'ẳ' => 'ẳ',
                    'ẵ' => 'ẵ',
                    'ặ' => 'ặ',
                    'â' => 'â',
                    'ấ' => 'ấ',
                    'ầ' => 'ầ',
                    'ẩ' => 'ẩ',
                    'ậ' => 'ậ',
                    'ẫ' => 'ẫ',
                    'ó' => 'ó',
                    'ò' => 'ò',
                    'ỏ' => 'ỏ',
                    'õ' => 'õ',
                    'ọ' => 'ọ',
                    'ô' => 'ô',
                    'ố' => 'ố',
                    'ồ' => 'ồ',
                    'ổ' => 'ổ',
                    'ỗ' => 'ỗ',
                    'ộ' => 'ộ',
                    'ơ' => 'ơ',
                    'ớ' => 'ớ',
                    'ờ' => 'ờ',
                    'ở' => 'ở',
                    'ỡ' => 'ỡ',
                    'ợ' => 'ợ',
                    'ú' => 'ú',
                    'ù' => 'ù',
                    'ủ' => 'ủ',
                    'ũ' => 'ũ',
                    'ụ' => 'ụ',
                    'ư' => 'ư',
                    'ứ' => 'ứ',
                    'ừ' => 'ừ',
                    'ử' => 'ử',
                    'ự' => 'ự',
                    'ữ' => 'ữ',
                    'é' => 'é',
                    'è' => 'è',
                    'ẻ' => 'ẻ',
                    'ẽ' => 'ẽ',
                    'ẹ' => 'ẹ',
                    'ê' => 'ê',
                    'ế' => 'ế',
                    'ề' => 'ề',
                    'ể' => 'ể',
                    'ễ' => 'ễ',
                    'ệ' => 'ệ',
                    'í' => 'í',
                    'ì' => 'ì',
                    'ỉ' => 'ỉ',
                    'ĩ' => 'ĩ',
                    'ị' => 'ị',
                    'ý' => 'ý',
                    'ỳ' => 'ỳ',
                    'ỷ' => 'ỷ',
                    'ỹ' => 'ỹ',
                    'ỵ' => 'ỵ',
                    'đ' => 'đ',

                // Capital
                    'Á' => 'Á',
                    'À' => 'À',
                    'Ả' => 'Ả',
                    'Ã' => 'Ã',
                    'Ạ' => 'Ạ',
                    'Ă' => 'Ă',
                    'Ắ' => 'Ắ',
                    'Ằ' => 'Ằ',
                    'Ẳ' => 'Ẳ',
                    'Ẵ' => 'Ẵ',
                    'Ặ' => 'Ặ',
                    'Â' => 'Â',
                    'Ấ' => 'Ấ',
                    'Ầ' => 'Ầ',
                    'Ẩ' => 'Ẩ',
                    'Ậ' => 'Ậ',
                    'Ẫ' => 'Ẫ',
                    'Ó' => 'Ó',
                    'Ò' => 'Ò',
                    'Ỏ' => 'Ỏ',
                    'Õ' => 'Õ',
                    'Ọ' => 'Ọ',
                    'Ô' => 'Ô',
                    'Ố' => 'Ố',
                    'Ồ' => 'Ồ',
                    'Ổ' => 'Ổ',
                    'Ỗ' => 'Ỗ',
                    'Ộ' => 'Ộ',
                    'Ơ' => 'Ơ',
                    'Ớ' => 'Ớ',
                    'Ờ' => 'Ờ',
                    'Ở' => 'Ở',
                    'Ỡ' => 'Ỡ',
                    'Ợ' => 'Ợ',
                    'Ú' => 'Ú',
                    'Ù' => 'Ù',
                    'Ủ' => 'Ủ',
                    'Ũ' => 'Ũ',
                    'Ụ' => 'Ụ',
                    'Ư' => 'Ư',
                    'Ứ' => 'Ứ',
                    'Ừ' => 'Ừ',
                    'Ử' => 'Ử',
                    'Ữ' => 'Ữ',
                    'Ự' => 'Ự',
                    'É' => 'É',
                    'È' => 'È',
                    'Ẻ' => 'Ẻ',
                    'Ẽ' => 'Ẽ',
                    'Ẹ' => 'Ẹ',
                    'Ê' => 'Ê',
                    'Ế' => 'Ế',
                    'Ề' => 'Ề',
                    'Ể' => 'Ể',
                    'Ễ' => 'Ễ',
                    'Ệ' => 'Ệ',
                    'Í' => 'Í',
                    'Ì' => 'Ì',
                    'Ỉ' => 'Ỉ',
                    'Ĩ' => 'Ĩ',
                    'Ị' => 'Ị',
                    'Ý' => 'Ý',
                    'Ỳ' => 'Ỳ',
                    'Ỷ' => 'Ỷ',
                    'Ỹ' => 'Ỹ',
                    'Ỵ' => 'Ỵ',
                    'Đ' => 'Đ');
                    
    return strtr($str, $maps);
} 
    
add_filter( 'wp_insert_post_data' , 'caia_fix_unicode_to_hop' , 99, 2 );
function caia_fix_unicode_to_hop( $data , $postarr ) {
    
    if (isset($data['post_type']) && $data['post_type'] !== 'revision'){
        $data['post_title'] = caia_to_unicode_dung_san($data['post_title']);
        $data['post_content'] = caia_to_unicode_dung_san($data['post_content']);

        // caia_log('unicode', 'update', 1);
    }
    
    return $data;
}

// end fix unicode to top



//---------------------------------
// funtion ho tro

/**
 * Remove an object hook (action/filter).
 *
 * @param  string $tag                Hook name.
 * @param  string $class              Class name. Use 'Closure' for anonymous functions.
 * @param  string|void $method        Method name. Leave empty for anonymous functions.
 * @param  string|int|void $priority  Priority
 * @return void
 */
function remove_object_hook( $tag, $class, $method = NULL, $priority = NULL ) {
    $filters = $GLOBALS['wp_filter'][ $tag ];
    if ( empty ( $filters ) ) {
        return;
    }
    foreach ( $filters as $p => $filter ) {
        if ( ! is_null($priority) && ( (int) $priority !== (int) $p ) ) continue;
        $remove = FALSE;
        foreach ( $filter as $identifier => $function ) {
            $function = $function['function'];
            if (
            is_array( $function )
            && (
              is_a( $function[0], $class )
              || ( is_array( $function ) && $function[0] === $class )
            )) {
                $remove = ( $method && ( $method === $function[1] ) );
            } elseif ( $function instanceof Closure && $class === 'Closure' ) {
                $remove = TRUE;
            }
            if ( $remove ) {
                unset( $GLOBALS['wp_filter'][$tag][$p][$identifier] );
            }
        }
    }
}
