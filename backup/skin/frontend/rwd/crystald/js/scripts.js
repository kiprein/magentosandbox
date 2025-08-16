jQuery.noConflict();

(function ($) {
  $(function () {
    $(document).ready(function(){
	  //Initial setup of window size check
	  windowSize();

	  /** Special functionality for mobile **/
	  function windowSize() {
		  windowHeight = window.innerHeight ? window.innerHeight : $(window).height();
		  windowWidth = window.innerWidth ? window.innerWidth : $(window).width();
    }
    
   
    $(window).on("load", function(){
      // Internet Explorer 6-11
	    var isIE = /*@cc_on!@*/false || !!document.documentMode;
       // Edge 20+
      var isEdge = !isIE && !!window.StyleMedia;
      if(!isEdge && !isIE) {
        $(window).scroll(function () {
		var scroll = $(window).scrollTop();
		if (windowWidth > 767) {
			if (scroll >= 150) {
				$('#sticky-menu-wrapper').removeClass('hidden');
			} else {
				$('#sticky-menu-wrapper').addClass('hidden');
			}
		}
          if (windowWidth > 991) {
            //Product view media scroll if not in mobile
            if (document.getElementById("large-slider-check") != null) {
                var imageDiv = $("#product-image-check").height();
                var sliderDiv = document.getElementById("large-slider-check").offsetTop;
                var stop = parseInt(sliderDiv) - parseInt(imageDiv) - 200;

                // console.log('Image Div: ' + imageDiv);
                //console.log('Large Slider: ' + sliderDiv);
                // console.log('Stop: ' + stop);

                if (scroll >= 200 && scroll < stop) {
                  topPosition = parseInt(scroll) - 200;
                  $('.product-image-gallery').css('top', topPosition + 'px');
                  $('.product-image-gallery').css('position', 'relative');
                }
              }
            } else {
              $('.product-image-gallery').css('top', '0px');
              $('.product-image-gallery').css('position', 'static');
            }
          });
        }
    });
	  
      
      /**
       * LOGIC FOR OPENING AND CLOSING SEARCH ON Sticky
       */
      $('#sticky-menu-wrapper').on('click', '.search-icon', function() {
        $('#sticky-search').toggleClass('hidden');
        $('#sticky-search .form-search .input-search').focus()
      });
      
      //On the product view page the accordions need to start in the open postion to make sure all js code fires
      //This closes them a split second later
      
      $('footer .collapse').removeClass('in');
      
      // Find all iframes
      var $iframes = $( "iframe" );
      
      // Find &#x26; save the aspect ratio for all iframes
      $iframes.each(function () {
        $( this ).data( "ratio", this.height / this.width )
        // Remove the hardcoded width &#x26; height attributes
        .removeAttr( "width" )
        .removeAttr( "height" );
      });
      
      // Resize the iframes when the window is resized
      $( window ).resize( function () {
        $iframes.each( function() {
          // Get the parent container&#x27;s width
          var width = $( this ).parent().width();
          $( this ).width( width )
          .height( width * $( this ).data( "ratio" ) );
        });
        // Resize to fix all iframes on page load.
      }).resize();
      
      //Mobile Menu functionality
      var $menu = $j("#nav2").mmenu({
        "slidingSubmenus": true
      }, {
        // configuration
        offCanvas: {
          pageSelector: "#wrapper"
        },
        //clone: true
      });
      var $icon = $j("#mobile-icon");
      var API = $menu.data( "mmenu" );
      
      $icon.on( "click", function() {
        API.open();
      });
      
      API.bind( "opened", function() {
        setTimeout(function() {
          $icon.addClass( "is-active" );
        }, 100);
      });
      API.bind( "closed", function() {
        setTimeout(function() {
          $icon.removeClass( "is-active" );
        }, 100);
      });
      
      //Used for anywhere there is a product grid
      //$('.product-list ul li.product-container').matchHeight();
      $('.my-wishlist ul.wishlist-list li.item').matchHeight();
      $('ul.wishlist-share li.item').matchHeight();
      $('.wishlist-recently-view #recent-viewed-container .col-md-6').matchHeight();
      $('.related-products .item-block').matchHeight();
      $('.selling-marketing li').matchHeight();
      
      //Zero out filters
      $('.amshopby-filters-left li input').click(function() {
        $(this).val('');
      })
      
      //Popups on the product view page
      $(".availability-popup").colorbox({close: 'X'});
      $(".compare-price").colorbox({close: 'X'});
      $(".other-sizes-available").colorbox({close: 'X'});
      
      //Used for any popup forms around the site
      $(".form-popup-button").click(function () {
        $.colorbox({
          inline: true,
          href: '.form-popup',
          width: '95%',
          maxWidth: '960px',
          scrolling: false
        });
      });
      
      //SHow the different popup forms on the product page
      $(".dealer-form-trigger").click(function () {
        $.colorbox({
          inline: true,
          href: '.dealer-form',
          width: '95%',
          maxWidth: '960px',
          scrolling: false,
          close: 'X',
          scrolling: false
        });
      });
      
      $(".quote-form-trigger").click(function () {
        $.colorbox({
          inline: true,
          href: '.quote-form',
          width: '95%',
          maxWidth: '960px',
          close: 'X',
          scrolling: false
        });
      });
      
      $(".friend-form-trigger").click(function () {
        $.colorbox({
          inline: true,
          href: '.email-friend-form',
          width: '95%',
          maxWidth: '960px',
          close: 'X',
          scrolling: false
        });
      });
      
      $(".sample-form-trigger").click(function () {
        $.colorbox({
          inline: true,
          href: '.sample-form',
          width: '95%',
          maxWidth: '960px',
          close: 'X',
          scrolling: false
        });
      });
      
      //Need to run when the calendar changes
      $( "#available_before" ).change(function() {
        var inventory = $('#inventory').val();
        var before = $('#available_before').val();
        
        var params = getSearchParameters();
        
        if(inventory.length > 0) {
          params.inventory = inventory;
        }
        if(before.length > 0) {
          params.available_before = before;
        }
        
        var url = getFormattedUrl(params);
        $('#inventory-link').attr('href', url);
    });
    
    /**
     * Build the inventory url on text blur
     */
    $('#inventory-wrapper').on('blur', '.inventory-fields', function(e){
      var inventory = $('#inventory').val();
      var before = $('#available_before').val();
      
      var params = getSearchParameters();
      if(inventory.length > 0) {
        params.inventory = inventory;
      }
      if(before.length > 0) {
        params.available_before = before;
      }
      
      //Finally build the url
      var url = getFormattedUrl(params);
      $('#inventory-link').attr('href', url);
    });
    
    
    
    /**
     * This, getSearchParameters, and transformToAssocArray are responsible for getting the correct query params you
     * need, building a clean cleanly formatted url with search parameters also setting up the obj making it easy to use
     * moving forward.
     * @returns {*}
     */
    function getFormattedUrl(params) {
      var currentUrl = window.location.href;
      var baseUrl = currentUrl.split('?')[0];
      
      var queryString = '';
      for(var key in params) {
        if(key.length > 0) {
          queryString += key + '=' + params[key] + '&';
        }
      }
      
      return baseUrl + '?' + queryString;
      
    }
    function getSearchParameters() {
      var prmstr = window.location.search.substr(1);
      return prmstr != null && prmstr != "" ? transformToAssocArray(prmstr) : {};
    }
    
    function transformToAssocArray( prmstr ) {
      var params = {};
      var prmarr = prmstr.split("&");
      for ( var i = 0; i < prmarr.length; i++) {
        var tmparr = prmarr[i].split("=");
        //Fixes an issue where extra var was messing things up
        if(tmparr[1] != undefined) {
          params[tmparr[0]] = tmparr[1];
        }
      }
      return params;
    }
    
    $('.col-left').on('click', '.filter-group .category-checkbox', function () {
      $(this).toggleClass('selected');
      
      //changes checkbox image on category filters
      jQuery('img', this).attr('src', function(i, oldSrc) {
        //Not the best solution but works for now
        var baseUrl = window.location.origin;
        var baseCheckedUrl =  baseUrl + '/skin/frontend/rwd/crystald/images/ch.png';
        if(oldSrc == baseCheckedUrl || oldSrc == '/skin/frontend/rwd/crystald/images/ch.png') {
          return '/skin/frontend/rwd/crystald/images/unchecked.png';
        } else {
          return '/skin/frontend/rwd/crystald/images/ch.png';
        }
      });
      
      //This makes sure to reset everything
      var categoryIds = '';
      
      categoryIds = $('.category-checkbox.selected').map(function() {
        return $(this).data("category-id");
      }).get().join(',');
      
      //Need to go through the current params and rebuild the url.  A pain to do it this way but I'm not sure how else
      //to modify the categories on the file based on what is selected
      var params = getSearchParameters();
      params.cat = categoryIds;
      
      //Finally build the url
      //console.log(params);
      var url = getFormattedUrl(params);
      $('a.category-submit').attr('href', url);
    });
    
    /**
     * The slide toggle command searches for the first element in the tree and makes it show
     * The toggle class then add a class to the parent div so you can control and movement or changes to the title section
     */
    $('.col-left').on('click', '.filter-group div.parent-category', function () {
      $(this).toggleClass('collapsed');
      
      $(this).parent('.filter-group').find('.sub-category-list').slideToggle(function () {
        $(this).parent('.filter-group').toggleClass('active-group');
      });
    });
    
    if (screen.width < 1000) {
      /**
       * LOGIC FOR ALL FILTER BOXES
       * For some reason on mobile the filters do not register a click that expands the filter this fixes that
       */
      $('#narrow-by-list dt').on('click', function (e) {
        //The stop method is used to make sure any weird clicks registered finish before firing the slideToggle
        $(this).toggleClass('amshopby-collapsed');
        $(this).parent('.filter-wrapper').find('dd ol').toggleClass('no-display-current');
        return false;
      });
      
      /**
       * LOGIC FOR OPENING AND CLOSING SEARCH ON MOBILE
       */
      $('.mobile-header').on('click', '#mobile-search-icon', function () {
        $('.mobile-search').toggleClass('no-display-mobile');
	    });
      
	    /**
       * LOGIC MAIN CATEGORY IMAGES SHOW LINKS ON MOBILE CLICK
       */
      //$('.category-list').hide();
      // $('.category-group .category-list-image').on('click', function(e) {
        //   $(this).next('div').toggleClass('no-display-mobile');
        //   return false;
        // });
      }
      
      /**
       * Make sure all mobile functions continue to work when resizing
       */
      // For example, get window size on window resize
      $(window).resize(function() {
        windowSize();
        if (screen.width < 768) {
          /**
           * LOGIC FOR ALL FILTER BOXES
           * For some reason on mobile the filters do not register a click that expands the filter this fixes that
           */
          $('#narrow-by-list dt').on('touchstart click', function (e) {
            //The stop method is used to make sure any weird clicks registered finish before firing the slideToggle
            e.stopPropagation();
            $(this).toggleClass('amshopby-collapsed');
            $(this).parent('.filter-wrapper').find('dd ol').stop(true, false).slideToggle();
            return false;
          });
          
          /**
           * LOGIC MAIN CATEGORY IMAGES SHOW LINKS ON MOBILE CLICK
           */
          // $('.category-wrapper .category-list-image').on('touchstart click', function(e) {
            //   $(this).next('div').toggleClass('no-display-mobile');
            //   return false;
            // });
            
            /**
             * LOGIC FOR OPENING AND CLOSING SEARCH ON MOBILE
             */
            $('.mobile-header').on('click', '.search-button', function() {
              $('#search_mini_form .input-search').toggleClass('workingThatSearch');
              $('#search_mini_form .search-background').toggleClass('no-display');
            });
          }
        });
      });
      });
    })(jQuery);
    
