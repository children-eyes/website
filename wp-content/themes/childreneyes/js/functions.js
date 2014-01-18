
jQuery(document).ready(function ($) {
  //char counter
  var $texts = $('textarea[maxlength]');
  $texts.each(function () {
      var $this = $(this),
      max = $this.attr('maxlength'),
      textId = $this.attr('id'),
      $parent = $this.parent(),
      countId = textId + '-count',

      $div = $('<div>Zeichen übrig: </div>').addClass('count-down').insertAfter($this),
      $input = $('<span/>').attr({
          id: countId
      }).css({
          width: "25px",
          marginTop: "5px",
          marginBottom: "10px"
      }).appendTo($div);

      $this.on({
          keyup: function () {
              var val = $this.val(),
              countVal = $('#' + countId).text();
              if (val.length > max) {
                  $this.val(val.substr(0, max));
                  alert('Maximale Länge erreicht: ' + max);
                  return false;
              } else {
                  $('#' + countId).text(max - val.length);
              }
          },
          blur: function () {
              var val = $this.val();
              if (val.length > max) {
                  $this.val(val.substr(0, max));
                  alert('Maximale Länge erreicht: ' + max);
                  return false;
              } else {
                  $('#' + countId).text(max - val.length);
              }
          }
      });

      //caculate it first time
      $this.trigger('keyup');
  });

  //form validation
  var child_form = $('form[name="childrenform"]');
  var child_name = $('input[name="name"]', child_form);
  child_form.submit(function(e){
    if(!child_name.val().length){
      child_name.addClass('error');
      e.preventDefault();
    }
  });

  child_name.change(function(){
    if(!child_name.val().length)
      child_name.addClass('error');
    else
      child_name.removeClass('error');
  });


  //countdown
  var is_there_a_countdown = false;
  $('.countdown').each(function(){
    var $this = $(this).removeClass('countdown').addClass('running_countdown');

    //unixtme to date
    var n = new Date(),
        t = new Date(parseInt($this.text()) * 1000),
        h = t.getHours() - n.getHours(),
        m = ((h)*60)+t.getMinutes() - n.getMinutes(),
        s = t.getSeconds() - n.getSeconds();

    if(m <= 0 && s <= 0){
      $this.text('0:00');
      return ;
    }

    is_there_a_countdown = true;

    if(s < 0){
      m--;
      s = 59 + s;
    }

    if(m <= 0){
      $this.removeClass('running_countdown');
      $this.text('0:00');
      return ;
    }

    if(s < 10)
      s = '0'+s;

    $this.text(m+':'+s);
  });

  if(is_there_a_countdown)
    window.setTimeout('update_countdowns();', 1000);
});

var update_countdowns = function(){
  jQuery('.running_countdown').each(function(){
    var $this = jQuery(this);
    var tmp = $this.text().split(':'),
        m = parseInt(tmp[0]),
        s = parseInt(tmp[1]);

    s--;
    if(s <= 0){
      m--;
      s = 59;
    }

    if(m <= 0){
      $this.removeClass('running_countdown');
      $this.text('0:00');
      return ;
    }

    if(m <= 0 && s <= 0){
      $this.text('0:00');
      return ;
    }

    window.setTimeout('update_countdowns();', 1000);

    if(s < 10)
      s = '0'+s;

    $this.text(m+':'+s);
  });
};


/**
 * Theme functions file
 *
 * Contains handlers for navigation, accessibility, header sizing
 * footer widgets and Featured Content slider
 *
 */
( function( $ ) {
	var body    = $( 'body' ),
		_window = $( window );

	// Enable menu toggle for small screens.
	( function() {
		var nav = $( '#primary-navigation' ), button, menu;
		if ( ! nav ) {
			return;
		}

		button = nav.find( '.menu-toggle' );
		if ( ! button ) {
			return;
		}

		// Hide button if menu is missing or empty.
		menu = nav.find( '.nav-menu' );
		if ( ! menu || ! menu.children().length ) {
			button.hide();
			return;
		}

		$( '.menu-toggle' ).on( 'click.childreneyes', function() {
			nav.toggleClass( 'toggled-on' );
		} );
	} )();

	/*
	 * Makes "skip to content" link work correctly in IE9 and Chrome for better
	 * accessibility.
	 *
	 * @link http://www.nczonline.net/blog/2013/01/15/fixing-skip-to-content-links/
	 */
	_window.on( 'hashchange.childreneyes', function() {
		var element = document.getElementById( location.hash.substring( 1 ) );

		if ( element ) {
			if ( ! /^(?:a|select|input|button|textarea)$/i.test( element.tagName ) ) {
				element.tabIndex = -1;
			}

			element.focus();

			// Repositions the window on jump-to-anchor to account for header height.
			window.scrollBy( 0, -80 );
		}
	} );

	$( function() {
		// Search toggle.
		$( '.search-toggle' ).on( 'click.childreneyes', function( event ) {
			var that    = $( this ),
				wrapper = $( '.search-box-wrapper' );

			that.toggleClass( 'active' );
			wrapper.toggleClass( 'hide' );

			if ( that.is( '.active' ) || $( '.search-toggle .screen-reader-text' )[0] === event.target ) {
				wrapper.find( '.search-field' ).focus();
			}
		} );

		/*
		 * Fixed header for large screen.
		 * If the header becomes more than 48px tall, unfix the header.
		 *
		 * The callback on the scroll event is only added if there is a header
		 * image and we are not on mobile.
		 */
		if ( _window.width() > 781 ) {
			var mastheadHeight = $( '#masthead' ).height(),
				toolbarOffset, mastheadOffset;

			if ( mastheadHeight > 48 ) {
				body.removeClass( 'masthead-fixed' );
			}

			if ( body.is( '.header-image' ) ) {
				toolbarOffset  = body.is( '.admin-bar' ) ? $( '#wpadminbar' ).height() : 0;
				mastheadOffset = $( '#masthead' ).offset().top - toolbarOffset;

				_window.on( 'scroll.childreneyes', function() {
					if ( ( window.scrollY > mastheadOffset ) && ( mastheadHeight < 49 ) ) {
						body.addClass( 'masthead-fixed' );
					} else {
						body.removeClass( 'masthead-fixed' );
					}
				} );
			}
		}

		// Focus styles for menus.
		$( '.primary-navigation, .secondary-navigation' ).find( 'a' ).on( 'focus.childreneyes blur.childreneyes', function() {
			$( this ).parents().toggleClass( 'focus' );
		} );
	} );

	// Arrange footer widgets vertically.
	if ( $.isFunction( $.fn.masonry ) ) {
		$( '#footer-sidebar' ).masonry( {
			itemSelector: '.widget',
			columnWidth: function( containerWidth ) {
				return containerWidth / 4;
			},
			gutterWidth: 0,
			isResizable: true,
			isRTL: $( 'body' ).is( '.rtl' )
		} );
	}

	// Initialize Featured Content slider.
	_window.load( function() {
		if ( body.is( '.slider' ) ) {
			$( '.featured-content' ).featuredslider( {
				selector: '.featured-content-inner > article',
				controlsContainer: '.featured-content'
			} );
		}
	} );
} )( jQuery );
