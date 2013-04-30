/**
 *  Copyright 2010-2011, Adi Sayoga
 */


/**
 * Repeat suatu string
 */
String.prototype.repeat = function(num) {
	return new Array((num * 1) + 1).join(this);
};

/**
 * Menampilkan pesan informasi pada header
 * @param message - pesan yang ditampilkan
 * @param state - state message (info | alert | error)
 */
function displayMessage(message, state) {
	// default
	var iconClass = "ui-icon-info";
	var backgroundClass = "ui-state-highlight";
	
	switch (state) {
	case "info":   
		iconClass = "ui-icon-info"; 
		backgroundClass = "ui-state-highlight";
		break;
	case "alert":  
		iconClass = "ui-icon-alert"; 
		backgroundClass = "ui-state-highlight";
		break;
	case "error":  
		iconClass = "ui-icon-circle-close";
		backgroundClass = "ui-state-error";
		break;
	}
	// Icon
	var $icon = $("<div>").addClass("ui-icon " + iconClass).css({ "float": "left", "margin-right": "10px" });
	
	// Message
	var $messageDiv = $("<div>").text(message).css({ "overflow": "hidden" });
	
	// Container
	var $container = $("<div>").addClass(backgroundClass + " ui-corner-all")
		.append($icon).append($messageDiv).css({ "padding": "5px 10px" });
	
	$(".header .message").empty().append($container).fadeIn("slow").delay(5000).fadeOut(1000);
	
	// Tampilkan di tengah
	var marginLeft = Math.floor(($(".header").width() - $container.width()) / 2);
	$(".header .message").css({ "left" : marginLeft });
}
