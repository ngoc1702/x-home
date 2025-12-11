<?php

define('CAIA_SCHEMA_VERSION', '3.1');

/*
* 09/12/20: nâng cấp hỗ trang profile với sameas
* 05/12/20: nâng cấp dùng micro-data cho cả tổ chức và cá nhân, vì có thể dùng sameas với microdata rồi. Nhung product van giu json-ld
* 05/12/20: gỡ bở rating ở article
* 22/08/21: tối ưu schema rating, hỗ trợ màn hình 320
* 12/08/21: fix lỗi logo ở Organization
* 15/07/22: bổ sung aggregateRating cho singular article ở dạng CreativeWorkSeries json-ld
* 31/10/22: bổ sung thêm FAQs Schema và fix lỗi schema aggregateRating trường hợp owner là Person
*/

if (defined('CAIA_YOAST_SEO_VERSION') && version_compare( CAIA_YOAST_SEO_VERSION, '8.6', '>=' )) {
	$caia_schema = new Caia_Schema();		
}


class Caia_Schema{
	// ---- các loại tổ chức hiện dc hỗ trợ ----
	public $org_types = array(
					'TravelAgency'					=> 		'Agency du lịch', 
					'LocalBusiness' 				=>		'Doanh nghiệp địa phương',
					'MedicalBusiness'				=> 		'Doanh nghiệp về sức khỏe',				
					'HealthAndBeautyBusiness'		=> 		'Doanh nghiệp về sức khỏe và làm đẹp', 
					'HomeAndConstructionBusiness'	=> 		'Doanh nghiệp về nhà ở và xây dựng', 
					'ChildCare'						=>		'Doanh nghiệp về chăm sóc trẻ em',
					'Dentist'						=> 		'Phòng khám nha khoa', 					
					'NewsMediaOrganization'			=>		'Tổ chức truyền thông',					
				);



	// -----------------------------------------
	private $author;

	// type hien duoc ho tro: article, product, how-to, ---> next version: course, critic review, event,  job posting, recipe, software app
	private $main_single_item; // article / product / howto
	private $sub_items = array(); // 
	private $item_list = array();

	//type la: webpage, faq page, q&a page
	private $webpage;
	private $website;
	private $owner_type; // null | personal | organization
	private $owner; // null | personal | organization info
	private $org;
	private $user_dd_org = false; // thanh vien dai dien org dung ten bai viet
	private $owner_member_id = false;

	
	

	function __construct(){		

		$this->owner_type = get_option('caia_schema_owner_type', 'Person');

		add_action( 'wp_head', array($this, 'init_schema_info') );
		
		add_action( 'wp_head', array($this, 'disable_genesis_schema') );	
		
		// them page quan tri
		add_action( 'admin_menu', array($this, 'create_menu') ); // uncomment this line to run this demo

		// them social profile cho user
		add_filter( 'user_contactmethods', array($this, 'add_user_social_links') );
		
		add_filter( 'rwmb_meta_boxes',   array($this, 'add_faq_metabox_form'), 100 );

	}


	function add_faq_metabox_form( $meta_boxes ) {
		
		$prefix = '';
		
		$meta_boxes[] = array(
			'title'      => esc_html__( 'FAQs Schema', 'caia' ),			
			'post_types' => array( 'post', ),						
			'priority'   => 'high',
			'context'    => 'normal',
			'autosave'   => true,
			'fields'     => array(		
				array(	            
					'id' => $prefix . 'faq_schema',
					'type' => 'group',
					'name' => esc_html__( 'FAQs', 'caia' ),						
					'fields' => array(
						array(
							'name' => 'Câu hỏi',
							'id' => 'cau_hoi',
							'type' => 'text',
							'size' => 60
						),
						array(
							'name' => 'Trả lời',
							'id' => 'tra_loi',
							'type' => 'textarea',
						),						
					),
					'clone' => true,
				),
			),
		);
		
		return $meta_boxes;
	}


	function add_user_social_links( $user_contact ) {  	
		unset($user_contact['facebook']);
		unset($user_contact['twitter']);
		
		$user_contact['profile_url'] = __('Trang Profile', 'caia');
		$user_contact['facebook'] = __('Facebook Home', 'caia');
		$user_contact['twitter'] = __('Twitter Home', 'caia');   
		$user_contact['pinterest'] = __('Pinterest Home', 'caia');
		$user_contact['linkedin'] = __('LinkedIn Home', 'caia');
		$user_contact['youtube'] = __('Youtube Channel', 'caia');
		
		unset($user_contact['googleplus']);
		unset($user_contact['aim']);
		unset($user_contact['yim']);
		unset($user_contact['jabber']);

		return $user_contact;
	}

	function disable_genesis_schema(){
		if (! genesis_html5()){

			// ko dung html5 -> remove hcard o comment di
			add_filter( 'post_class', array($this, 'remove_hentry') );
		}
				
	}

	function remove_hentry( $class ) {
		$class[] = 'entry';
		$class = array_diff( $class, array( 'hentry' ) );
		return $class;
	}

	function remove_genesis_schema_attributes( $attr ) {
		unset( $attr['itemprop'], $attr['itemtype'], $attr['itemscope'] );
		return $attr;
	}

	// --------	PHẦN ADMIN ----------

	function create_menu() {    
	    // add_options_page( 'Caia Schema', 'Caia Schema', 'administrator', 'caia-schema', array($this, 'options_page') );

	    // add_menu_page( __( 'CAIA Schema', 'caia' ), __( 'CAIA Schema', 'caia' ), 'administrator', 'caia-schema', array( $this, 'options_page' ) );    	
	    add_submenu_page( 'wpseo_dashboard', __( 'Chủ thể Website', 'caia' ), __( 'Chủ thể Website', 'caia' ), 'administrator', 'caia-schema-owner', array( $this, 'options_page' ));

	    // tạm thời chưa dùng ở version này
    	// add_submenu_page( 'wpseo_dashboard', __( 'Phân loại Pages', 'caia' ), __( 'Phân loại Pages', 'caia' ), 'administrator', 'caia-schema-webpages', array( $this, 'options_webpage' ));
	}

	function options_page() {
		// chon loai owner
		echo '<h1>Cập nhật thông tin dữ liệu cấu trúc hóa</h1><br>';

		$valid_nonce = isset($_REQUEST['_wpnonce']) ? wp_verify_nonce($_REQUEST['_wpnonce'], 'caia-schema') : false;


		echo '<a name="general"></a><h2>☛ Bước 1: Cập nhật thông tin đại diện Web</h2>';		

		if ( isset( $_POST[ 'cs_update_general' ]) && $valid_nonce ) {
			$owner = $_POST[ 'cs_owner' ];
			update_option('caia_schema_owner_type', $owner);											
		}else{
			$owner = get_option('caia_schema_owner_type', 'None');
		}

		echo '<form name="csf_update_general" action="#general" method="post">';
		echo '<label><input type="hidden" value="1" name="cs_update_general"/> </label>';	

		echo '<table class="form-table">';
		echo '<tr>';
		echo '<th>Dạng chủ thể Website:</th>';
		echo '<td>';
		echo '<fieldset class="options">';
		echo "<select name='cs_owner'>";
  		// echo "<option value='None' " . selected( $owner, 'None' ) . ">None</option>";
  		echo "<option value='Person' " . selected( $owner, 'Person' ) . ">Cá nhân</option>";
  		echo "<option value='Organization' " . selected( $owner, 'Organization' ) . ">Tổ chức</option>";
		echo '</select>';

		echo '<div class="submit"><input class="button-primary" type="submit" value="Cập nhật" /></div>';
		wp_nonce_field('caia-schema');

		echo '</fieldset>';	
		echo '</td>';
		echo '</tr>';
		echo '</table>';
		echo '</form>';
		
			

		if ($owner == 'Person'){
			// $this->options_person_form( $valid_nonce );
		}else if($owner == 'Organization'){
			$this->options_organization_form( $valid_nonce );
		}

	}

	function options_person_form($valid_nonce){
		echo '<a name="person"></a><h2>☛ Bước 2: Cập nhật thông tin chủ thể Cá nhân</h2>';				

		if ( isset( $_POST[ 'cs_update_person' ]) && $valid_nonce ) {
			$owner = $_POST[ 'cs_person' ];
			update_option('caia_schema_owner_person', $owner);											
		}else{
			$owner = get_option('caia_schema_owner_person', false);
		}

		echo '<form name="csf_update_person" action="#person" method="post">';
		echo '<label><input type="hidden" value="1" name="cs_update_person"/> </label>';	

		echo '<table class="form-table">';
		echo '<tr>';
		echo '<th>Chủ thể tương ứng với user:</th>';
		echo '<td>';
		echo '<fieldset class="options">';
		echo "<select name='cs_person'>";
  		echo "<option value='None' " . selected( $owner, false ) . ">None</option>";
  		$user_list = get_users( array('role__not_in' => 'subscriber', 'fields' => array( 'ID', 'user_login', 'display_name' ) ) );  		
  		foreach ($user_list as $user) {
  			echo "<option value='{$user->ID}' " . selected( $owner, $user->ID ) . ">{$user->user_login} > {$user->display_name}</option>";
  		}
		echo '</select>';

		echo '<div class="submit"><input class="button-primary" type="submit" value="Cập nhật" /></div>';
		wp_nonce_field('caia-schema');

		echo '</fieldset>';	
		echo '</td>';
		echo '</tr>';
		echo '</table>';
		echo '</form>';
	}

	function options_organization_form($valid_nonce){

		echo '<a name="organization"></a><h2>☛ Bước 2: Cập nhật thông tin chủ thể Tổ chức</h2>';				

		if ( isset( $_POST[ 'cs_update_organization' ]) && $valid_nonce ) {
			$owner = stripslashes_deep( $_POST[ 'cs_org' ] );
			// print_r($owner);
			update_option('caia_schema_owner_organization', $owner);											
		}else{
			$owner = get_option('caia_schema_owner_organization', array());
		}

		echo '<form name="csf_update_organization" action="#organization" method="post">';
		echo '<label><input type="hidden" value="1" name="cs_update_organization"/> </label>';	

		echo '<table class="form-table">';
		
		echo '<tr>';
		echo '<th>Thành viên đại diện:</th>';
		echo '<td>';		
		echo "<select name='cs_org[user_dd]'>";
  		echo "<option value='None' " . selected( _gis($owner['user_dd'], false), false ) . ">None</option>";
  		$user_list = get_users( array('role__not_in' => 'subscriber', 'fields' => array( 'ID', 'user_login', 'display_name' ) ) );  		
  		foreach ($user_list as $user) {
  			echo "<option value='{$user->ID}' " . selected( _gis($owner['user_dd'], false), $user->ID ) . ">{$user->user_login} > {$user->display_name}</option>";
  		}
		echo '</select>';
		echo '<p class="description">Những bài viết bởi thành viên này, sẽ dùng profile của tổ chức</p>';
		echo '</td>';
		echo '</tr>';


		echo '<tr>';
		echo '<th>Tên tổ chức</th>';
		echo '<td>';	
		echo "<input name='cs_org[name]' type='text' value='{$owner['name']}' size='60'>";
		echo '<p class="description">Vd: Công ty CP Công nghệ và Truyền thông Caia</p>';
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<th>Loại tổ chức:</th>';
		echo '<td>';		
		echo "<select name='cs_org[type]'>";  		
  				
  		foreach ($this->org_types as $key => $name) {
  			echo "<option value='{$key}' " . selected( $key, _gis($owner['type'], 'LocalBusiness') ) . ">{$name}</option>";
  		}  		
		echo '</select>';
		echo '<p class="description">Nên chọn loại càng sát với nghiệp vụ của tổ chức càng tốt, danh mục đầy đủ lấy từ schema.org, 
		<br>liên hệ tuannm nếu cần bổ sung thêm loại tổ chức cần thiết</p>';
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<th>Mô tả</th>';
		echo '<td>';	
		echo "<textarea name='cs_org[description]' rows='4' cols='62'>{$owner['description']}</textarea>";	
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<th>Website chính thức:</th>';
		echo '<td>';		
		echo "<input name='cs_org[url]' type='text' value='{$owner['url']}' size='60'>";
		echo '<p class="description">Vd: https://caia.vn</p>';
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<th>Logo:</th>';
		echo '<td>';		
		echo "<input name='cs_org[logo]' type='text' value='{$owner['logo']}' size='60'>";
		echo '<p class="description">Chú ý, không chọn ảnh logo quá lớn, size < 256px, và nhỏ hơn 100Kb</p>';
		echo '<p class="description">Vd: https://caia.vn/logo.jpg</p>';

		echo '</td>';
		echo '</tr>';

		
		echo '<tr>';
		echo '<th>Số điện thoại</th>';
		echo '<td>';		
		echo "<input name='cs_org[telephone]' type='text' value='{$owner['telephone']}' size='60'>";
		echo '<p class="description">Vd: 0912 345 678</p>';
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<th>Email</th>';
		echo '<td>';		
		echo "<input name='cs_org[email]' type='text' value='{$owner['email']}' size='60'>";
		echo '<p class="description">Vd: sales@caia.vn</p>';
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<th>Giờ mở cửa</th>';
		echo '<td>';		
		echo "<textarea rows='2' cols='57' name='cs_org[openingHours]'>{$owner['openingHours']}</textarea>";		
		echo '<p class="description">Mỗi mô tả giờ mở cửa chiếm 1 dòng, theo đúng format như sau:</p>';
		echo '<p class="description">Vd 1:  Mo-Fr 08:00-17:30</p>';
		echo '<p class="description">Vd 2:  Sa 08:00-12:00</p>';
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<th>Khoảng giá</th>';
		echo '<td>';		
		echo "<input name='cs_org[priceRange]' type='text' value='{$owner['priceRange']}' size='60'>";
		echo '<p class="description">Vd: 50.000 - 1.000.000 Vnd</p>';
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<th>Tọa độ</th>';
		echo '<td>';		
		echo "<input name='cs_org[geo][latitude]' type='text' value='{$owner['geo']['latitude']}' size='25' placeholder='vĩ độ, vd: 10.83'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<input name='cs_org[geo][longitude]' type='text' value='{$owner['geo']['longitude']}' size='25' placeholder='kinh độ, vd: 106.67'>";
		echo '<p class="description">Điền vĩ độ (latitude) ở ô 1, và kinh độ (longitude) ở ô 2</p>';
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<th>Link Google Map:</th>';
		echo '<td>';		
		echo "<input name='cs_org[hasMap]' type='text' value='{$owner['hasMap']}' size='60'>";
		echo '<p class="description">Ex: https://www.google.com/maps/place/Caia.vn/@10.830954,106.679629</p>';
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<th>Địa chỉ</th>';
		echo '<td>';		
		echo "<input name='cs_org[address]' type='text' value='{$owner['address']}' size='60'>";
		echo '<p class="description">Vd: tầng 5, số nhà 20, ngõ 4, Phương Mai, Đống Đa, Hà Nội</p>';
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<th>Page MXH khác</th>';
		echo '<td>';		
		echo "<textarea name='cs_org[sameAs]' rows='4' cols='62'>{$owner['sameAs']}</textarea>";	
		echo '<p class="description">Điền mỗi link là một dòng, vd:</p>';		
		echo '<p class="description">https://facebook.com/caia.vn/<br>https://www.linkedin.com/company/caia.vn</p>';		
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<th>Hình ảnh khác</th>';
		echo '<td>';		
		echo "<textarea name='cs_org[image]' rows='4' cols='62'>{$owner['image']}</textarea>";	
		echo '<p class="description">Điền mỗi link ảnh là một dòng, ko lấy có có kích thước > 150Kb, vd:</p>';		
		echo '<p class="description">https://caia.vn/image1.jpg<br>https://caia.vn/image2.jpg';		
		echo '</td>';
		echo '</tr>';

		
		echo '<tr>';
		echo '<th>&nbsp;</th>';
		echo '<td>';

		echo '<div class="submit"><input class="button-primary" type="submit" value="Cập nhật" /></div>';
		wp_nonce_field('caia-schema');

		
		echo '</td>';
		echo '</tr>';
		echo '</table>';
		echo '</form>';

	}

	function options_webpage(){
		// ho tro cac loai webpage:
		// AboutPage -
		// CheckoutPage
		// CollectionPage
		// ContactPage -
		// FAQPage -
		// ItemPage
		// MedicalWebPage
		// ProfilePage
		// QAPage -
		// SearchResultsPage 

		$valid_nonce = isset($_REQUEST['_wpnonce']) ? wp_verify_nonce($_REQUEST['_wpnonce'], 'caia-schema') : false;

		echo '<a name="organization"></a><h1>☛ Cập nhật cấu hình phân loại trang trong</h1>';				

		if ( isset( $_POST[ 'cs_update_webpages' ]) && $valid_nonce ) {
			$webpages = stripslashes_deep( $_POST[ 'cs_webpages' ] );
			$wp_opt = array();
			foreach ($webpages as $key => $value) {
				if ($value){
					caia_log('schema', 'url', $value);
					$tmp = parse_url(trim($value));
					$wp_opt[$key] = $tmp['path'];
				}
			}	
			caia_log('schema', 'url', $wp_opt);		
			update_option('caia_schema_webpages', $wp_opt);											
		}else{
			$webpages = get_option('caia_schema_webpages', array());
			foreach ($webpages as $key => $value) {
				if ($value){
					$webpages[$key] = rtrim(get_home_url(), '/') . $value;
				}
			}
			
		}

		echo '<form name="csf_update_organization" action="#webpage" method="post">';
		echo '<label><input type="hidden" value="1" name="cs_update_webpages"/> </label>';	

		echo '<table class="form-table">';
		
		echo '<tr>';
		echo '<th>Trang giới thiệu</th>';
		echo '<td>';	
		echo "<input name='cs_webpages[gioi_thieu]' type='text' value='{$webpages['gioi_thieu']}' size='60'>";
		echo '<p class="description">Vd: https://caia.vn/gioi-thieu/</p>';
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<th>Trang liên hệ</th>';
		echo '<td>';	
		echo "<input name='cs_webpages[lien_he]' type='text' value='{$webpages['lien_he']}' size='60'>";
		echo '<p class="description">Vd: https://caia.vn/lien-he/</p>';
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<th>Trang hỏi đáp</th>';
		echo '<td>';	
		echo "<input name='cs_webpages[hoi_dap]' type='text' value='{$webpages['hoi_dap']}' size='60'>";
		echo '<p class="description">Vd: https://caia.vn/questions/</p>';
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<th>Trang FAQs</th>';
		echo '<td>';	
		echo "<input name='cs_webpages[faqs]' type='text' value='{$webpages['faqs']}' size='60'>";
		echo '<p class="description">Vd: https://caia.vn/faqs/</p>';
		echo '</td>';
		echo '</tr>';

		
		echo '<tr>';
		echo '<th>&nbsp;</th>';
		echo '<td>';

		echo '<div class="submit"><input class="button-primary" type="submit" value="Cập nhật" /></div>';
		wp_nonce_field('caia-schema');

		
		echo '</td>';
		echo '</tr>';
		echo '</table>';
		echo '</form>';

	}

	// -------- END PHẦN ADMIN --------

	function generate_json_ld(){



		$data = array();
		

		if ($this->owner_type == 'Organization'){
			$data['@context'] = 'https://schema.org';
			$data['@graph'] = array();

			if ($this->main_single_item){
				if ($this->owner){
					$data['@graph'][] = $this->owner;					
				}		
				if ($this->website){
					$data['@graph'][] = $this->website;	
				}				
				if ($this->webpage){
					$data['@graph'][] = $this->webpage;	
				}
				if ($this->main_single_item){
					$data['@graph'][] = $this->main_single_item;	
				}
				
				
				if ($this->owner && $this->author && $this->owner['@id'] != $this->author['@id'])
					$data['@graph'][] = $this->author;
					// print_r($this->author);
					// die;

			}else{
				if ($this->owner){
					$data['@graph'][] = $this->owner;					
				}
				if ($this->website){
					$data['@graph'][] = $this->website;	
				}				
				if ($this->webpage){
					$data['@graph'][] = $this->webpage;	
				}
				if ($this->item_list){
					// tao data chi item_list
					$full_item_list = array();
					$full_item_list['@type'] = 'ItemList';
					$full_item_list['itemListElement'] = array();
					if (isset($this->webpage) && isset($this->webpage['@id'])){					
						$full_item_list['mainEntityOfPage'] = $this->webpage['@id'];
					}
					foreach ($this->item_list as $key => $item) {					
						$item['position'] = $key + 1;								
						$full_item_list['itemListElement'][] = $item;
					}

					$data['@graph'][] = $full_item_list;


				}
			}
		}else{
			if ($this->website){
				$data = $this->website;	
				$data['@context'] = 'https://schema.org';
			}			
		}
		
		if ($this->sub_items){
			foreach ($this->sub_items as $key => $sub_item) {				
				$data['@graph'][] = $sub_item;	
			}			
		}


		echo '<!-- caia_schema --><script type="application/ld+json">';
		if ($this->org && is_home()){
			// chi hien thi thong tin o trang chu:
			echo json_encode($this->org, JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE |  JSON_UNESCAPED_SLASHES );	
			echo '</script><script type="application/ld+json">';
		}
		if ($data){
			echo json_encode($data, JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE |  JSON_UNESCAPED_SLASHES );	
		}
		
		echo '</script>';
	}


	function init_schema_info(){
		
		remove_shortcode( 'post_author', 'genesis_post_author_shortcode' );		
		add_shortcode( 'post_author', array($this, 'post_author_shortcode' ) );
		

		if ($this->owner_type == 'Organization'){
			$this->fill_owner_info();		
			$this->fill_website_info();
			
			add_action( 'genesis_before_loop', array($this, 'fill_schema_info') );						
			
			add_action('wp_footer', array($this, 'generate_json_ld'), 0);	
		}else{
			// la loai Person or None
			if (genesis_html5()){

				// add_filter('caia_rating_info', array($this, 'schema_rating_info'), 10, 3);

				$this->fill_website_info();	

				add_action( 'genesis_before_loop', array($this, 'fill_schema_info') );				

				add_action('wp_footer', array($this, 'generate_json_ld'), 0);	
			}
		}					

	}

	
	function post_author_shortcode( $atts ) {

		if ( ! post_type_supports( get_post_type(), 'author' ) ) {
			return '';
		}

		$author = get_the_author();

		if ( ! $author ) {
			return '';
		}

		$defaults = array(
			'after'  => '',
			'before' => '',
		);

		$atts = shortcode_atts( $defaults, $atts, 'post_author' );

		$author_id =  get_the_author_id();

		if (genesis_html5()){
			if (is_singular() && $this->owner_type == 'Organization' 
				&& $author_id == $this->user_dd_org && $this->user_dd_org > 0){					
				
				

				$entry_author = 'class="entry-author" itemprop="author" itemscope itemtype="https://schema.org/Organization"';

				$same_as = '';
				$opt = get_option( 'caia_schema_owner_organization', false );


				if (isset($opt['sameAs'])){					
					$org['sameAs'] = array_map('trim', explode("\n", $opt['sameAs']));

					foreach ($org['sameAs'] as $value) {
						$same_as .= "<link itemprop='sameAs' href='{$value}' />";
					}
				}

			}else{
				$entry_author = 'class="entry-author" itemprop="author" itemscope itemtype="https://schema.org/Person"';

				
				$facebook = get_the_author_meta( 'facebook', $author_id );
				$linkedin = get_the_author_meta( 'linkedin', $author_id );
				$twitter = get_the_author_meta( 'twitter', $author_id );
				$youtube = get_the_author_meta( 'youtube', $author_id );
				$pinterest = get_the_author_meta( 'pinterest', $author_id );
				$profile_url = get_the_author_meta( 'profile_url', $author_id );

				$same_as = '';
				
				if ($facebook) $same_as .= "<link itemprop='sameAs' href='{$facebook}' />";
				if ($linkedin) $same_as .= "<link itemprop='sameAs' href='{$linkedin}' />";
				if ($twitter) $same_as .= "<link itemprop='sameAs' href='{$twitter}' />";
				if ($youtube) $same_as .= "<link itemprop='sameAs' href='{$youtube}' />";
				if ($pinterest) $same_as .= "<link itemprop='sameAs' href='{$pinterest}' />";
				if ($profile_url) $same_as .= "<link itemprop='sameAs' href='{$profile_url}' />";
				
				if ( defined('CAIA_WPSEO_DISABLE_AUTHOR_REDIRECT') ){					
					$author_url = home_url( '', null ) . '/author/' . get_the_author_meta('user_login', $author_id);
					$same_as .= "<link itemprop='sameAs' href='{$author_url}' />";	
				} 
				
			}
		}

		if (!$entry_author) $entry_author = genesis_attr( 'entry-author' );

		
		if ( genesis_html5() ) {
			$output  = sprintf( '<span %s>', $entry_author );
			$output .= $atts['before'];
			$output .= sprintf( '<span %s>', genesis_attr( 'entry-author-name' ) ) . esc_html( $author ) . '</span>';
			$output .= $same_as;
			$output .= $atts['after'];
			$output .= '</span>';
		} else {
			$output = sprintf( '<span class="author vcard">%2$s<span class="fn">%1$s</span>%3$s</span>', esc_html( $author ), $atts['before'], $atts['after'] );
		}

		return apply_filters( 'genesis_post_author_shortcode', $output, $atts );

	}



	function schema_rating_info($text, $rate_str, $count){		
		if ($count){
			return "<span itemprop='aggregateRating' itemscope itemtype='http://schema.org/AggregateRating'><span class='rating_value' itemprop='ratingValue'>{$rate_str}</span><span class='rating_split'> - </span><span class='rating_count' itemprop='reviewCount'>{$count}</span> đánh giá</span>";
		}else{
			return $text;
		}
		
	}

	// dung cho organization dung json-ld
	function fill_schema_info(){
		// global $wp_query;
		// print_r($wp_query);	
		
		if (is_main_query() || true){ // chi ap dung voi post page

		
			// caia_log('schema', 'beforeloop', $_SERVER['REQUEST_URI']);
			
			if (is_singular()){
				// article, product, 				
				$this->fill_main_single_item();						
			}
		}		
	}
	

	

	function fill_main_single_item(){
		$post_id = get_the_ID();
		// lay ra type tuong ung voi main_item: article, product, how-to
		// uu tien o postmeta > rule > default: article if yoast seo index
		
		$item_type = apply_filters( 'caia_schema_single_item_type', 'article', $post_id );

		if ($item_type == 'product'){
			$this->fill_product_info($post_id);
		} else if ($item_type == 'article'){
			// 17/07/22
			$this->fill_article_info($post_id);
		}

		// bo sung them faqs schema
		$faqs = get_post_meta($post_id, 'faq_schema', true);
		if ($faqs && count($faqs) > 1){
			$link = get_permalink( $post_id );
			$sub_item = array();				
			$childs = [];
			foreach ($faqs as $key => $faq) {
				$hoi = $faq['cau_hoi'] ?? '';
				$tra_loi = $faq['tra_loi'] ?? '';

				if ($hoi && $tra_loi){
					if (strpos($tra_loi, $link) === false){
						$tra_loi .= " <a href='{$link}' target='_blank'>[xem thêm]</a>";
					}
					$childs[] = [	'@type'			=> 'Question',
									'name'			=> $hoi,
									'acceptedAnswer'=> [ '@type' => 'Answer', 'text' => $tra_loi ]
								];
				}
			}			

			if (count($childs) > 1){
				$sub_item['@type'] = 'FAQPage';
				$sub_item['mainEntity'] = $childs;
				$this->sub_items[] = $sub_item;
			}
		}				
	}



	function fill_article_info($post_id){
		
		$count = get_post_meta( $post_id, 'caia_rating_count', true );

		if ($count){
			$count = intval($count);
			$point = get_post_meta( $post_id, 'caia_rating_point', true );
			if ($point) $point = intval($point);

			$rate = round($point*10/$count)/10;
			if ($rate > 5) $rate = 5;


			$sub_item = array();	
			$sub_item['@type'] = 'CreativeWorkSeries';
			$sub_item['name'] = get_the_title();
			$sub_item['aggregateRating'] = array( '@type' 		=> 'AggregateRating',
												'ratingValue' 	=> $rate,
												'reviewCount'	=> $count
												);
			$this->sub_items[] = $sub_item;

		}


		


		
		

	}

	function fill_product_info($pro_id){
		$url = get_the_permalink();
		$product = array();
		$product['@type'] = 'Product';
		$product['@id'] = $url . '#product';
		
		if (isset($this->webpage) && isset($this->webpage['@id'])){
			// $product['isPartOf'] = array('@id' => $this->webpage['@id']);	
			$product['mainEntityOfPage'] = $this->webpage['@id'];
		}

		
		$product['name'] = get_the_title();				
		
		$product['url'] = $url;

		// $image = get_the_post_thumbnail_url(); //(array('format' => 'url'));
		$image = genesis_get_image(array('format' => 'url'));
		if ($image) $product['image'] = $image;
		

		$desc = get_post_meta($pro_id, '_yoast_wpseo_metadesc', true);
		if ($desc){
			$product['description'] = $desc;
		}else{
			$excerpt = get_the_excerpt();
			if ($excerpt) $product['description'] = $excerpt;

		}

		// con aggregateRating
		$count = get_post_meta( $pro_id, 'caia_rating_count', true );

		if ($count){
			$count = intval($count);
			$point = get_post_meta( $pro_id, 'caia_rating_point', true );
			if ($point) $point = intval($point);

			$rate = round($point*10/$count)/10;
			if ($rate > 5) $rate = 5;

			if ($rate > 0){
				$product['aggregateRating'] = array( '@type' 		=> 'AggregateRating',
													'ratingValue' 	=> $rate,
													'reviewCount'	=> $count
													);
			}
		}

		$brand_name = apply_filters( 'caia_schema_product_brand', '', $pro_id );
		if ($brand_name){
			$product['brand'] = array('@type' => 'Thing', 'name' => $brand_name);
		}

		$price = apply_filters( 'caia_schema_product_price', false, $pro_id );
		if ( $price !== false ){
			$currency = apply_filters( 'caia_schema_product_price_currency', 'Vnd', $pro_id );
			$product['offers'] = array( '@type' 	=> 'AggregateOffer',
										'lowPrice' 	=> $price,
										'highPrice'	=> $price,
										'priceCurrency' => $currency);
		}

		$this->main_single_item = apply_filters( 'caia_schema_product', $product, $pro_id );
	}



	function fill_owner_info(){
		$owner_type = get_option( 'caia_schema_owner_type', false );

		if ($owner_type == 'Person'){
			$owner_member_id = get_option( 'caia_schema_owner_person', false );			
			if ( is_numeric($owner_member_id) ){
				$this->owner = $this->make_person_type( intval($owner_member_id), '#person' );
				// print_r($this->owner);
				// die;
				$this->owner_member_id = $owner_member_id;
			}else{
				$this->owner = null;
			}

		}else if($owner_type == 'Organization'){
			$opt = get_option( 'caia_schema_owner_organization', false );
			if ($opt && isset($opt['type'])){
				$org = array();
				$org['@context'] = "https://schema.org";
				$org['@type'] = $opt['type'];				
				if (isset($opt['url'])){
					$url = $opt['url'];
				}else{
					$url = get_home_url();
				}
				$org['url'] = $url;
				
				if (isset($opt['name'])) $org['name'] = $opt['name'];
				if (isset($opt['description'])) $org['description'] = $opt['description'];
				if (isset($opt['logo'])) {
					// $org['logo'] = $opt['logo'];
					$org['logo'] = array( '@type' => 'ImageObject', 'url' => $opt['logo'] );
				}
				if (isset($opt['telephone'])) $org['telephone'] = $opt['telephone'];
				if (isset($opt['email'])) $org['email'] = $opt['email'];
				if (isset($opt['openingHours'])) {
					$org['openingHours'] = array_map('trim', explode("\n", $opt['openingHours'])); ;	
				} 
				if (isset($opt['address'])) $org['address'] = $opt['address'];
				if (isset($opt['hasMap'])) $org['hasMap'] = $opt['hasMap'];
				if (isset($opt['geo']) && isset($opt['geo']['latitude']) && isset($opt['geo']['longitude'])) 
					$org['geo'] = array('@type' => 'GeoCoordinates', 'latitude' => $opt['geo']['latitude'], 'longitude' => $opt['geo']['longitude']);
				
				if (isset($opt['sameAs'])){					
					$org['sameAs'] = array_map('trim', explode("\n", $opt['sameAs']));
				}
				if (isset($opt['image'])){					
					$org['image'] = array_map('trim', explode("\n", $opt['image']));
				}

				if (isset($opt['priceRange'])) $org['priceRange'] = $opt['priceRange'];
				
				$this->org = $org;

				if (isset($opt['user_dd'])) {
					$this->user_dd_org = $opt['user_dd'];
				}

				// ----- lam organization cho publisher
				$owner = array();				
				$owner['@type'] = 'Organization';
				$owner['@id'] = $url . '#organization';
				$owner['url'] = $url;
				if (isset($opt['name'])) $owner['name'] = $opt['name'];
				if (isset($opt['description'])) $owner['description'] = $opt['description'];
				if (isset($opt['logo'])) {
					$owner['logo'] = array( '@type' => 'ImageObject', 'url' => $opt['logo'] );
				}
				if (isset($opt['telephone'])) $owner['telephone'] = $opt['telephone'];
				if (isset($opt['address'])) $owner['address'] = $opt['address'];
				if (isset($opt['sameAs'])){					
					$owner['sameAs'] = array_map('trim', explode("\n", $opt['sameAs']));
				}

				$this->owner = $owner;
			}else{
				$this->owner = null;
			}
					
		}else{
			$this->owner = null;
		}

		// print_r($this->owner);

	}


	function fill_website_info(){		

		$website = array();
		$website['@type'] = 'Website';
		$url = get_home_url();
		$website['@id'] = $url . '#website';
		$website['url'] = $url;
		$website['name'] = get_bloginfo('name');
		$desc = WPSEO_Frontend::get_instance()->options['metadesc-home-wpseo'];
		if ($desc) $website['description'] = $desc;



		if ($this->owner && isset($this->owner['@id'])){
			$website['publisher'] = array('@id' => $this->owner['@id']);
		}
		
		$website['potentialAction'] = array('@type' 	=> 'SearchAction',
											'target'	=> "{$url}?s={search_term_string}",
											'query-input'	=> 'required name=search_term_string');
		
		$this->website = $website;
		
		// print_r($website);
		// die;
	}

	function make_person_type($member_id, $id_anchor='#person'){
		$author = array();
		$author['@type'] = 'Person';
		$author['@id'] = get_author_posts_url($member_id) . $id_anchor;
		$author['name'] = get_the_author();
		$author['image'] = get_avatar_url($member_id);

		
		$facebook = get_the_author_meta( 'facebook', $member_id );
		$linkedin = get_the_author_meta( 'linkedin', $member_id );
		$twitter = get_the_author_meta( 'twitter', $member_id );
		$youtube = get_the_author_meta( 'youtube', $member_id );
		$pinterest = get_the_author_meta( 'pinterest', $member_id );
		$profile_url = get_the_author_meta( 'profile_url', $member_id );
		
		if ($facebook) $author['sameAs'][] = $facebook;
		if ($linkedin) $author['sameAs'][] = $linkedin;
		if ($twitter) $author['sameAs'][] = $twitter;
		if ($youtube) $author['sameAs'][] = $youtube;
		if ($pinterest) $author['sameAs'][] = $pinterest;
		if ($profile_url) $author['sameAs'][] = $profile_url;
		
		$description = get_the_author_meta( 'description', $member_id );
		if ($description) $author['description'] = $description;

		return $author;
	}


} // end of class Caia_Schema

