<?php

/* Template Name: Trang blog */

add_filter( 'genesis_site_layout', 'caia_cpt_layout' );
function caia_cpt_layout() {
  return 'full-width-content';
}


// Xóa tiêu đề
//remove_action('genesis_entry_header', 'genesis_do_post_title');

// Xóa social share
add_action('genesis_before_loop', 'remove_caia_rating');
function remove_caia_rating(){
	global $caia_rating;
	$star_pri = has_filter( 'the_content', array($caia_rating, 'add_rating_content_bottom'));
	if ($star_pri !== false){
		remove_filter('the_content', array($caia_rating, 'add_rating_content_bottom'), $star_pri);
	}

	global $caia_social;
	$social_pri = has_filter( 'the_content', array($caia_social, 'add_native_share_button_at_bottom'));
	if ($social_pri !== false){
		remove_filter('the_content', array($caia_social, 'add_native_share_button_at_bottom'), $social_pri);
	}
}


// Xóa post-info và post-meta
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
remove_action( 'genesis_entry_footer', 'genesis_post_meta' );

//Đưa ảnh lên trước tiêu đề
remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
add_action( 'genesis_entry_header', 'genesis_do_post_image', 1 );

// remove_action( 'genesis_after_header', 'genesis_do_breadcrumbs',9);
remove_action( 'genesis_loop', 'genesis_do_loop' );
remove_action('genesis_before_footer','caia_add_content_after_footer',8);

add_action ('genesis_after_header' , 'add_page_banner');
function add_page_banner() {
    global $post;

    $images = rwmb_meta( 'anh_banner', ['size' => 'full'] ); 

    echo '<div class="content-hero section">';
    echo '  <div class="hero-image">';

    if ( ! empty( $images ) ) {
        foreach ( $images as $image ) {
            echo '<img src="' . esc_url( $image['url'] ) . '" alt="' . esc_attr( $image['alt'] ) . '">';
        }
    }

    echo '  </div>';
    echo '<div class="content-breadcrumb">';
    echo '  <h2 class="title">' . get_the_title() . '</h2>';

    echo do_shortcode('[breadcrumb]');

    echo '</div>';
    echo '</div>';
}


add_action('genesis_loop', 'xhome_dual_posts_layout');

function xhome_dual_posts_layout($left_cat = null, $right_cat = null, $count = 4) {

  echo '<div class="content-blog">';
  echo '<h2 class="page-title">' . esc_html(get_the_title(get_queried_object_id())) . '</h2>';

  $count = max(1, intval($count));

  $resolve_cat_id = function ($cat) {
    if (empty($cat)) return 0;
    if (is_numeric($cat)) return (int) $cat;

    $cat  = sanitize_title($cat);
    $term = get_category_by_slug($cat);
    return $term ? (int) $term->term_id : 0;
  };

  // Lấy danh sách category theo thứ tự (bạn có thể đổi orderby)
  $all_cats = get_categories(array(
    'hide_empty' => true,
    'orderby'    => 'name',   // đổi sang 'term_order' nếu bạn có plugin sort term
    'order'      => 'ASC',
  ));

  // Xác định LEFT/RIGHT tự động nếu chưa truyền vào
  $left_cat_id  = $resolve_cat_id($left_cat);
  $right_cat_id = $resolve_cat_id($right_cat);

  if (!$left_cat_id || !$right_cat_id) {

    // Nếu đang ở category archive: ưu tiên category hiện tại làm LEFT
    $current_id = (is_category() && get_queried_object_id()) ? (int) get_queried_object_id() : 0;

    if (!$left_cat_id) {
      $left_cat_id = $current_id ?: (!empty($all_cats) ? (int) $all_cats[0]->term_id : 0);
    }

    if (!$right_cat_id) {
      // Tìm category "kế tiếp" sau LEFT trong danh sách
      $right_cat_id = 0;
      if (!empty($all_cats)) {
        $ids = array_map(fn($t) => (int) $t->term_id, $all_cats);
        $pos = array_search((int) $left_cat_id, $ids, true);

        // nếu không tìm thấy LEFT trong list -> lấy phần tử 2 (nếu có)
        if ($pos === false) {
          $right_cat_id = isset($ids[1]) ? (int) $ids[1] : (int) $left_cat_id;
        } else {
          $next = $pos + 1;
          $right_cat_id = isset($ids[$next]) ? (int) $ids[$next] : (isset($ids[0]) ? (int) $ids[0] : (int) $left_cat_id);
        }
      }
    }
  }

  // Query LEFT/RIGHT
  $left_q = new WP_Query(array(
    'post_type'           => 'post',
    'posts_per_page'      => $count,
    'ignore_sticky_posts' => true,
    'cat'                 => $left_cat_id ?: 0,
  ));

  $right_q = new WP_Query(array(
    'post_type'           => 'post',
    'posts_per_page'      => $count,
    'ignore_sticky_posts' => true,
    'cat'                 => $right_cat_id ?: 0,
  ));
  ?>

  <section class="xhome-dual-posts">
    <div class="xhome-dual-posts__row">

      <!-- LEFT -->
      <div class="xhome-dual-posts__left">
        <?php if ($left_q->have_posts()) : ?>
          <div class="xhome-dual-posts__left-list">
            <?php while ($left_q->have_posts()) : $left_q->the_post(); ?>
              <?php $img = get_the_post_thumbnail_url(get_the_ID(), 'large'); ?>
              <article class="xhome-dual-posts__left-item">
                <a class="xhome-dual-posts__left-link" href="<?php the_permalink(); ?>">
                  <?php if ($img) : ?>
                    <img class="xhome-dual-posts__left-img"
                         src="<?php echo esc_url($img); ?>"
                         alt="<?php the_title_attribute(); ?>"
                         loading="lazy">
                  <?php endif; ?>
                  <h3 class="xhome-dual-posts__left-title"><?php echo esc_html(get_the_title()); ?></h3>
                </a>
              </article>
            <?php endwhile; ?>
          </div>
          <?php wp_reset_postdata(); ?>
        <?php else : ?>
          <div class="xhome-dual-posts__empty">Không có bài ở category bên trái.</div>
        <?php endif; ?>
      </div>

      <!-- RIGHT -->
      <aside class="xhome-dual-posts__right">
        <?php if ($right_q->have_posts()) : ?>
          <div class="xhome-dual-posts__right-list">
            <?php while ($right_q->have_posts()) : $right_q->the_post(); ?>
              <?php $thumb = get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>
              <article class="xhome-dual-posts__right-item">
                <?php if ($thumb) : ?>
                  <a class="xhome-dual-posts__right-thumb" href="<?php the_permalink(); ?>">
                    <img class="xhome-dual-posts__right-img"
                         src="<?php echo esc_url($thumb); ?>"
                         alt="<?php the_title_attribute(); ?>"
                         loading="lazy">
                  </a>
                <?php endif; ?>
                <div class="xhome-dual-posts__right-content">
                  <a class="xhome-dual-posts__right-link" href="<?php the_permalink(); ?>">
                    <h4 class="xhome-dual-posts__right-title"><?php echo esc_html(get_the_title()); ?></h4>
                  </a>
                </div>
              </article>
            <?php endwhile; ?>
          </div>
          <?php wp_reset_postdata(); ?>
        <?php else : ?>
          <div class="xhome-dual-posts__empty">Không có bài ở category bên phải.</div>
        <?php endif; ?>
      </aside>

    </div>
  </section>

  <?php
  echo '</div>';
}


remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action ('genesis_loop' , 'add_page_blog');
function add_page_blog() {

    	if( is_active_sidebar( 'content-posts' ) ){
		echo '<div class="content-posts section"><div class="wrap">';
			dynamic_sidebar( 'Tin tức - Bài viết nổi bật' );
		echo '</div></div>';
	}


    // Lấy tất cả danh mục có bài viết
    $categories = get_categories(array(
        'hide_empty' => true
    ));

    if (empty($categories)) return;

    foreach ($categories as $category) {

        // Query bài viết theo từng danh mục
        $args = array(
            'post_type'      => 'post',
            'posts_per_page' => 6,
            'cat'            => $category->term_id,
            'post_status'    => 'publish'
        );

        $query = new WP_Query($args);

        if ($query->have_posts()) {

            echo '<section class="blog-category">';

                // Tiêu đề danh mục
                echo '<h2 class="category-title">' . esc_html($category->name) . '</h2>';

                echo '<div class="blog-grid">';

                while ($query->have_posts()) {
                    $query->the_post(); ?>

                    <article class="blog-item">
                        <a href="<?php the_permalink(); ?>">
                            <div class="thumb">
                                <?php 
                                if (has_post_thumbnail()) {
                                    the_post_thumbnail('medium');
                                }
                                ?>
                            </div>

                            <h3 class="title"><?php the_title(); ?></h3>
                        </a>
                    </article>

                <?php }

                echo '</div>'; 
            echo '</section>';

            wp_reset_postdata();
        }
    }
}




// Mobile
if (wp_is_mobile() ){

}

genesis();
