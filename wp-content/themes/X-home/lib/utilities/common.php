<?php
/*
*   Change log:
    - 16/01/21: bổ sung hàm caia_get_domain, và caia_start_with, và tối ưu caia_get_domain chút
    - 06/05/21: bổ sung thêm 2 hàm caia_get_post_type_with_meta_html_fields và caia_is_public_post_type
    - 31/05/21: bổ sung thêm caia_get_abs_path_from_url và caia_opti_imagejpeg
    - 08/06/21: nâng default quality jpg lên 90 (= wp) tại caia_opti_imagejpeg
    - 14/07/21: bổ sung caia_get_client_ip
    - 17/07/21: nâng cấp cách store của hàm caia_log
    - 29/07/21: nâng cấp caia_validate_vietnamese_text hỗ trợ tiếng việt ko dấu
    - 29/10/21: bổ sung hàm caia_extract_link_from_html
    - 15/07/22: fix hàm caia_validate_phone_number với trường hợp sdt có chữ cái
*/


// CAIA const
if (defined('WP_HOME') && (WP_HOME === 'http://caia2.vn' || WP_HOME === 'http://comem2.vn') ){    
    define('MASTER_SITE_URL', 'http://seocaia.vn');
}else{      
    define('MASTER_SITE_URL', 'https://co.caia.vn');
}

define ('CAIA_API_URL', MASTER_SITE_URL . '/api.php');  
define ('CAIA_AJAX_URL', MASTER_SITE_URL . '/ajax.php');  

define ('CAIA_API_CODE', 'hdu3ie97o)h20jfd08823hfojl0jw09e9j288I92&6' );




// ----- function log ----------------------------
// ham dung de ghi log phuc vu test, ghi log ra file 
if (! function_exists('caia_log')){
    function caia_log($log_name, $item_name, $item_value)
    {    
        if( !defined('DISABLE_CAIA_LOG') || !DISABLE_CAIA_LOG ){
            date_default_timezone_set('Asia/Saigon');
            $_log_file = wp_upload_dir()['basedir'] . '/logs/' . $log_name . '/' . date('my') . '.log';
            if (! file_exists(dirname($_log_file))){
                mkdir(dirname($_log_file), 0777, true);
            }
            if (is_array($item_value) || is_object($item_value)){
                // $item_value = serialize($item_value);
                $item_value = json_encode($item_value, JSON_UNESCAPED_UNICODE);
            }else if(is_string($item_value)){
                $item_value = str_replace("\n", '__NL__', $item_value);
            }
            $log_message = $item_name . ' : ' .  $item_value . ' - ' . date('d/m/y:G:i:s') . "\n";
            error_log( $log_message, 3, $_log_file );  
        }
    }
}
if (!function_exists('_gis')){
    function _gis(&$value, $default = null){
        return isset($value) ? $value : $default;
    }
}

// ----- function khac ----------------------------
add_action('wp_head', 'caia_cookie_js', 9);
function caia_cookie_js()
{
	echo '<script type="text/javascript">function setCookie(e,t,o){if(0!=o){var i=new Date;i.setTime(i.getTime()+24*o*60*60*1e3);var n="expires="+i.toUTCString();document.cookie=e+"="+t+";"+n+";path=/"}else document.cookie=e+"="+t+";path=/"}function getCookie(e){for(var t=e+"=",o=document.cookie.split(";"),i=0;i<o.length;i++){for(var n=o[i];" "==n.charAt(0);)n=n.substring(1);if(0==n.indexOf(t))return n.substring(t.length,n.length)}return""}</script>';
}

function caia_ext_404(){
	if (function_exists('caia_log')){
		$url_path = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		caia_log('404', 'url', $url_path);		
	}


	if( defined( 'CAIA_REDIRECT_404_HOMEPAGE' ) ){

		header("HTTP/1.1 301 Moved Permanently");
		header("Location: ".get_bloginfo('url'));
		exit();
		
	}

	add_action('wp_footer', 'add_caia_404_event', 11);
	function add_caia_404_event(){
		?>
		<script>if (ega && typeof ega === 'function'){
			var referer=document.referrer;""==referer&&(referer="empty");var current_url=window.location.href;
			ega('404', current_url, referer);}</script>	
		<?php
	}
}

function caia_post_type_has_tax($post_type, $tax){
    $tax_arr = get_object_taxonomies($post_type);    
    return in_array($tax, $tax_arr);    
}


function caia_get_post_type_with_meta_html_fields(){
    // caia_html_fields => array[post_type] => (field_id => array('id' => field_id, 'name' => name));
    $post_types = get_option( 'caia_html_fields' );
    if (function_exists('fitqa_create_post_type')){
        $html_fields = array( 'fitqa_answer' => array( 'id' => 'fitqa_answer', 'name' => 'Trả lời') );
        $post_types['fitwp_question'] = $html_fields;        
    }

    return $post_types;
}

function caia_is_public_post_type($post_type){
    if ($post_type === 'attachment'){
        return false;
    }else{
        $ptypes = get_post_types(array(
                       'public'   => true,                       
                    ), 'names');
        // print_r($ptypes);
        return isset($ptypes[$post_type]);
    }
}

function caia_get_user_role($user = null){
    if (!$user){
        $user = wp_get_current_user();    
    }
    
    // print_r($user);
    // die;    

    if ($user){
        $urole = $user->roles;
        $ctv_user_role = array_shift($urole);        
        if (! $ctv_user_role){
            $key_caps = array_keys($user->caps);
            $caia_role_arr = array('administrator', 'caia_sub_admin', 'caia_client_admin', 'editor', 'author', 'contributor');
            foreach ($key_caps as $key => $role) {
                if ( in_array($role, $key_caps) ){                    
                    return $role;           
                }
            }
            return false;       
        }else{            
            return $ctv_user_role;
        }   
    }else{        
        return false;
    }
    
}

function caia_get_client_ip(){
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = $_SERVER['HTTP_X_FORWARDED_FOR'];
        $ip_arr = explode( ',', $ips );
        $ip = trim($ip_arr[0], " ");
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    return $ip;
}

function caia_get_domain($url){
    $tmp = parse_url($url);

    if ( isset($tmp['host']) ){
        $host = $tmp['host'];
        return caia_start_with( $host, 'www.' ) ? substr($host, 4) : $host;            
    } else { 
        $host = trim($url, "/ \r\n\t\\?");
        if ( caia_start_with( $host, 'www.' ) ){
            $host = substr($host, 4);
        }       

        $pos = strpos($host, '/');
        if ($pos){
            $host = substr($host, 0, $pos);
        }
        $pos = strpos($host, '?');
        if ($pos){
            $host = substr($host, 0, $pos);
        }

        return  $host;
    }   
}

function caia_start_with($haystack, $needle){
    return substr($haystack, 0, strlen($needle)) === $needle;
}

function caia_is_same_domain($domain1, $domain2){
    $dm1 = parse_url($domain1);
    $host1 = isset($dm1['host']) ? $dm1['host'] : $domain1;
    if (substr($host1, 0, 4) === 'www.'){
        $host1 = substr($host1, 4);
    }
    $dm2 = parse_url($domain2);
    $host2 = isset($dm2['host']) ? $dm2['host'] : $domain2;
    if (substr($host2, 0, 4) === 'www.'){
        $host2 = substr($host2, 4);
    }
    return $host1 === $host2;
    
}

function caia_array_diff_full($arr1, $arr2, &$diff12, &$share, &$diff21){
    foreach ($arr1 as $key1 => $value) {
        $key2 = array_search($value, $arr2);
        if ($key2 !== false){
            $share[] = $value;
            unset($arr1[$key1]);
            unset($arr2[$key2]);
        }
    }
    $diff12 = $arr1;
    $diff21 = $arr2;
}

function caia_add_to_set($item, &$arr){
    if (is_array($arr)){
        if (!in_array($item, $arr)) {
            $arr[] = $item;
        }       
    }else if ( empty( $arr ) ){
        $arr = array($item);
    }else{
        if ($item != $arr){
            $arr = array($item, $arr);  
        }
        
    }

    return $arr;
    
}

function caia_validate_phone_number($phone){
    $phone = preg_replace("/[+().,\- ]/", '', $phone);
    // echo $phone;

    if( preg_match("/^[0-9]+$/", $phone) ) {
        if (substr($phone, 0, 2) == '84'){
            $phone = '0' . substr($phone, 2);
        }
        if (substr($phone, 0, 3) == '084'){
            $phone = '0' . substr($phone, 3);
        }
        if (substr($phone, 0, 4) == '0084'){
            $phone = '0' . substr($phone, 4);
        }
        // echo $phone;

        $prefix = substr($phone, 0, 2);
        $len = strlen($phone);
        if ( ($prefix == '09' || $prefix == '08' || $prefix == '03' || $prefix == '07' || $prefix == '05' || $prefix == '04') && $len == 10 ) {
            return true;
        }else if (( $prefix == '01' && $len == 11) || $prefix == '02' && ($len == 11 || $len == 12)){
            return true;
        }else{
            return false;
        }        
    }
    
    return false;

}

function caia_validate_vietnamese_text($text, $strip_tags = true){
    if ($strip_tags){
        $text = strip_tags($text);    
    }    
    $post = array(  'api_name'     =>  'detect_vietnamese',
                    'text'         =>  $text              
                ); 
    $lang = call_co_caia_api($post);
    
    if ($lang === 'vietnamese' || $lang ===  NULL){
        return true;
    }else{
        return false;
    }
}

// doc line_count dong cuoi cung cua file, thg dung cho big_file
function caia_read_last_lines($path, $line_count, $block_size = 512){
    $lines = array();
    
    $leftover = "";

    $fh = fopen($path, 'r');    
    fseek($fh, 0, SEEK_END);
    do{        
        $can_read = $block_size;
        if(ftell($fh) < $block_size){
            $can_read = ftell($fh);
        }

        fseek($fh, -$can_read, SEEK_CUR);
        $data = fread($fh, $can_read);
        $data .= $leftover;
        fseek($fh, -$can_read, SEEK_CUR);
        
        $split_data = array_reverse(explode("\n", $data));
        $new_lines = array_slice($split_data, 0, -1);
        $lines = array_merge($lines, $new_lines);
        $leftover = $split_data[count($split_data) - 1];
    }
    while(count($lines) < $line_count && ftell($fh) != 0);
    if(ftell($fh) == 0){
        $lines[] = $leftover;
    }
    fclose($fh);    
    return array_slice($lines, 0, $line_count);
}

// tối ưu thêm imagejpeg, có set width tối đa
function caia_opti_imagejpeg($img_url, $quality = 90, $max_width = 750)
{    
    $path = caia_get_abs_path_from_url($img_url);
    if ($path && file_exists($path)){
         $mime = mime_content_type($path);
         if ($mime === 'image/jpeg'){            
            $image = imagecreatefromjpeg($path); 
            list($org_width, $org_height) = getimagesize($path);
            if ( $org_width > 0 && $org_height > 0 && $org_width > $max_width){
                $width = $max_width;
                $height = round($org_height * ($max_width / $org_width));   
                // echo $width . 'x' . $height . "\n";  
                $new_image = imagecreatetruecolor($width, $height);
                imagecopyresampled($new_image, $image, 0, 0, 0, 0, $width, $height, $org_width, $org_height);
                $path2 = $path . '.new';
                imagejpeg($new_image, $path2, $quality);                
                imagedestroy($new_image);
                if (file_exists($path2)){
                    if (filesize($path2) < filesize($path)){                        
                        unlink($path);
                        rename($path2, $path);
                    }else{                        
                        unlink($path2);
                    }
                }
            }else{
                $path2 = $path . '.new';
                imagejpeg($image, $path2, $quality);
                if (file_exists($path2)){
                    if (filesize($path2) < filesize($path)){                                         
                        unlink($path);
                        rename($path2, $path);
                    }else{                                 
                        unlink($path2);
                    }
                }
            }
            imagedestroy($image);
            return true;
        }
    }

    return false;   
}

function caia_get_abs_path_from_url($img_url){
    $home = home_url( '', null );
    // make 4 version of home_url
    $home_arr = array($home);
    if ( strpos($home, '://www.') ){
        $home_arr[] = str_replace( '://www.', '://', $home);
    }else{
        $home_arr[] = str_replace( '://', '://www.', $home);
    }
    if (strpos($home, 'http://') === 0){
        $home_arr[] = str_replace( 'http://', 'https://', $home_arr[0]);
        $home_arr[] = str_replace( 'http://', 'https://', $home_arr[1]); 
    }
    if (strpos($home, 'https://') === 0){
        $home_arr[] = str_replace( 'https://', 'http://', $home_arr[0]);
        $home_arr[] = str_replace( 'https://', 'http://', $home_arr[1]); 
    }

    // print_r($home_arr);
    // remove home_url from
    $rel_path = false;
    foreach ($home_arr as $key => $home_url) {
        if (strpos($img_url, $home_url) === 0){
            $rel_path = substr($img_url, strlen($home_url));
        }
    }

    if ($rel_path){
        $item = '/htdocs/';     
        $head_path = substr( __FILE__, 0, strpos( __FILE__, $item ) + strlen($item) );      
        $abs_path = $head_path . trim($rel_path, '/');

        // echo $abs_path;
        // if (file_exists($abs_path)){
        //     return $abs_path;           
        // }else{
        //     return 404;
        // }
        return $abs_path;
    }

    return false;
}

function caia_get_scheme(){
    if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443){
        $scheme = 'https';
    }else{
        $scheme = 'http';
    }

    return $scheme;
}

function caia_convert_no_sign_vn($str) {
    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
    $str = preg_replace("/(đ)/", "d", $str);
    $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", "A", $str);
    $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", "E", $str);
    $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", "I", $str);
    $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", "O", $str);
    $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", "U", $str);
    $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", "Y", $str);
    $str = preg_replace("/(Đ)/", "D", $str);    
    return $str;
}

function caia_set_encode_cookie($name, $value = "", $expires = 0, $path = "", $domain = "", $secure = false, $httponly = true){
    $c = 'AES-128-CBC';
    $k = 'hdu3ie97o)h20jfd08823hfkfjd8()93kjfso';
    $c_value = array('value' => $value, 't' => time());
    $json_value = json_encode($c_value, JSON_UNESCAPED_UNICODE);
    $encode_value = @openssl_encrypt($json_value, $c, $k);    
    setcookie($name, $encode_value, $expires, $path, $domain, $secure, $httponly);
}

function caia_get_encode_cookie($name){
    $encode_value = isset($_COOKIE[$name]) ? $_COOKIE[$name] : '';    
    // echo $encode_value;    
    if (!$encode_value) return false;
    $c = 'AES-128-CBC';
    $k = 'hdu3ie97o)h20jfd08823hfkfjd8()93kjfso';        
    $json_value = @openssl_decrypt($encode_value, $c, $k);    
    $c_value = json_decode($json_value, true);     
    // print_r($c_value);       
    $value = isset($c_value['value']) && isset($c_value['t']) && is_int($c_value['t']) ? $c_value['value'] : false;

    return $value;
    
}

function caia_gen_token(){
    $c = 'AES-128-CBC';
    $k = 'hdu3ie97o)h20jfd08823hfo';
    $time = time();
    $code = strval($time);

    $token = @openssl_encrypt($code, $c, $k);

    return $token;
}

function caia_extract_link_from_html($content){
    $regex = '/(<a("[^"]*"|\'[^\']*\'|[^\'>"])*>)/i'; 

    $found = preg_match_all($regex, $content, $matches, PREG_OFFSET_CAPTURE);   
    // print_r($matches);
    $a_tags = isset($matches[0]) ? $matches[0] : false;
    $hrefs = array();
    if ($a_tags){
        foreach ($a_tags as $key => $item) {
            $open = $item[0];
            $open_pos = intval($item[1]);            
            $close_pos = strpos($content, '</a>', $open_pos + strlen($open));
            if ($close_pos !== false){
              $anchor = substr($content, $open_pos + strlen($open), $close_pos - $open_pos - strlen($open));
              $href = caia_attr_value($open, 'href');   
              $hrefs[] = array('href' => $href, 'anchor' => $anchor); 
            }                 
        }
    }
        
    return $hrefs;
}

// hàm này chuẩn hóa lại post content cho caia dolink:
//   . Chuyển các thẻ a với link id dự phòng ->shortcode [a]
function caia_dl_normalize_content($content, &$has_wait_link){
    $content = stripslashes_deep( $content );
    // echo $content;

    $has_wait_link = false;

    // lay danh sach the a
    $regex = '/(<a("[^"]*"|\'[^\']*\'|[^\'>"])*>)/i'; 

    $found = preg_match_all($regex, $content, $matches, PREG_OFFSET_CAPTURE);
    // print_r($matches);
    if (isset($matches[0])){
        $a_tags = $matches[0];    
    }
    // print_r($img_tags);

    $items = array();

    foreach ($a_tags as $key => $a_tag) {
        $href = caia_attr_value($a_tag[0], 'href');
        if ($href){
            $tmp_arr = explode('://', $href);
            $tmp = count($tmp_arr) == 1 ? $tmp_arr[0] : $tmp_arr[1];
            $tmp = intval(trim($tmp));
            if ($tmp > 0){
                $items[] = array('gid' => $tmp, 'tag' => $a_tag);                
            }        
        }        
    }



    if ($items){
        $has_wait_link = true;

        // print_r($items);
        // echo $content;
        // tien hanh update the <a> href id -> shortcode a
        for ($i = count($items) - 1; $i >= 0; $i--) { 
            $item = $items[$i];
            // print_r($item);
            $gid = $item['gid'];
            $tag = $item['tag'][0];
            $tag_pos = $item['tag'][1];

            // echo 'item: ' . $tag . '-' . $tag_pos;
            $open_sc = "[a id={$gid}]";

            // thay the open shortcode tag
            $content = substr($content, 0, $tag_pos) . $open_sc . substr($content, $tag_pos + strlen($tag));            

            // thay the close shortcode tag
            $cpos = strpos($content, '</a>', $tag_pos);
            $content = substr($content, 0, $cpos) . '[/a]' . substr($content, $cpos + strlen('[/a]'));
            
        }
    }

    

    if (!$has_wait_link){
        // kiem tra xem co short code p ko
        $regex = '/(\[p("[^"]*"|\'[^\']*\'|[^\'\]"])*\])/i'; 

        $found = preg_match_all($regex, $content, $matches);        
        // print_r($matches);
        $has_wait_link = isset($matches[0]);        

        if (!$has_wait_link){
            // kiem tra xem co short code p ko
            $regex = '/(\[a("[^"]*"|\'[^\']*\'|[^\'\]"])*\])/i'; 

            $found = preg_match_all($regex, $content, $matches);        
            // print_r($matches);
            $has_wait_link = isset($matches[0]);  
        }
    }
    // echo 'wailink:' . $has_wait_link . "\n";
    // echo $content;
    
    return $content;
}

function caia_attr_value($open_tag, $attr){
    $reg = "/\s{$attr}[\s]*=[\s]*/Ui";
    $found = preg_match($reg, $open_tag, $matches, PREG_OFFSET_CAPTURE, 0);
    if ($found){
        // print_r($matches);
        $match = $matches[0][0];
        $pos = $matches[0][1];
        $next_pos = $pos + strlen($match);
        $next = substr($open_tag, $next_pos, 1);
        // echo $next;
        if ($next === '"'){
            $reg1 = '/\G"|[^\\\\]"/Ui';
            $found = preg_match($reg1, $open_tag, $matches, PREG_OFFSET_CAPTURE, $next_pos + 1);
            if ($found){
                $pos2 = $matches[0][1];
                $value = substr($open_tag, $next_pos + 1, $pos2 - $next_pos);
                return $value;
            }
        }elseif ($next === '\''){
            $reg1 = '/\G\'|[^\\\\]\'/Ui';
            $found = preg_match($reg1, $open_tag, $matches, PREG_OFFSET_CAPTURE, $next_pos + 1);
            if ($found){
                $pos2 = $matches[0][1];
                $value = substr($open_tag, $next_pos + 1, $pos2 - $next_pos);
                return $value;
            }           
        }
        // truong hop chua lay dc value
        $reg1 = '/\s|>|\/>/Ui';
        $found = preg_match($reg1, $open_tag, $matches, PREG_OFFSET_CAPTURE, $next_pos);
        // print_r($matches);
        if ($found){
                $pos2 = $matches[0][1];
                $value = substr($open_tag, $next_pos, $pos2 - $next_pos);
                return $value;
        }else{
            return false;
        }

    }else{
        return false;   
    }
    
}

function caia_check_length_serp_title($title, &$new_title){
    $arr = preg_split('/(?<!^)(?!$)/u', $title );    
    $len = 0;
    $new_title = '';
    $arr1 = array('i', 'ì', 'í', 'ỉ', 'ị', 'ĩ', 'I', 'Ì', 'Í', 'Ỉ', 'Ị', 'Ĩ', 'l', 't');
    $count = count($arr);
    $pos = 0;
    foreach ($arr as $ch) {
        if ($ch === ' '){
            $len += 2;            
        }else if (in_array($ch, $arr1)){
            $len += 1;            
        }else{
            $len += 3;
        }
        $new_title .= $ch;   
        $pos ++;
        if ($len > 156){
            if ($pos < $count){
                $new_title = mb_substr($new_title, 0, mb_strlen($new_title) - 2) . '...';
                return false;
            }
        }     
    }
    $new_title = $title;
    return true;
}


// --------------------- V. BEGIN CO.CAIA API FUNCTION --------------------
/*
--- api1: remote_back_login: -> bỏ ko dùng
$post = array(  'api_name'      =>  'remote_back_login',
                'url'           =>  'http://caia2.vn/wp-admin/post.php?post=5100&action=edit&caia_enable_login=1',              
            );

--- api2: task_update_remote:
$post = array(  'api_name'      =>  'task_update_remote',
                'task_id'       =>  1,
                'token'         => 'abcxyz',
                'link'          => 'http://demo.vn'
                'quality_point' => 80,
                'total_point'   => 100,
                'author_email'  => 'tuannm@caia.vn',
                'do_time'       => date("Y-m-d H:i:s"),
            );

--- api3: ctv_hocviec_get_real_doer_email:
$post = array(  'api_name'      =>  'ctv_hocviec_get_real_doer_email',
                'caia_task_id'  =>  '24',                
            ); 
--- api4: ctv_new_get_leader_email:
$post = array(  'api_name'      =>  'ctv_new_get_leader_email',
                'caia_task_id'  =>  '24',                
            ); 
--- api5: ctv_new_get_leader_email:
$post = array(  'api_name'      =>  'ctv_get_remote_role',
                'email'         =>  'tuannm@caia.vn',  
                'website'       =>  'caia2.vn'              
            );                         
*/
function call_co_caia_api($post){

    $api_code = CAIA_API_CODE;
    $post_url = CAIA_API_URL;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $post_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));

    curl_setopt($ch, CURLOPT_HTTPHEADER, array( "Authorization: {$api_code}", "Content-Type: application/json", "Accept: application/json" ));

    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);    
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

    // Receive server response ...
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch);

    $final_url = curl_getinfo( $ch, CURLINFO_EFFECTIVE_URL );

    curl_close ($ch);

    if ($final_url != $post_url){
        $purl1 = parse_url($post_url);
        $purl2 = parse_url($final_url);
        if ($purl1['path'] == $purl2['path']){
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $final_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));

            curl_setopt($ch, CURLOPT_HTTPHEADER, array( "Authorization: {$api_code}", "Content-Type: application/json", "Accept: application/json" ));

            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_FAILONERROR, true);    
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

            // Receive server response ...
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $server_output = curl_exec($ch);

            curl_close ($ch);   
        }   
    }


    return json_decode($server_output, true);

}

//---------------- END CO.CAIA API ---------------