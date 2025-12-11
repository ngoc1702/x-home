<?php
/**
 * Archive Module
 *
 * @version $Id: archive_module.php 1095126 2015-02-20 12:59:35Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	defined('ABSPATH') or die("No script kiddies please!");

	class DW_Archive extends DWModule {
		protected static $info = 'Thiết lập này không bao gồm trang Tác Giả và Chuyên Mục. '; // 'This option does not include Author and Category Pages.';
		public static $option = array( 'archive' => 'Archive Pages' );
		protected static $question =  'Hiển thị widget trên trang archive?'; //'Show widget on archive pages?';
	}
?>