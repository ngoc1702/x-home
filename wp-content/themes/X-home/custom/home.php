<?php

// Thêm nôi dung slider trang chủ
add_action('genesis_after_header', 'caia_add_content_slider');
function caia_add_content_slider()
{

	if (is_active_sidebar('content-banner')) {
		echo '<div class="content-banner section">';
		dynamic_sidebar('Trang chủ - Banner');
		echo '</div>';
	}

	if (is_active_sidebar('content-vechungtoi')) {
		echo '<div class="content-vechungtoi section"><div class="wrap">';
		dynamic_sidebar('Trang chủ - Về chúng tôi');
		echo '</div></div>';
	}


	if (is_active_sidebar('content-dichvu')) {
		echo '<div  class="content-dichvu section"><div class="wrap">';
		dynamic_sidebar('Trang chủ - Dịch vụ');
		echo '</div></div>';
	}

	if (is_active_sidebar('content-congtrinhthucte')) {
		echo '<div  class="content-congtrinhthucte section">';
		dynamic_sidebar('Trang chủ - Công trình thực tế');
		echo '</div>';
	}

	if (is_active_sidebar('content-tieudefeedback')) {
		echo '<div  class="content-tieudefeedback section"><div class="wrap">';
		dynamic_sidebar('Trang chủ -  Tiêu đề Feedback');
		echo '</div></div>';
	}

	if (is_active_sidebar('content-feedback')) {
		echo '<div  class="content-feedback section"><div class="wrap">';
		dynamic_sidebar('Trang chủ - Feedback của khách hàng');
		echo '</div></div>';
	}


	if (is_active_sidebar('content-taisao')) {
		echo '<div  class="content-taisao section"><div class="wrap">';
		dynamic_sidebar('Trang chủ - Tại sao nên chọn chúng tôi');
		echo '</div></div>';
	}

	if (is_active_sidebar('content-news')) {
		echo '<div  class="content-news section"><div class="wrap">';
		dynamic_sidebar('Trang chủ - Câu chuyện kiến trúc');
		echo '</div></div>';
	}

	if (is_active_sidebar('content-dangky')) {
		echo '<div  class="content-dangky section"><div class="wrap">';
		dynamic_sidebar('Trang chủ - Đăng ký tư vấn');
		echo '</div></div>';
	}
}

if (wp_is_mobile()) {
}
