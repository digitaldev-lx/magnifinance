$(document).ready(function () {

  //CLOSE MODAL
  $('.close_filter_modal').on('click', function(){
  $('#modal-container').addClass('out');
  $('body').removeClass('modal-active');
});

  // DESKTOP MEGAMENU
  $(window).resize(function(){
    if ($(window).width() >= 980){

        // when you hover a toggle show its dropdown menu
        $(".navbar .dropdown-toggle").hover(function () {
           $(this).parent().toggleClass("show");
           $(this).parent().find(".dropdown-menu").toggleClass("show");
         });

          // hide the menu when the mouse leaves the dropdown
        $( ".navbar .dropdown-menu" ).mouseleave(function() {
          $(this).removeClass("show");
        });

      // do something here
    }
  });

  // BANNER SLIDER
  var owl = $('#banner_slider');
  owl.owlCarousel({
    nav: true,
    dots: false,
    loop: true,
    autoplayHoverPause: true,
    autoplay: false,
    responsive: {
      0: {
        items: 1,
        nav: false
      },
      600: {
        items: 1
      },
      1000: {
        items: 1
      }
    }

  })
  $(".owl-prev").html('<i class="zmdi zmdi-chevron-left"></i>');
  $(".owl-next").html('<i class="zmdi zmdi-chevron-right"></i>');

  // FEATURED DEAL SLIDER
  var owl = $('#featured_deal_slider');
  owl.owlCarousel({
    margin: 20,
    nav: true,
    dots: false,
    loop: false,
    autoplayHoverPause: true,
    autoplay: false,
    responsive: {
      0: {
        margin: 0,
        items: 1
      },
      600: {
        items: 1
      },
      1000: {
        items: 2
      }
    }
  })
  $(".owl-prev").html('<i class="zmdi zmdi-chevron-left"></i>');
  $(".owl-next").html('<i class="zmdi zmdi-chevron-right"></i>');

  // LATEST COUPON SLIDER
  var owl = $('#latest_coupon_slider');
  owl.owlCarousel({
    margin: 20,
    nav: true,
    dots: false,
    loop: false,
    autoplayHoverPause: true,
    autoplay: false,
    responsive: {
      0: {
        items: 1
      },
      600: {
        items: 1
      },
      1000: {
        items: 1
      }
    }
  })
  $(".owl-prev").html('<i class="zmdi zmdi-chevron-left"></i>');
  $(".owl-next").html('<i class="zmdi zmdi-chevron-right"></i>');

  // DEAL DETAIL SLIDER
  var owl = $('#deal_detail_slider');
  owl.owlCarousel({
    nav: true,
    dots: false,
    loop: false,
    autoplayHoverPause: true,
    autoplay: false,
    responsive: {
      0: {
        items: 1
      },
      600: {
        items: 1
      },
      1000: {
        items: 1
      }
    }

  })
  $(".owl-prev").html('<i class="zmdi zmdi-chevron-left"></i>');
  $(".owl-next").html('<i class="zmdi zmdi-chevron-right"></i>');

  // SPOTLIGHT SLIDER
  var owl = $('#spotlight_slider');
  owl.owlCarousel({
    // center: true,
    margin: 25,
    nav: true,
    dots: false,
    loop: false,
    autoplayHoverPause: true,
    // autoplay: true,
    responsive: {
      0: {
        items: 1
      },
      600: {
        items: 2
      },
      1000: {
        items: 4
      },
      1300: {
        items: 4
      }
    }
  })

    // ARTICLES SLIDER
    var owl = $('#articles_slider');
    owl.owlCarousel({
        // center: true,
        margin: 25,
        nav: true,
        dots: false,
        loop: false,
        autoplayHoverPause: true,
        // autoplay: true,
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 2
            },
            1000: {
                items: 4
            },
            1300: {
                items: 4
            }
        }
    })
});

//Fix header bottom on scroll
$(window).scroll(function () {
  var sticky = $('.headerBottom'),
  scroll = $(window).scrollTop();
  if (scroll >= 85 && screen.width >= 767) sticky.addClass('fixed');
  else sticky.removeClass('fixed');
});

// MOBILE MENU
  $('body').on('click', '.open-nav', function() {
    document.getElementById("mySidenav").style.width = "250px";
  })

  $('body').on('click', '.close-nav', function() {
    document.getElementById("mySidenav").style.width = "0";
  })

// SELECT TO PLUGIN
$(".myselect").select2();

//CATEGORY VIEW MORE
$('body').on('click', '.view_all_categories', function() {
  var dots = document.getElementById("category_span");
  var moreText = document.getElementById("more_category");
  var btnText = document.getElementById("view_all_categories");

  if (dots.style.display === "none") {
      dots.style.display = "inline";
      btnText.innerHTML = "View All";
      moreText.style.display = "none";
  } else {
      dots.style.display = "none";
      btnText.innerHTML = "View Less";
      moreText.style.display = "inline";
  }
});

//CATEGORY VIEW MORE MOBILE
$('body').on('click', '.view_all_mbl_categories', function() {
  var dots = document.getElementById("category_mbl_span");
  var moreText = document.getElementById("more_mbl_category");
  var btnText = document.getElementById("view_all_mbl_categories");

  if (dots.style.display === "none") {
      dots.style.display = "inline";
      btnText.innerHTML = "View All";
      moreText.style.display = "none";
  } else {
      dots.style.display = "none";
      btnText.innerHTML = "View Less";
      moreText.style.display = "inline";
  }
});

//LOCATION VIEW MORE
function locationFunction() {
  var dots = document.getElementById("location_span");
  var moreText = document.getElementById("more_location");
  var btnText = document.getElementById("view_all_location");

  if (dots.style.display === "none") {
      dots.style.display = "inline";
      btnText.innerHTML = "View All";
      moreText.style.display = "none";
  } else {
      dots.style.display = "none";
      btnText.innerHTML = "View Less";
      moreText.style.display = "inline";
  }
}


//LOCATION VIEW MORE
function companyFunction() {
  var dots = document.getElementById("company_span");
  var moreText = document.getElementById("more_company");
  var btnText = document.getElementById("view_all_companies");

  if (dots.style.display === "none") {
      dots.style.display = "inline";
      btnText.innerHTML = "View All";
      moreText.style.display = "none";
  } else {
      dots.style.display = "none";
      btnText.innerHTML = "View Less";
      moreText.style.display = "inline";
  }
}

//COMPANY VIEW MORE MOBILE
function companyMblFunction() {
  var dots = document.getElementById("company_mbl_span");
  var moreText = document.getElementById("more_mbl_company");
  var btnText = document.getElementById("view_all_mbl_companies");

  if (dots.style.display === "none") {
      dots.style.display = "inline";
      btnText.innerHTML = "View All";
      moreText.style.display = "none";
  } else {
      dots.style.display = "none";
      btnText.innerHTML = "View Less";
      moreText.style.display = "inline";
  }
}


//LOCATION VIEW MORE MOBILE
function locationMblFunction() {
  var dots = document.getElementById("location_mbl_span");
  var moreText = document.getElementById("more_mbl_location");
  var btnText = document.getElementById("view_all_mbl_location");

  if (dots.style.display === "none") {
      dots.style.display = "inline";
      btnText.innerHTML = "View All";
      moreText.style.display = "none";
  } else {
      dots.style.display = "none";
      btnText.innerHTML = "View Less";
      moreText.style.display = "inline";
  }
}

//MODAL
$('.filter_modal_wrapper').click(function(){
  var buttonId = $(this).attr('id');
  $('#modal-container').removeAttr('class').addClass(buttonId);
  $('body').addClass('modal-active');
})

//DEAL DETAIL INCREASE DECREASE NUMBER
// function increaseValue() {
//   var value = parseInt(document.getElementById('number').value, 10);
//   value = isNaN(value) ? 0 : value;
//   value++;
//   document.getElementById('number').value = value;
// }

// function decreaseValue() {
//   var value = parseInt(document.getElementById('number').value, 10);
//   value = isNaN(value) ? 0 : value;
//   value < 1 ? value = 1 : '';
//   value--;
//   document.getElementById('number').value = value;
// }



