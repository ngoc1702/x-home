<?php
/**
 * Template Module
 *
 * @version $Id: tpl_module.php 1095126 2015-02-20 12:59:35Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	defined('ABSPATH') or die("No script kiddies please!");

	class DW_Tpl extends DWModule {
		protected static $info = 'Thiết lập này sẽ thực thi trước lựa chọn về Trang và Chi tiết Bài Viết'; // 'This options takes precedence above other options like Pages and/or Single Posts.';
		protected static $except =  'Ngoại trừ template'; //'Except the templates';
		public static $option = array( 'tpl'	=> 'Templates' );
		protected static $overrule = TRUE;
		protected static $question =  'Hiển thị widget trên tất cả template'; //'Show widget on every template?';
		protected static $type = 'complex';

		public static function admin() {
			parent::admin();

			$tpl = get_page_templates();
			if ( count($tpl) > 0 ) {
				$list = array();
				foreach ( $tpl as $tplname => $tplfile ) {
					$list[basename($tplfile)] = $tplname;
				}
				self::mkGUI(self::$type, self::$option[self::$name], self::$question, self::$info, self::$except, $list);
			}
		}
	}
?>