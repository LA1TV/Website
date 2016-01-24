define(["jquery"], function(jQuery) {

	// This has been modified to call resizer() at a set interval to reduce impact
	// of issues where an elements width is adjusted with css
	// TODO provide a destroy method or something which can be called to clear
	// the timer and remove the event listener

	/*global jQuery */
	/*!
	* FitText.js 1.2
	*
	* Copyright 2011, Dave Rupert http://daverupert.com
	* Released under the WTFPL license
	* http://sam.zoy.org/wtfpl/
	*
	* Date: Thu May 05 14:23:00 2011 -0600
	*/

	(function( $ ){

	  $.fn.fitText = function( kompressor, options ) {

		// Setup options
		var compressor = kompressor || 1,
			settings = $.extend({
			  'minFontSize' : Number.NEGATIVE_INFINITY,
			  'maxFontSize' : Number.POSITIVE_INFINITY
			}, options);

		return this.each(function(){

		  // Store the object
		  var $this = $(this);

		  // Resizer() resizes items based on the object width divided by the compressor * 10
		  var resizer = function () {
			$this.css('font-size', Math.max(Math.min($this.width() / (compressor*10), parseFloat(settings.maxFontSize)), parseFloat(settings.minFontSize)));
		  };

		  // Call once to set.
		  resizer();

		  // Call on resize. Opera debounces their resize by default.
		  $(window).on('resize.fittext orientationchange.fittext', resizer);

		  // so that if the width is resized in css it will stil cope ok
		  setInterval(resizer, 15);
		});

	  };

	})( jQuery );

});