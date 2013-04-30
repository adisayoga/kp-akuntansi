/**
 * JQuery form validation Plugin 1.2.0
 * 
 * Copyright 2010-2011, Adi Sayoga
 * Dual licensed under the MIT or GPL Version 2 licenses.
 */

(function($) {
	/** Validasi form */
	$.fn.validate = function(options) {
		var options = $.extend($.fn.validate.defaults, options);
		options.smd.setAsync(true); // pastikan async callback
		
		return this.each(function() {
			// validasi form saat submit
			$(this).submit(function(e) {
				e.preventDefault();
				
				var data = {};
				$(this).find("input:hidden").each(function() {
					data[$(this).attr("name")] = $(this).val();
				});
				$(options.toValidate).each(function() {
					data[$(this).attr("name")] = $(this).val();
				});
				
				// hapus error message
				$(this).find(".errors-element").removeClass(".errors-element");
				$(this).find(".errors-icon").remove();
				
				validate(this, data, options);
			});
			
			// validasi masing-masing input control
			var _form = this;
			$(options.toValidate).each(function() {
				$(this).blur(function() { 
					var data = {};
					$(_form).find("input:hidden").each(function() {
						data[$(this).attr("name")] = $(this).val();
					});
					data[$(this).attr("name")] = $(this).val();
					
					// hapus error message
					$(this).removeClass("errors-element");
					$(this).parent().find(".errors-icon").remove();

					validate($(this), data, options); 
				});
			});
		});
	};
	
	/** Default options */
	$.fn.validate.defaults = {
		smd: null,				// smd jsonRPC
		functionName: "",       // nama fungsi validasi jsonRPC
		toValidate: "",         // yang divalidasi
		submitRef: "",          // lokasi redirect jika data valid
		exclude: null,          // value yang tidak diikut sertakan pada validasi
		onSubmit: function() {},
		onValid: function(_self) {},
		onInvalid: function(_self) { displayMessage("Data yang dimasukkan tidak valid, periksa kembali inputan."); }
	};
	
	/**
	 * validasi ke server
	 * @param object _self
	 * @param json data
	 * @param json options
	 */
	function validate(_self, data, options) {
		// panggil fungsi json-rpc
		options.smd[options.functionName](data, options.exclude, { success: function(respons) {
			if (!$.isEmptyObject(respons)) {
				// invalid
				for (var elementId in respons) {
					var errorsData = respons[elementId];
					$("#" + elementId).addClass("errors-element");
					$("#" + elementId).parent().append(errorIcon(elementId, errorsData));
				}
				$.isFunction(options.onInvalid) && options.onInvalid(_self);
				
			} else {
				// valid
				$.isFunction(options.onSubmit) && $(_self).is("form") && options.onSubmit();
				$.isFunction(options.onValid) && options.onValid(_self);
				if (options.submitRef) {
					window.location = options.submitRef;
				}
			}
		}});
	};
	
	/** 
	 * Generate error icon 
	 * @param string elementId
	 * @param json errorData
	 */
	function errorIcon(elementId, errorsData) {
		// message
		var message = '<ul class="ui-state-highlight ui-corner-all errors-message" '
			+ 'style="position: absolute; display: inline-block; margin: 0 25px; padding: 3px 5px; '
			+ 'color: #f00; list-style: none; font-style: italic; '
			+ '-webkit-box-shadow: 3px 3px 3px rgba(0, 0, 0, .2); '
			+ '-moz-box-shadow: 3px 3px 3px rgba(0, 0, 0, .2); ">';
		
		for (var errorKey in errorsData) {
			message += '<li>' + errorsData[errorKey] + '</li>';
		}
		message += '</ul>';
		
		// error icon
		var $errorIcon = $('<span class="ui-icon ui-icon-alert errors-icon" '
				+ 'style="position: absolute; display: inline-block; margin: 5px; cursor: help; "></span>');
		
		// tampilkan error message saat icon di-hover
		$errorIcon.hover(function() {
			$(this).parent().append(message);
		},
		function() {
			$(this).parent().find(".errors-message").remove();
		});
		
		return $errorIcon;
	}
	
})(jQuery);