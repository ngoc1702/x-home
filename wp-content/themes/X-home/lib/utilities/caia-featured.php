<?php
/*
 * Handles including the utilities Caia-Featured
 * Readmore: utilites/caia-featured.php
 * WordPress.
 *
 * @category CAIA
 * @package  Utilities
 * @author   TuanNM
 * @date     23/10/2017
 * Changelog:
 * - 06/10/21: fix js tương thích với wp 5.5
 */
 
if( !defined('GF_URL') )
    define( 'GF_URL', get_stylesheet_directory_uri() . '/' );
    

/**********************************************************/
// import tu DB cu
if(is_admin()){
    if ( get_option('caia_featured_upgraded', false) === false){
        $sql = 
            "UPDATE {$wpdb->postmeta} meta 
              inner join (SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'gf_featured' and meta_value = 'false') meta2
              on meta.post_id = meta2.post_id
            SET meta_value = '0' 
            WHERE meta_key = 'gf_featured_order'";

        $wpdb->query( $sql );

        $sql = "delete from {$wpdb->postmeta} where meta_key = 'gf_featured'";
        $wpdb->query( $sql );


        $sql = 
            "UPDATE {$wpdb->postmeta} meta 
              inner join (SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'gf_featured_by_cat' and meta_value = 'false') meta2
              on meta.post_id = meta2.post_id
            SET meta_value = '0' 
            WHERE meta_key = 'gf_featured_by_cat_order' ";

        $wpdb->query( $sql );


        $sql = "delete from {$wpdb->postmeta} where meta_key = 'gf_featured_by_cat'";
        $wpdb->query( $sql ) ;

        caia_log('featured', 'upgraded', '4.8');

        update_option('caia_featured_upgraded', '4.8');
    }
}

/**********************************************************/
// Tich hop featured meta box
if(is_admin()){
    //Add Featured Meta Box
    add_action('add_meta_boxes', 'gf_featured_meta_box');
    add_action( 'save_post', 'gf_save_featureddata' );
}
function gf_featured_meta_box() {
    $post_types = get_post_types( array( 'public' => true, 'show_ui' => true, ) );
    unset($post_types['attachment']);
    unset($post_types['page']);
    unset($post_types['ordering']);
    // caia_log('featured', 'post_type', $post_types);

    foreach( $post_types as $post_type ) {
        add_meta_box(
            'caia_featured',
            __( 'Featured', 'caia' ),
            'gf_inner_featured_box',
            $post_type,
            'side',
            'high'
        );
    }

}

function gf_inner_featured_box($post) {
    // Use nonce for verification
    wp_nonce_field( plugin_basename( __FILE__ ), 'gf_nonce' );
    
    $featured = get_post_custom($post->ID);   
    $gf_featured_order = isset( $featured['gf_featured_order'][0] ) ? $featured['gf_featured_order'][0] : 0 ;
    $gf_featured_by_cat_order = isset( $featured['gf_featured_by_cat_order'][0] ) ? $featured['gf_featured_by_cat_order'][0] : 0 ;

?>
    <div style="margin-bottom: 20px;">
        
        <label for="gf_featured_order">
           <?php _e("STT nổi toàn trang: ", 'caia' ); ?>
        </label>
        <input type="text" id="gf_featured_order" name="gf_featured_order" value="<?php echo $gf_featured_order; ?>" size="2" />
        <input type="button" id="gf_make_newest_featured" class="button" style="cursor: pointer;" value="<?php _e('Be topest', 'caia'); ?>" />
        
    </div>

    <div>
        <label for="gf_featured_order">
           <?php _e("STT nổi chuyên mục: ", 'caia' ); ?>
        </label>
        <input type="text" id="gf_featured_by_cat_order" name="gf_featured_by_cat_order" value="<?php echo $gf_featured_by_cat_order; ?>" size="2" />
        <input type="button" id="gf_make_newest_fbc" class="button" style="cursor: pointer;" value="<?php _e('Be topest', 'caia'); ?>" />       
    </div>
    <i>&nbsp; - STT bằng 0 nghĩa là không nổi bật.<br>&nbsp; - STT càng cao, càng có thứ hạng cao.</i>
    
    <script type="text/javascript">
        jQuery.noConflict();
        jQuery(document).ready(function($) {
            $("#gf_make_newest_featured").click(function(){
                 $("#gf_featured_order").val(<?php echo get_option('gf_featured_order_num') + 1; ?>);
            });
            
            
            $("#gf_make_newest_fbc").click(function(){
                 $("#gf_featured_by_cat_order").val(<?php echo get_option('gf_fbc_order_num') + 1; ?>);
            });
            
        });
    </script>
    
<?php
}

function gf_save_featureddata($post_id) {
    // verify if this is an auto save routine. 
    // If it is our form has not been submitted, so we dont want to do anything
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
        return;
    
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    
    if ( !wp_verify_nonce( $_POST['gf_nonce'], plugin_basename( __FILE__ ) ) )
        return;
    
    
    // Check permissions
    if ( 'page' === $_POST['post_type'] ) {
        return;
    }
    else {
        if ( !current_user_can( 'edit_post', $post_id ) )
            return;
    }
    
    // OK, we're authenticated: we need to find and save the data
    
    //Current post featured status
    $featured = get_post_custom($post_id);
    
    //New post featured status    
    $gf_featured_order = intval($_POST['gf_featured_order']);
    
    $gf_fbc_order = intval($_POST['gf_featured_by_cat_order']);
    
    
    $gf_featured_num = get_option('gf_featured_order_num');
    if($gf_featured_num == ''){        
        add_option('gf_featured_order_num', 0, '', 'no');
        $gf_featured_num = 0;
    }

    $gf_fbc_num = get_option('gf_fbc_order_num');
    if($gf_fbc_num == '') {        
        add_option('gf_fbc_order_num', 0, '', 'no');
        $gf_fbc_num = 0;
    }

    
    update_post_meta($post_id, 'gf_featured_order', $gf_featured_order);
    
    update_post_meta($post_id, 'gf_featured_by_cat_order', $gf_fbc_order);
        
    //Update options
    if($gf_featured_num < $gf_featured_order)
        update_option('gf_featured_order_num', $gf_featured_order);
        
    if($gf_fbc_num < $gf_fbc_order)
        update_option('gf_fbc_order_num', $gf_fbc_order);
}

// End tich hop feature meta box


/************************************************************/
// Tich hop featured admin header -> Ajax
if(is_admin()){
    //Add script and css to edit post page
    add_action( 'admin_head-edit.php', 'gf_admin_head' );    
    add_action( 'admin_footer-edit.php', 'do_caia_featured_javascript' );
    add_action( 'wp_ajax_do_caia_featured', 'do_caia_featured_ajax' );
}
function gf_admin_head() {
?>
    <style>
        .feature-star {
            display: inline-block;
            width: 32px;
            height: 32px;
            background: url(<?php echo GF_URL; ?>images/caia-featured/featured-stars.png) no-repeat 0 0;
            vertical-align: middle;
            text-indent: -9999px;
        }
        
        .gf-featured {
            background-position: -32px 0 ;
        }
        
        .gf-not-featured {
            background-position: 0 0 ;
        }
        
        td.gf_featured, td.gf_featured_by_cat {
            text-align: center;
            vertical-align: middle;
        }
        
        .gf-loading {
            background: url(<?php echo GF_URL; ?>images/caia-featured/loading.gif) no-repeat 0 0;
        }
    </style>    
        
<?php
}


function do_caia_featured_javascript() {
    ?>
    <script type="text/javascript" >
    jQuery.noConflict();
    jQuery(document).ready(function($) {
        $(document).on('click', "#gf-link-ajax", function(){
            var cur_postid = $(this).attr('post');
            var cur_action = $(this).attr('action');
            var currentItem = $(this);
            var data = {
                action : 'do_caia_featured',
                do_action: cur_action,
                post : cur_postid                 
            };
            

            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: data,
                dataType: 'json',
                beforeSend : function(){
                    currentItem.addClass("gf-loading");
                },
                success: function(data) {
                    // alert(data);             
                    // currentItem.removeClass("gf-loading");
                    if(cur_action.indexOf('set')) {                 
                        currentItem.removeClass('gf-featured').addClass('gf-not-featured');
                    } else {                  
                        currentItem.removeClass('gf-not-featured').addClass('gf-featured');
                    }
                    console.log(cur_action.indexOf('set'));
                    if( typeof data.order !== 'undefined')
                        currentItem.next('span').text(data.order);
                    
                    currentItem.attr('action', data.newAction);
                    currentItem.removeClass("gf-loading");
                }
            });

            // de ko scroll browser len top
            return false; 
            
        });
    });
    </script>
    <?php
}


function do_caia_featured_ajax() {
    /* Begin ajax */
    $action = $_POST['do_action'];
    $post_id = $_POST['post'];
    

    $post_custom = get_post_custom($post_id);

    $featured_num = get_option('gf_featured_order_num');
    if($featured_num == '')
    {        
        add_option('gf_featured_order_num', 0, '', 'no');
        $featured_num = 0;
    }
    $featured_by_cat_num = get_option('gf_fbc_order_num');
    if($featured_by_cat_num == '')
    {        
        add_option('gf_fbc_order_num', 0, '', 'no');
        $featured_by_cat_num = 0;
    }


    if( isset($action) && isset($post_id) ) {
        $data = array();
        switch( $action ) {
            case 'setFeatured' :
                update_post_meta($post_id, 'gf_featured_order', ++$featured_num);
                update_option('gf_featured_order_num', $featured_num);
                $data['order'] = $featured_num;
                $data['newAction'] = 'removeFeatured';
                break;

            case 'removeFeatured' :
                update_post_meta($post_id, 'gf_featured_order', '0');
                $data['order'] = 0;
                $data['newAction'] = 'setFeatured';
                break;

            case 'setFeaturedByCat' :
                update_post_meta($post_id, 'gf_featured_by_cat_order', ++$featured_by_cat_num);
                update_option('gf_fbc_order_num', $featured_by_cat_num);
                $data['order'] = $featured_by_cat_num;
                $data['newAction'] = 'removeFeaturedByCat';
                break;

            case 'removeFeaturedByCat' :
                update_post_meta($post_id, 'gf_featured_by_cat_order', '0');
                $data['order'] = 0;
                $data['newAction'] = 'setFeaturedByCat';
                break;
        }
                
        echo json_encode($data);
    }

    die(); // this is required to return a proper result
}

// End featured admin header


/**************************************************/
// Tich hop manage column
if(is_admin()){

    $post_types = array('attachment', 'page', 'ordering');

    $typenow = null; // Khởi tạo biến trước

    if (isset($_GET['post_type']) && in_array($_GET['post_type'], $post_types)) {
        $typenow = $_GET['post_type'];
    }

    //$typenow = $_GET['post_type'];

    if ( !in_array($typenow, $post_types) || empty($typenow) ) {
        add_filter('manage_posts_columns', 'gf_manage_posts_columns');
    }

}
function gf_manage_posts_columns($columns) {
    $columns['gf_featured'] = __('Nổi bật toàn bộ', 'caia');
    $columns['gf_featured_by_cat'] = __('Nổi bật chuyên mục', 'caia');
    
    return $columns;
}

if(is_admin()){
    add_action('manage_posts_custom_column', 'gf_manage_posts_custom_columns');
    // manage_' . $post_type . '_custom_column -> ko hoat dong
    // foreach( $post_types as $post_type )
    //     add_action('manage_' . $post_type . '_custom_column', 'gf_manage_posts_custom_columns');
}
function gf_manage_posts_custom_columns($name) {
    global $post;
    $post_custom = get_post_custom($post->ID);
    // print_r($post_custom);
    switch($name) {
        case 'gf_featured' :            
            $gf_featured_order = isset( $post_custom['gf_featured_order'][0]) ? $post_custom['gf_featured_order'][0] : 0;

            $featured = $gf_featured_order != 0 ? 'gf-featured' : 'gf-not-featured';
            $action = $featured == 'gf-featured' ? 'removeFeatured' : 'setFeatured';
            echo '<a title="Featured it!" href="#" id="gf-link-ajax" class="feature-star '.$featured.' " post="' . $post->ID . '" action="' . $action . '" >Featured</a>';
            echo '<span style="display: inline-block; margin-left: 10px;">' . $gf_featured_order . '</span>';
            break;
        
        case 'gf_featured_by_cat' :            
            $gf_featured_by_cat_order = isset( $post_custom['gf_featured_by_cat_order'][0] ) ? $post_custom['gf_featured_by_cat_order'][0]  : 0;

            $featured = $gf_featured_by_cat_order != 0 ? 'gf-featured' : 'gf-not-featured';
            $action = $featured == 'gf-featured' ? 'removeFeaturedByCat' : 'setFeaturedByCat';
            echo '<a title="Featured it by category!" href="#" id="gf-link-ajax" class="feature-star '.$featured.' " post="' . $post->ID . '" action="' . $action . '" >Not Featured</a>';
            echo '<span style="display: inline-block; margin-left: 10px;">' . $gf_featured_by_cat_order . '</span>';
            break;
    }
}


if(is_admin()){
    // add_filter("manage_edit-post_sortable_columns", 'gf_sortable_columns');
    foreach( $post_types as $post_type )
        add_filter('manage_edit-' . $post_type . '_sortable_columns', 'gf_sortable_columns');

    add_action( 'pre_get_posts', 'gf_featured_orderby' );

}
function gf_sortable_columns( $columns ) {
    $custom = array(
        // meta column id => sortby value used in query
        'gf_featured'    => 'Feautured',
        'gf_featured_by_cat' => 'FeauturedByCategory',
    );
    
    return wp_parse_args($custom, $columns);
    
}


function gf_featured_orderby( $query ) {
    // if( ! is_admin() ) return;
 
    $orderby = $query->get( 'orderby');
 
    if( 'Feautured' == $orderby ) {
        $query->set('meta_key','gf_featured_order');
        $query->set('orderby','meta_value_num');
    }

    if( 'FeauturedByCategory' == $orderby ) {
        $query->set('meta_key','gf_featured_by_cat_order');
        $query->set('orderby','meta_value_num');
    }
}



// End Manage column

/**********************************************************/
// Tich hop featured query vars

//Add new query var
add_filter('query_vars', 'gf_add_query_vars', 5, 1);
function gf_add_query_vars($public_query_vars) {
    $public_query_vars[] = 'featured';  
    return $public_query_vars;
}

//Add filter to pre_get_posts
add_filter('pre_get_posts', 'gf_pre_get_posts', PHP_INT_MAX);
function gf_pre_get_posts( $query ) {  

    if($query->get('featured') === 'all') {
        // feature_all
       
        if (isset($query->query['meta_key'])){
            $featured_query = array();

            $featured_query[] = array (
                    'key'     => 'gf_featured_order',
                    'value'   => 1,
                    'type'    => 'numeric',
                    'compare' => '>=',

                );

            $query->set('meta_query', $featured_query);  

        }else{

            $query->set('meta_key', 'gf_featured_order');
            $query->set('meta_type', 'SIGNED');
            $query->set('meta_value', '1');
            $query->set('meta_compare', '>=');
                           
        }

        if($query->get('orderby') === 'featured_order') {                            
            $query->set('orderby', 'meta_value_num');
        }
             

    } elseif($query->get('featured') === 'category') {
        // feature by category

        if (isset($query->query['meta_key'])){
            $featured_query = array();

            $featured_query[] = array (
                    'key'     => 'gf_featured_by_cat_order',
                    'value'   => 1,
                    'type'    => 'numeric',
                    'compare' => '>=',

                );

            $query->set('meta_query', $featured_query);  

        }else{

            $query->set('meta_key', 'gf_featured_by_cat_order');
            $query->set('meta_type', 'SIGNED');
            $query->set('meta_value', '1');
            $query->set('meta_compare', '>=');
                           
        }

        if($query->get('orderby') === 'featured_by_cat_order') {                            
            $query->set('orderby', 'meta_value_num');
        }
       
    }    

    return $query;
}

// end tich hop featured-query-vars

