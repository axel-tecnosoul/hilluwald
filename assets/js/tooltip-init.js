(function($) {
	"use strict";
	var tooltip_init = {
		init: function() {
			$("button").tooltip();
			$("a").tooltip({ boundary: 'window' });
			$("input").tooltip();
			$("img").tooltip();
		}
	};
    tooltip_init.init()
})(jQuery);