<?php
define('CAIA_MENUMOBILE_VERSION', '2.6');

/*
30/08/2021: 
- Bổ sung setting menu mobile trong Caia Setting
- Style lại giao diện mặc định Menu
- Tối ưu lại jquery Menu
08/09/2021: 
- Fix thêm lỗi hiển thị menu
16/05/2022:
- Sửa HTML thẻ click menu lỗi Mobile
- Minify Js
20/7/2022
- Sửa lỗi click mở ra toàn bộ menu cấp 2,3
07/02/2023
- Sửa lại hiển thị nút click menu đẹp và gọn hơn
- Sửa thêm trường hợp lỗi click mở ra toàn bộ menu cấp 2,3
*/




if (! class_exists('ResponsiveMenu')) {

	register_nav_menus(array('mobile-menu' => 'Mobile Menu'));

	class Caia_Menumobile
	{

		function __construct()
		{

			add_action('caia_settings_metaboxes', array($this, 'add_theme_settings_boxes_menumobile'));

			if (wp_is_mobile()) {
				add_action('genesis_after_footer', array($this, 'caia_add_function_menu_mobile_menu'));
				add_action('wp_head', array($this, 'caia_add_style_mobile'));
				add_action('wp_footer', array($this, 'caia_add_js_mobile'));
			}
		}

		function add_theme_settings_boxes_menumobile($pagehook)
		{
			add_meta_box('caia-menumobile', __('Cài Đặt Menu Mobile', 'caia'), array($this, 'options_admin_menumobile'), $this->pagehook, 'main');
		}

		function options_admin_menumobile()
		{
			$cs_field = CAIA_SETTINGS_FIELD;

			$align_menu = caia_get_option('align_menu');
			$color_line = caia_get_option('color_line');
			$top_click = caia_get_option('top_click');
			$top_menu = caia_get_option('top_menu');
			$background_menu = caia_get_option('background_menu');
			$color_menu = caia_get_option('color_menu');

			echo '<table class="form-table">';

			echo '<tr>';
			echo '<th>Vị trí nút Click Menu</th>';
			echo '<td>';
			echo "<input type='radio' name='{$cs_field}[align_menu]' value='left' " . checked('left', $align_menu, false) . '> Bên trái &nbsp;&nbsp;&nbsp;&nbsp;';
			echo "<input type='radio' name='{$cs_field}[align_menu]' value='right' " . checked('right', $align_menu, false) . '> Bên phải';
			echo '</td>';
			echo '</tr>';

			echo '<tr>';
			echo '<th>Màu nút Click Menu</th>';
			echo '<td>';
			echo "<input name='{$cs_field}[color_line]' type='text' value='{$color_line}' size='60'>";
			echo '<p class="description">Ví dụ: #333, Mặc định: #fff.</p>';
			echo '</td>';
			echo '</tr>';

			echo '<tr>';
			echo '<th>Khoảng cách Click Menu tới đầu trang</th>';
			echo '<td>';
			echo "<input name='{$cs_field}[top_click]' type='text' value='{$top_click}' size='60'>";
			echo '<p class="description">Ví dụ: 150px, Mặc định: 0px.</p>';
			echo '</td>';
			echo '</tr>';

			echo '<tr>';
			echo '<th>Màu nền Menu</th>';
			echo '<td>';
			echo "<input name='{$cs_field}[background_menu]' type='text' value='{$background_menu}' size='60'>";
			echo '<p class="description">Ví dụ: #365899, Mặc định: #333.</p>';
			echo '</td>';
			echo '</tr>';

			echo '<tr>';
			echo '<th>Màu chữ Menu</th>';
			echo '<td>';
			echo "<input name='{$cs_field}[color_menu]' type='text' value='{$color_menu}' size='60'>";
			echo '<p class="description">Ví dụ: #333, Mặc định: #fff.</p>';
			echo '</td>';
			echo '</tr>';

			echo '<tr>';
			echo '<th>Khoảng cách Menu tới đầu trang</th>';
			echo '<td>';
			echo "<input name='{$cs_field}[top_menu]' type='text' value='{$top_menu}' size='60'>";
			echo '<p class="description">Ví dụ: 150px, Mặc định: 0px.</p>';
			echo '</td>';
			echo '</tr>';

			echo '</table>';
		}

		function caia_add_function_menu_mobile_menu()
		{
			if (has_nav_menu('mobile-menu')) {
				echo '<div id="responsive-menu">';
				echo '<div class="logomobile"></div>';
				do_action('caia_before_mobile-menu');
				wp_nav_menu(array(
					'theme_location' => 'mobile-menu',
					'container' => 'div',
					'container_class' => 'mobile-menu'
				));
				do_action('caia_after_mobile-menu');
				echo '</div>';
				echo '<div id="click-menu" class="click-menu">
				
					<div class="line line1"></div>
					<div class="line line2"></div>
					<div class="line line3"></div>
				</div>';
			}
		}

		function caia_add_style_mobile()
		{
			$align_menu = (empty(caia_get_option('align_menu'))) ? 'right' : caia_get_option('align_menu');
			$top_click = (empty(caia_get_option('top_click'))) ? '0px' : caia_get_option('top_click');
			$top_menu = (empty(caia_get_option('top_menu'))) ? '0px' : caia_get_option('top_menu');
			$background_menu = (empty(caia_get_option('background_menu'))) ? '#333' : caia_get_option('background_menu');
			$color_menu = (empty(caia_get_option('color_menu'))) ? '#fff' : caia_get_option('color_menu');
			$color_line = (empty(caia_get_option('color_line'))) ? '#fff' : caia_get_option('color_line');
?>
			<style>
				.click-menu {
					text-align: center;
					position: absolute;
					<?php echo $align_menu; ?>: 5%;
					top: <?php echo $top_click; ?>;
					z-index: 5000;
				}

				.logged-in .click-menu {
					top: calc(<?php echo $top_click; ?> + 46px);
				}

				.click-menu .line {
					height: 3px;
					margin-bottom: 6px;
					background: <?php echo $color_line; ?>;
					width: 30px;
					transition: 0.4s;
				}

				.click-menu.change {
					padding-top: 3px;
				}

				.click-menu.change .line1 {
					-webkit-transform: rotate(-45deg) translate(-9px, 6px);
					transform: rotate(-45deg) translate(-5px, 4px);
				}

				.click-menu.change .line2 {
					opacity: 0;
				}

				.click-menu.change .line3 {
					-webkit-transform: rotate(45deg) translate(-8px, -8px);
					transform: rotate(45deg) translate(-8px, -8px);
				}

				#responsive-menu {
					<?php if ($top_menu == '0px') { ?>position: fixed;
					height: 100%;
					width: 85%;
					<?php } else { ?>position: absolute;
					height: calc(100% - <?php echo $top_menu; ?>);
					width: 100%;
					<?php } ?>top: <?php echo $top_menu; ?>;
					left: auto;
					right: 0;
					background: <?php echo $background_menu; ?>;
					z-index: 999999;
					display: none;
					box-shadow: 0px 4px 10px #d6d6d6;
				}

				.logged-in #responsive-menu {
					<?php if ($top_menu == '0px') { ?>top: 0;
					height: 100%;
					<?php } else { ?>top: calc(<?php echo $top_menu; ?> + 46px);
					height: calc(100% + 46px - <?php echo $top_menu; ?>);
					<?php } ?>
				}

		

				.active-menu {
					color: <?php echo $color_menu; ?>;
					position: absolute;
					right: 10px;
					top: 50%;
					transform: translateY(-50%);
					z-index: 10;
					cursor: pointer;
					font-size: 14px;
				}

				.active-menu.close {
					display: none;
				}

				#responsive-menu ul li > ul > li:last-child,
#responsive-menu ul li > ul > li:last-of-type {
  border-bottom: none !important;
      padding: 10px 30px 0px 15px !important;
}
/* ===== FORCE STACKED, FULL-WIDTH SUBMENUS ON MOBILE ===== */
.mobile-menu,
.mobile-menu ul,
.mobile-menu li {
  width: 100%;
  box-sizing: border-box;
  list-style: none;
  margin: 0;
  padding: 0;
}

/* Submenu mặc định ẩn */
.mobile-menu ul.sub-menu {
  display: none;
  position: static !important;
  width: 100% !important;
  margin: 0;
  padding: 0;
  box-shadow: none !important;
  border: 0 !important;
  background: none;
}

/* Khi mở class "open" thì hiển thị submenu */
.mobile-menu li.open > ul.sub-menu {
  display: block;
}

/* Thụt lề submenu theo cấp */
.mobile-menu ul.sub-menu > li            { padding-left: 20px; }
.mobile-menu ul.sub-menu .sub-menu > li  { padding-left: 35px; }
.mobile-menu ul.sub-menu .sub-menu .sub-menu > li { padding-left: 50px; }

/* Nút toggle */
.mobile-menu li {
  position: relative;
}
.mobile-menu .active-menu {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  font-size: 14px;
  user-select: none;
}

			</style>
		<?php
		}

		function caia_add_js_mobile()
		{
		?>
			<script>
				jQuery(document).ready(function($) {

					$("#click-menu").click(function() {
						$(this).toggleClass("change");
						$("#responsive-menu").toggle();
					});


					$("#responsive-menu li.menu-item-has-children").each(function() {
						if (!$(this).find("> .active-menu").length) {
							$(this).prepend(
								'<span class="active-menu open">▼</span><span class="active-menu close">▲</span>'
							);
						}
					});


					$(document).on("click", ".active-menu.open", function(e) {
						e.stopPropagation();
						var parentLi = $(this).closest("li");
						$(this).hide();
						parentLi.children(".active-menu.close").show();
						parentLi.addClass("open");
						parentLi.children("ul.sub-menu").stop(true, true).slideDown();
					});


					$(document).on("click", ".active-menu.close", function(e) {
						e.stopPropagation();
						var parentLi = $(this).closest("li");
						$(this).hide();
						parentLi.children(".active-menu.open").show();
						parentLi.removeClass("open");
						parentLi.children("ul.sub-menu").stop(true, true).slideUp();
					});
				});
			</script>

<script>
jQuery(document).ready(function($) {
  var $menuRoot = $(".mobile-menu");

  // Thêm nút toggle cho tất cả li có submenu
  $menuRoot.find("li.menu-item-has-children").each(function() {
    if (!$(this).find("> .active-menu").length) {
      $(this).prepend(
        '<span class="active-menu toggle">▼</span>'
      );
    }
  });

  // Toggle mở/đóng submenu
  $(document).on("click", ".mobile-menu .active-menu.toggle", function(e) {
    e.preventDefault();
    e.stopPropagation();
    var $li = $(this).closest("li");

    if ($li.hasClass("open")) {
      // Đóng
      $li.removeClass("open");
      $li.children("ul.sub-menu").stop(true, true).slideUp();
      $(this).text("▼"); // icon đóng
    } else {
      // Mở
      $li.addClass("open");
      $li.children("ul.sub-menu").stop(true, true).slideDown();
      $(this).text("▲"); // icon mở
    }
  });
});
</script>

<?php
		}
	}
	$caia_menumobile = new Caia_Menumobile();
}
