(function($) {
	$.fn.inputFilter = function(inputFilter) {
		return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
		if (inputFilter(this.value)) {
			this.oldValue = this.value;
			this.oldSelectionStart = this.selectionStart;
			this.oldSelectionEnd = this.selectionEnd;
		} else if (this.hasOwnProperty("oldValue")) {
			this.value = this.oldValue;
			this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
		} else {
			this.value = "";
		}
		});
	};
}(jQuery));

$(function(){
    $(".numeric").inputFilter(function(value) {
		return /^\d*$/.test(value);    // Allow digits only, using a RegExp
	});

    $(".alphanumeric").inputFilter(function(value) {
		return /^[a-z0-9]*$/i.test(value);    // Allow alphanumeric only, using a RegExp
	});

    $(".alphanumericdot").inputFilter(function(value) {
		return /^(?!\.)(?!.*?\.\.)[a-zA-Z0-9.]*$/i.test(value);    // Allow alphanumeric and dot only, using a RegExp
	});
});
