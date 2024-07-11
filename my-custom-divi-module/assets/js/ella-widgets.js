(function($){
    "use strict";
    
	
      // Carousel Handler
        var WidgetHtsliderCarouselHandler = function ($scope, $) {
    
            var carousel_elem = $scope.find( '#ellaslider-sections' ).eq(0);
            if ( carousel_elem.length > 0 ) {
               
				const swiper4 = new Swiper("#ellaslider-sections .swiper-container", {
					// Optional parameters
					slidesPerView: 2,
					grabCursor: true,
					loop: true,
					mousewheel: false,
			
					// Enabled autoplay mode
					autoplay: {
					  delay: 3000,
					  disableOnInteraction: false
					},
			
					// If we need pagination
					pagination: {
					  el: ".swiper-pagination",
					  dynamicBullets: false,
					  clickable: true
					},
			
					// If we need navigation
					navigation: {
					  nextEl: ".swiper-button-next",
					  prevEl: ".swiper-button-prev"
					},
			
					// Responsive breakpoints
					breakpoints: {
					  320: {
						slidesPerView: 2,
						spaceBetween: 20
					  },
					  768: {
						slidesPerView: 3,
						spaceBetween: 20
					  },
					  1025: {
						slidesPerView: 4,
						spaceBetween: 20
					  }
					}
				  });
            }

          
        }

       
        
        // Run this code under Elementor.
        $(window).on('elementor/frontend/init', function () {
         //   elementorFrontend.hooks.addAction('frontend/element_ready/ella-custom-widget.default', WidgetHtsliderCarouselHandler);
          
    
        });
    
    })(jQuery);
