<?php

// 9/01/2021: Bật tắt chức năng tối ưu seo
// 29/01/2021: Thay đổi câu chữ đỡ hiểu lầm


// toi uu Admin panel
if(is_admin()){	
    // toi uu admin cua SEO by Yoast
    if(defined('WPSEO_VERSION')){
        if(defined('WPSEO_EDIT_BY_CAIA')){ // la ban chinh rieng cho CAIA
            if (!empty( caia_get_option( 'caia_seo' ) )) { 
		        // disable Page Analysis and calcul seo score
		        add_filter('wpseo_use_page_analysis', '__return_true');
		        // remove column of SEO by Yoast from list page of post/page/post type
		        add_filter('caia_wpseo_add_meta_to_post_list', '__return_true');
		                   
            
            }else{
                add_filter('wpseo_use_page_analysis', '__return_false');
                add_filter('caia_wpseo_add_meta_to_post_list', '__return_false');
            }           
        }
        
    }

} // end of is_admin()

//bật tính năng tối ưu seo
    add_action( 'caia_settings_metaboxes', 'caia_add_theme_settings_seo' );
    function caia_add_theme_settings_seo( $pagehook ){
        add_meta_box( 'caia-settings-seo', __( 'Bật tắt hiển thị kiểm tra seo', 'caia' ), 'caia_settings_toi_uu_seo', $pagehook, 'main' );
    }

    function caia_settings_toi_uu_seo(){
        ?>
        <table class="form-table">
        <tbody>             
            <tr valign="top">
                <th scope="row">Bật kiểm tra seo</th>
                <td>
                    <input id='caia_seo' type="checkbox" name="<?php echo CAIA_SETTINGS_FIELD; ?>[caia_seo]" value="1" <?php checked( 1, caia_get_option( 'caia_seo' ) ); ?> /><label for="caia_seo"><i>Bật kiểm tra seo</i></label>
                    <p class="description"> Sẽ hiển thị thêm cột SEO ở trang danh sách bài viết, giúp xem trạng thái tối ưu của post đó.</p>
                </td>
            </tr>                   
        </tbody>
        </table>
        <?php
    }


