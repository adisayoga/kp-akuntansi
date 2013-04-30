/**
 * jQuery pagination Plugin 1.0.0
 * 
 * Copyright 2010-2011, Adi Sayoga
 * Dual licensed under the MIT or GPL Version 2 licenses.
 */

(function($) {
	/** Menampilkan data per halaman */
	$.fn.displayPagedData = function(options) {
		options = $.extend($.fn.displayPagedData.defaults, options);
		
		return this.each(function() {
			var $tbody = $(this).find("tbody");
			$tbody.empty();
			
			$.each(options.data, function() {
				var $row = $("<tr></tr>");
				filterBulan = ((options.tahun)? '/tahun/' + options.tahun: '') 
						    + ((options.bulan)? '/bulan/' + options.bulan: '');
				
				// Action (update/delete)
				$row.append('<td class="action">'
						+ '<a class="delete" href="' + options.url + '/delete/id/' + this.id + filterBulan + '"></a>'
						+ '<a class="update" href="' + options.url + '/update/id/' + this.id + filterBulan + '"></a>'
						+ '</td>');
				
				// Field
				for (var idx in options.fields) {
					// Konstanta
					var CELL_PADDING = 3;
					var DEPTH_ALIGN = 20;
					var POS_DEBIT = 1;
					var POS_KREDIT = -1;
					var DEFAULT_ALIGN = "left";
					
					var field = options.fields[idx];
					
					var depth = (field.depth)? this[field.depth] * DEPTH_ALIGN + CELL_PADDING: CELL_PADDING;
					var pos = (field.pos)? (this[field.pos] == POS_KREDIT)? DEPTH_ALIGN + CELL_PADDING: CELL_PADDING: CELL_PADDING;
					var align = (field.align)? field.align: DEFAULT_ALIGN;
					
					$row.append('<td style="padding-left: ' + Math.max(depth, pos) + 'px; text-align: ' + align + '; ">' 
							+ this[field.name] + '</td>');
				}
				$tbody.append($row);
			});
		});
	};
	
	/** Default options untuk data per halaman */
	$.fn.displayPagedData.defaults = {
		data: null,		  // Data yang didapatkan dari server
		url: "",          // url ke server (controller)
		tahun: 0,		  // filter tahun
		bulan: 0,		  // filter tahun
		fields: null      // Daftar field yang disertakan yang dapat berupa: name, depth, pos, align
	};
	
	/** Control navigasi pada halaman */
	$.fn.paginationControl = function(options) {
		options = $.extend($.fn.paginationControl.defaults, options);
		
		return this.each(function() {
			$(this).empty();
			filterBulan = ((options.tahun)? '/tahun/' + options.tahun: '') 
						+ ((options.bulan)? '/bulan/' + options.bulan: '');
			
			// Halaman sebelumnya
			var prev;
			if (options.pagination.previous) {
				prev = '<a href="' + options.url + '/page/' + options.pagination.previous + filterBulan 
					 + '" class="button">&lt Sebelumnya</a>';
			} else {
				prev = '<span class="button button-disabled">&lt Sebelumnya</span>';
			}
			
			// Daftar halaman
			var pages = "";
			$.each(options.pagination.pagesInRange, function() {
				if (this == options.pagination.current) {
					pages += '<span class="button button-current">' + this + '</span>';
				} else {
					pages += '<a href="' + options.url + '/page/' + this + filterBulan + '" class="button">' 
						  +  this + '</a>';
				}
			});
			
			// Halaman berikutnya
			var next;
			if (options.pagination.next) {
				next = '<a href="' + options.url + '/page/' + options.pagination.next + filterBulan
					+ '" class="button">Berikutnya &gt;</a>';
			} else {
				next = '<span class="button button-disabled">Berikutnya &gt;</span>';
			}
			
			$(this).append(prev).append(pages).append(next);
		});
	};
	
	/** Default options control navigasi */
	$.fn.paginationControl.defaults = {
		pagination: null,  // Objek (json) yang didapatkan dari server
		url: "",           // url ke server
		tahun: 0,		   // filter tahun
		bulan: 0 		   // filter bulan
	};
})(jQuery);