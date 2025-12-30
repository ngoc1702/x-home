<?php

// Thêm file jquery
add_action('wp_footer', 'caia_add_file_jquery');
function caia_add_file_jquery()
{
?>

  <script>
    jQuery(document).ready(function($) {

      $('.site-header section:nth-child(3) .widget_text p').click(function() {
        $('.nhantuvan').fadeIn();
      });

      $('.nhantuvan .widget_caldera_forms_widget .widgettitle').click(function() {
        $('.nhantuvan').fadeOut();

      });
    })



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
    })
  </script>

  <script>
    jQuery(function($) {
      function parseNumber(text) {
        const m = (text || "").replace(/\s+/g, "").match(/\d+/);
        return m ? Number(m[0]) : null;
      }

      function formatInt(n) {
        return Math.round(n).toString(); // số nguyên, không dấu
      }

      function animateEm($em, duration = 4000) {
        if ($em.data("counted")) return;

        const original = $em.text();
        const target = parseNumber(original);
        if (target === null) return;

        const hasPlus = /\+/.test(original);
        const startTime = performance.now();
        $em.data("counted", true);

        function tick(now) {
          const t = Math.min(1, (now - startTime) / duration);
          const eased = 1 - Math.pow(1 - t, 3);
          const value = Math.round(target * eased);

          $em.text(formatInt(value) + (hasPlus ? " +" : ""));

          if (t < 1) requestAnimationFrame(tick);
        }
        requestAnimationFrame(tick);
      }

      const $ems = $("ul li em");

      if ("IntersectionObserver" in window) {
        const io = new IntersectionObserver((entries) => {
          entries.forEach((e) => {
            if (e.isIntersecting) animateEm($(e.target));
          });
        }, {
          threshold: 0.3
        });

        $ems.each(function() {
          io.observe(this);
        });
      } else {
        $ems.each(function() {
          animateEm($(this));
        });
      }
    });
  </script>


<script>
document.addEventListener("DOMContentLoaded", () => {
  const cards = document.querySelectorAll('.card-item');
  const tabs  = document.querySelectorAll('.giaithuong-tab');

  let current = 0;
  let timer = null;
  const delay = 6000;

  function activate(index){
    if(index === current) return;

    cards[current].classList.remove('active');
    tabs[current].classList.remove('tab-active');

    cards[index].classList.add('active');
    tabs[index].classList.add('tab-active');

    current = index;
  }

  function autoPlay(){
    timer = setInterval(() => {
      activate((current + 1) % cards.length);
    }, delay);
  }

  function resetAuto(){
    clearInterval(timer);
    autoPlay();
  }

  // Init
  cards[0].classList.add('active');
  tabs[0].classList.add('tab-active');
  autoPlay();

  tabs.forEach((tab,i)=>{
    tab.addEventListener('click',()=>{
      activate(i);
      resetAuto();
    });
  });
});
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


    jQuery(document).ready(function($) {
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
        responsive: [{
          breakpoint: 768,
          settings: {
            slidesToShow: 3,
            slidesToScroll: 1,
          }
        }]
      });

    });

    jQuery(function($) {
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
          var cand = srcset.split(",").map(function(s) {
            return s.trim();
          });
          var hit = cand.find(function(s) {
            return /600w/.test(s);
          });
          if (hit) return hit.split(" ")[0];
          return cand[0].split(" ")[0];
        }
        return $img.prop("currentSrc") || $img.attr("src") || "";
      }

      // ===== 6) Build thumbs items fresh =====
      $thumbs.empty();
      $main.find("section.widget.widget_media_image img").each(function() {
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
      $thumbs.off("click.xhome").on("click.xhome", ".slick-slide", function(e) {
        e.preventDefault();

        var idx = $(this).index();
        if (idx < 0) return;
        $main.slick("slickGoTo", idx, false);
        $main.slick("slickPlay");
      });


      // (optional) main chạy -> kéo thumbs chạy theo cho đẹp
      $main.on("afterChange", function(e, slick, current) {
        $thumbs.slick("slickGoTo", current);
      });
    });


    jQuery(function($) {
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


        responsive: [{
            breakpoint: 992,
            settings: {
              slidesToShow: 1,
              centerPadding: "60px"
            }
          },
          {
            breakpoint: 768,
            settings: {
              slidesToShow: 1,
              centerPadding: "24px",
              arrows: false
            }
          },
        ],
      });

      $(window).on("load resize", function() {
        $s.slick("setPosition");
      });
    });


    jQuery(function($) {
      const $s = $(".content-doingu .doingu-wrapper");

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


        responsive: [{
            breakpoint: 992,
            settings: {
              slidesToShow: 1,
              centerPadding: "60px"
            }
          },
          {
            breakpoint: 768,
            settings: {
              slidesToShow: 1,
              centerPadding: "24px",
              arrows: false
            }
          },
        ],
      });

      $(window).on("load resize", function() {
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
        slidesToShow: 1,
        slidesToScroll: 1,
        responsive: [{
          breakpoint: 768,
          settings: {
            slidesToShow: 1,
          },
        }, ],
      });
    });

     jQuery(document).ready(function($) {
      $(".content-doitac .doitac-wrapper").slick({
        arrows: false,
        dots: true,
        speed: 600,
        autoplay: true,
        autoplaySpeed: 5000,
        pauseOnHover: false,
        pauseOnFocus: false,
        slidesToShow: 5,
        slidesToScroll: 1,
        responsive: [{
          breakpoint: 768,
          settings: {
            slidesToShow: 1,
          },
        }, ],
      });
    });


    
    jQuery(document).ready(function($) {
      $(".adsdigi-catbar__grid").slick({
        arrows: true,
        dots: true,
        speed: 600,
        autoplay: true,
        autoplaySpeed: 5000,
        pauseOnHover: false,
        pauseOnFocus: false,
        slidesToShow: 5,
        slidesToScroll: 1,
        responsive: [{
          breakpoint: 768,
          settings: {
            slidesToShow: 1,
          },
        }, ],
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
    document.addEventListener("DOMContentLoaded", function() {
      const counters = document.querySelectorAll(".content-thanhtuu ul li span");

      counters.forEach(counter => {
        const target = parseInt(counter.textContent.replace(/\D/g, "")); 
        counter.textContent = "0"; 

        let count = 0;
        const speed = 30; 
        const increment = Math.ceil(target / 100); 

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


      var youtube = document.querySelectorAll(".youtube");
      for (var i = 0; i < youtube.length; i++) {
        // thumbnail image source.
        var source = "https://img.youtube.com/vi/" + youtube[i].dataset.embed + "/hqdefault.jpg";

        // Load the image asynchronously
        var image = new Image();
        image.src = source;
        image.addEventListener("load", function() {
          youtube[i].appendChild(image);
        }(i));

        youtube[i].addEventListener("click", function() {

          var iframe = document.createElement("iframe");

          iframe.setAttribute("frameborder", "0");
          iframe.setAttribute("allowfullscreen", "");
          iframe.setAttribute("src", "https://www.youtube.com/embed/" + this.dataset.embed + "?rel=0&showinfo=0&autoplay=1");

          this.innerHTML = "";
          this.appendChild(iframe);
        });

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
      widgets.find('.title').click(function() {
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
      var head = $('.site-header');
      var click = $('#click-menu');
      var menu = $('#responsive-menu');
      var close = $('#click-menu-close');
      var nhantuvan = $('.nhantuvan');

      $(window).scroll(function() {
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


      } else {



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
          customPaging: function(slider, i) {
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
        responsive: [{
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
              customPaging: function(slider, i) {
                return '<span class="line"></span>';
              },
            }
          }
        ]
      });


      $('a[href*=\\#]:not([href=\\#])').click(function() {
        if (location.pathname.replace('/^\//', '') == this.pathname.replace('/^\//', '') && location.hostname == this.hostname) {
          var target = $(this.hash);
          target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
          if (target.length) {
            $('html,body').animate({
              scrollTop: target.offset().top - 50
            }, 500);
            return false;
          }
        }
      });
    });
  </script>
  <script>
    jQuery(document).ready(function($) {
      $(".content .comment-form #submit").click(function() {
        var comment_content = $(".content .comment-form #comment").val();
        if (!comment_content) {
          alert('Bạn chưa nhập nội dung bình luận!');
          return false;
        } else {
          $(".content .comment-form .popup-comment").fadeIn();
          return false;
        }
      });
      $(".content .comment-form .popup-comment .close-popup-comment").click(function() {
        $(".content .comment-form .popup-comment").fadeOut();
      });
      $("main.content #respond input#submit-commnent").click(function() {
        var comment_name = $(".content .comment-form #author").val();
        var comment_email = $(".content .comment-form #email").val();
        var comment_phone = $(".content .comment-form .comment-form-phone #author").val();
        if (!comment_name) {
          alert('Bạn chưa nhập họ và tên!');
          return false;
        } else if (!comment_email) {
          alert('Bạn chưa nhập email!');
          return false;
        } else if (!comment_phone) {
          alert('Bạn chưa nhập số điện thoại!');
          return false;
        } else {
          $(".content #commentform").submit();
          $(".content .comment-form .popup-comment").fadeOut();
        }
      });
    });
  </script>

<script>
jQuery(function ($) {
  const $form = $('form.variations_form');
  if (!$form.length) return;

  function setAttrByKey(attrKey, attrVal){
    // attrKey: attribute_pa_mau-sac
    const $select = $form.find(`select[name="${attrKey}"]`);
    if ($select.length){
      $select.val(attrVal).trigger('change');
    } else {
      $form.trigger('check_variations');
    }
  }

  function setMainImage(src, srcset, sizes){
    const $img = $('.custom-main-img').first();
    if(!$img.length) return;
    if(src) $img.attr('src', src);
    if(srcset !== undefined) $img.attr('srcset', srcset || '');
    if(sizes !== undefined) $img.attr('sizes', sizes || '');
  }

  // Click thumb ảnh
  $(document).on('click', '.custom-variation-thumb', function(){
    const $btn = $(this);
    $('.custom-variation-thumb').removeClass('is-active');
    $btn.addClass('is-active');

    // đổi ảnh ngay
    const $img = $btn.find('img').first();
    if($img.length){
      setMainImage($img.attr('src'));
    }

    // set variation attribute -> Woo tự update giá
    setAttrByKey($btn.data('attr-key'), $btn.data('attr-val'));
  });

  // Khi Woo found variation -> sync ảnh + active thumb + giá
  $form.on('found_variation', function (e, variation) {
    if (variation && variation.image && variation.image.src) {
      setMainImage(variation.image.src, variation.image.srcset, variation.image.sizes);
    }
    if (variation && variation.image_id) {
      $('.custom-variation-thumb').removeClass('is-active');
      $(`.custom-variation-thumb[data-image-id="${variation.image_id}"]`).addClass('is-active');
    }
    if (variation && variation.price_html) {
      $('.custom-variation-price').html(variation.price_html);
      $('.custom-base-price').hide();
    }
  });

  $form.on('reset_data hide_variation', function(){
    $('.custom-variation-price').empty();
    $('.custom-base-price').show();
  });
});


</script>


<script>
  jQuery(function($){
  const el = document.querySelector('.adsdigi-pdp__thumbs');
  if(!el) return;

  // Nếu còn slick thì tắt để khỏi xung đột
  if (window.jQuery && $(el).hasClass('slick-initialized')) {
    $(el).slick('unslick');
  }

  let isDown = false;
  let startX = 0;
  let startScrollLeft = 0;

  // velocity cho inertia
  let vx = 0;
  let lastX = 0;
  let lastT = 0;
  let raf = null;

  // chỉnh độ mượt
  const DRAG_SPEED = 1.0;   // kéo nhanh/chậm
  const FRICTION   = 0.92;  // 0.90-0.96 (cao = trôi lâu, mượt hơn)
  const STOP_V     = 0.08;  // ngưỡng dừng

  function animate(){
    el.scrollLeft += vx;
    vx *= FRICTION;

    if (Math.abs(vx) > STOP_V) {
      raf = requestAnimationFrame(animate);
    } else {
      vx = 0;
      raf = null;
    }
  }

  function onDown(e){
    isDown = true;
    el.classList.add('is-dragging');

    // stop inertia đang chạy
    if (raf) { cancelAnimationFrame(raf); raf = null; vx = 0; }

    startX = (e.touches ? e.touches[0].pageX : e.pageX);
    startScrollLeft = el.scrollLeft;

    lastX = startX;
    lastT = performance.now();
  }

  function onMove(e){
    if(!isDown) return;

    const x = (e.touches ? e.touches[0].pageX : e.pageX);
    const dx = x - startX;

    // kéo content
    el.scrollLeft = startScrollLeft - dx * DRAG_SPEED;

    // tính velocity
    const now = performance.now();
    const dt = Math.max(16, now - lastT);
    const vxNow = (lastX - x) / dt * 16; // chuẩn hoá theo ~60fps

    vx = vxNow;
    lastX = x;
    lastT = now;

    // chặn việc “kéo” gây select text / drag ảnh
    e.preventDefault?.();
  }

  function onUp(){
    if(!isDown) return;
    isDown = false;
    el.classList.remove('is-dragging');

    // inertia
    if (Math.abs(vx) > STOP_V && !raf) {
      raf = requestAnimationFrame(animate);
    }
  }

  // Mouse
  el.addEventListener('mousedown', onDown);
  window.addEventListener('mousemove', onMove);
  window.addEventListener('mouseup', onUp);
  window.addEventListener('mouseleave', onUp);

  // Touch
  el.addEventListener('touchstart', onDown, {passive:false});
  el.addEventListener('touchmove', onMove, {passive:false});
  el.addEventListener('touchend', onUp);

  // Chặn drag ảnh mặc định
  el.querySelectorAll('img').forEach(img => img.setAttribute('draggable','false'));
});

</script>

<script>
  document.addEventListener('click', function (e) {
  const minus = e.target.closest('.adsdigi-qty__btn--minus');
  const plus  = e.target.closest('.adsdigi-qty__btn--plus');
  if (!minus && !plus) return;

  const qtyWrap = e.target.closest('.quantity');
  const input = qtyWrap ? qtyWrap.querySelector('input.qty') : null;
  if (!input) return;

  const step = parseFloat(input.getAttribute('step') || '1');
  const min  = parseFloat(input.getAttribute('min') || '1');
  const maxAttr = input.getAttribute('max');
  const max  = maxAttr !== null && maxAttr !== '' ? parseFloat(maxAttr) : Infinity;

  let val = parseFloat(input.value || '0');

  if (minus) val = Math.max(min, val - step);
  if (plus)  val = Math.min(max, val + step);

  input.value = val;
  input.dispatchEvent(new Event('change', { bubbles: true }));
});

  </script>


<script>
document.addEventListener("DOMContentLoaded", function () {
  const faqWrap = document.querySelector(".content-cauhoi.section .wrap");
  if (!faqWrap) return;

  const items = faqWrap.querySelectorAll("section.widget_text:not(:first-child)");

  // Đóng hết ban đầu
  items.forEach(item => {
    const content = item.querySelector(".textwidget");
    if (content) content.style.maxHeight = "0px";
  });

  items.forEach((item) => {
    const title = item.querySelector(".widget-title, .widgettitle");
    const content = item.querySelector(".textwidget");
    if (!title || !content) return;

    title.style.cursor = "pointer";

    title.addEventListener("click", () => {
      const isOpen = item.classList.contains("active");

      // Đóng tất cả
      items.forEach((it) => {
        const c = it.querySelector(".textwidget");
        it.classList.remove("active");
        if (c) c.style.maxHeight = "0px";
      });

      // Mở nếu trước đó đang đóng
      if (!isOpen) {
        item.classList.add("active");
        content.style.maxHeight = content.scrollHeight + "px";
      }
    });
  });

  // Nếu nội dung thay đổi kích thước (responsive), cập nhật lại maxHeight cho item đang mở
  window.addEventListener("resize", () => {
    const openItem = faqWrap.querySelector("section.widget_text.active");
    if (!openItem) return;
    const content = openItem.querySelector(".textwidget");
    if (content) content.style.maxHeight = content.scrollHeight + "px";
  });
});
</script>


<?php
}
