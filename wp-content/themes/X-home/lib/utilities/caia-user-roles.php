<?php
/*
 * User roles management by CAIA Standards
 * WordPress.
 *
 * @category CAIA
 * @package  Utilities
 * @author   TuanNM
 * @version  2.0
 * @date     05/01/2022
 */

/*
* Chú ý:
* 'delete_users' => true, -> cho phép vào cấu hình wpsupercache ( trong function is_super_admin() of wp ) (với caia super cache <= 8.5)
* tương thích với SEO by Yoast từ 1.4 trở lên.
* Change log:
* 24/04/2020: thay đổi cách lấy user_role đáng tin cậy hơn
* 26/06/2019: cho phép client-admin có thể thêm user cho khách hàng
* 26/12/2020: nâng cấp dùng hàm caia_get_user_role
* 05/01/2022: cho phép setting để client admin có thể vào dc genesis, caia-setting, wpsupercache (kết hợp với caia super cache >= 8.6)
*/




// ----------------------------------------------------------------
// PHẦN CODE PHẦN QUYỀN LẠI CHO CẤP CAIA ADMIN VÀ CLIENT ADMIN
if(is_admin())
{
    add_caia_role();


    //------------------------------
    // PHÂN QUYỀN LẠI TỪ ĐÂY
    $user = wp_get_current_user();
    // echo '<!--';
    // print_r($user);
    // die;
    // echo '-->';

    // $user_role = $user ? array_shift( array_keys($user->caps) ) : false;
    $user_role = caia_get_user_role($user);


    if($user_role === 'administrator') // neu là super admin
    {
        // hiển thị quản trị CAIA Roles                
        $caia_roles = new CAIA_Roles_Setting();        

        
        // disable check update by http request
        $is_full_power = CAIA_Roles_Setting::get_is_full_power();        
        if(empty($is_full_power)){
            add_filter( 'pre_http_request', 'caia_block_update_request', 10, 3 );

            // Remove nhung thanh phan menu ko can thiet voi sub admin
            add_action( 'admin_menu', 'caia_remove_menus_super_admin', 999 );              
            // Cam truy cap options-media
            add_action('admin_head-options-media.php', 'disable_the_options_page');   

            // Hide register user
            add_action('admin_head-options-general.php', 'caia_hide_register_user');           

            // Hide Delete all Data When delete User
            add_action('admin_head-users.php', 'caia_hide_delete_all_content');            

        }

           


    }else if($user_role === 'caia_sub_admin') // neu ko phai la super admin
    {               

        // cấm user admin cấp thấp sửa user admin cấp cao hơn
        add_filter( 'editable_roles', 'caia_editable_roles' );
        add_filter( 'map_meta_cap', 'caia_map_meta_cap', 10, 4 );         

        // Hide register user
        add_action('admin_head-options-general.php', 'caia_hide_register_user');        

        // Remove nhung thanh phan menu ko can thiet voi sub admin
        add_action( 'admin_menu', 'caia_remove_menus_sub_admin', 999 );  

        // Cam truy cap options-media
        add_action('admin_head-options-media.php', 'disable_the_options_page'); 
                                    
        // disable check update by http request
        add_filter( 'pre_http_request', 'caia_block_update_request', 10, 3 );

        // Hide Delete all Data When delete User
        add_action('admin_head-users.php', 'caia_hide_delete_all_content');  

        // add redirection to subadmin neu cai redirection plugin
        // if(defined('REDIRECTION_VERSION')){
        //     // add_filter( 'redirection_role', 'add_redirection_sub_admin');            
        // }

    } else if($user_role === 'caia_client_admin')
    {            

        // cấm user admin cấp thấp sửa user admin cấp cao hơn
        add_filter( 'editable_roles', 'caia_editable_roles' );
        add_filter( 'map_meta_cap', 'caia_map_meta_cap', 10, 4 ); 

        // die;

        // Hide register user
        add_action('admin_head-options-general.php', 'caia_hide_register_user');
        
        // remove các thành phần menu ko cần thiết của Client Admin
        add_action( 'admin_menu', 'caia_remove_menus_client_admin', 999 );  

        // hide thành phần khó hiểu trong Genesis đối với client admin
        // add_action('admin_head-caia-social-share', 'caia_hide_complex_metabox_from_client_admin'); 

        // Cam truy cap options-media
        add_action('admin_head-options-media.php', 'disable_the_options_page'); 

        // cam Client Admin thao tac Widget Logic
        remove_action( 'sidebar_admin_setup', 'widget_logic_expand_control');
        remove_action( 'sidebar_admin_page', 'widget_logic_options_control');        

        // remove layout setting trong cat, tag, tax setting
        remove_action( 'admin_init', 'genesis_add_taxonomy_layout_options' );   

        // Hide Delete all Data When delete User
        add_action('admin_head-users.php', 'caia_hide_delete_all_content');        
        
                   
    }else if($user_role === 'editor')
    {
        // remove layout setting trong cat, tag, tax setting
        remove_action( 'admin_init', 'genesis_add_taxonomy_layout_options' ); 
                
    }

    // Khi co plugin activate or desactivate, can add lai role
    add_action('activated_plugin', 'readd_caia_role_caps', 10, 2);
    add_action('deactivated_plugin', 'readd_caia_role_caps', 10, 2);
    

    // disable options.php voi tat ca user
    add_action('admin_head-options.php', 'disable_the_options_page');
    
}

// add redirection to caia sub admin
// function add_redirection_sub_admin($redirection_role)
// {
//     return 'caia_sub_admin';  
// }


// function caia_hide_complex_metabox_from_client_admin()
// {
//     echo 'tuancaia';
//     $style = 'div#genesis-theme-settings-version{display:none;}';

//     echo '<style>' . $style . '</style>';
// }

// hide option hide all content when delete user
function caia_hide_delete_all_content()
{
    $action = $_GET["action"];
    if($action === 'delete'){
        // echo 'TuanNM Delete';
        echo '<style>form#updateusers fieldset ul >li:first-child {display: none;}</style>';
    }
}

// disable a option page
function disable_the_options_page(){
  wp_die( __( 'You do not have sufficient permissions to manage options for this site.' ), 403 );
}



// readd caia role caps when a plugin activate or desactivate
function readd_caia_role_caps()
{
    remove_role('caia_sub_admin');
    remove_role('caia_client_admin');
    add_caia_role();
}

// hàm này sẽ add 2 roles CAIA Sub Admin và CAIA Client Admin Role
function add_caia_role()
{
    //------------------------------
    // ĐĂNG KÝ CÁC ROLES BỔ SUNG
    // remove_role('subcriber');    
    // remove_role('wpseo_editor'); 
    // remove_role('wpseo_manager'); 
    // remove_role('caia_sub_admin');
    // remove_role('caia_client_admin'); 
      

        
    if(!get_role('caia_sub_admin')){

        $not_allow_caia_subadmin_capas = array('switch_themes', 
                                        'edit_themes',
                                        'edit_plugins',
                                        'level_10',
                                        'update_plugins',
                                        'update_themes',
                                        'install_themes',
                                        'update_core',
                                        'delete_themes'
                                        );

        $admin_role = get_role('administrator'); // subadmin co quyen administrator
        $admin_capas = $admin_role->capabilities;
        
        $def_caia_sub_admin_capas = $admin_capas;
        $def_caia_sub_admin_capas['administrator'] = true;
        
        foreach ($not_allow_caia_subadmin_capas as $value) {
            $def_caia_sub_admin_capas[$value] = false;
        }                            


        $result = add_role(
            'caia_sub_admin',
            __( 'CAIA Sub Administrator', 'caia' ),
            $def_caia_sub_admin_capas
        );
    }

    if(!get_role('caia_client_admin')){
        $not_allow_caia_client_admin_capas = array('switch_themes', 
                                        'edit_themes',
                                        'edit_plugins',
                                        'level_10',
                                        'update_plugins',
                                        'update_themes',
                                        'install_themes',
                                        'update_core',
                                        'delete_themes',
                                        'activate_plugins',
                                        'level_9',
                                        // 'create_users',
                                        'delete_users',
                                        'delete_plugins',
                                        'install_plugins',
                                        'remove_users',
                                        'add_users',
                                        // 'promote_users',
                                        //'edit_theme_options'
                                        );
        $admin_role = get_role('administrator');
        $admin_capas = $admin_role->capabilities;


        $def_caia_client_admin_capas = $admin_capas;
        
        foreach ($not_allow_caia_client_admin_capas as $value) {
            $def_caia_client_admin_capas[$value] = false;
        }  

        $result = add_role(
            'caia_client_admin',
            __( 'CAIA Client Administrator', 'caia' ),
            $def_caia_client_admin_capas
        );
    }
}



// hide register user
function caia_hide_register_user()
{?>
    <style type="text/css">
        table.form-table > tbody > tr:nth-child(6),
        table.form-table > tbody > tr:nth-child(7){
            display:none;
        }
    </style>
<?php
}



function caia_remove_menus_super_admin() {
    remove_submenu_page( 'options-general.php', 'options-media.php' );
}

// gỡ bớt menu ko cần thiết cho Caia Sub Admin
function caia_remove_menus_sub_admin() {
    remove_submenu_page( 'options-general.php', 'options-writing.php' );
    remove_submenu_page( 'options-general.php', 'options-media.php' );
    remove_submenu_page( 'themes.php', 'themes.php' );
}
// gỡ bớt những menu ko nên dùng dành cho Client Admin
function caia_remove_menus_client_admin() {

    // remove_menu_page( 'genesis' );
    remove_submenu_page( 'genesis', 'genesis-import-export' );    
    remove_submenu_page( 'themes.php', 'themes.php' );
    remove_submenu_page( 'themes.php', 'customize.php?return=%2Fwp-admin%2Findex.php' );
    remove_submenu_page( 'themes.php', 'caia-layout' );
    remove_submenu_page( 'themes.php', 'caia-design' );

    remove_menu_page( 'tools.php' );

    remove_submenu_page( 'options-general.php', 'options-writing.php' );
    remove_submenu_page( 'options-general.php', 'options-reading.php' );
    remove_submenu_page( 'options-general.php', 'options-discussion.php' );
    remove_submenu_page( 'options-general.php', 'options-media.php' );
    remove_submenu_page( 'options-general.php', 'options-permalink.php' );

    remove_submenu_page( 'users.php', 'limit-login-attempts' );

    $not_allow_slugs = CAIA_Roles_Setting::get_not_allow_slugs();


    // remove o level 1
    global $menu; 
    // echo '<!--';
    // print_r($menu);
    // echo '-->';
    // die;

    // themes.php : users.php : options-general.php : separator : genesis : WP-Optimize : custompage :
    foreach ($menu as $key => $value) {                
        if(isset($value[2]) && in_array($value[2], $not_allow_slugs)){                
            remove_menu_page( $value[2] );
        }                
    }
    
    // remove trong Menu Setting
    global $submenu;
    // echo '<!--';
    // print_r($submenu);
    // echo '-->';

    if(isset($submenu['options-general.php'])){

        foreach ($submenu['options-general.php'] as $key => $value) {                        
            if(isset($value[2]) && in_array($value[2], $not_allow_slugs)){
                remove_submenu_page( 'options-general.php', $value[2] );
            }
        }
        
    }

}

// cam request update online
function caia_block_update_request($pre, $args, $url) {
    /* Empty url */
    if( empty( $url ) ) {
        return $pre;
    }

    /* Invalid host */
    if( !$host = parse_url($url, PHP_URL_HOST) ) {
        return $pre;
    }

    $url_data = parse_url( $url );

    /* block request */
    // if( false !== stripos( $host, 'api.wordpress.org' ) && 
    //         (false !== stripos( $url_data['path'], 'update-check' ) || 
    //         false !== stripos( $url_data['path'], 'browse-happy' )) ) {
    //     return true;
    // }
    if( false !== stripos( $host, 'wordpress.org' )) {
        return true;
    }

    return $pre;
}


// Remove 'Administrator' from the list of roles if the current user is not an admin
function caia_editable_roles( $roles ){
    $current_user = wp_get_current_user();
    // print_r($current_user->caps);
    // print_r(array_keys($roles));

    if( isset( $roles['administrator'] ) && $current_user->roles[0] !== 'administrator' ){
        unset( $roles['administrator']);
    }

    if( $current_user->roles[0] !== 'administrator' && isset($current_user->caps['caia_client_admin']) &&  $current_user->caps['caia_client_admin'] ){        
        if (isset( $roles['caia_sub_admin'] )) unset( $roles['caia_sub_admin']);
        if (isset( $roles['caia_client_admin'] )) unset( $roles['caia_client_admin']);
    }
    
    // print_r(array_keys($roles));
    
    return $roles;
}



// If someone is trying to edit or delete and admin and that user isn't an admin, don't allow it
function caia_map_meta_cap( $caps, $cap, $user_id, $args ){
    // update_option('caia_test_role2', $cap );
    $current_user = wp_get_current_user();
    switch( $cap ){
        case 'edit_user':
        case 'remove_user':
        case 'promote_user':
            if( isset($args[0]) && $args[0] == $user_id )
                break;
            elseif( !isset($args[0]) )
                $caps[] = 'do_not_allow';
            $other = new WP_User( absint($args[0]) );
            if( $other->roles[0] === 'administrator' ){
                if($current_user->roles[0] !== 'administrator'){
                    $caps[] = 'do_not_allow';
                }
            }
            elseif ( in_array('caia_sub_admin', $other->roles ) ){
                if ($current_user->roles[0] !== 'administrator' && !in_array('caia_sub_admin', $current_user->roles)){
                    $caps[] = 'do_not_allow';
                }
            }         
            break;
        case 'delete_user':
        case 'delete_users':
            if( !isset($args[0]) )
                break;
            $other = new WP_User( absint($args[0]) );
            if( $other->roles[0] === 'administrator' ){
                if($current_user->roles[0] !== 'administrator'){
                    $caps[] = 'do_not_allow';
                }
            }
            elseif ( in_array('caia_sub_admin', $other->roles ) ){
                if ($current_user->roles[0] !== 'administrator' && !in_array('caia_sub_admin', $current_user->roles)){
                    $caps[] = 'do_not_allow';
                }
            }            
            break;
        default:
            break;
    }
    return $caps;
}



class CAIA_Roles_Setting
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    // la array cac slug and capabilities   
    private static $defaults = array(
            'slugs_list' => 'wpsupercache, genesis, akismet-key-config, text-replace-advanced, caia-context, sml_options',
            'is_full_power' => false,
        ); 

    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        

        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_theme_page(
            'Settings Admin', 
            'CAIA Roles', 
            'administrator', 
            'caia-roles-management', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property               
        $this->options = get_option( '_caia_roles_option', self::$defaults);
        

        ?>
        <div class="wrap">
            
            <h2>Quản lý truy cập của CAIA Sub and CAIA Client Administrator</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'caia_roles_option_group' );   // group option name
                do_settings_sections( 'caia-roles-admin' ); // page
                submit_button(); 
            ?>
            </form>
            
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        // tuannm group ------------
        register_setting(
            'caia_roles_option_group', // Option group
            '_caia_roles_option', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );  

        add_settings_section(
            'caia_roles_section_id', // ID
            '', // Title
            array( $this, 'caia_roles_section_info' ), // Callback
            'caia-roles-admin' // Page
        );

        add_settings_field(
            'slugs_list', // id
            'Danh sách slug không cho phép Client Admin truy cập: ', // title
            array( $this, 'caia_slugs_callback' ), // callback
            'caia-roles-admin', // page
            'caia_roles_section_id' // section id
        ); 

        add_settings_field(
            'capas_list', // id
            'Cho phép Adminsitrator đủ quyền: ', // title
            array( $this, 'caia_restore_full_power' ), // callback
            'caia-roles-admin', // page
            'caia_roles_section_id' // section id
        );
  
    }

    /**
     * Sanitize each setting field as needed => làm sạch dữ liệu input
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {       
        
        $new_input = array();
        if(isset($input['slugs_list'])){
                        
            $slugs = $input['slugs_list'];            
            $arr = explode(',', $slugs);
            $new_arr = array();
            foreach ($arr as $value) {
                $tmp = esc_attr(trim($value));
                if($tmp) $new_arr[] = $tmp;
            }             
            
            $new_input['slugs_list'] = implode(', ', $new_arr);                       
        }
        $new_input['is_full_power'] = $input['is_full_power'];

        readd_caia_role_caps();

        return $new_input;

    }

    public function caia_slugs_callback()
    {
        
        $slugs = isset($this->options['slugs_list']) ? $this->options['slugs_list'] : '';       
                

        ?>        
        <input type='textbox' id='slugs_list' name='_caia_roles_option[slugs_list]' value='<?php echo $slugs;?>' size='90'><br>
        <h4>Ví dụ:<h4>
        <i>
        - http://.../add-new.php => slug: add-new.php<br>
        - http://.../admin.php?page=caia-context => slug: caia-context<br>        
        - http://.../options-general.php?page=caia-roles-page => slug: caia-roles-page<br>
        Cần nhập vào: add-new.php, caia-context, caia-roles-page (mỗi slug cách nhau dấy ,) để cấm CAIA Client Admin truy cập 3 thành phần trên.<br>
        </i>
        <?php
    }

    public function caia_restore_full_power()
    {            
        
        $is_full_power = isset($this->options['is_full_power']) ? $this->options['is_full_power'] : self::$defaults['is_full_power'];        
        ?>        
        <input type='checkbox' id='is_full_power' name='_caia_roles_option[is_full_power]' value='1' <?php if($is_full_power) echo 'checked';?> >
        <h4>Giải thích:<h4>
        <i> Ngầm định, để tăng cường tốc độ và bảo mật, CAIA tắt tính năng bớt một số tính năng của Administrator đi, gồm:
            <ul>
            <li> - Cài đặt và nâng cấp plugin, Wordpress Core online.</li>
            <li> - Cài đặt cho phép user tự đăng ký.<li>
            <li> - Cấu hình kích thước file ảnh.<li>
            </ul>
            CHỈ CHECK tính năng này khi cần sử dụng, sau đó UNCHECK lựa chọn này ngay.
        </i>
        <?php


    }

    public function caia_roles_section_info()
    {
        echo '<h3>Cấu hình hạn chế chức năng của các vị trí Administrator và Client Administrator:</h3>';        
    }   

    public static function get_not_allow_slugs(){
        $defaults = self::$defaults;
        $value = get_option('_caia_roles_option', $defaults);
        
        if(isset($value['slugs_list'])){
            $res = explode(', ', $value['slugs_list']); 
            return $res;
        }else
            return self::$defaults['slugs_list'];  
    }

    public static function get_is_full_power(){
        $defaults = self::$defaults;
        $value = get_option('_caia_roles_option', $defaults);        
        
        if(isset($value['is_full_power'])){
            $res = $value['is_full_power']; 
            return $res;
        }else
            return self::$defaults['is_full_power'];     
        
            
    }   
}