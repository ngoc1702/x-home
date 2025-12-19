<?php

// Thêm file jquery
add_action('wp_footer', 'caia_add_file_jquery');
function caia_add_file_jquery(){
?>

<script>

document.querySelectorAll('.circle p').forEach(p => {
  if (!p.textContent.trim()) {
    p.remove();
  }
});


jQuery(document).ready(function($) {

	$('.site-header section:nth-child(3) .widget_text p').click(function() {
			$('.nhantuvan').fadeIn();
	});

	$('.nhantuvan .widget_caldera_forms_widget .widgettitle').click(function() {
			$('.nhantuvan').fadeOut();
    	
	});
}
)


// jQuery(document).ready(function($) {
//   $('.nhanbaogia span').click(function() {
//     $('.nhantuvan').fadeIn();
//     $('.nhantuvan .widget_caldera_forms_widget').fadeIn();
//   });

//   $('.nhantuvan .close-popup').click(function() {
//     $('.nhantuvan').fadeOut();
//   });
// });


jQuery(document).ready(function($) {
  $('.btn_baogia').click(function() {
    $('.nhantuvan').fadeIn();
    $('.nhantuvan .widget_caldera_forms_widget').fadeIn();
  });

  $('.nhantuvan .close-popup').click(function() {
    $('.nhantuvan').fadeOut();
  });
});

jQuery(document).ready(function($) {

	$('.content-info .textwidget p:nth-child(5) ').click(function() {
			$('.nhantuvan').fadeIn();
    $('.nhantuvan .widget_caldera_forms_widget').fadeIn();
	});

	$('.nhantuvan .widget_caldera_forms_widget .widgettitle').click(function() {
			$('.nhantuvan').fadeOut();
    	
	});
}
)

</script>


<script>
jQuery(document).ready(function($) {
  $(".xhome-dual-posts__left-list").slick({
    arrows: false,
    infinite: true,
    dots: true,
    speed: 600,
    autoplay: true,
    autoplaySpeed: 5000,
    pauseOnHover: false,
    pauseOnFocus: false,
    slidesToShow: 1,
    slidesToScroll: 1,
  });
});


	jQuery(document).ready( function($){
$('.slide_sp .slider-for').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  infinite: true,
  arrows: false,
  fade: true,
  asNavFor: '.slide_sp .slider-nav'
});

$('.slide_sp .slider-nav').slick({
  slidesToShow: 4,
  slidesToScroll: 1,
  infinite: true,
  asNavFor: '.slide_sp .slider-for',
  dots: false,
  arrows: false,
  centerMode: false,
  focusOnSelect: true,
  responsive: [
    {
      breakpoint: 768,  
      settings: {
        slidesToShow:3,
        slidesToScroll: 1,
      }
    }
  ]
});

});

jQuery(function ($) {
  var $banner = $(".content-banner");
  if (!$banner.length) return;

  // ===== 1) Setup main wrapper =====
  var $main = $banner.children(".xhome-main-slider");
  if (!$main.length) {
    $main = $('<div class="xhome-main-slider"></div>');
    $banner.prepend($main);
  }

  // ===== 2) Move all media_image into main slider =====
  $banner.children("section.widget.widget_media_image").appendTo($main);

  // ===== 3) Text widget overlay =====
  var $text = $banner.children("section.widget.widget_text").first();
  if ($text.length) $text.addClass("xhome-text-overlay").appendTo($banner);

  // ===== 4) Create thumbs wrap inside banner =====
  var $thumbWrap = $banner.children(".xhome-thumbs-wrap");
  if (!$thumbWrap.length) {
    $thumbWrap = $('<div class="xhome-thumbs-wrap"><div class="xhome-thumbs"></div></div>');
    $banner.append($thumbWrap);
  }
  var $thumbs = $thumbWrap.find(".xhome-thumbs");

  // ===== 5) Unslick clean =====
  if ($main.hasClass("slick-initialized")) $main.slick("unslick");
  if ($thumbs.hasClass("slick-initialized")) $thumbs.slick("unslick");

  // ===== helper: pick 600w from srcset if available =====
  function pickThumbSrc(img) {
    var $img = $(img);
    var srcset = $img.attr("srcset") || "";
    if (srcset) {
      var cand = srcset.split(",").map(function (s) { return s.trim(); });
      var hit = cand.find(function (s) { return /600w/.test(s); });
      if (hit) return hit.split(" ")[0];
      return cand[0].split(" ")[0];
    }
    return $img.prop("currentSrc") || $img.attr("src") || "";
  }

  // ===== 6) Build thumbs items fresh =====
  $thumbs.empty();
  $main.find("section.widget.widget_media_image img").each(function () {
    var t = pickThumbSrc(this);
    if (!t) return;
    $thumbs.append('<div class="xhome-thumb"><img src="' + t + '" alt=""></div>');
  });

  var count = $main.find("section.widget.widget_media_image").length;
  if (count <= 1) return;

  var showThumbs = Math.min(3, count);

  // ===== 7) Init thumbs =====
  $thumbs.slick({
    arrows: false,
    dots: false,
    infinite: false,
    autoplay: false,
    slidesToShow: showThumbs,
    slidesToScroll: 1,
    swipeToSlide: true
  });

  // ===== 8) Init main =====
$main.slick({
  arrows: false,
  dots: false,
  infinite: true,
  speed: 900,            
  autoplay: true,
  autoplaySpeed: 4500,
  pauseOnHover: false,
  pauseOnFocus: false,
  slidesToShow: 1,
  slidesToScroll: 1,
  fade: true,
  cssEase: "ease-in-out", 
  waitForAnimate: false
});


  // ===== 9) CLICK THUMB -> GO MAIN (index theo vị trí thật) =====
$thumbs.off("click.xhome").on("click.xhome", ".slick-slide", function (e) {
  e.preventDefault();

  var idx = $(this).index();
  if (idx < 0) return;
  $main.slick("slickGoTo", idx, false);
  $main.slick("slickPlay");
});


  // (optional) main chạy -> kéo thumbs chạy theo cho đẹp
  $main.on("afterChange", function (e, slick, current) {
    $thumbs.slick("slickGoTo", current);
  });
});


jQuery(function ($) {
  const $s = $(".content-congtrinhthucte .main-posts");

  if ($s.hasClass("slick-initialized")) $s.slick("unslick");

  $s.slick({
    arrows: true,
    dots: true,
    speed: 600,
    autoplay: true,
    autoplaySpeed: 5000,
    pauseOnHover: false,
    pauseOnFocus: false,
    infinite: true,

    centerMode: true,
    centerPadding: "0px",   
    slidesToShow: 3,        
    slidesToScroll: 1,
    focusOnSelect: true,


    responsive: [
      { breakpoint: 992, settings: { slidesToShow: 1, centerPadding: "60px" } },
      { breakpoint: 768, settings: { slidesToShow: 1, centerPadding: "24px", arrows: false } },
    ],
  });

  $(window).on("load resize", function () {
    $s.slick("setPosition");
  });
});





jQuery(document).ready(function($) {
  $(".content-feedback .wrap").slick({
    arrows: true,
    dots: false,
    speed: 600,
    autoplay: true,
    autoplaySpeed: 5000,
    pauseOnHover: false,
    pauseOnFocus: false,
    slidesToShow: 3,
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 768, 
        settings: {
          slidesToShow:1,
        },
      },
    ],
  });
});


jQuery(document).ready(function($) {
  function initSlickForMobile(selector, options) {
    if ($(window).width() < 768) {
      if (!$(selector).hasClass('slick-initialized')) {
        $(selector).slick(options);
      }
    } else {
      if ($(selector).hasClass('slick-initialized')) {
        $(selector).slick('unslick');
      }
    }
  }

  function handleSlickInit() {
    // Cấu hình riêng cho từng class
    initSlickForMobile('.chatluong-wrapper', {
      arrows: false,
      infinite: true,
      dots: true,
      speed: 600,
      autoplay: false,
      autoplaySpeed: 6000,
      pauseOnHover: false,
      pauseOnFocus: false,
      slidesToShow: 1,
      slidesToScroll: 1
    });

    initSlickForMobile('.camket-box', {
      arrows: false,
      infinite: false,
      dots: true,
      speed: 600,
      autoplay: true,
      autoplaySpeed: 4000,
      pauseOnHover: true,
      pauseOnFocus: false,
      slidesToShow: 1.6,
      slidesToScroll: 2
    });
  }

  // Gọi khi tải trang
  handleSlickInit();

  // Gọi lại khi resize cửa sổ
  $(window).on('resize', function() {
    handleSlickInit();
  });
});




</script>



<script>

	document.addEventListener("DOMContentLoaded", function () {
  const counters = document.querySelectorAll(".content-thanhtuu ul li span");

  counters.forEach(counter => {
    const target = parseInt(counter.textContent.replace(/\D/g, "")); // Lấy số từ <em>
    counter.textContent = "0"; // Bắt đầu từ 0

    let count = 0;
    const speed = 30; // ms mỗi lần tăng
    const increment = Math.ceil(target / 100); // tăng từng bước nhỏ

    const updateCount = () => {
      count += increment;
      if (count >= target) {
        counter.textContent = target;
      } else {
        counter.textContent = count;
        setTimeout(updateCount, speed);
      }
    };

    updateCount();
  });
});

jQuery(document).ready(function($) {

	$('.site-header p').click(function() {
			$('.nhantuvan .widget_caldera_forms_widget').show();
	});

	$('.nhantuvan .widget_caldera_forms_widget .widgettitle').click(function() {
			$('.nhantuvan .widget_caldera_forms_widget').hide();
	});


  var youtube = document.querySelectorAll( ".youtube" );
      for (var i = 0; i < youtube.length; i++) {
          // thumbnail image source.
          var source = "https://img.youtube.com/vi/"+ youtube[i].dataset.embed +"/hqdefault.jpg"; 

          // Load the image asynchronously
          var image = new Image();
              image.src = source;
              image.addEventListener( "load", function() {
                  youtube[ i ].appendChild( image );
              }( i ) );

          youtube[i].addEventListener( "click", function() {

          var iframe = document.createElement( "iframe" );
   
              iframe.setAttribute( "frameborder", "0" );
              iframe.setAttribute( "allowfullscreen", "" );
              iframe.setAttribute( "src", "https://www.youtube.com/embed/"+ this.dataset.embed +"?rel=0&showinfo=0&autoplay=1" );
   
              this.innerHTML = "";
              this.appendChild( iframe );
      } );
       
      }

  // câu hỏi

   var widgets = $('.list_ques .question');

			// Ẩn tất cả .textwidget và bỏ class active
			widgets.find('.answer').hide();
			widgets.find('.title').removeClass('active');

			// Mặc định hiện widget đầu tiên
			// widgets.first().find('.title').addClass('active');
			// widgets.first().find('.answer').show();

			// Bắt sự kiện click
			widgets.find('.title').click(function () {
			  var $title = $(this);
			  var $widget = $title.closest('.question');
			  var $textwidget = $widget.find('.answer');
			  var isActive = $title.hasClass('active');

			  // Ẩn tất cả và remove class
			$('.list_ques .question .answer').slideUp();
  	   $('.list_ques .question .title').removeClass('active');

			  // Nếu chưa active thì mở, còn đang mở thì đóng
			  if (!isActive) {
			    $textwidget.slideDown();
			    $title.addClass('active');
			  }
			});


  // quy trình

   var widgets = $('.list_buoc .buoc');

  // Ẩn tất cả .textwidget và bỏ class active
  widgets.find('.noidung').hide();
  widgets.find('.ten').removeClass('active');

  // Mặc định hiện widget đầu tiên
  // widgets.first().find('.ten').addClass('active');
  // widgets.first().find('.noidung').show();

  // Bắt sự kiện click
  widgets.find('.ten').click(function() {
    var $title = $(this);
    var $widget = $title.closest('.buoc');

    // Ẩn tất cả và remove class
    widgets.find('.noidung').slideUp();
    widgets.find('.ten').removeClass('active');

    // Hiện cái được click
    $widget.find('.noidung').slideDown();
    $title.addClass('active');
  });


  //


	$('').slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		infinite: false,
		arrows: false,
		fade: true,
		asNavFor: ''
	});

	$('').slick({
	    slidesToShow: 2,
	    slidesToScroll: 1,
	    infinite: false,
	    asNavFor: '',
	    dots: false,
	    arrows: true,
	    centerMode: false, 
	    focusOnSelect: true
	});

	var nav = $('.nav-primary');
	var head =$('.site-header');
	var click =$('#click-menu');
	var menu = $('#responsive-menu');
	var close = $('#click-menu-close');
	var nhantuvan = $('.nhantuvan');

	$(window).scroll(function () {
		if ($(this).scrollTop() > 100) {
			nav.addClass("f-nav");
			head.addClass("f-head");
			click.addClass("f-click");
			menu.addClass("f-menu");
			close.addClass("f-close");
			nhantuvan.addClass("f-tuvan");

		} else {
			nav.removeClass("f-nav");
			head.removeClass("f-head");
			click.removeClass("f-click");
			menu.removeClass("f-menu");
			close.removeClass("f-close");
			nhantuvan.removeClass("f-tuvan");
		}
		
	});


// đại diện vn

if ($(window).width() > 960) {

	// const slider = document.querySelector('.content-doitac ul');
  // slider.innerHTML += slider.innerHTML; // Clone toàn bộ li

	// $(".content-doitac ul").slick({
	//   arrows: false,
  // infinite: true,
  // dots: false,
  // speed: 15000,             // chậm dần nhưng liên tục
  // cssEase: 'linear',
  // autoplay: true,
  // autoplaySpeed: 0,
  // pauseOnHover: false,
  // pauseOnFocus: false,
  // slidesToShow: 6,
  // slidesToScroll: 1,
  // variableWidth: true,
	// });


}else{

	

	$("").slick({
		arrows: false,
			infinite: true,
			dots: true,
			speed: 600,	
			autoplay: true,
			autoplaySpeed: 5000,	
			pauseOnHover: false,
			pauseOnFocus: false,
			slidesToShow: 1,
			slidesToScroll: 1,
			adaptiveHeight: false,
			variableWidth: false,
			dotsClass: 'custom_paging',
		    customPaging: function (slider, i) {
		        return '<span class="line"></span>';
		    },
	});	


}


$("").slick({
		arrows: true,
		infinite: true,
		dots: false,
		speed: 600,	
		autoplay: false,
		autoplaySpeed: 5000,	
		pauseOnHover: false,
		pauseOnFocus: false,
		slidesToShow: 4,
		slidesToScroll: 1,
		responsive: [
			{
			breakpoint: 961,
				settings: {
					slidesToShow: 4,
				}
			},
			{
			breakpoint: 769,
				settings: {
					slidesToShow: 2,
					slidesToScroll: 2,
					arrows: false,
					dots: true,
					dotsClass: 'custom_paging',
		    customPaging: function (slider, i) {
		        return '<span class="line"></span>';
		    },
				}
			}
		]
	});

	
	$('a[href*=\\#]:not([href=\\#])').click(function() {
		if (location.pathname.replace('/^\//','') == this.pathname.replace('/^\//','') && location.hostname == this.hostname) {
		  var target = $(this.hash);
		  target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
		  if (target.length) {
			$('html,body').animate({
			  scrollTop: target.offset().top-50
			}, 500);
			return false;
		  }
		}
	});	
});
</script>
<script>
jQuery(document).ready( function($){
	$(".content .comment-form #submit").click(function(){
		var comment_content = $(".content .comment-form #comment").val();
		if( !comment_content )
		{
			alert('Bạn chưa nhập nội dung bình luận!');
			return false;
		}else{
			$(".content .comment-form .popup-comment").fadeIn();
			return false;
		}
	});
	$(".content .comment-form .popup-comment .close-popup-comment").click(function(){
		$(".content .comment-form .popup-comment").fadeOut();
	});
	$("main.content #respond input#submit-commnent").click(function(){
		var comment_name = $(".content .comment-form #author").val();
		var comment_email = $(".content .comment-form #email").val();
		var comment_phone = $(".content .comment-form .comment-form-phone #author").val();
		if( !comment_name ){
			alert('Bạn chưa nhập họ và tên!');
			return false;
		}else if( !comment_email ){
			alert('Bạn chưa nhập email!');
			return false;
		}else if( !comment_phone ){
			alert('Bạn chưa nhập số điện thoại!');
			return false;
		}else{
			$(".content #commentform").submit();
			$(".content .comment-form .popup-comment").fadeOut();
		}
	});
});
</script>


<?php
}
