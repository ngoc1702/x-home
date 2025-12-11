/**** CAIA FEATURED ******/
	
	Add 2 new query var:
		. New query var: featured
		. New query var orderby: featured_order, featured_by_cat_order

	Use as query_posts or WP_Query query vars:
	    'featured' => 'all' OR 'featured' => 'category'
	    'orderby' => 'featured_order' OR 'orderby' => 'featured_by_cat_order'


	Ex:
	Get 5 featured posts of website:
	    $featureds = new WP_Query( 'featured=all&showposts=5&ignore_sticky_posts=1' );

	Get 5 featured posts of website, orderby featured order:
	    $featureds = new WP_Query( 'featured=all&showposts=5&orderby=featured_order' );

	Get 5 featured posts of category 21:
	    $featureds = new WP_Query( 'cat=21&featured=category&showposts=5' );

	Get 5 featured posts of category 21, orderby featured order:
	    $featureds = new WP_Query( 'cat=21&featured=category&showposts=5&orderby=featured_by_cat_order' );


/**** CAIA BLOCK ******/
Với CAIA Childtheme có thể kéo thả các Caia_Block trong admin cho hiện thị trang chủ. Với các page khác ta vẫn có thể sử dụng các Caia Block, nhưng bằng code, như ví dụ dưới đây (since version 2.0)

1. Caia_News_Block
	Ex1: hỗ trợ call print_block trong code.

	$args =	array(
			'title'                    => '', // ex: 'Tác dụng của dầu tỏi'
			'auto_title'               => 0, // auto chose term name as title if title is empty. Has 2 value: 0, 1. Default: 0
			'taxonomy'                 => 'category', // taxonomy, ex: 'post_tag'. Default: 'category'
			'term'                     => '', // term_id, ex: 3. Default: ''
			'post_type'                => 'post', 
			'post_info'                => '', ex: [post_date] by [post_author_posts_link][post_comments]. Default: ''  			
			'num_posts'                => 1, // number of main post to show
			'num_relateds'             => 0, // number of related post to show, ex: 3
			'related_title'            => '', // ex: 'Bài tiếp theo: '
			'image_size'               => 'thumbnail',				
			'related_post_image_size'  => 'thumbnail',
			'image_align'              => 'alignnone', // 3 value: 'alignnone', 'alignleft', 'alignright'. Default: 'alignnone'
			'related_post_image_align' => 'alignnone', // 3 value: 'alignnone', 'alignleft', 'alignright'. Default: 'alignnone'
			'title_position'		   => 'before_thumbnail', // 2 value: 'before_thumbnail', 'after_thumbnail'. Default: 'before_thumbnail'
			'related_image'            => 0, // To display feauture images of next post. Has 2 value: 0, 1. Default: 0.
			'featured'                 => 0, // To display only feautured post or not. Has 2 value: 0, 1. Default: 0.
			'content_limit'            => 200, // the content limit. Default: 200
			'read_more'                => '', // ex: 'xem thêm'. Default: ''
			'more_text'                => '', // ex: 'xem đầy đủ chuyên mục'. Default: ''
			'more_position'            => 'top', // 2 value: 'top', 'bottom'. Default: 'top'
			'menu'                     => '' // the menu name, ex: nav_menu. Default: ''
			);

	$block = new Caia_News_Block();
	$block->print_block($args);

	Ex2: hỗ trợ lấy ra danh sách post_ids mới print ra.
	$block = new Caia_News_Block();
	$block->print_block($args);
	$posts_just_shown = $block->get_just_shown_post_ids(); => trả về array of post_id mới dc show hoặc print ra.

	Ex3: hỗ trợ exclude một danh sách các post_ids (dùng khi ko muốn print trùng post đã print rồi)
	$block = new Caia_News_Block();
	$block->print_block($args);
	$posts_just_shown = $block->get_just_shown_post_ids();
	
	echo '<hr>';	
	$block->set_exclude_post_ids($posts_just_shown); // truyền vào array những post_ids ko muốn hiện ra, call trước khi print.
	$block->print_block($args);

2. Caia_Flexible_Block
	Block này có thể dùng tùy biến tốt để hiện thì các khối danh sách bài post, sản phẩm hỗ trợ Hook để thay đổi cho nhanh. Ví dụ Block sản phẩm của bepmoi, trangkim, hay có thể dụng hook để tạo các khối slide trượt (jCarousel) các bài viết.
	$args =	array(
			'code'					   => '', // default: ''. Có thể gán thuộc tính này để do_action riêng cho mỗi code.
			'title'                    => '', // ex: 'Tác dụng của dầu tỏi'
			'auto_title'               => 0, // auto chose term name as title if title is empty. Has 2 value: 0, 1. Default: 0
			'taxonomy'                 => 'category', // taxonomy, ex: 'post_tag'. Default: 'category'
			'term'                     => '', // term_id, ex: 3. Default: ''
			'post_type'                => 'post', // Default: 'post'
			'post_info'                => '', ex: [post_date] by [post_author_posts_link][post_comments]. Default: ''  			
			'num_posts'                => 1, // number of main post to show
			'num_relateds'             => 0, // number of related post to show, ex: 3
			'related_title'            => '', // ex: 'Bài tiếp theo: '. Default: ''
			'image_size'               => 'thumbnail',				
			'related_post_image_size'  => 'thumbnail',
			'image_align'              => 'alignnone', // 3 value: 'alignnone', 'alignleft', 'alignright'. Default: 'alignnone'
			'related_post_image_align' => 'alignnone', // 3 value: 'alignnone', 'alignleft', 'alignright'. Default: 'alignnone'
			'title_position'		   => 'before_thumbnail', // 2 value: 'before_thumbnail', 'after_thumbnail'. Default: 'before_thumbnail'
			'related_image'            => 0, // To display feauture images of next post. Has 2 value: 0, 1. Default: 0.
			'featured'                 => 0, // To display only feautured post or not. Has 2 value: 0, 1. Default: 0.
			'content_limit'            => 200, // the content limit. Default: 200
			'read_more'                => '', // ex: 'xem thêm'. Default: ''
			'more_text'                => '', // ex: 'xem đầy đủ chuyên mục'. Default: ''
			'more_position'            => 'top', // 2 value: 'top', 'bottom'. Default: 'top'
			'menu'                     => '', // the menu name, ex: nav_menu. Default: ''
			'support_jcarousel'		   => 0 // hiện thị ở dạng ul-li tương thích với jcarousel => làm block slider. Default: 1.
			);

	$block = new Caia_Flexible_Block();
	$block->print_block($args);

	Caia_Flexible_Block hỗ trợ đầy đủ những chức năng mà Caia_News_Block có, gồm (đọc phần Caia_News_Block để hiểu hơn):
	$posts_just_shown = $block->get_just_shown_post_ids();	
	$block->set_exclude_post_ids($posts_just_shown); 


	Có 5 hook có thể điều chỉnh (nên đọc file class-flexible-block.php để hiểu hơn):
		Filter: caia_flexible_block_query'
		Action: 'caia_flexible_block_main_post_show'
		Action: 'caia_flexible_block_next_post_show'
		Action: 'caia_flexible_block_before_content'
		Action: 'caia_flexible_block_after_content'
	Có thể sử dụng 5 hook này để điều chỉnh Html show ra của Flexible_Block, và đặc biệt có thể sử dụng code để customise riêng cho mỗi block.
	
	Ex1:

	add_action('genesis_before_loop', 'new_func' );

	function new_func(){
		$args = array(
			'title'                    => 'Tác dụng của dầu tỏi' , 
			'term'                     => '3', // category_id = 3			
			'num_posts'                => 1, 
			'num_relateds'             => 3, 
			'related_title'            => 'Next Posts:',			
			);
		
		// change html
		remove_action('caia_flexible_block_main_post_show', 'caia_flexible_block_main_post_show', 10, 2);
		add_action('caia_flexible_block_main_post_show', 'caia_flexible_block_main_post_show_new', 10, 2);

		$block = new Caia_Flexible_Block();
		$block->print_block($args);

		// sau khi print_block xong thì PHẢI ADD LẠI CÁC DEFAULT FUNCTION VÀO HOOK CỦA BLOCK.
		remove_action('caia_flexible_block_main_post_show', 'caia_flexible_block_main_post_show_new', 10, 2);
		add_action('caia_flexible_block_main_post_show', 'caia_flexible_block_main_post_show', 10, 2);

	} // end new_func
	function caia_flexible_block_main_post_show_new($code, $options, $post_heading)
	{
		// code print the post here		
	}


	Ngoài ra, riêng với Caia_Flexible_Block, có thể truyền trực tiếp 2 hàm hiển thị main_post_show và next_post_show thông qua phương thức print_block. Như vậy sẽ tránh phải remove function cũ, add function mới rồi sau đó phải reset lại 2 action nói trên (cách call này chạy nhanh hơn, và code ngắn hơn), nên đọc code của Caia_Flexible_Block để hiểu sâu hơn, ví dụ mẫu như sau:

	Ex2: 
	add_action('genesis_before_loop', 'new_func' );

	function new_func(){
		$args = array(
			'title'                    => 'Tác dụng của dầu tỏi' , 
			'term'                     => '3', // category_id = 3			
			'num_posts'                => 1, 
			'num_relateds'             => 3, 
			'related_title'            => 'Next Posts:',			
			);
		

		$block = new Caia_Flexible_Block();
		$block->print_block($args, 'caia_flexible_block_main_post_show_new'); // => truyền hàm print main post tại đây luôn.

	} // end new_func

	function caia_flexible_block_main_post_show_new($code, $options, $post_heading)
	{
		// code print the post here			
	}

	Ex3: 
	Ví dụ này tương tự Ex1 bên trên, nhưng dùng 2 hàm save_than_clean_hooks() và restore_hooks của Caia_Flexible_Block

	add_action('genesis_before_loop', 'new_func' );

	function new_func(){
		$args = array(
			'title'                    => 'Tác dụng của dầu tỏi' , 
			'term'                     => '3', // category_id = 3			
			'num_posts'                => 1, 
			'num_relateds'             => 3, 
			'related_title'            => 'Next Posts:',			
			);
		
		$block = new Caia_Flexible_Block();
		$block->save_than_clean_hooks(); // lưu lại và clean các hook hiện hành của Flexible_Block đi để add Hook mới riêng cho Block này.

		// sử dụng hàm mới cho action
		add_action('caia_flexible_block_main_post_show', 'caia_flexible_block_main_post_show_new', 10, 2);
		
		$block->print_block($args);

		// sau khi print_block xong thì retore lại hook của block để ko ảnh hưởng lên các Block cùng loại khác.
		$block->restore_hooks();
		

	} // end new_func
	function caia_flexible_block_main_post_show_new($code, $options, $post_heading)
	{
		// code print the post here		
	}


3. Caia_Code_Block
	Là Block đơn giản, giúp coder có thể dễ dàng thêm 1 Block mới dùng hook vào Block này sử dụng code. Dùng khi Coder muốn thêm tạo thêm 1 block đơn giản (block slide ảnh) mà ko muốn phải viết code class block mới, thì đơn giản hook vào trong block này.
	
	Ex:
	caia_register_block_code('block_code_01'); // dky 1 block code mới
	add_action('caia_code_block_do_content', 'new_hook_block_code_01', 10, 2);	

	function new_hook_block_code_01($code, $title)
	{
		if($code === 'block_code_01'){
			echo 'This is block code 01!';
		}

	}


/**** CAIA Widget *****/
1. CAIA Post List Widget
	Thực chất là phiên bản ghép của CAIA Recents Post và CAIA Featured Post, hỗ trợ đầy đủ Custom Post Type + Taxonomy. Ngoài ra Widget này còn hỗ trợ thêm một số hook cơ bản cho phép tùy biến thành Widget hiện sản phẩm theo bố cục riêng, hoặc biến thành widget jcarousel... 
	Chứa các hook sau (đọc lib/widgets/post-list.widget.php để hiểu hơn):
	- Action: 'caia_post_list_widget_before_main_posts'
	- Action: 'caia_post_list_widget_after_main_posts'
	- Action: 'caia_post_list_widget_do_post'

	Ngoài ra bản nâng cấp này cũng hỗ trợ widget_code, cho phép style riêng cho từng code (tự bổ sung thêm class tương ứng với code của widget), hoặc bố cục riêng cho từng code.

2. CAIA Code Widget
	Là widget đơn giản, sử dụng code để cho phép coder tạo một widget đơn giản riêng bằng cách hook vào widget này thay vì phải viết một widget mới hoàn toàn.
	
	Ex: 
	caia_register_widget_code('widget_code_01'); // dky 1 widget code mới
	add_action('caia_code_widget_do_content', 'new_hook_widget_code_01', 10, 2);	
	function new_hook_widget_code_01($code, $title)
	{
		if($code === 'widget_code_01'){
			echo 'This is widget code 01!';
		}
	}
	
3. CAIA Online Support
	Là widget tạo hỗ trợ trực tuyến bằng yahoo, skype. Có thể sử dụng icon support mặc định của yahoo skype, icon mặc định của CAIA(là img nằm trong thư mục caia\images\supportonline) or các hình ảnh tùy chọn riêng.
	- Dùng ảnh mặc định của yahoo, skype chỉ việc chọn ảnh ở Skype Icon, Yahoo Icon ngay sau tên yahoo.
	- Dùng ảnh mặc định của CAIA chỉ việc tích vào "Use Caia Custom Icon"
	- Dùng ảnh tùy chọn riêng, tích vào "Use Caia Custom Icon" và faste link ảnh vào 4 ô text tương ưng ở dưới.
	
4. Limit login attempts
	- Là setting hỗ trợ hạn chế tỷ lệ cố gắng đăng nhập, bao gồm cả các tập tin cookie bằng cách, cho mỗi IP.
	- Vào setting theo đường dẫn: User -> Limit Login Attempts.

/**** UPDATE CAIA CHILD THEME FROM 1.X -> 2.0 *****/
Vì Caia child theme 2.0 gộp chung 2 widget CAIA Recent Post + CAIA Featured Post => CAIA Post List nên khi nâng cấp lên, 2 widget cũ sẽ biến mất khỏi danh mục widget => website ko hiển thị nữa. Ngoài ra Block Caia_Extra_News_Block cũng bị gộp chung vào CAIA_News_Block nên khi nâng cấp lên thì block nói trên cũng bị mất => VIỆC NÂNG CẤP LÊN CẦN LÀM CẨN THẬN THEO CÁC BƯỚC SAU:
1- Vào cpanel backup DB của web hiện tại xuống (nên backup = cpanel thay vì dùng export của phpmyadmin).
2- Backup thư mục:
	. Thư mục themes (chỉ cần compress thư mục themes).
	. Thư mục plugins (compress thư mục plugins lại, hoặc chỉ cần 2 plugin url-vietnamese, Caia_Featured, simple-login-log là đủ).
3- Chụp hình lại cấu hình của Widget CAIA Recent Post + CAIA Featured Post (dùng FS Capture cũng dc).
4- Chụp hình lại lại cấu hình của CAIA Extra News Block & CAIA News Block trong CAIA Design (nếu có).
5- Xóa bớt một số thành phần:	
	. Uninstall plugin Simple Login Log (từ daskboard để xóa bỏ DB của plugin đó đi).
	. Xóa thư mục lib (themes/caia/lib) + themes/genesis đi.
	. Xóa bỏ 2 plugin Url Vietnamese + CAIA Featured (hoặc GOMM Featured) đi (vì tích hợp sẵn trong theme rồi).	
	. Copy đè thư mục caia + genesis của childtheme 2.0 vào.
6- Nếu là update từ child theme version trước 1.0.5 thì cần copy file custom.css vào cuối file style.css.
7- Nếu là update từ child theme version trước 2.1 thì cần copy phần css của 'Caia widget advertise' ở cuối style-sample.css vào cuối file style.css.
8- Dựa vào cấu hình dc lưu lại của Widget CAIA Recent Post + CAIA News Block, cấu hình cho Widget thay thế là CAIA Post List.
9- Dựa vào cấu hình dc lưu lại của Block News => cấu hình cho Block News mới (cập nhật lại trường Term giống với Category trước đây).
10- Khử bỏ include file của CAIA_Extra_News_Block (và dòng caia_register_block('Caia_Extra_News_Block');) trong theme, sau đó dùng CAIA News Block thay thế.
11- Kiểm tra kĩ lại bố cục web có vấn đề ji ko, có thể cần style lại vài chỗ (do sự thay đổi Extra News Block -> News Block).
12- Xóa cache của supper-cache đi.

CHÚ Ý: việc kiểm tra style của web cần kiểm tra ở trạng thái login, để ko bị xem bản cũ do Super Cache lưu lại.
Trường hợp nâng cấp gặp lỗi ko xử lý dc, thì restore lại bản cũ gồm DB + theme + plugins.

/**** UPDATE CAIA CHILD THEME FROM 2.X -> 3.0 *****/
Child Theme 3.0 cải tiến tăng cường bảo mật, và tính dễ dùng bằng cách disable những chức năng ko dùng tới của Genesis và WP Admin đi, đặt sẵn giá trị ngầm định cho những thành phần ko cần thiết, trong đó có setting Rewrite Rule mặc định, khiến việc nâng cấp CÓ THỂ GÂY THAY ĐỔI CẤU TRÚC URL CỦA WEBSITE, RẤT NGUY HIỂM, bởi vậy khi nâng cấp từ 2.x lên 3.0 cần làm theo các bước sau:
1- Backup DB hiện tại.
2- Backup Theme + Plugin hiện tại bằng cách nén lại.
3- Lấy cấu hình Permalink hiện tại trong quản trị, ex: /%post_id%/%postname%/
4- Nếu cấu hình Permalink hiện tại khác với giá trị ngầm định là /%postname%-%post_id%  thì cần bổ sung thêm hàm sau vào custom/function.php:

// thiet lap lai gia tri permalink cho website nay
remove_action( 'init', 'default_permalinks' );
add_action( 'init', 'my_permalinks' );
function my_permalinks()
{
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure( 'cau hinh permalink hien tai' ); // ex: /%post_id%/%postname%/
}

5- Cài đặt tiếng việt (nếu cần): thay thế thư mục wp-content/languages bởi thư mục languages trong folder của childtheme 3.0
6- Xóa bớt một số plugin thừa nếu có như CAIA Feautures, WP SMTP Mail, Limit Login Attempts, Comment Reply Notification
7- Nâng cấp Wordpress lên version 4.1 từ Dropbox Web Team (Child Theme 3.0 dành cho chuẩn WP từ 4.x trở lên)
8- Cài đặt Wordpress SEO dành riêng cho CAIA:
	. Desactivate plugin Google XML Sitemaps.
	. Xóa plugin Wordpress SEO (SEO by Yoast) đi, thay thế bằng thư mục wordpress-seo có sẵn trong folder childtheme 3.0
	. Vào quản trị, vào CAIA SEO -> Tổng quan -> Restore default setting -> click OK	
	. Kiểm tra tính năng Sitemap có hoạt động không, vào CAIA SEO -> XML Sitemap -> Enable, sau đó kiểm tra bằng cách vào link http://domain.vn/sitemap_index.xml, nếu bị lỗi 404 thì fix theo hướng dẫn sau: 
		. Update lại permalink.
		. Nếu vẫn không được thì fix theo hướng dẫn sau http://kb.yoast.com/article/77-my-sitemap-index-is-giving-a-404-error-what-should-i-do
		. Nếu thành công thì xóa Google XML Sitemaps đi, và nhắn SEOer đăng ký lại Sitemap mới.

9- Xóa cache của WP Super Cache đi.
10- Test kĩ lại sự hoạt động ổn định của Website, đặc biệt là:
	. Permalink có như trước đây hay ko.
	. Style các trang có ổn định hay không.
	. Phần quản trị có thông báo gì đặc biệt không.

11- Zip lại 2 folder plugins và themes, đổi tên theo ngày để phòng trường hợp sau này hacker chèn virus vào thì dễ fix.