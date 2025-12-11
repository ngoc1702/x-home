<?php
/*
* View logs before save - Caia Setting - Widget
* Author: HuyBQ
* Since: 1.0
*/

class CaiaLogs
{
    private $is_installed;
    public $table = "caia_logs_utilities";
    public $data_labels = array();
    private $values;

    function __construct()
    {
        global $wpdb;

        if ( is_multisite() )
        {
            // get main site's table prefix
            $main_prefix = $wpdb->get_blog_prefix(1);
            $this->table = $main_prefix . $this->table;
        }
        else
        {
            // non-multisite - regular table name
            $this->table = $wpdb->prefix . $this->table;
        }

        //Install DB if not yet
        $this->is_installed = get_option( "caia_logs_installed", false );

        if( !$this->is_installed )
        {
            $this->install();
        }

        if( is_admin() )
        {
            add_action( 'admin_menu', array($this, 'caia_logs_admin_menu') );
            add_action( 'admin_head-users_page_caia_logs', array($this, 'caia_screen_options') );

            //Style the log table
            add_action( 'admin_head-users_page_caia_logs', array($this, 'caia_admin_header') );

            if ( isset( $_POST['importDataAction'] ) && 
                ( $_POST['importDataAction'] == 'save_genesis_theme_setting' || $_POST['importDataAction'] == 'restore_genesis_theme_setting' ))
            {
                add_action( 'init', array($this, 'caia_export_restore_genesis_theme_setting') );
            }else{
                add_action( 'init', array($this, 'caia_export_genesis_theme_setting' ) );
            }

            if( defined('CAIA_LIB_DIR') )
            {

                if ( isset ( $_POST['importDataAction'] ) && 
                    ($_POST['importDataAction'] == 'save_caia_setting' || $_POST['importDataAction'] == 'restore_caia_setting' ))
                {
                    add_action( 'init', array($this, 'caia_export_restore_caia_theme_setting') );
                }else{
                     add_action( 'init', array($this, 'caia_export_caia_theme_setting' ) );
                }

                if ( isset( $_POST['importDataAction'] ) && 
                    ($_POST['importDataAction'] == 'save_caia_layout_setting' || $_POST['importDataAction'] == 'restore_caia_layout_setting' ))
                {
                    add_action( 'init', array($this, 'caia_export_restore_caia_layout_settings') );
                }else{
                    add_action( 'init', array($this, 'caia_export_caia_layout_settings' ) );
                }

                if ( isset( $_POST['importDataAction'] ) && 
                    ($_POST['importDataAction'] == 'save_caia_design_setting' || $_POST['importDataAction'] == 'restore_caia_design_setting' ))
                {
                    add_action( 'init', array($this, 'caia_export_restore_caia_design_settings') );
                }else{
                    add_action( 'init', array($this, 'caia_export_caia_design_settings' ) );
                }
            }

            if( isset($_POST['action']) && $_POST['action'] == 'save-widget' )
            {
                add_action( 'admin_init', array($this, 'caia_export_widget') );
            }

            if ( isset ($_POST['importDataAction']) && 
                ($_POST['importDataAction'] == 'save_widget' || $_POST['importDataAction'] == 'restore_widget' ))
            {
                add_action( 'admin_init', array($this, 'caia_export_restore_widget') );
            }

            if( !empty( $_POST['importDataID'] ) )
            {
                add_action( 'admin_init', array($this, 'caia_import_data') );
            }
        }

        //For translation purposes
        $this->data_labels = array(
            'id'                => __('ID', 'caia'),
            'action_log'        => __('Action', 'caia'),
            'time'              => __('Time', 'caia'),
            'user_login'        => __('Username', 'caia'),
            'ip'                => __('IP Address', 'caia'),
            'data'              => __('Data', 'caia'),
            'restore'           => __('Restore', 'caia'),
        );
    }

    function save_data($values, $format)
    {
        global $wpdb;

        $wpdb->insert( $this->table, $values, $format );
    }

    function set($name, $value)
    {
        $this->values[$name] = $value;
    }


    function get($name)
    {
        return (isset($this->values[$name])) ? $this->values[$name] : false;
    }

    function install()
    {
        if( !$this->is_installed )
        {
            global $wpdb;
            //if table does't exist, create a new one
            if( !$wpdb->get_row("SHOW TABLES LIKE '{$this->table}'") ){
                $sql = "CREATE TABLE  " . $this->table . "
                    (
                        id INT( 11 ) NOT NULL AUTO_INCREMENT ,
                        action_log VARCHAR( 100 ) NOT NULL ,
                        time DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL ,
                        user_login VARCHAR( 60 ) NOT NULL ,
                        ip VARCHAR( 100 ) NOT NULL ,
                        data LONGTEXT CHARACTER SET utf8 NOT NULL ,
                        PRIMARY KEY ( id ) ,
                        INDEX ( action_log, ip )
                    );";

                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);

            }
            update_option( "caia_logs_installed", true );
        }
    }

    function caia_screen_options()
    {
        $page = ( isset($_GET['page']) ) ? esc_attr($_GET['page']) : false;
        if( 'caia_logs' !== $page )
            return;

        $this->log_table = new CaiaLogs_List_Table();
    }

    function caia_logs_admin_menu()
    {
        add_submenu_page( 'users.php', __('Caia Logs Utilities', 'caia'), __('Caia Logs', 'caia'), 'list_users', 'caia_logs', array($this, 'caia_log_manager') );
    }

    function caia_admin_header()
    {
        $page = ( isset($_GET['page']) ) ? esc_attr($_GET['page']) : false;
        if( 'caia_logs' != $page )
            return;

        echo '<style type="text/css">';
        echo 'table.users { table-layout: auto; }';
        echo '</style>';
    }

    function caia_log_manager()
    {
        $log_table = $this->log_table;
        $log_table->prepare_items();

        echo '<div class="wrap srp">';
            echo '<h2>' . __('Caia Logs Utilities', 'caia') . '</h2>';
        echo '</div>';

        if( !empty($_POST['importDataID']) )
        {
            echo '<h3 style="color: red;">Restore Data Success!</h3>';
        }

        $log_table->display();
    }

    /* Widget ----------*/

    function caia_export_widget()
    {
        $data = array();
        $user_login = wp_get_current_user();
        $user_login = $user_login->user_login;

        // lay thong tin widget
        $sidebars_widgets = get_option('sidebars_widgets');
        $data['sidebars_widgets'] = $sidebars_widgets;


        $wig_array = array();
        foreach ($sidebars_widgets as $key => $sidebar)
        {
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

        foreach ($wig_array as $wname)
        {
            $option_name = 'widget_' . $wname;
            $val = get_option( $option_name , '' );
            $data[$option_name] = $val;
        }

        $data_export = serialize($data);

        $values = array(
            'action_log' => 'save_widget',
            'time'       => current_time('mysql'),
            'user_login' => $user_login,
            'ip'         => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? esc_attr($_SERVER['HTTP_X_FORWARDED_FOR']) : esc_attr($_SERVER['REMOTE_ADDR']),
            'data'       => $data_export
            );

        $format = array( '%s', '%s', '%s', '%s', '%s' );

        $this->save_data( $values, $format );
    }

    function caia_export_restore_widget()
    {
        $data = array();
        $user_login = wp_get_current_user();
        $user_login = $user_login->user_login;

        // lay thong tin widget
        $sidebars_widgets = get_option('sidebars_widgets');
        $data['sidebars_widgets'] = $sidebars_widgets;


        $wig_array = array();
        foreach ($sidebars_widgets as $key => $sidebar)
        {
            if($key === 'array_version') continue;

            if(!is_array($sidebar)) continue;

            foreach ($sidebar as $wname)
            {
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

        foreach ($wig_array as $wname)
        {
            $option_name = 'widget_' . $wname;
            $val = get_option( $option_name , '' );
            $data[$option_name] = $val;
        }

        $data_export = serialize($data);

        $values = array(
            'action_log' => 'restore_widget',
            'time'       => current_time('mysql'),
            'user_login' => $user_login,
            'ip'         => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? esc_attr($_SERVER['HTTP_X_FORWARDED_FOR']) : esc_attr($_SERVER['REMOTE_ADDR']),
            'data'       => $data_export
            );

        $format = array( '%s', '%s', '%s', '%s', '%s' );

        $this->save_data( $values, $format );
    }

    /* Caia Settings------*/

    function caia_export_caia_theme_setting()
    {
        add_filter( 'pre_update_option_caia-settings', array( $this, 'get_old_value_caia_settings'), 10, 2 );
    }

    function get_old_value_caia_settings( $value, $old_value )
    {
        $data = array();
        $user_login = wp_get_current_user();
        $user_login = $user_login->user_login;

        $option_name = 'caia-settings';
        $val = serialize($old_value);
        $data[$option_name] = $val;

        $data_export = serialize($data);

        $values = array(
                'action_log' => 'save_caia_setting',
                'time'       => current_time('mysql'),
                'user_login' => $user_login,
                'ip'         => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? esc_attr($_SERVER['HTTP_X_FORWARDED_FOR']) : esc_attr($_SERVER['REMOTE_ADDR']),
                'data'       => $data_export
            );

        $format = array( '%s', '%s', '%s', '%s', '%s' );

        $this->save_data( $values, $format );
        return $value;
    }

    function caia_export_restore_caia_theme_setting()
    {
        add_filter( 'pre_update_option_caia-settings', array( $this, 'restore_old_value_caia_settings'), 10, 2 );
    }

    function restore_old_value_caia_settings( $value, $old_value )
    {
        $data = array();
        $user_login = wp_get_current_user();
        $user_login = $user_login->user_login;

        $option_name = 'caia-settings';
        $val = serialize($old_value);
        $data[$option_name] = $val;

        $data_export = serialize($data);

        $values = array(
                'action_log' => 'restore_caia_setting',
                'time'       => current_time('mysql'),
                'user_login' => $user_login,
                'ip'         => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? esc_attr($_SERVER['HTTP_X_FORWARDED_FOR']) : esc_attr($_SERVER['REMOTE_ADDR']),
                'data'       => $data_export
            );

        $format = array( '%s', '%s', '%s', '%s', '%s' );

        $this->save_data( $values, $format );
        return $value;
    }

    /* Caia Layout Settings ----------*/

    function caia_export_caia_layout_settings()
    {
        add_filter( 'pre_update_option_caia-layout-settings', array( $this, 'get_old_value_caia_layout_settings'), 10, 2 );
    }

    function get_old_value_caia_layout_settings( $value, $old_value )
    {
        $data = array();
        $user_login = wp_get_current_user();
        $user_login = $user_login->user_login;

        $option_name = 'caia-layout-settings';
        $val = serialize($old_value);
        $data[$option_name] = $val;

        $data_export = serialize($data);

        $values = array(
                'action_log' => 'save_caia_layout_setting',
                'time'       => current_time('mysql'),
                'user_login' => $user_login,
                'ip'         => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? esc_attr($_SERVER['HTTP_X_FORWARDED_FOR']) : esc_attr($_SERVER['REMOTE_ADDR']),
                'data'       => $data_export
            );

        $format = array( '%s', '%s', '%s', '%s', '%s' );

        $this->save_data( $values, $format );
        return $value;
    }

    function caia_export_restore_caia_layout_settings()
    {
        add_filter( 'pre_update_option_caia-layout-settings', array( $this, 'restore_old_value_caia_layout_settings'), 10, 2 );
    }

    function restore_old_value_caia_layout_settings( $value, $old_value )
    {
        $data = array();
        $user_login = wp_get_current_user();
        $user_login = $user_login->user_login;

        $option_name = 'caia-layout-settings';
        $val = serialize($old_value);
        $data[$option_name] = $val;

        $data_export = serialize($data);

        $values = array(
                'action_log' => 'restore_caia_layout_setting',
                'time'       => current_time('mysql'),
                'user_login' => $user_login,
                'ip'         => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? esc_attr($_SERVER['HTTP_X_FORWARDED_FOR']) : esc_attr($_SERVER['REMOTE_ADDR']),
                'data'       => $data_export
            );

        $format = array( '%s', '%s', '%s', '%s', '%s' );

        $this->save_data( $values, $format );
        return $value;
    }

    /* Caia Design Settings ----------*/

    function caia_export_caia_design_settings()
    {
        add_filter( 'pre_update_option_caia-design-settings', array( $this, 'get_old_value_caia_design_settings'), 10, 2 );
    }

    function get_old_value_caia_design_settings( $value, $old_value )
    {
        $data = array();
        $user_login = wp_get_current_user();
        $user_login = $user_login->user_login;

        $option_name = 'caia-design-settings';
        $val = serialize($old_value);
        $data[$option_name] = $val;

        $data_export = serialize($data);

        $values = array(
                'action_log' => 'save_caia_design_setting',
                'time'       => current_time('mysql'),
                'user_login' => $user_login,
                'ip'         => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? esc_attr($_SERVER['HTTP_X_FORWARDED_FOR']) : esc_attr($_SERVER['REMOTE_ADDR']),
                'data'       => $data_export
            );

        $format = array( '%s', '%s', '%s', '%s', '%s' );

        $this->save_data( $values, $format );
        return $value;
    }

    function caia_export_restore_caia_design_settings()
    {
        add_filter( 'pre_update_option_caia-design-settings', array( $this, 'restore_old_value_caia_design_settings'), 10, 2 );
    }

    function restore_old_value_caia_design_settings( $value, $old_value )
    {
        $data = array();
        $user_login = wp_get_current_user();
        $user_login = $user_login->user_login;

        $option_name = 'caia-design-settings';
        $val = serialize($old_value);
        $data[$option_name] = $val;

        $data_export = serialize($data);

        $values = array(
                'action_log' => 'restore_caia_design_setting',
                'time'       => current_time('mysql'),
                'user_login' => $user_login,
                'ip'         => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? esc_attr($_SERVER['HTTP_X_FORWARDED_FOR']) : esc_attr($_SERVER['REMOTE_ADDR']),
                'data'       => $data_export
            );

        $format = array( '%s', '%s', '%s', '%s', '%s' );

        $this->save_data( $values, $format );
        return $value;
    }

    /* Genesis Settings ----------*/

    function caia_export_genesis_theme_setting()
    {
        add_filter( 'pre_update_option_genesis-settings', array( $this, 'get_old_value_genesis_settings'), 10, 2 );
    }

    function get_old_value_genesis_settings( $value, $old_value )
    {
        $data = array();
        $user_login = wp_get_current_user();
        $user_login = $user_login->user_login;

        $option_name = 'genesis-settings';
        $val = serialize($old_value);
        $data[$option_name] = $val;

        $data_export = serialize($data);

        $values = array(
                'action_log' => 'save_genesis_theme_setting',
                'time'       => current_time('mysql'),
                'user_login' => $user_login,
                'ip'         => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? esc_attr($_SERVER['HTTP_X_FORWARDED_FOR']) : esc_attr($_SERVER['REMOTE_ADDR']),
                'data'       => $data_export
            );

        $format = array( '%s', '%s', '%s', '%s', '%s' );

        $this->save_data( $values, $format );
        return $value;
    }

    function caia_export_restore_genesis_theme_setting()
    {
        add_filter( 'pre_update_option_genesis-settings', array( $this, 'restore_old_value_genesis_settings'), 10, 2 );
    }

    function restore_old_value_genesis_settings( $value, $old_value )
    {
        $data = array();
        $user_login = wp_get_current_user();
        $user_login = $user_login->user_login;

        $option_name = 'genesis-settings';
        $val = serialize($old_value);
        $data[$option_name] = $val;

        $data_export = serialize($data);

        $values = array(
                'action_log' => 'restore_genesis_theme_setting',
                'time'       => current_time('mysql'),
                'user_login' => $user_login,
                'ip'         => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? esc_attr($_SERVER['HTTP_X_FORWARDED_FOR']) : esc_attr($_SERVER['REMOTE_ADDR']),
                'data'       => $data_export
            );

        $format = array( '%s', '%s', '%s', '%s', '%s' );

        $this->save_data( $values, $format );
        return $value;
    }

    /*------------------------------------------*/

    function caia_import_data()
    {
        global $wpdb;
        $ID = $_POST['importDataID'];

        $data = $wpdb->get_results( "SELECT * FROM $this->table WHERE id = $ID", 'ARRAY_A' );

        $data = $data[0]['data'];

        $udata = unserialize($data);

        foreach ($udata as $key => $value)
        {
            if( $_POST['importDataAction'] == 'save_widget' || $_POST['importDataAction'] == 'restore_widget' )
            {
                update_option( $key, $value );
            }else{
                update_option( $key, unserialize($value) );
            }
        }
    }

    function log_get_data( $per_page = 10, $page_number = 1 )
    {
        global $wpdb;

        $sql = "SELECT * FROM $this->table";

        if ( !empty( $_REQUEST['orderby'] ) )
        {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= !empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }else{
            $sql .= ' ORDER BY time DESC ';
        }

        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }
}

if( class_exists( 'CaiaLogs' ) )
{
    $CaiaLogs = new CaiaLogs;
}

if(!class_exists('WP_List_Table'))
{
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CaiaLogs_List_Table extends WP_List_Table
{
    private $CaiaLogsData;

    function __construct()
    {
        global $CaiaLogs, $_wp_column_headers;

        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'caia_log',     //singular name of the listed records
            'plural'    => 'caia_logs',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );

        $this->data_labels = $CaiaLogs->data_labels;

    }

    function set($name, $value)
    {
        $this->CaiaLogsData[$name] = $value;
    }

    function get($name)
    {
        return (isset($this->CaiaLogsData[$name])) ? $this->CaiaLogsData[$name] : false;
    }

    function get_columns()
    {
        global $status;
        $columns = array(
            'id'                => __('STT', 'caia'),
            'action_log'        => __('Action', 'caia'),
            'time'              => __('Time', 'caia'),
            'user_login'        => __('Username', 'caia'),
            'ip'                => __('IP Address', 'caia'),
            'restore'           => __('Restore', 'caia'),
        );
        return $columns;
    }


    function column_default( $item, $column_name )
    {
        $user_login = wp_get_current_user();

        switch ( $column_name )
        {
            case 'id':
            case 'action_log':
            case 'time':
            case 'user_login':
            case 'ip':
                return $item[$column_name];
            case 'restore':
                if( $user_login->caps['administrator'] == true )
                {
                    $data = '<form method="POST"><input type="hidden" name="importDataAction" value="'.$item['action_log'].'"><input type="hidden" name="importDataID" value="'.$item['id'].'"><input type="submit" name="btnimportData" value="Restore" style="cursor: pointer;" /></form>';
                }else{
                    $data = 'Bạn cần quyền administrator để restore.';
                }
                return $data;
            default:
                return $item[$column_name];
        }
    }

    function prepare_items()
    {
        global $wpdb, $CaiaLogs;

        $this->_column_headers = $this->get_column_info();

        $per_page     = 10;
        $current_page = $this->get_pagenum();

        $sql = "SELECT COUNT(*) FROM {$CaiaLogs->table}";
        $total_items = $wpdb->get_var($sql);

        $this->set('TotalItems', $total_items);

        $this->items = $CaiaLogs->log_get_data( $per_page, $current_page );

        $this->set_pagination_args( array(
                'total_items' => $total_items,
                'per_page'    => $per_page,
                'total_pages' => ceil($total_items/$per_page)
            ));
    }
}