// JavaScript Document
(function( $ ){
	$.fn.uberFadeIn = function(c) {
		if(!this.is(':visible')) {
			var elemHtml = this.html();
			this.height(this.height()).html('').slideDown(function() {
				$(this).hide().css('height', '').html(elemHtml).fadeIn(c);
			});
		}
	};
})( jQuery );
(function( $ ){
	$.fn.uberFadeOut = function(c) {
		if(this.is(':visible')) {
			var elemHeight = this.height();
			var elemHtml = this.html();
			this.fadeOut(function() {
				$(this).html('').height(elemHeight).show().slideUp(function() {
					$(this).html(elemHtml).css('height', '').hide(c);
				});
			});
		}
	};
})( jQuery );

jQuery(function($) {
	$('#add_auth').click(function(e) {
		if($('#auth').is(':hidden')) {
			$('#auth').uberFadeIn();
			$(this).addClass('selected').attr('title', 'Remove HTTP Authentication');
		} else {
			$('#auth').uberFadeOut(function() {
				$('#auth input').attr('value', '');
			});
			$(this).removeClass('selected').attr('title', 'Add HTTP Authentication');
		}
		e.preventDefault();
	});
	
	var req_val = $('.req_val:first').remove();
	$('#add_req_val').click(function(e) {
		if($('#req_vals').is(':hidden')) {
			$(req_val).clone().appendTo('#req_vals').show();
			$('#req_vals').uberFadeIn();
		} else {
			$(req_val).clone().appendTo('#req_vals').uberFadeIn();
		}
		e.preventDefault();
	});
	
	$('#execute').click(function(e) {
		if($('#url').attr('value').indexOf("?") > 0) {
			alert("Please include GET values through the interface");
		} else {
			
			$('#header').uberFadeOut();
			$.ajax({
				url: "ajax/exec.php",
				type: "POST",
				data: $("#request_form").serialize(),
				dataType: 'json',
				success: function(d) {
					
					//fill request data
					$('#request .method').html(d.request.requestLine.method);
					$('#request .uri').html(d.request.requestLine.uri);
					$('#request .http_version').html(d.request.requestLine.httpVersion);
					
					$('#request .headers, #request .body').html('');
					$.each(d.request.headers, function(k, v) {
						$('#request .headers').append('<div><span class="header_name">'+v.name+':</span> <span class="header_value">'+v.value+'</span></div>');
					});
					$('#request .body').html(d.request.body);
					
					
					//fill response data
					$('#response .http_version').html(d.response.statusLine.httpVersion);
					$('#response .code').html(d.response.statusLine.code);
					$('#response .phrase').html(d.response.statusLine.phrase);
					switch(d.response.statusLine.code.charAt(0)) {
						case '1':
							$('#response .code, #response .phrase').removeClass('successful redirection client_error server_error').addClass('information');
							break;
						case '2':
							$('#response .code, #response .phrase').removeClass('information redirection client_error server_error').addClass('successful');
							break;
						case '3':
							$('#response .code, #response .phrase').removeClass('information successful client_error server_error').addClass('redirection');
							break;
						case '4':
							$('#response .code, #response .phrase').removeClass('information successful redirection server_error').addClass('client_error');
							break;
						case '5':
							$('#response .code, #response .phrase').removeClass('information successful redirection client_error').addClass('server_error');
							break;
					}
					
					$('#response .headers, #response .body').html('');
					$.each(d.response.headers, function(k, v) {
						$('#response .headers').append('<div><span class="header_name">'+v.name+':</span> <span class="header_value">'+v.value+'</span></div>');
					});
					switch(d.response.type) {
						case 'text':
							$('#response .body').html(d.response.body);
							break;
						case 'image':
							$('#response .body').append('<img src="'+d.response.image.path+'" />');
					}
					
					$('#results').uberFadeIn();
				}
			});
		}
		e.preventDefault();
	});
	
	$('#req_vals .req_val_type_chooser ul li a').on('click', function(e) {
		$(this).parentsUntil('.req_val_type_chooser').children('li.selected').removeClass('selected');
		$(this).parent().toggleClass('selected');
		$(this).parentsUntil('.req_val').parent().children('input.req_val_type').attr('value', $(this).text());
		e.preventDefault();
	});
	
	$('#req_vals .req_val .req_val_remove').live('click', function(e) {
		$(this).parent().uberFadeOut(function() {
			$(this).remove();
			if($('#req_vals .req_val').length == 0) {
				$('#req_vals').uberFadeOut();
			}
		});
		e.preventDefault();
	});
});