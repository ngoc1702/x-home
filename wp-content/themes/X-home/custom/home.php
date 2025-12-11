<?php

// Thêm nôi dung slider trang chủ
add_action('genesis_after_header','caia_add_content_slider');
function caia_add_content_slider(){

	if( is_active_sidebar( 'content-banner' ) ){
		echo '<div class="content-banner section">';
			dynamic_sidebar( 'Trang chủ - Banner' );
		echo '</div>';
	}

	if( is_active_sidebar( 'content-tieuchi' ) ){
		echo '<div class="content-tieuchi section"><div class="wrap">';
			dynamic_sidebar( 'Trang chủ - Tiêu chí' );
		echo '</div></div>';
	}

	if( is_active_sidebar( 'content-vechungtoi' ) ){
		echo '<div class="content-vechungtoi section"><div class="wrap">';
			dynamic_sidebar( 'Trang chủ - Về chúng tôi' );
		echo '</div></div>';
	}


	if( is_active_sidebar( 'content-tiepnhan' ) ){
		echo '<div  class="content-tiepnhan section"><div class="wrap">';
			dynamic_sidebar( 'Trang chủ - Tiếp nhận' );
		echo '</div></div>';
	}
	
	if( is_active_sidebar( 'content-product' ) ){
		echo '<div  class="content-product section"><div class="wrap">';
			dynamic_sidebar( 'Trang chủ - Sản phẩm' );
		echo '</div></div>';
	}

		if( is_active_sidebar( 'content-camket' ) ){
		echo '<div  class="content-camket section"><div class="wrap">';
			dynamic_sidebar( 'Trang chủ - Cam kết chất lượng' );
		echo '</div></div>';
	}

	if( is_active_sidebar( 'content-news' ) ){
		echo '<div  class="content-news section"><div class="wrap">';
			dynamic_sidebar( 'Trang chủ - Tin tức' );
		echo '</div></div>';
	}

		if( is_active_sidebar( 'content-tieudefeedback' ) ){
		echo '<div  class="content-tieudefeedback section"><div class="wrap">';
			dynamic_sidebar( 'Trang chủ -  Tiêu đề Feedback' );
		echo '</div></div>';
	}

	if( is_active_sidebar( 'content-feedback' ) ){
		echo '<div  class="content-feedback section"><div class="wrap">';
			dynamic_sidebar( 'Trang chủ - Feedback của khách hàng' );
		echo '</div></div>';
	}

}

if (wp_is_mobile() ){

}