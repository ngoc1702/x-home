<?php

define('CAIA_RATING_VERSION', '3.0');

/*
* 22/8/19 - 2.1: chỉnh style font cr_hint phu hop voi man 320px
* 21/8/19 - 2.0: chinh lai style ngoi sao width:1em, phu hop voi man 320px
* 10/6/21 : fix ẩn vote sao mobi : dòng 32
* 03/11/22: fix hiện vote sao mobi - 23
*/

$caia_rating = new Caia_Rating();


class Caia_Rating{
	function __construct(){		
		

		// phan ajax server side
		add_action( 'wp_ajax_nopriv_do_vote_star', array($this, 'do_vote_star') );
		add_action( 'wp_ajax_do_vote_star', array($this, 'do_vote_star') );	

		// add_filter( 'caia_rating_show',  array($this, 'is_show_rating'), 10, 2 );
		add_shortcode( 'caia_rating',  array($this, 'shortcode_rating') );
		
	}

	function shortcode_rating($args, $content) {
				
		$post_type = get_post_type();

		if ($post_type) {
			// hien an vote voi 1 so post_type o mobile
			$is_show = apply_filters( 'caia_rating_show', true, $post_type );
		
			if ($is_show){
				$post_id = get_the_ID();

				return $this->gen_do_rating($post_id);
			}			
		}					
	}
	
	function is_show_rating($show, $post_type){		
		global $caia_detected_device;
		if ( isset($caia_detected_device) && ( $caia_detected_device == 'Mobile') ) {
			return $post_type && $post_type !== 'post' && $post_type !== 'page';
		}else{
			return $show;
		}
		
	}

	// function ajax
	function do_vote_star() {		
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) { 			
			$post_id = $_POST['data_id'];

			$voted = $_COOKIE["caia_rating_voted_{$post_id}"];
			if ($voted) die();

			$count = get_post_meta( $post_id, 'caia_rating_count', true );

			if ($count){
				$count = intval($count);
				$point = get_post_meta( $post_id, 'caia_rating_point', true );
				if ($point){
					$point = intval($point);
				}else{
					$point = 4 * $count;
				}

			}else{
				$count = 0;
				$point = 0;
			}

			update_post_meta( $post_id, 'caia_rating_count', $count + 1 );
			update_post_meta( $post_id, 'caia_rating_point', $point + $_POST['point'], '' );

			$html = $this->gen_rating($post_id, true);

			setcookie("caia_rating_voted_{$post_id}", 1, time() + 24 * 3600, '/');  /* expire in 1 day */			

			echo $html;  
		}
		die();
	}


	function gen_do_rating($post_id){		
		$rating_html = $this->gen_rating($post_id, false);

		if (!has_action('wp_footer', array($this, 'add_css'))){
			add_action( 'wp_footer', array($this, 'add_css'), 0 );
			add_action( 'wp_footer', array($this, 'add_js'), 10 );
		}

		return $rating_html;
	}

	function show_rating($post_id){		
		
		echo $this->gen_do_rating($post_id);
	}

	function add_css(){
		global $caia_detected_device;
		$style = '.caia_rating{display:inline-block;font-size:1.3em;text-align:left;width:9.2em}rstar.cr_star{width:1.0em;display:inline-block;color:gray}rstar.cr_star.on,rstar.cr_star.on_by_hover{color:gold}rstar.cr_star.after{color:gold;position:absolute;overflow:hidden}rstar.cr_star:hover{color:gold}rstar.cr_star:hover~rstar{color:gray}rstar.cr_star:hover~rstar.after{display:none}label.cr_hint{margin-left:0.2em;font-size:.60em;color:grey;font-family:sans-serif;}';
		if (isset($caia_detected_device) && $caia_detected_device === 'Mobile'){
			$style .= '@media only screen and (max-width: 500px){span.rating_value,span.rating_split{display:none;} rating.caia_rating{width:7.9em !important}}';
		}
		echo '<style>' . $style . '</style>';
	}

	function add_js(){
		?>
		<script>
		var caia_rating = {"ajax_url":"<?php echo admin_url( 'admin-ajax.php' );?>"};
		jQuery(document).ready(function(){		  		
			jQuery('.caia_rating>rstar.show').hover( function(){
				var on_star = parseInt(jQuery(this).data('value'), 10);
				// console.log(on_star);				
				jQuery(this).parent().children('rstar.cr_star.show').each(function(e){					
					if (e < on_star - 1) {
						// console.log(e);
						jQuery(this).addClass("on_by_hover");
					}	  
				});

			}).mouseout(function(){
				// console.log('out');
				jQuery(this).parent().children('rstar.cr_star.show.on_by_hover').each(function(e){
				  jQuery(this).removeClass('on_by_hover');
				});
			});

			
			jQuery('.caia_rating>rstar.show').click(function(){
				var on_star = parseInt(jQuery(this).data('value'), 10); // The star currently selected
				var parent = jQuery(this).parent();
				var data_id = parseInt(parent.data('id'), 10);
				parent.html('<label class="cr_hint">Cảm ơn bạn đã bình chọn 🎔</label>');			

				console.log(data_id + '_' + on_star);
										       
		        jQuery.ajax({
					url : caia_rating.ajax_url,
					type : 'post',
					data : {
						action : 'do_vote_star',
						data_id : data_id,
						point : on_star,     
					},
					success : function( response ) {						
						if (response){
							jQuery('.caia_rating[data-id=' + data_id + ']').html( response );	
						}else{
							parent.children('label').html('Bình chọn không hợp lệ!');							
						}						
					}
		        });		 		        		    
			});  		  
		});

		</script>
		<?php
	}

	function gen_rating($post_id, $only_content = false){
		
		$count = get_post_meta( $post_id, 'caia_rating_count', true );

		if ($count){
			$count = intval($count);
			$point = get_post_meta( $post_id, 'caia_rating_point', true );
			if ($point) $point = intval($point);

			$rate = round($point*10/$count)/10;
			if ($rate > 5) $rate = 5;
		}else{
			$count = 0;
			$rate = 0;
		}

		// $count = 10;
		// $rate = 4.5;
		$mark = $rate * 2;

		$rate_str = number_format($rate, 1);
		// $count = 120;
		$tit = "{$rate_str} - {$count} đánh giá";
		$msg = apply_filters('caia_rating_info', "<span class='rating_value'>{$rate_str}</span><span class='rating_split'> - </span>{$count} đánh giá", $rate_str, $count);

		if ($only_content){
			$html = '';
		}else{
			if ($count < 10){
				$width = 9.5;
			}else if ($count < 100){
				$width = 10.0;				
			}else{
				$width = 10.5;
			}
			$html = "<rating class='caia_rating' title='{$tit}' data-id='{$post_id}' style='width:{$width}em;'>";	
		}

				
		$is_on = ($rate >= 1) ? ' on' : '';
		$html .= "<rstar class='cr_star show{$is_on}' data-value='1' title='Yếu'>★</rstar>";
		$is_on = ($rate >= 2) ? ' on' : '';
		$html .= "<rstar class='cr_star show{$is_on}' data-value='2' title='Hơi Yếu'>★</rstar>";
		$is_on = ($rate >= 3) ? ' on' : '';
		$html .= "<rstar class='cr_star show{$is_on}' data-value='3' title='Bình thường'>★</rstar>";
		$is_on = ($rate >= 4) ? ' on' : '';
		$html .= "<rstar class='cr_star show{$is_on}' data-value='4' title='Tốt'>★</rstar>";
		$is_on = ($rate >= 5) ? ' on' : '';
		$html .= "<rstar class='cr_star show{$is_on}' data-value='5' title='Rất tốt'>★</rstar>";

		/*Point1 - center: 4-0.968 __ 3-2.068 __ 2-3.168 __ 1-4.268 __ 0-5.368 */
    	/*margin-left: -4.268em;  */
    	/*Point2: 0.83 = 100%, 0.415 = 50%*/
    	/*width: 0.415em; */

    	/*Point1 left: 4-1.098 __ 3-2.198 __ 2-3.298 __ 1-4.398 __ 0-5.498 */

		$head = floor($rate);
		$tail = $rate - $head;
		if ( ($head == 0 & $tail == 0) || ($head >= 5) ){
			$style_after = 'display:none;';
		}else{
			$width = 0.83 * $tail;

			switch ($head) {
				case 0:	
					$margin_left  = -5.0;				
					break;
				case 1:	
					$margin_left  = -4.0;				
					break;
				case 2:	
					$margin_left  = -3.0;				
					break;
				case 3:	
					$margin_left  = -2.0;				
					break;
				case 4:	
					$margin_left  = -1.0;				
					break;				
			}
			$style_after = "margin-left:{$margin_left}em;width:{$width}em;";
		}

		$html .= "<rstar class='cr_star after' style='{$style_after}'>★</rstar>";
		$html .= "<label class='cr_hint'>{$msg}</label>";
		
		if (!$only_content){
			$html .= "</rating>";
		}


		return $html;
	}
}


