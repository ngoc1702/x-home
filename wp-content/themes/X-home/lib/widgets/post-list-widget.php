<?php

class Caia_Post_List_Widget extends WP_Widget{
	protected $defaults;
	function __construct()
	{
		$this->defaults = array(
			'code'					  => '', // widget code
			'title'                   => '',
			'post_type'               => 'post',
			'taxonomy'				  => 'category',
			'term'	               	  => '', // term_id replace 'posts_cat'			
			'auto_detect_category' 	  => 0,			
			'ignore_sticky_posts'     => 0,
			'featured'			      => 0, // featured or not			
			'posts_num'               => 1,
			'posts_offset'            => 0,
			'orderby'                 => '',
			'order'                   => '',
			'show_image'              => 0,
			'image_alignment'         => '',
			'image_size'              => '',
			'show_image_extra'              => 0,
			'image_alignment_extra'         => '',
			'image_size_extra'              => '',
			'show_gravatar'           => 0,
			'gravatar_alignment'      => '',
			'gravatar_size'           => '',
			'show_title'              => 0,
			'show_byline'             => 0,
			'check_meta'             => 0,
			'post_info'               => '[post_date] ' . __( 'bởi', 'caia' ) . ' [post_author_posts_link] [post_comments]',
			'show_byline_extra'             => 0,
			'post_info_extra'               => '[post_date] ' . __( 'bởi', 'caia' ) . ' [post_author_posts_link] [post_comments]',
			'show_content'            => 'content-limit',
			'show_card'            => 'card_h',
			'show_card_title'            => 'card_h',
			'show_card_title_extra'            => 'card_p',
			'content_limit'           => '',
			'more_text'               => __( 'Xem thêm', 'caia' ),
			'extra_num'               => 0, // number of extra post to show
			'extra_title'             => '',
			'name_class'             => '',
			'more_from_category'      => '',
			'link_category'      => '',
			'more_from_category_text' => __( 'Đường dẫn chuyên mục', 'caia' ),
			'support_extra'		      => '',
		);

		$widget_ops = array(
			'classname'   => 'caia-post-list-widget',
			'description' => __( 'Hiển thị danh sách bài viêt', 'caia' ),
		);

		$control_ops = array(
			'id_base' => 'caia-post-list',
			'width'   => 505,
			'height'  => 350,
		);

		$this->WP_Widget( 'caia-post-list', __( 'CAIA - Post List', 'caia' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance )
	{		
		extract( $args );

		/** Merge with defaults */
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		extract( $instance );

		$total_posts = $posts_num + $extra_num;

		if( !$total_posts )
			return;

		if( $auto_detect_category && !is_category() && !is_single() && !is_tag() && !is_tax() )
            return;
        		
		if( $post_type == 'fitwp_question' ){
			$query_args = array(  
				'showposts' 			=> $total_posts,
				'offset' 				=> $posts_offset,                
				'order' 				=> $order,
				'post_type' 			=> $post_type,
				'meta_query' 	=> array(
					array(
						'key'   => 'fitqa_state',
						'value' => 1,
					)
				),		
				'ignore_sticky_posts' 	=> $ignore_sticky_posts ? 1 : 0		
			);	
		}else{
			$query_args = array(                 
				'showposts' 			=> $total_posts,
				'offset' 				=> $posts_offset,                
				'order' 				=> $order,
				'post_type' 			=> $post_type,
				'ignore_sticky_posts' 	=> $ignore_sticky_posts ? 1 : 0
			);			
		}
		
		// xu ly auto detect category bao gom auto detect tax
        if($auto_detect_category) {
            if(is_category() || is_tag() || is_tax() ) {
            	$cur_queried_obj = get_queried_object();
            	$term = $cur_queried_obj->term_id;
            	$taxonomy = $cur_queried_obj->taxonomy;             	               
            } elseif(is_single()) {
            	global $post;
            	$cur_post_id = $post->ID;

            	if($post->post_type === $post_type){

	            	if($taxonomy === 'category'){            		
		            	$term_arr = get_the_category($cur_post_id);
		                $term = isset($term_arr[0]) ? $term_arr[0]->cat_ID : '';	
	            	}else{
	            		// neu ko phai category, la other tax
	            		$term_arr = wp_get_post_terms( $cur_post_id, $taxonomy);

	            		$term = isset($term_arr[0]) ? $term_arr[0]->term_id : '';	
	            	}
            	}else{
            		// neu khac post_type thi return luon
            		return;
            	}
                
            }                        
        }
        
        // tax_query
        if($term && $taxonomy){
			if($taxonomy === 'category'){
				$query_args['cat'] = $term;
			}else if( $taxonomy === 'post_tag'){
				$query_args['tag_id'] = $term;
			}else{
				$query_args['tax_query'] = array( 
										array('taxonomy' => $taxonomy, 
												'field' => 'id', 
												'terms' => $term));
			}
		}

		// featured & orderby
		if($featured){
			$query_args['featured'] = $term ? 'category' : 'all';

			if( $orderby === 'featured_order' ){				
				$query_args['orderby'] = $term ? 'featured_by_cat_order' : 'featured_order';
			}else{
				$query_args['orderby'] = $orderby;
			}
		}else{
			if( $orderby === 'featured_order' ){	
				// khi user chon orderby feature but dont check feautured => orderby date is default
				$query_args['orderby'] = 'date'; // by date default
			}else{
				$query_args['orderby'] = $orderby;
			}
		}
				
		
        // print_r($query_args);

		$featured_posts = new WP_Query( $query_args );

		if(!$featured_posts->have_posts())
		{
			wp_reset_postdata();
			return;
		}
		$still_have_posts = true;			

		// begin show widget after query
		echo $before_widget;

		/** Set up the author bio */
		
		$title_link = '';
		
		if ( ! empty( $title ) ){
			
			if ( $term ){
				$title_link = get_term_link( intval($term), $taxonomy );
				if( $show_card == "card_h" ){
					echo $before_title . '<h2><a href="'.$title_link.'">'. $title .'</a></h2>'. $after_title;
				}else if( $show_card == "card_p" ){
					echo $before_title . '<p><a href="'.$title_link.'">'. $title .'</a></p>'. $after_title;
				}
			}else{
				if( $show_card == "card_h" ){
					echo $before_title . '<h2>'. $title .'</h2>'. $after_title;
				}else if( $show_card == "card_p" ){
					echo $before_title . '<p>'. $title .'</p>'. $after_title;
				}			
			}
	
		}	

		if( !empty($support_extra)){
			echo '<div class="support-extra">' . $support_extra . '</div>';
		}		
		
		if ( ! empty( $name_class ) ){
			echo '<div class="main-posts '.$name_class.'">';
		}else{
			echo '<div class="main-posts">';
		}

		// co the dung de them vao duoi va cuoi main nhung thanh phan html can thiet
		do_action( 'caia_post_list_widget_before_main_posts', $code, $instance );

		$index = 0;

		if ( $posts_num > 0 && $featured_posts->have_posts() ){
			while ( ($still_have_posts = $featured_posts->have_posts()) && $index < $posts_num ){
				$featured_posts->the_post();
				
				do_action( 'caia_post_list_widget_do_post', $code, $instance );
				
				$index ++;
			} // end while
		} // end if main post

		do_action( 'caia_post_list_widget_after_main_posts', $code, $instance );

		echo '</div>'; // div main-posts
		
		/** The EXTRA Posts (list) */
		if ( ! empty( $extra_num ) && $still_have_posts ) {
			if ( ! empty( $extra_title ) )
				echo '<ul><p class="widget-sub-title">' . esc_html( $extra_title ) . '</p></ul>';			

			$listitems = '';						
			while ( $featured_posts->have_posts() && $index < $total_posts) {
				$featured_posts->the_post();

				if ( ! empty( $show_image_extra ) ){	
					if ( ! empty( $show_byline_extra ) && ! empty( $post_info_extra ) ){
						
						if( $show_card_title_extra == "card_h" ){

							if ( ! empty( $link_category ) ) {
								global $post;
								$cat = get_the_category( $post->ID );
								$name_cat = $cat[0]->name;
								$link_cat = get_category_link( $cat[0]->term_id );	
								$cur_post_title_attr = the_title_attribute( array( 'echo' => false, 'post' => $post ) );
                                
								$post_type_obj = get_post_type_object( get_post_type( $post->ID ) );
$post_type_name = $post_type_obj ? $post_type_obj->labels->name : '';


								$listitems .= sprintf( '<li><a href="%s" title="%s" class="%s">%s</a><p class="name-category"><a href="%s">%s</a></p><p class="byline post-info">%s</p><h3><a href="%s" title="%s">%s</a></h3></li>', 
									get_permalink(),
									$cur_post_title_attr,
									esc_attr( $image_alignment_extra ),
									genesis_get_image( array( 
											'format' => 'html', 
											'size' 	 => $image_size_extra, 					
											'attr' => array(							
													'title' => $cur_post_title_attr,
													'alt'   => $cur_post_title_attr,
													)					
											)),
									$link_cat,
									$name_cat,
									do_shortcode( $post_info_extra ),
									get_permalink(), 
									the_title_attribute( 'echo=0' ), 
									get_the_title(),
									 );
							}
						
							else{
								$listitems .= sprintf( '<li><a href="%s" title="%s" class="%s">%s</a><p class="byline post-info">%s</p><h3><a href="%s" title="%s">%s</a></h3></li>', 
									get_permalink(),
									$cur_post_title_attr,
									esc_attr( $image_alignment_extra ),
									genesis_get_image( array( 
											'format' => 'html', 
											'size' 	 => $image_size_extra, 					
											'attr' => array(							
													'title' => $cur_post_title_attr,
													'alt'   => $cur_post_title_attr,
													)					
											)),
									do_shortcode( $post_info_extra ),
									get_permalink(), 
									the_title_attribute( 'echo=0' ), 
									get_the_title(),
									 );
							}
								
						}else{
						$listitems .= sprintf(
    '<li>
        <a href="%s" title="%s" class="%s">%s</a>
        <div class="box-info">
            <p><a href="%s" title="%s">%s</a></p>
            <p class="byline post-info">%s</p>
            <a href="%s" class="read-more">Xem thêm</a>
        </div>
    </li>',
    get_permalink(),
    $cur_post_title_attr,
    esc_attr($image_alignment_extra),
    genesis_get_image(array(
        'format' => 'html',
        'size'   => $image_size_extra,
        'attr'   => array(
            'title' => $cur_post_title_attr,
            'alt'   => $cur_post_title_attr,
        )
    )),
    get_permalink(),
    the_title_attribute('echo=0'),
    get_the_title(),
    do_shortcode($post_info_extra),
    get_permalink()
);
						
						}
						
					}else{
						if( $show_card_title_extra == "card_h" ){
							global $post;
								$cur_post_title_attr = the_title_attribute( array( 'echo' => false, 'post' => $post ) );

							$listitems .= sprintf( '<li><a href="%s" title="%s" class="%s">%s</a><h3><a href="%s" title="%s">%s</a></h3></li>', 
								get_permalink(),
								$cur_post_title_attr,
								esc_attr( $image_alignment_extra ),
								genesis_get_image( array( 
										'format' => 'html', 
										'size' 	 => $image_size_extra, 					
										'attr' => array(							
												'title' => $cur_post_title_attr,
												'alt'   => $cur_post_title_attr,
												)					
										)),
								get_permalink(), 
								the_title_attribute( 'echo=0' ), 
								get_the_title() );								
						}else{
							$listitems .= sprintf( '<li><a href="%s" title="%s" class="%s">%s</a><p><a href="%s" title="%s">%s</a></p></li>', 
								get_permalink(),
								$cur_post_title_attr,
								esc_attr( $image_alignment_extra ),
								genesis_get_image( array( 
										'format' => 'html', 
										'size' 	 => $image_size_extra, 					
										'attr' => array(							
												'title' => $cur_post_title_attr,
												'alt'   => $cur_post_title_attr,
												)					
										)),
								get_permalink(), 
								the_title_attribute( 'echo=0' ), 
								get_the_title() );								
						}						
					}				
				}else{
					if ( ! empty( $show_byline_extra ) && ! empty( $post_info_extra ) ){
						if( $show_card_title_extra == "card_h" ){
							$listitems .= sprintf( '<li><a href="%s" title="%s"><h3>%s</h3></a><p class="byline post-info">%s</p></li>', 
								get_permalink(), 
								the_title_attribute( 'echo=0' ), 
								get_the_title(),
								do_shortcode( $post_info_extra ) );								
						}else{
							$listitems .= sprintf( '<li><a href="%s" title="%s">%s</a><p class="byline post-info">%s</p></li>', 
								get_permalink(), 
								the_title_attribute( 'echo=0' ), 
								get_the_title(),
								do_shortcode( $post_info_extra ) );								
						}							
					}else{
						if( $show_card_title_extra == "card_h" ){
							$listitems .= sprintf( '<li><h3><a href="%s" title="%s">%s</a></h3></li>', 
								get_permalink(), 
								the_title_attribute( 'echo=0' ), 
								get_the_title() );								
						}else{
							$listitems .= sprintf( '<li><a href="%s" title="%s">%s</a></li>', 
								get_permalink(), 
								the_title_attribute( 'echo=0' ), 
								get_the_title() );								
						}
							
					}			
				}
				
				$index ++;
			}

			if ( strlen( $listitems ) > 0 ){
				if ( ! empty( $name_class ) ){
					echo '<ul class="'.$name_class.'">' . $listitems  . '</ul>';
				}else{
					echo '<ul>' . $listitems  . '</ul>';
				}				
			}
				
			
		}

		if ( $term && $taxonomy && ! empty( $more_from_category )){

			if($taxonomy === 'category'){
				$term_name = get_cat_name( $term );
			}else{
				$term_arra = get_term_by('id', $term, $taxonomy, ARRAY_A);
				$term_name = $term_arra['name'];
			}

			$cur_href = esc_url( get_term_link( intval($term), $taxonomy ) );
			$cur_title = esc_attr( $term_name );
			$cur_anchor_text = ( $more_from_category_text );
			echo '<p class="more-from-category"><a href="' . $cur_href . '" title="' . $cur_title . '"><span>' . $cur_anchor_text . '</span></a></p>'; 
			
		}

		echo $after_widget;
		wp_reset_postdata();
	}

	/**
	 * Update a particular instance.
	 *
	 * This function should check that $new_instance is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If "false" is returned, the instance won't be saved/updated.
	 *
	 * @since 1.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form()
	 * @param array $old_instance Old settings for this instance
	 * @return array Settings to save or bool false to cancel saving
	 */
	function update( $new_instance, $old_instance )
	{
		$new_instance['title']     = stripslashes( wp_filter_post_kses( addslashes($new_instance['title']) ) );
		$new_instance['more_text'] = strip_tags( $new_instance['more_text'] );
		$new_instance['post_info'] = wp_kses_post( $new_instance['post_info'] );
		return $new_instance;
	}

	/**
	 * Echo the settings update form.
	 *
	 * @since 1.0
	 *
	 * @param array $instance Current settings
	 */
	function form( $instance )
	{
		/** Merge with defaults */
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		?>
	    <p>
	        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Tiêu đề:', 'caia' ); ?>:</label>
	        <input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
	    </p>
		
	        <div class="genesis-widget-column-box genesis-widget-column-box-top">

		        <p>
			        <?php $post_types = get_post_types( array( 'public' => true ), 'objects' ); ?>
			        <label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Định dạng' ) ?>:</label>
                    <select id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>">
	                	<?php foreach( $post_types as $post_type => $post_type_obj ) : ?>
	                    	<option value="<?php echo $post_type; ?>" <?php selected( $post_type, $instance['post_type'] ); ?>><?php echo $post_type_obj->labels->singular_name; ?></option>
						<?php endforeach; ?>
	                </select>
				    <label>
				    	<?php 
				    	$taxonomies = get_taxonomies( array('public' => true), 'objects');  
				    	$ajax_id = '_ajax_' . $this->id_base . '-' . $this->number; 
				    	_e( 'Dạng chuyên mục', 'caia' );?>:
						<select name="<?php echo $this->get_field_name( 'taxonomy' ); ?>" class="caia-tax-ajax-select <?php echo $ajax_id; ?>">
							<?php foreach ($taxonomies as $name => $value) {?>					
							<option value="<?php echo $name; ?>" <?php selected( $name,  $instance['taxonomy'] )?> ><?php echo $value->labels->name?></option>
							<?php } ?>			
						</select>										        
				    </label>
				    <label class='caia-loading-ajax_<?php echo $ajax_id;?>'>&nbsp;</label>
		        </p>

		        <p>
	                <label for="<?php echo $this->get_field_id( 'term' ); ?>"><?php _e( 'Tên', 'caia' ); ?>:</label>
					<?php
					$categories_args = array(
						'name'            => $this->get_field_name( 'term' ),
						'selected'        => $instance['term'],
						'orderby'         => 'Name',
						'hierarchical'    => 1,
						'show_option_all' => __( 'Tất cả', 'caia' ),
						'hide_empty'      => '0',
						'taxonomy'		  => $instance['taxonomy'],
						'class'			  => 'caia-term-ajax_' . $ajax_id ,	
					);
					wp_dropdown_categories( $categories_args ); ?>
	            </p>
				
	            <p>
	                <label for="<?php echo $this->get_field_id( 'show_card' ); ?>"><?php _e( 'Thẻ tiêu đề', 'caia' ); ?>:</label>
	                <select id="<?php echo $this->get_field_id( 'show_card' ); ?>" name="<?php echo $this->get_field_name( 'show_card' ); ?>">
	                    <option value="card_h" <?php selected( 'card_h' , $instance['show_card'] ); ?>><?php _e( 'Hiển thị thẻ H2', 'caia' ); ?></option>
	                    <option value="card_p" <?php selected( 'card_p' , $instance['show_card'] ); ?>><?php _e( 'Hiển thị thẻ p', 'caia' ); ?></option>
	                </select>
	            </p>

		        <p>
		        	<input type="checkbox" id="<?php echo $this->get_field_id('auto_detect_category'); ?>" name="<?php echo $this->get_field_name('auto_detect_category'); ?>" value="1" <?php checked(1, $instance['auto_detect_category']); ?>/> 
		        	<label for="<?php echo $this->get_field_id('auto_detect_category'); ?>"><?php _e('Bài viết từng chuyên mục', 'caia'); ?></label>
		        	<input type="checkbox" id="<?php echo $this->get_field_id( 'ignore_sticky_posts' ); ?>" name="<?php echo $this->get_field_name( 'ignore_sticky_posts' ); ?>" value="1" <?php checked( 1, $instance['ignore_sticky_posts'] ); ?> />
	                <label for="<?php echo $this->get_field_id( 'ignore_sticky_posts' ); ?>"><?php _e( 'Loại trừ bài nổi bật', 'caia' ); ?></label>
		        	<input type="checkbox" id="<?php echo $this->get_field_id( 'featured' ); ?>" name="<?php echo $this->get_field_name( 'featured' ); ?>" value="1" <?php checked( 1, $instance['featured'] ); ?> />
	                <label for="<?php echo $this->get_field_id( 'featured' ); ?>"><?php _e( 'Bài viết nổi bật', 'caia' ); ?></label>	
		        </p>

				
	        </div>

	    <div class="genesis-widget-column" style="width: 49%; margin-bottom: 15px;">

	        <div class="genesis-widget-column-box">
			
				<p><b><?php _e( 'Nội dung chính', 'caia' ); ?>:</b></p>

	            <p>
	                <label for="<?php echo $this->get_field_id( 'posts_num' ); ?>"><?php _e( 'Số lượng bài viết chính', 'caia' ); ?>:</label>
	                <input type="text" id="<?php echo $this->get_field_id( 'posts_num' ); ?>" name="<?php echo $this->get_field_name( 'posts_num' ); ?>" value="<?php echo esc_attr( $instance['posts_num'] ); ?>" size="2" />
	            </p>

	            <p>
	                <label for="<?php echo $this->get_field_id( 'posts_offset' ); ?>"><?php _e( 'Số bài loại trừ:', 'caia' ); ?>:</label>
	                <input type="text" id="<?php echo $this->get_field_id( 'posts_offset' ); ?>" name="<?php echo $this->get_field_name( 'posts_offset' ); ?>" value="<?php echo esc_attr( $instance['posts_offset'] ); ?>" size="2" />
	            </p>

	            <p>
	                <label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php _e( 'Sắp xếp theo:', 'caia' ); ?>:</label>
	                <select id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>">
	                	<option style="padding-right:10px;" value="featured_order" <?php selected('featured_order', $instance['orderby']); ?>><?php _e('Nổi bật', 'caia'); ?></option>						
	                    <option value="date" <?php selected( 'date', $instance['orderby'] ); ?>><?php _e( 'Ngày tháng', 'caia' ); ?></option>
	                    <option value="title" <?php selected( 'title', $instance['orderby'] ); ?>><?php _e( 'Tiêu đề', 'caia' ); ?></option>
	                    <option value="parent" <?php selected( 'parent', $instance['orderby'] ); ?>><?php _e( 'Liên kết', 'caia' ); ?></option>
	                    <option value="ID" <?php selected( 'ID', $instance['orderby'] ); ?>><?php _e( 'ID', 'caia' ); ?></option>
	                    <option value="comment_count" <?php selected( 'comment_count', $instance['orderby'] ); ?>><?php _e( 'Số bình luận', 'caia' ); ?></option>
	                    <option value="rand" <?php selected( 'rand', $instance['orderby'] ); ?>><?php _e( 'Ngẫu nhiên', 'caia' ); ?></option>
	                </select>
	            </p>

	            <p>
	                <label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'Sắp xếp', 'caia' ); ?>:</label>
	                <select id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>">
	                    <option value="DESC" <?php selected( 'DESC', $instance['order'] ); ?>><?php _e( 'Mới nhất', 'caia' ); ?></option>
	                    <option value="ASC" <?php selected( 'ASC', $instance['order'] ); ?>><?php _e( 'Cũ nhất', 'caia' ); ?></option>
	                </select>
	            </p>
				
	            <p>
	                <input id="<?php echo $this->get_field_id( 'show_image' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_image' ); ?>" value="1" <?php checked( $instance['show_image'] ); ?>/>
	                <label for="<?php echo $this->get_field_id( 'show_image' ); ?>"><?php _e( 'Hiển thị ảnh đại diện', 'caia' ); ?></label>
	            </p>

	            <p>
	                <label for="<?php echo $this->get_field_id( 'image_size' ); ?>"><?php _e( 'kích thước ảnh', 'caia' ); ?>:</label>
	                <select id="<?php echo $this->get_field_id( 'image_size' ); ?>" name="<?php echo $this->get_field_name( 'image_size' ); ?>">
	                    <option value="thumbnail">thumbnail (<?php echo get_option( 'thumbnail_size_w' ); ?>x<?php echo get_option( 'thumbnail_size_h' ); ?>)</option>
						<?php
						$sizes = genesis_get_additional_image_sizes();
						foreach( (array) $sizes as $name => $size )
							echo '<option value="'.esc_attr( $name ).'" '.selected( $name, $instance['image_size'], FALSE ).'>'.esc_html( $name ).' ( '.$size['width'].'x'.$size['height'].' )</option>';
						?>
	                </select>
	            </p>

	            <p>
	                <label for="<?php echo $this->get_field_id( 'image_alignment' ); ?>"><?php _e( 'Căn lề ảnh', 'caia' ); ?>:</label>
	                <select id="<?php echo $this->get_field_id( 'image_alignment' ); ?>" name="<?php echo $this->get_field_name( 'image_alignment' ); ?>">
	                    <option value="alignnone">- <?php _e( 'không căn', 'caia' ); ?> -</option>
	                    <option value="alignleft" <?php selected( 'alignleft', $instance['image_alignment'] ); ?>><?php _e( 'Căn trái', 'caia' ); ?></option>
	                    <option value="alignright" <?php selected( 'alignright', $instance['image_alignment'] ); ?>><?php _e( 'Căn phải', 'caia' ); ?></option>
	                </select>
	            </p>
				
	            <p>
	                <input id="<?php echo $this->get_field_id( 'show_title' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_title' ); ?>" value="1" <?php checked( $instance['show_title'] ); ?>/>
	                <label for="<?php echo $this->get_field_id( 'show_title' ); ?>"><?php _e( 'Hiển thị tiêu đề bài viết', 'caia' ); ?></label>
	            </p>
				
	            <p>
	                <label for="<?php echo $this->get_field_id( 'show_card_title' ); ?>"><?php _e( 'Thẻ tiêu đề', 'caia' ); ?>:</label>
	                <select id="<?php echo $this->get_field_id( 'show_card_title' ); ?>" name="<?php echo $this->get_field_name( 'show_card_title' ); ?>">
	                    <option value="card_h" <?php selected( 'card_h' , $instance['show_card_title'] ); ?>><?php _e( 'Hiển thị thẻ H3', 'caia' ); ?></option>
	                    <option value="card_p" <?php selected( 'card_p' , $instance['show_card_title'] ); ?>><?php _e( 'Hiển thị thẻ p', 'caia' ); ?></option>
	                </select>
	            </p>

	            <p>
	                <input id="<?php echo $this->get_field_id( 'show_byline' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_byline' ); ?>" value="1" <?php checked( $instance['show_byline'] ); ?>/>
	                <label for="<?php echo $this->get_field_id( 'show_byline' ); ?>"><?php _e( 'Hiển thị thông tin bài viết:', 'caia' ); ?></label>
	                <input type="text" id="<?php echo $this->get_field_id( 'post_info' ); ?>" name="<?php echo $this->get_field_name( 'post_info' ); ?>" value="<?php echo esc_attr( $instance['post_info'] ); ?>" class="widefat" />
	            </p>

	            <p>
	                <label for="<?php echo $this->get_field_id( 'show_content' ); ?>"><?php _e( 'Kiểu nội dung', 'caia' ); ?>:</label>
	                <select id="<?php echo $this->get_field_id( 'show_content' ); ?>" name="<?php echo $this->get_field_name( 'show_content' ); ?>">
	                    <option value="content" <?php selected( 'content' , $instance['show_content'] ); ?>><?php _e( 'Hiển thị toàn bộ', 'caia' ); ?></option>
	                    <option value="excerpt" <?php selected( 'excerpt' , $instance['show_content'] ); ?>><?php _e( 'Hiển thị tóm tắt', 'caia' ); ?></option>
	                    <option value="content-limit" <?php selected( 'content-limit' , $instance['show_content'] ); ?>><?php _e( 'Hiển thị giới hạn', 'caia' ); ?></option>
	                    <option value="" <?php selected( '' , $instance['show_content'] ); ?>><?php _e( 'Không hiển thị', 'caia' ); ?></option>
	                </select>
	                <br />
	                <label for="<?php echo $this->get_field_id( 'content_limit' ); ?>"><?php _e( 'Số ký tự', 'caia' ); ?>
	                    <input type="text" id="<?php echo $this->get_field_id( 'image_alignment' ); ?>" name="<?php echo $this->get_field_name( 'content_limit' ); ?>" value="<?php echo esc_attr( intval( $instance['content_limit'] ) ); ?>" size="3" />
						<?php _e( 'ký tự', 'caia' ); ?>
	                </label>
	            </p>

	            <p>
	                <label for="<?php echo $this->get_field_id( 'more_text' ); ?>"><?php _e( 'Đường dẫn bài viết(không bắt buộc)', 'caia' ); ?>:</label>
	                <input type="text" id="<?php echo $this->get_field_id( 'more_text' ); ?>" name="<?php echo $this->get_field_name( 'more_text' ); ?>" value="<?php echo esc_attr( $instance['more_text'] ); ?>" />
	            </p>
				
				<p>
					<input id="<?php echo $this->get_field_id( 'link_category' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'link_category' ); ?>" value="1" <?php checked( $instance['link_category'] ); ?>/>
					<label for="<?php echo $this->get_field_id( 'link_category' ); ?>"><?php _e( 'Hiển thị chuyên mục chứa bài viết', 'caia' ); ?></label>
				</p>
				<p>
					<input type="checkbox" id="<?php echo $this->get_field_id( 'check_meta' ); ?>" name="<?php echo $this->get_field_name( 'check_meta' ); ?>" value="1" <?php checked( 1, $instance['check_meta'] ); ?> />
					<label for="<?php echo $this->get_field_id( 'check_meta' ); ?>"><?php _e( 'Hiển thị nội dung metabox', 'caia' ); ?></label>	
				</p>
	        </div>

	    </div>

	    <div class="genesis-widget-column genesis-widget-column-right" style="width:49%">

	        <div class="genesis-widget-column-box">

	            <p><b><?php _e( 'Nội dung phụ', 'caia' ); ?>:</b></p>

	            <p>
	                <label for="<?php echo $this->get_field_id( 'extra_title' ); ?>"><?php _e( 'Tiêu đề bài viết', 'caia' ); ?>:</label>
	                <input type="text" id="<?php echo $this->get_field_id( 'extra_title' ); ?>" name="<?php echo $this->get_field_name( 'extra_title' ); ?>" value="<?php echo esc_attr( $instance['extra_title'] ); ?>" class="widefat" />
	            </p>

	            <p>
	                <label for="<?php echo $this->get_field_id( 'extra_num' ); ?>"><?php _e( 'Số lượng bài viết:', 'caia' ); ?>:</label>
	                <input type="text" id="<?php echo $this->get_field_id( 'extra_num' ); ?>" name="<?php echo $this->get_field_name( 'extra_num' ); ?>" value="<?php echo esc_attr( $instance['extra_num'] ); ?>" size="2" />
	            </p>
				
	            <p>
	                <label for="<?php echo $this->get_field_id( 'show_card_title_extra' ); ?>"><?php _e( 'Thẻ tiêu đề', 'caia' ); ?>:</label>
	                <select id="<?php echo $this->get_field_id( 'show_card_title_extra' ); ?>" name="<?php echo $this->get_field_name( 'show_card_title_extra' ); ?>">
	                    <option value="card_h" <?php selected( 'card_h' , $instance['show_card_title_extra'] ); ?>><?php _e( 'Hiển thị thẻ H3', 'caia' ); ?></option>
	                    <option value="card_p" <?php selected( 'card_p' , $instance['show_card_title_extra'] ); ?>><?php _e( 'Hiển thị thẻ a', 'caia' ); ?></option>
	                </select>
	            </p>
				
	            <p>
	                <input id="<?php echo $this->get_field_id( 'show_image_extra' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_image_extra' ); ?>" value="1" <?php checked( $instance['show_image_extra'] ); ?>/>
	                <label for="<?php echo $this->get_field_id( 'show_image_extra' ); ?>"><?php _e( 'Hiển thị ảnh đại diện', 'caia' ); ?></label>
	            </p>

	            <p>
	                <label for="<?php echo $this->get_field_id( 'image_size_extra' ); ?>"><?php _e( 'Kích thước ảnh', 'caia' ); ?>:</label>
	                <select id="<?php echo $this->get_field_id( 'image_size_extra' ); ?>" name="<?php echo $this->get_field_name( 'image_size_extra' ); ?>">
	                    <option value="thumbnail">thumbnail (<?php echo get_option( 'thumbnail_size_w' ); ?>x<?php echo get_option( 'thumbnail_size_h' ); ?>)</option>
						<?php
						$sizes = genesis_get_additional_image_sizes();
						foreach( (array) $sizes as $name => $size )
							echo '<option value="'.esc_attr( $name ).'" '.selected( $name, $instance['image_size_extra'], FALSE ).'>'.esc_html( $name ).' ( '.$size['width'].'x'.$size['height'].' )</option>';
						?>
	                </select>
	            </p>

	            <p>
	                <label for="<?php echo $this->get_field_id( 'image_alignment_extra' ); ?>"><?php _e( 'Căn lề ảnh', 'caia' ); ?>:</label>
	                <select id="<?php echo $this->get_field_id( 'image_alignment_extra' ); ?>" name="<?php echo $this->get_field_name( 'image_alignment_extra' ); ?>">
	                    <option value="alignnone">- <?php _e( 'Không căn', 'caia' ); ?> -</option>
	                    <option value="alignleft" <?php selected( 'alignleft', $instance['image_alignment_extra'] ); ?>><?php _e( 'Căn trái', 'caia' ); ?></option>
	                    <option value="alignright" <?php selected( 'alignright', $instance['image_alignment_extra'] ); ?>><?php _e( 'Căn phải', 'caia' ); ?></option>
	                </select>
	            </p>
				
	            <p>
	                <input id="<?php echo $this->get_field_id( 'show_byline_extra' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'show_byline_extra' ); ?>" value="1" <?php checked( $instance['show_byline_extra'] ); ?>/>
	                <label for="<?php echo $this->get_field_id( 'show_byline_extra' ); ?>"><?php _e( 'Hiển thị thông tin bài viết phụ:', 'caia' ); ?></label>
	                <input type="text" id="<?php echo $this->get_field_id( 'post_info_extra' ); ?>" name="<?php echo $this->get_field_name( 'post_info_extra' ); ?>" value="<?php echo esc_attr( $instance['post_info_extra'] ); ?>" class="widefat" />
	            </p>

				


	            <!-- <p>
	                <label for="<?php echo $this->get_field_id( 'show_content' ); ?>"><?php _e( 'Kiểu nội dung', 'caia' ); ?>:</label>
	                <select id="<?php echo $this->get_field_id( 'show_content' ); ?>" name="<?php echo $this->get_field_name( 'show_content' ); ?>">
	                    <option value="content" <?php selected( 'content' , $instance['show_content'] ); ?>><?php _e( 'Hiển thị toàn bộ', 'caia' ); ?></option>
	                    <option value="excerpt" <?php selected( 'excerpt' , $instance['show_content'] ); ?>><?php _e( 'Hiển thị tóm tắt', 'caia' ); ?></option>
	                    <option value="content-limit" <?php selected( 'content-limit' , $instance['show_content'] ); ?>><?php _e( 'Hiển thị giới hạn', 'caia' ); ?></option>
	                    <option value="" <?php selected( '' , $instance['show_content'] ); ?>><?php _e( 'Không hiển thị', 'caia' ); ?></option>
	                </select>
	                <br />
	                <label for="<?php echo $this->get_field_id( 'content_limit' ); ?>"><?php _e( 'Số ký tự', 'caia' ); ?>
	                    <input type="text" id="<?php echo $this->get_field_id( 'image_alignment' ); ?>" name="<?php echo $this->get_field_name( 'content_limit' ); ?>" value="<?php echo esc_attr( intval( $instance['content_limit'] ) ); ?>" size="3" />
						<?php _e( 'ký tự', 'caia' ); ?>
	                </label>
	            </p>

	            <p>
	                <label for="<?php echo $this->get_field_id( 'more_text' ); ?>"><?php _e( 'Đường dẫn bài viết(không bắt buộc)', 'caia' ); ?>:</label>
	                <input type="text" id="<?php echo $this->get_field_id( 'more_text' ); ?>" name="<?php echo $this->get_field_name( 'more_text' ); ?>" value="<?php echo esc_attr( $instance['more_text'] ); ?>" />
	            </p>
				
				<p>
					<input id="<?php echo $this->get_field_id( 'link_category' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'link_category' ); ?>" value="1" <?php checked( $instance['link_category'] ); ?>/>
					<label for="<?php echo $this->get_field_id( 'link_category' ); ?>"><?php _e( 'Hiển thị chuyên mục chứa bài viết', 'caia' ); ?></label>
				</p>
				<p>
					<input type="checkbox" id="<?php echo $this->get_field_id( 'check_meta' ); ?>" name="<?php echo $this->get_field_name( 'check_meta' ); ?>" value="1" <?php checked( 1, $instance['check_meta'] ); ?> />
					<label for="<?php echo $this->get_field_id( 'check_meta' ); ?>"><?php _e( 'Hiển thị nội dung metabox', 'caia' ); ?></label>	
				</p> -->




	        </div>	        


	        <div class="genesis-widget-column-box" style="margin-bottom: 15px;">
			
				<p>
					<input id="<?php echo $this->get_field_id( 'more_from_category' ); ?>" type="checkbox" name="<?php echo $this->get_field_name( 'more_from_category' ); ?>" value="1" <?php checked( $instance['more_from_category'] ); ?>/>
					<label for="<?php echo $this->get_field_id( 'more_from_category' ); ?>"><?php _e( 'Hiển thị đường dẫn chuyên mục', 'caia' ); ?></label>
				</p>

				<p>
					<label for="<?php echo $this->get_field_id( 'more_from_category_text' ); ?>"><?php _e( 'Tên đường dẫn chuyên mục', 'caia' ); ?>:</label>
					<input type="text" id="<?php echo $this->get_field_id( 'more_from_category_text' ); ?>" name="<?php echo $this->get_field_name( 'more_from_category_text' ); ?>" value="<?php echo esc_attr( $instance['more_from_category_text'] ); ?>" class="widefat" />
				</p>
				
	            <p>
	                <label for="<?php echo $this->get_field_id( 'name_class' ); ?>"><?php _e( 'Đặt Class cho khung', 'caia' ); ?>:</label>
	                <input type="text" id="<?php echo $this->get_field_id( 'name_class' ); ?>" name="<?php echo $this->get_field_name( 'name_class' ); ?>" value="<?php echo esc_attr( $instance['name_class'] ); ?>" class="widefat" />
	            </p>
				
	            <p><?php _e( 'Nội dung HTML', 'caia' ); ?>:</p>				
	            <p>	                
	                <textarea id="<?php echo $this->get_field_id( 'support_extra' ); ?>" name="<?php echo $this->get_field_name( 'support_extra' ); ?>" style="width: 98%; height:70px;"><?php echo esc_textarea( $instance['support_extra'] ); ?></textarea>
	            </p>
	        </div>	

	    </div>

		<?php
	}

} // end widget


add_action('caia_post_list_widget_do_post', 'caia_post_list_widget_do_post', 10, 2);
function caia_post_list_widget_do_post($widget_code, $instance){	
	extract($instance);

	echo '<div class="' . implode( ' ', get_post_class() ) . '">';

	$cur_post_title_attr = the_title_attribute( 'echo=0' );
	
	if( !empty($check_meta) ){
		do_action('caia_post_list_widget_before_image_post');
	}

	if ( ! empty( $show_image ) ) {
	printf(
		'<div class="box-img"><a href="%s" title="%s" class="%s">%s</a></div>',
		get_permalink(),
		$cur_post_title_attr,
		esc_attr( $image_alignment ),
		genesis_get_image( array( 
			'format' => 'html', 
			'size'   => $image_size, 					
			'attr' => array(							
				'title' => $cur_post_title_attr,
				'alt'   => $cur_post_title_attr,
			)					
		))
	);
}


	echo '<div class="list-info">';
	// 	if ( ! empty( $categories ) ) {
	// 	foreach ( $categories as $category ) {
	// 		$cat_html .= sprintf(
	// 			'<a href="%s" class="post-category">%s</a> ',
	// 			esc_url( get_category_link( $category->term_id ) ),
	// 			esc_html( $category->name )
	// 		);
	// 	}
	// }

		
	if( !empty($check_meta) ){
		do_action('caia_post_list_widget_after_image_post');
	}

	if ( ! empty( $show_gravatar ) ) {
		echo '<span class="' . esc_attr( $gravatar_alignment ) . '">';
		echo get_avatar( get_the_author_meta( 'ID' ), $gravatar_size );
		echo '</span>';
	}
	
	if ( ! empty( $link_category ) ) {
		do_action('caia_post_list_widget_term_before_title_post');
		echo '<p class="name-category">';
			global $post;
			// $cat = get_the_category( $post->ID );
			// $name_cat = $cat[0]->name;
			// $link_cat = get_category_link( $cat[0]->term_id );	
			if ( 'product' === get_post_type( $post ) ) {
    $product_cats = get_the_terms( $post->ID, 'product_cat' );
    if ( ! empty( $product_cats ) && ! is_wp_error( $product_cats ) ) {
        $name_cat = $product_cats[0]->name;
        $link_cat = get_term_link( $product_cats[0]->term_id, 'product_cat' );
    } else {
        $name_cat = '';
        $link_cat = '';
    }
} else {
    $cat = get_the_category( $post->ID );
    $name_cat = $cat[0]->name;
    $link_cat = get_category_link( $cat[0]->term_id );
}
			echo '<a >'.$name_cat.'</a>';
		echo '</p>';
	}
	
	if( !empty($check_meta) ){
		do_action('caia_post_list_widget_before_title_post');
	}

	if ( ! empty( $show_title ) ){
		if( $show_card_title == "card_h" ){
			printf( '<h3 class="widget-item-title"><a href="%s" title="%s">%s</a></h3>', get_permalink(), $cur_post_title_attr, get_the_title() );
		}else{
			printf( '<p class="widget-item-title"><a href="%s" title="%s">%s</a></p>', get_permalink(), $cur_post_title_attr, get_the_title() );
		}
	}

	if ( ! empty( $show_byline ) && ! empty( $post_info ) )
		printf( '<p class="byline post-info">%s</p>', do_shortcode( $post_info ) );
	
	if ( ! empty( $link_category ) ) {
		do_action('caia_post_list_widget_term_after_title_post');
	}
	
	if( !empty($check_meta) ){
		do_action('caia_post_list_widget_before_content_post');
	}

	// if ( ! empty( $show_content ) ) {
	// 	if ( 'excerpt' === $show_content ){			
	// 		$my_excerpt = get_the_excerpt();
	// 		echo '<span>'.mb_substr($my_excerpt, 0, $content_limit);
	// 		if($content_limit < strlen($my_excerpt)) echo '...</span>'; 
	// 	}elseif ( 'content-limit' === $show_content )
	// 		the_content_limit( (int)$content_limit, esc_html( $more_text ) );
	// 	else
	// 		the_content( esc_html( $more_text ) );
	// }

if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

if ( ! empty( $show_content ) ) {

    echo '<div class="entry-text">';

    if ( 'excerpt' === $show_content ) {
        // Excerpt là text thuần
        $raw   = get_the_excerpt();
        $limit = (int) $content_limit;

        $needs_more = mb_strlen($raw) > $limit;
        $trim       = $limit > 0 ? mb_substr($raw, 0, $limit) : $raw;

        // In phần text đã cắt, giữ xuống dòng
        echo nl2br( esc_html( $trim ) );

        // Nếu bị cắt thì thêm "… + Xem thêm"
        if ( $needs_more && ! empty( $more_text ) ) {
            echo ' <span class="ellipsis">…</span> ';
            echo '<a class="more-link" href="' . esc_url( get_permalink() ) . '" title="' . esc_attr( get_the_title() ) . '">'
                . esc_html( $more_text ) . '</a>';
        }

    } elseif ( 'content-limit' === $show_content ) {
        // Lấy content đã áp filter (shortcode, embeds, v.v.)
        $html      = apply_filters( 'the_content', get_the_content() );
        // Cắt theo ký tự an toàn (bỏ tag để tránh vỡ HTML)
        $text_only = wp_strip_all_tags( $html, false );
        $limit     = (int) $content_limit;

        $needs_more = mb_strlen($text_only) > $limit;
        $trim       = $limit > 0 ? mb_substr( $text_only, 0, $limit ) : $text_only;

        // In phần text đã cắt, giữ xuống dòng
        echo nl2br( esc_html( $trim ) );

        // Nếu bị cắt thì thêm "… + Xem thêm"
        if ( $needs_more && ! empty( $more_text ) ) {
            echo ' <span class="ellipsis">…</span> ';
            echo '<a class="more-link" href="' . esc_url( get_permalink() ) . '" title="' . esc_attr( get_the_title() ) . '">'
                . esc_html( $more_text ) . '</a>';
        }

    } else {
        // Hiển thị toàn bộ content, tôn trọng <!--more--> và $more_text
        the_content( esc_html( $more_text ) );
    }

    echo '</div>';
}


	
	if( !empty($check_meta) ){
		do_action('caia_post_list_widget_after_content_post');
	}

	echo '</div>';

	if($image_alignment !== 'alignnone')echo '<div class="clear"></div>';
	
	echo '</div>';
}