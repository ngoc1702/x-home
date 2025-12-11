<?php
/**
 * Front Page Module
 *
 * @version $Id: frontpage_module.php 1095126 2015-02-20 12:59:35Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	defined('ABSPATH') or die("No script kiddies please!");

	class DW_Front_page extends DWModule {
		protected static $except = 'Ngoại trừ:';
		protected static $info = 'Thiết lập này chỉ hiệu lực nếu bạn thiết lập trang chủ hiển thị danh sách bài viết mới nhất (See Settings &gt; Reading).<br />Khi trang tĩnh được thiết lập, bạn có thể sử dụng cấu hình cho trang tĩnh ở bên dưới.';
		public static $option = array( 'front-page' => 'Front Page' );
		protected static $question = 'Hiển thị widget trên trang chủ?';
		protected static $type = 'complex';

		public static function admin() {
			parent::admin();

			$list = array( 1 => __('First page') );

			if ( get_option('show_on_front') == 'page' ) {
				self::$option = array( 'front-page' => 'Posts Page' );
				self::$question = 'Show widget on the posts page?';
			}
			self::mkGUI(self::$type, self::$option[self::$name], self::$question, self::$info, self::$except, $list);
		}
	}
?>