/* ========================================================================
 * DOM-based Routing
 * Based on http://goo.gl/EUTi53 by Paul Irish
 *
 * Only fires on body classes that match. If a body class contains a dash,
 * replace the dash with an underscore when adding it to the object below.
 *
 * .noConflict()
 * The routing is enclosed within an anonymous function so that you can
 * always reference jQuery with $, even when in .noConflict() mode.
 * ======================================================================== */
(function($) {

  // Use this variable to set up the common and page specific functions. If you
  // rename this variable, you will also need to rename the namespace below.
  var Sage = {
    // All pages
    'common': {
      init: function() {

        /*
         *_Debounced resize
         */
        function debounce(func, wait, immediate) {
          var timeout;
          return function() {
            var context = this,
              args = arguments;
            var later = function() {
              timeout = null;
              if (!immediate) {
                func.apply(context, args);
              }
            };
            if (immediate && !timeout) {
              func.apply(context, args);
            }
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
          };
        }


        /*
         *_custom inputs
         */
        function custom_inputs(parent) {
          // Material choices
          $(parent).find('.checkbox input[type=checkbox]').after("<span class=checkbox-material><span class=check></span></span>");
          $(parent).find('.radio input[type=radio]').after("<span class=radio-material><span class=circle></span><span class=check></span></span>");
          $(parent).find('.togglebutton input[type=checkbox]').after("<span class=toggle></span>");

          // Gravity Forms render tweak
          $(parent).find('.gfield_checkbox > li').addClass('checkbox');
          $(parent).find('.gfield_radio > li').addClass('radio');
          $(parent).find('select.gfield_select').addClass('form-control');
        }


        // init form custom inputs
        custom_inputs($('.form'));

        // apply material inputs on ajax forms
        $(document).bind('gform_post_render', function(event, form_id, cur_page) {
          var form = $('#gform_' + form_id);
          custom_inputs(form);
        });


        // Dropdown align plugin
        jQuery.fn.dropdownAlign = function(options){

          options = $.extend({
            itemClass: 'dropdown-rtl',
            dropdownClass: 'dropdown-menu'
          }, options);

          var make = function(){
            var $dropdownEL = $(this).find('>.' + options.dropdownClass),
                $navbarEl = $(this).parent();

            if($dropdownEL.length && $navbarEl.length){

              $navbarEl.css('oveflow', 'hidden');

              if(($(this).offset().left - $navbarEl.offset().left + $dropdownEL.width()) < $navbarEl.width()){
                $(this).removeClass(options.itemClass);
              } else{
                $(this).addClass(options.itemClass);
              }

              $navbarEl.css('oveflow', 'visible');

            }

          };

          $(this).on('show.bs.dropdown', function () {
            $(this).each(make);
          });

          return this.each(make);
        };


        // wait until users finishes resizing the browser
        var debouncedResize = debounce(function() {

          // fire dropdown align plugin
          $('.navbar-nav .nav-item-has-children').dropdownAlign();

        }, 100);


        //window load callback
        $(window).load(function() {
          // needed by preloaded
          $('body').addClass('loaded');

          // when the window resizes, redraw the grid
          $(window).resize(debouncedResize).trigger('resize');

        });


        // Disable 300ms click delay on mobile
        FastClick.attach(document.body);


        // Responsive video
        $('.main').fitVids();


        // Video lightbox
        $('.video-lightbox').magnificPopup({
          type: 'iframe'
        });


        // Image gallery lightbox
        $('.gallery').each(function() {
          var $thumb = $(this).find('a.gallery-thumb');

          $thumb.each(function() {

            $(this).magnificPopup({
              type: 'image',
              enableEscapeKey: true,
              gallery: {
                enabled: false,
                tPrev: '',
                tNext: '',
                tCounter: '%curr% | %total%'
              },
              mainClass: 'mfp-with-zoom',
              zoom: {
                enabled: true,
                duration: 300,
                easing: 'ease-in-out',
              }
            });
          });
        });


        /**
         * ripples
         */
        $([
          ".navbar-toggler",
          ".nav-link",
          ".btn"
        ].join(",")).ripples();


        // position sticky polifill
        $('.navbar-sticky').Stickyfill();


        // Highlight X letter
        $('.brand-text').each(function() {
          var title = $(this).children().first(),
              text = title.text().replace(/(^\w+\s)(K\.)$/g, '$1<em>$2</em>');

          title.html(text);
        });


        // Handle hash anchors
        $('.nav-link').on('click', function(e){
            var target = $($(this).attr('href'));

            if(target.length){
                var offset = Math.round(target.offset().top - $('.navbar-sticky').outerHeight() - $('.navbar-sticky-top').outerHeight() - $('#wpadminbar').outerHeight());
                $('html,body').animate({ scrollTop: offset }, 1000, 'easeInOutCubic');
            }

            e.preventDefault();

        });


        // Portfolio carousel
        $('.portfolio-carousel .jcarousel').jcarousel({
          animation: {
            duration: 500,
            easing:   'ease-out'
          },
          transitions: Modernizr.csstransitions ? {
            transforms:   Modernizr.csstransforms,
            transforms3d: Modernizr.csstransforms3d,
            easing:       'ease-out'
          } : false,
          wrap: 'circular'
        }).on('jcarousel:reload jcarousel:create', function () {

          var carousel = $(this),
          width = carousel.innerWidth();

          if (width >= 722) {
            width = width / 3;
          } else if (width >= 528) {
            width = width / 2;
          }

          carousel.jcarousel('items').css('width', Math.ceil(width) + 'px');

        });


        // carousel controls
        $('.jcarousel-control-next').jcarouselControl({
          target: '+=1'
        });

        $('.jcarousel-control-prev').jcarouselControl({
          target: '-=1'
        });


        // Video lightbox
        $('.video-lightbox').magnificPopup({
          type: 'iframe',
          mainClass: 'mfp-with-zoom'
        });


        // Blog
        $('.section-blog').each(function(){

            var $list     = $(this).find('.article-list'),
                $button   = $(this).find('button'),
                $iterator = 0,
                $data     = {
                    'action' : 'get_posts',
                    'width'  : $(window).width(),
                    'offset' : $list.children().length
                },
                get_posts = function(data) {
                    jQuery.ajax({
                        type: "POST",
                        url: ajax_helper.ajax_url,
                        dataType: "json",
                        data: data,
                        success: function(response) {
                            var $resp   = $(response.content);

                            $iterator++;

                            $resp.css({
                              'position'   : 'absolute',
                              'left'       : '0',
                              'width'      : '100%',
                              'visibility' : 'hidden',
                              'opacity'    : '0'
                            });

                            $list.css({
                              'position'   : 'relative'
                            });

                            $list.append($resp);

                            var $list_H = $list.height(),
                                $height = $resp.height() + $list_H;

                            $list.css('height', $list_H);

                            $button.removeClass('loading');

                            $list.animate({
                              'height': $height
                            }, 600, 'easeInOutQuad', function() {
                              $resp.each(function(i) {
                                var $target  = $(this),
                                    $timeout = 100 * i;

                                setTimeout(function() {
                                  $target.css({
                                    'position'   : 'static',
                                    'visibility' : 'visible',
                                    'opacity'    : '1'
                                  });

                                  $list.css({'height' : 'auto'});
                                }, $timeout);
                              });
                            });

                            if (response.status === 'full') {
                              $button.hide();
                            }

                        },
                        error: function(response){
                            console.log(response);
                        }
                    });
                };

            get_posts($data);

            $button.click(function(){
              $(this).addClass('loading');

              var $postop = Math.round($list.offset().top -
                  $(window).height()*0.2 -
                  $('.navbar-sticky').outerHeight() -
                  $('#wpadminbar').outerHeight() +
                  $iterator*$list.find('li:first').outerHeight());

              $('html,body').animate({
                scrollTop: $postop
              }, 600, 'easeInOutQuad');

              $list.height('auto');

              $data.offset = $list.children().length;

              get_posts($data);

            });

        });


      },
      finalize: function() {
        // JavaScript to be fired on all pages, after page specific JS is fired
      }
    },
    // Home page
    'home': {
      init: function() {
        // JavaScript to be fired on the home page
      },
      finalize: function() {
        // JavaScript to be fired on the home page, after the init JS
      }
    },
  };

  // The routing fires all common scripts, followed by the page specific scripts.
  // Add additional events for more control over timing e.g. a finalize event
  var UTIL = {
    fire: function(func, funcname, args) {
      var fire;
      var namespace = Sage;
      funcname = (funcname === undefined) ? 'init' : funcname;
      fire = func !== '';
      fire = fire && namespace[func];
      fire = fire && typeof namespace[func][funcname] === 'function';

      if (fire) {
        namespace[func][funcname](args);
      }
    },
    loadEvents: function() {
      // Fire common init JS
      UTIL.fire('common');

      // Fire page-specific init JS, and then finalize JS
      $.each(document.body.className.replace(/-/g, '_').split(/\s+/), function(i, classnm) {
        UTIL.fire(classnm);
        UTIL.fire(classnm, 'finalize');
      });

      // Fire common finalize JS
      UTIL.fire('common', 'finalize');
    }
  };

  // Load Events
  $(document).ready(UTIL.loadEvents);

})(jQuery); // Fully reference jQuery after this point.
